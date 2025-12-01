<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController; 
use App\Http\Controllers\UserController; // 💡 (BAGONG IMPORT)

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Ang mga route na ito ay karaniwang may /api prefix
|
*/

// --- 1. PUBLIC ROUTES (Walang Token Kailangan) ---
Route::post('/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

// --- 2. PROTECTED ROUTES (Kailangan ng Bearer Token) ---
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth Routes
    Route::post('/logout', [AuthController::class, 'logout']); // Logout route

    // Default User Route (para i-check kung sino ang naka-login)
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    
    // 💡 BAGONG ROUTE: Para i-update ang user profile gamit ang ID (Hal. PUT /api/user/14)
    Route::put('/user/{user}', [UserController::class, 'update']);
    // Maaari ring gamitin ang PATCH imbes na PUT, depende sa preference mo.

    // Product CRUD Routes (Lahat ng endpoints ay protected na ngayon)
    Route::apiResource('products', ProductController::class);
});