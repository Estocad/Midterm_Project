<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
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
     * Handle user registration.
     */
    public function register(Request $request)
    {
        // Parse data based on content type (XML or JSON)
        $requestData = $this->parseRequestData($request);
        $request->merge($requestData);
        
        // 1. Validation 
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // 2. Create User
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => bcrypt($validatedData['password']),
        ]);

        // 3. Generate Token
        $token = $user->createToken('auth_token')->plainTextToken;

        // 4. Return response
        return $this->formatResponse([
            'status' => 'success',
            'message' => 'User registered and logged in successfully.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'created_at' => $user->created_at,
                ],
                'token' => [
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ]
            ]
        ], 201);
    }
    
    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        // Parse data based on content type (XML or JSON)
        $requestData = $this->parseRequestData($request);
        $request->merge($requestData);
        
        // Simple validation
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt to log in
        if (!Auth::attempt($validatedData)) {
            return $this->formatResponse([
                'status' => 'error',
                'message' => 'Invalid login credentials.'
            ], 401);
        }

        // Kumuha ng user
        $user = User::where('email', $validatedData['email'])->first();

        // Gumawa ng token
        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->formatResponse([
            'status' => 'success',
            'message' => 'Login successful.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ],
                'token' => [
                    'access_token' => $token,
                    'token_type' => 'Bearer',
                ]
            ]
        ]);
    }
    
    /**
     * Handle user logout (protected route).
     */
    public function logout(Request $request)
    {
        // I-delete ang kasalukuyang token
        $request->user()->currentAccessToken()->delete();

        return $this->formatResponse([
            'status' => 'success',
            'message' => 'Successfully logged out.'
        ]);
    }
    
    /**
     * Get current authenticated user
     */
    public function user(Request $request)
    {
        $user = $request->user();
        
        return $this->formatResponse([
            'status' => 'success',
            'message' => 'User retrieved successfully.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'email_verified_at' => $user->email_verified_at,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]
            ]
        ]);
    }
}