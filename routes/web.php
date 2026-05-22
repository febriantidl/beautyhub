<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mua\AuthController;
use App\Http\Controllers\Mua\DashboardController;
use App\Http\Controllers\Mua\BookingController;
use App\Http\Controllers\Mua\PortfolioController;
use App\Http\Controllers\Mua\ServiceController;
use App\Http\Controllers\Mua\ProfileController;
use App\Http\Controllers\Mua\VerificationController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\MuaManagementController;
use App\Http\Controllers\Admin\UserManagementController;

// ── Root ───────────────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('mua.login'));

// ── Guest ──────────────────────────────────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/mua/login',  [AuthController::class, 'showLoginForm'])->name('mua.login');
    Route::post('/mua/login', [AuthController::class, 'login'])->name('mua.login.submit');
});

// ── Shared logout ──────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::post('/mua/logout', [AuthController::class, 'logout'])->name('mua.logout');
});

// ── MUA Panel ──────────────────────────────────────────────────────
Route::middleware(['auth', 'role:mua'])
    ->prefix('mua')
    ->name('mua.')
    ->group(function () {
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
            Route::get('/',        [PortfolioController::class, 'index'])->name('index');
            Route::post('/',       [PortfolioController::class, 'store'])->name('store');
            Route::put('/{id}',    [PortfolioController::class, 'update'])->name('update');
            Route::delete('/{id}', [PortfolioController::class, 'destroy'])->name('destroy');
        });

        // Services
        Route::prefix('services')->name('services.')->group(function () {
            Route::get('/',        [ServiceController::class, 'index'])->name('index');
            Route::post('/',       [ServiceController::class, 'store'])->name('store');
            Route::put('/{id}',    [ServiceController::class, 'update'])->name('update');
            Route::delete('/{id}', [ServiceController::class, 'destroy'])->name('destroy');
        });

        // Profile
        Route::get('/profile',          [ProfileController::class, 'index'])->name('profile');
        Route::put('/profile',          [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

        // QR Verification
        Route::get('/verification',  [VerificationController::class, 'index'])->name('verification');
        Route::post('/verification', [VerificationController::class, 'verify'])->name('verification.verify');
    });

// ── Admin Panel ────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // MUA Management
        Route::prefix('muas')->name('muas.')->group(function () {
            Route::get('/',                   [MuaManagementController::class, 'index'])->name('index');
            Route::post('/',                  [MuaManagementController::class, 'store'])->name('store');
            Route::get('/{id}',               [MuaManagementController::class, 'show'])->name('show');
            Route::post('/{id}/toggle-verified', [MuaManagementController::class, 'toggleVerified'])->name('toggle-verified');
        });

        // User Management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/',               [UserManagementController::class, 'index'])->name('index');
            Route::get('/{id}',           [UserManagementController::class, 'show'])->name('show');
            Route::post('/{id}/toggle-active',  [UserManagementController::class, 'toggleActive'])->name('toggle-active');
            Route::post('/{id}/update-role',    [UserManagementController::class, 'updateRole'])->name('update-role');
            Route::post('/{id}/reset-password', [UserManagementController::class, 'resetPassword'])->name('reset-password');
        });
    });

// ── Redirect admin/mua ke dashboard masing-masing setelah login ───
Route::middleware('auth')->get('/redirect', function () {
    return Auth::user()->isAdmin()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('mua.dashboard');
})->name('redirect');
