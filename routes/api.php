<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MuaApiController;
use App\Http\Controllers\Api\BookingApiController;
use App\Http\Controllers\Api\ReviewApiController;
use App\Http\Controllers\Api\ChatbotApiController;
use App\Http\Controllers\Api\SearchApiController;
use App\Http\Controllers\Api\MobileIntegrationController;
use App\Http\Controllers\Mua\BookingController as MuaBookingController;
use App\Http\Controllers\Mua\ServiceController;
use App\Http\Controllers\Api\NotificationApiController;


/*
|--------------------------------------------------------------------------
| BeautyHub API Routes
|--------------------------------------------------------------------------
*/   

// ── Public routes ──────────────────────────────────────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Mobile Integration (Public/Integrasi)
Route::get('/mobile/mua', [MobileIntegrationController::class, 'getMua']);
Route::post('/mobile/booking', [MuaBookingController::class, 'storeFromMobile']);
Route::get('/mua/{mua_id}/services', [ServiceController::class, 'apiIndex']);
Route::get(
    '/mua/{muaId}/availability',
    [MuaApiController::class, 'availability']
);

// ── Protected routes (Gunakan sanctum untuk Sanctum Token) ──────────
Route::middleware('auth:sanctum')->group(function () {

    // Notifications
    Route::get('/notifications',          [NotificationApiController::class, 'index']);
    Route::post('/notifications/read-all', [NotificationApiController::class, 'readAll']);

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // MUA - Discovery
    Route::prefix('muas')->group(function () {
        Route::get('/',              [MuaApiController::class, 'index']);
        Route::get('/{id}',          [MuaApiController::class, 'show']);
        Route::get('/{id}/portfolio', [MuaApiController::class, 'portfolio']);
        Route::get('/{id}/reviews',  [MuaApiController::class, 'reviews']);
    });

    // Bookings
    Route::prefix('bookings')->group(function () {
        Route::post('/',           [BookingApiController::class, 'store']);
        Route::get('/my',          [BookingApiController::class, 'myBookings']);
        Route::put('/{id}/cancel', [BookingApiController::class, 'cancel']);
    });

    // Reviews, Chat, Search
    Route::post('/reviews',           [ReviewApiController::class, 'store']);
    Route::post('/chatbot/message',   [ChatbotApiController::class, 'message']);
    Route::post('/search/by-image',   [SearchApiController::class, 'searchByImage']);
});

Route::get(
'/booking/{id}',
[BookingApiController::class,'show']
);