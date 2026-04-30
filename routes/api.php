<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MuaApiController;
use App\Http\Controllers\Api\BookingApiController;
use App\Http\Controllers\Api\ReviewApiController;
use App\Http\Controllers\Api\ChatbotApiController;
use App\Http\Controllers\Api\SearchApiController;

// Public routes (tanpa token)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (pakai JWT token)
Route::middleware(['auth:api'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // MUA
    Route::get('/muas', [MuaApiController::class, 'index']);
    Route::get('/muas/{id}', [MuaApiController::class, 'show']);
    Route::get('/muas/{id}/portfolio', [MuaApiController::class, 'portfolio']);
    Route::get('/muas/{id}/reviews', [MuaApiController::class, 'reviews']);

    // Booking
    Route::post('/bookings', [BookingApiController::class, 'store']);
    Route::get('/bookings/my', [BookingApiController::class, 'myBookings']);
    Route::put('/bookings/{id}/cancel', [BookingApiController::class, 'cancel']);

    // Review
    Route::post('/reviews', [ReviewApiController::class, 'store']);

    // Chatbot & Image Search
    Route::post('/chatbot/message', [ChatbotApiController::class, 'message']);
    Route::post('/search/by-image', [SearchApiController::class, 'searchByImage']);
});