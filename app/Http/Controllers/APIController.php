<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Helpers\XmlHelper; // ADD THIS LINE

class APIController extends Controller
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
     * Kumuha ng listahan ng lahat ng users (READ operation).
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request) // ADD Request parameter
    {
        // Kunin ang lahat ng data mula sa User table
        $users = User::all();

        // Ibalik ang data bilang JSON/XML response
        return $this->formatResponse([
            'status' => 'success',
            'message' => 'Users retrieved successfully',
            'data' => [
                'users' => $users
            ]
        ]);
    }
    
    /**
     * CREATE - Mag-save ng bagong user
     */
    public function store(Request $request)
    {
        // Parse data based on content type (XML or JSON)
        $requestData = $this->parseRequestData($request);
        $request->merge($requestData);
        
        // Validation
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users|max:255',
            'password' => 'required|string|min:8',
        ]);

        // Hash the password
        $validatedData['password'] = bcrypt($validatedData['password']);

        // Create user
        $user = User::create($validatedData);

        return $this->formatResponse([
            'status' => 'success',
            'message' => 'User created successfully',
            'data' => [
                'user' => $user
            ]
        ], 201);
    }

    /**
     * READ Specific - Kumuha ng isang user
     */
    public function show(Request $request, $id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return $this->formatResponse([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        return $this->formatResponse([
            'status' => 'success',
            'message' => 'User retrieved successfully',
            'data' => [
                'user' => $user
            ]
        ]);
    }

    /**
     * UPDATE - Baguhin ang user data
     */
    public function update(Request $request, $id)
    {
        // Parse data based on content type (XML or JSON)
        $requestData = $this->parseRequestData($request);
        $request->merge($requestData);
        
        $user = User::find($id);
        
        if (!$user) {
            return $this->formatResponse([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        // Validation
        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:8',
        ]);

        // Hash password if provided
        if (isset($validatedData['password'])) {
            $validatedData['password'] = bcrypt($validatedData['password']);
        }

        // Update user
        $user->update($validatedData);

        return $this->formatResponse([
            'status' => 'success',
            'message' => 'User updated successfully',
            'data' => [
                'user' => $user
            ]
        ]);
    }

    /**
     * DELETE - Burahin ang user
     */
    public function destroy(Request $request, $id)
    {
        $user = User::find($id);
        
        if (!$user) {
            return $this->formatResponse([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);
        }

        $user->delete();

        return $this->formatResponse([
            'status' => 'success',
            'message' => 'User deleted successfully'
        ]);
    }
}