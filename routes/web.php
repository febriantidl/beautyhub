<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mua\AuthController;
use App\Http\Controllers\Mua\DashboardController;
use App\Http\Controllers\Mua\BookingController;
use App\Http\Controllers\Mua\PortfolioController;
use App\Http\Controllers\Mua\ServiceController;
use App\Http\Controllers\Mua\ProfileController;
use App\Http\Controllers\Mua\VerificationController;

// ── Guest ──────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/mua/login',  [AuthController::class, 'showLoginForm'])->name('mua.login');
    Route::post('/mua/login', [AuthController::class, 'login'])->name('mua.login.submit');
});

// ── Authenticated MUA/Admin ────────────────────────────────────────
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
            Route::get('/',               [BookingController::class, 'index'])->name('index');
            Route::get('/{id}',           [BookingController::class, 'show'])->name('show');
            Route::post('/{id}/approve',  [BookingController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject',   [BookingController::class, 'reject'])->name('reject');
            Route::post('/{id}/complete', [BookingController::class, 'complete'])->name('complete');
            Route::post('/verify-qr',     [BookingController::class, 'verifyQr'])->name('verify-qr');
        });

        // Portfolio
        Route::prefix('portfolio')->name('portfolio.')->group(function () {
            Route::get('/',          [PortfolioController::class, 'index'])->name('index');
            Route::post('/',         [PortfolioController::class, 'store'])->name('store');
            Route::put('/{id}',      [PortfolioController::class, 'update'])->name('update');
            Route::delete('/{id}',   [PortfolioController::class, 'destroy'])->name('destroy');
        });

        // Services / Layanan
        Route::prefix('services')->name('services.')->group(function () {
            Route::get('/',         [ServiceController::class, 'index'])->name('index');
            Route::post('/',        [ServiceController::class, 'store'])->name('store');
            Route::put('/{id}',     [ServiceController::class, 'update'])->name('update');
            Route::delete('/{id}',  [ServiceController::class, 'destroy'])->name('destroy');
        });

        // Profile
        Route::get('/profile',           [ProfileController::class, 'index'])->name('profile');
        Route::put('/profile',           [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password',  [ProfileController::class, 'updatePassword'])->name('profile.password');

        // QR Verification
        Route::get('/verification',      [VerificationController::class, 'index'])->name('verification');
        Route::post('/verification',     [VerificationController::class, 'verify'])->name('verification.verify');
    });

// ── Root ───────────────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('mua.login'));
