<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Hindi ito kailangan kung bcrypt() ang ginamit, pero maganda ring may Hash facade
use App\Models\User; // Tiyakin na naka-import ang User Model

class AuthController extends Controller
{
    /**
     * Handle user registration.
     */
    public function register(Request $request)
    {
        // 1. Validation 
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // 2. Create User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password), 
        ]);

        // 3. Generate Token
        $token = $user->createToken('auth_token')->plainTextToken;

        // 4. Return response
        return response()->json([
            'status' => 'success',
            'message' => 'User registered and logged in successfully.',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ], 201);
    }
    
    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        // Simple validation
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to log in
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid login credentials.'
            ], 401);
        }

        // Kumuha ng user
        $user = User::where('email', $request->email)->first();

        // Gumawa ng token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful.',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }
    
    /**
     * Handle user logout (protected route).
     */
    public function logout(Request $request)
    {
        // I-delete ang kasalukuyang token
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out.'
        ]);
    }
}