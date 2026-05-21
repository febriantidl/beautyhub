<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mua\AuthController;
use App\Http\Controllers\Mua\DashboardController;
use App\Http\Controllers\Mua\BookingController;

// ── Guest routes ──────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/mua/login', [AuthController::class, 'showLoginForm'])->name('mua.login');
    Route::post('/mua/login', [AuthController::class, 'login'])->name('mua.login.submit');
});

// ── Authenticated MUA/Admin routes ────────────────────────────────
Route::middleware(['auth', 'role:mua,admin'])
    ->prefix('mua')
    ->name('mua.')
    ->group(function () {
        // Auth
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Bookings
        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::get('/',              [BookingController::class, 'index'])->name('index');
            Route::get('/{id}',          [BookingController::class, 'show'])->name('show');
            Route::post('/{id}/approve', [BookingController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject',  [BookingController::class, 'reject'])->name('reject');
            Route::post('/{id}/complete',[BookingController::class, 'complete'])->name('complete');
            Route::post('/verify-qr',    [BookingController::class, 'verifyQr'])->name('verify-qr');
        });
    });

// ── Root redirect ─────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('mua.login'));
