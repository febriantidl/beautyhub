<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MuaApiController;
use App\Http\Controllers\Api\BookingApiController;
use App\Http\Controllers\Api\ReviewApiController;
use App\Http\Controllers\Api\ChatbotApiController;
use App\Http\Controllers\Api\SearchApiController;

/*
|--------------------------------------------------------------------------
| BeautyHub API Routes
|--------------------------------------------------------------------------
| Semua route di sini ter-prefix /api secara otomatis.
*/

// ── Public routes (tidak perlu token) ────────────────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// ── Protected routes (butuh JWT Bearer Token) ─────────────────────
Route::middleware('auth:api')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // MUA - Discovery
    Route::prefix('muas')->group(function () {
        Route::get('/',                  [MuaApiController::class, 'index']);
        Route::get('/{id}',              [MuaApiController::class, 'show']);
        Route::get('/{id}/portfolio',    [MuaApiController::class, 'portfolio']);
        Route::get('/{id}/reviews',      [MuaApiController::class, 'reviews']);
    });

    // Bookings
    Route::prefix('bookings')->group(function () {
        Route::post('/',          [BookingApiController::class, 'store']);
        Route::get('/my',         [BookingApiController::class, 'myBookings']);
        Route::put('/{id}/cancel',[BookingApiController::class, 'cancel']);
    });

    // Reviews
    Route::post('/reviews', [ReviewApiController::class, 'store']);

    // Chatbot
    Route::post('/chatbot/message', [ChatbotApiController::class, 'message']);

    // Image Search
    Route::post('/search/by-image', [SearchApiController::class, 'searchByImage']);
});
