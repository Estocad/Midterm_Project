<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Mag-register ng bagong user.
     */
    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
        ]);

        // Awtomatikong mag-generate ng token pagkatapos mag-register
        $token = $user->createToken('myapptoken')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Mag-login ng user at mag-issue ng token.
     */
    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // I-check ang email
        $user = User::where('email', $validatedData['email'])->first();

        // I-check ang password
        if (!$user || !Hash::check($validatedData['password'], $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ], 401); // 401 Unauthorized
        }

        // Mag-generate ng bagong token
        $token = $user->createToken('myapptoken')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    /**
     * I-logout ang user (I-revoke ang kasalukuyang token).
     */
    public function logout(Request $request)
    {
        // 💡 ITO ANG SOLUSYON SA 401 ERROR MO SA LOGOUT:
        // Dapat siguraduhin na ang token na ginagamit ay dine-delete.
        // Ang 'auth:sanctum' middleware sa api.php ay nagpapares ng token, 
        // kaya ang auth()->user() ay nagre-return ng authenticated user.
        
        // Burahin lang ang kasalukuyang token na ginamit sa request na ito.
        $request->user()->currentAccessToken()->delete(); 

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out.'
        ], 200);
    }
}