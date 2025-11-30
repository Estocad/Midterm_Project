<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthController; 

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
    Route::post('/logout', [AuthController::class, 'logout']); // <-- Naka-protect na!

    // Default User Route (para i-check kung sino ang naka-login)
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Product CRUD Routes (Lahat ng endpoints ay protected na ngayon)
    // Tiyakin na ang index, store, show, update, destroy ay protected
    Route::apiResource('products', ProductController::class);
    
    // Kung hindi gumagana ang apiResource, puwede mo ring gamitin ito:
    // Route::get('/products/{product}', [ProductController::class, 'show']);
});