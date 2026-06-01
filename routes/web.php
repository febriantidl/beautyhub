<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mua\AuthController as MuaAuthController;
use App\Http\Controllers\Mua\DashboardController;
use App\Http\Controllers\Mua\BookingController;
use App\Http\Controllers\Mua\PortfolioController;
use App\Http\Controllers\Mua\ServiceController;
use App\Http\Controllers\Mua\VerificationController;
use App\Http\Controllers\Mua\ProfileController;

Route::get('/', fn() => redirect()->route('mua.login'));

Route::middleware('guest')->group(function () {
    Route::get('/mua/login', [MuaAuthController::class, 'showLoginForm'])->name('mua.login');
    Route::post('/mua/login', [MuaAuthController::class, 'login'])->name('mua.login.submit');
    Route::get('/register', [MuaAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [MuaAuthController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/mua/logout', [MuaAuthController::class, 'logout'])->name('mua.logout');
    
    Route::middleware('role:mua')->prefix('mua')->name('mua.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Profile
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
        Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
        
        // Verification
        Route::get('/verification', [VerificationController::class, 'index'])->name('verification');
        Route::post('/verification/verify', [VerificationController::class, 'verify'])->name('verification.verify');
        
        // Services
        // Cari bagian services di web.php, ubah jadi ini:
Route::prefix('services')->name('services.')->group(function () {
    Route::get('/', [ServiceController::class, 'index'])->name('index');
    Route::post('/', [ServiceController::class, 'store'])->name('store');
    Route::put('/{id}', [ServiceController::class, 'update'])->name('update'); // Wajib PUT
    Route::delete('/{id}', [ServiceController::class, 'destroy'])->name('destroy');
});

        Route::prefix('bookings')->name('bookings.')->group(function () {
    Route::get('/', [BookingController::class, 'index'])->name('index');
    Route::get('/{id}', [BookingController::class, 'show'])->name('show');
    Route::post('/{id}/approve', [BookingController::class, 'approve'])->name('approve');
    Route::post('/{id}/reject', [BookingController::class, 'reject'])->name('reject');
    Route::post('/{id}/complete', [BookingController::class, 'complete'])->name('complete');

    Route::post(
        '/verify-qr',
        [BookingController::class, 'verifyQr']
    )->name('verify-qr');

}); // <- INI YANG HILANG

// Portfolio
        Route::prefix('portfolio')->name('portfolio.')->group(function () {
            Route::get('/', [PortfolioController::class, 'index'])->name('index');
            Route::post('/', [PortfolioController::class, 'store'])->name('store');
            Route::delete('/{id}', [PortfolioController::class, 'destroy'])->name('destroy');
        });

    }); // role:mua

}); // auth