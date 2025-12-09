<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Helpers\XmlHelper; // ADD THIS

class AuthController extends Controller
{
    /**
     * Handle response format (JSON or XML)
     */
    private function formatResponse($data, $status = 200, $headers = [])
    {
        $request = request();
        
        if (XmlHelper::wantsXml($request)) {
            return XmlHelper::toXml($data, 'response', $status, $headers);
        }
        
        return response()->json($data, $status, $headers);
    }

    /**
     * Parse request data based on content type
     */
    private function parseRequestData(Request $request)
    {
        if (XmlHelper::isXml($request)) {
            $xmlContent = $request->getContent();
            return XmlHelper::toArray($xmlContent);
        }
        
        return $request->all();
    }

    /**
     * Mag-register ng bagong user.
     */
    public function register(Request $request)
    {
        // Parse data based on content type (XML or JSON)
        $requestData = $this->parseRequestData($request);
        $request->merge($requestData);
        
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

        return $this->formatResponse([
            'status' => 'success',
            'message' => 'User registered successfully',
            'data' => [
                'user' => $user,
                'token' => $token,
            ]
        ], 201);
    }

    /**
     * Mag-login ng user at mag-issue ng token.
     */
    public function login(Request $request)
    {
        // Parse data based on content type (XML or JSON)
        $requestData = $this->parseRequestData($request);
        $request->merge($requestData);
        
        $validatedData = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // I-check ang email
        $user = User::where('email', $validatedData['email'])->first();

        // I-check ang password
        if (!$user || !Hash::check($validatedData['password'], $user->password)) {
            return $this->formatResponse([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ], 401);
        }

        // Mag-generate ng bagong token
        $token = $user->createToken('myapptoken')->plainTextToken;

        return $this->formatResponse([
            'status' => 'success',
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $token,
            ]
        ], 200);
    }

    /**
     * I-logout ang user (I-revoke ang kasalukuyang token).
     */
    public function logout(Request $request)
    {
        // Burahin ang kasalukuyang token na ginamit sa request na ito.
        $request->user()->currentAccessToken()->delete(); 

        return $this->formatResponse([
            'status' => 'success',
            'message' => 'Successfully logged out.'
        ], 200);
    }
    
    /**
     * Get current authenticated user
     */
    public function user(Request $request)
    {
        return $this->formatResponse([
            'status' => 'success',
            'message' => 'User retrieved',
            'data' => [
                'user' => $request->user()
            ]
        ]);
    }
}