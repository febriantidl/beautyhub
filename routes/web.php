<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mua\AuthController;
use App\Http\Controllers\Mua\DashboardController;

// Guest (belum login)
Route::middleware('guest')->group(function () {
    Route::get('/mua/login', [AuthController::class, 'showLoginForm'])->name('mua.login');
    Route::post('/mua/login', [AuthController::class, 'login']);
});

// Authenticated MUA/Admin
Route::middleware(['auth', 'role:mua,admin'])->prefix('mua')->name('mua.')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Redirect root ke login MUA
Route::get('/', function () {
    return redirect()->route('mua.login');
});

Route::post('/mua/bookings/{id}/approve', [App\Http\Controllers\Mua\BookingController::class, 'approve'])->name('mua.bookings.approve');
Route::post('/mua/bookings/{id}/reject', [App\Http\Controllers\Mua\BookingController::class, 'reject'])->name('mua.bookings.reject');