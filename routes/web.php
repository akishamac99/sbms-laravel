<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// The public landing page (your old index.php) comes later. For now, go to the login page.
Route::get('/', fn () => redirect()->route('login'))->name('home');

/*
|--------------------------------------------------------------------------
| Guests only (logged-in users are sent to their own dashboard)
|--------------------------------------------------------------------------
*/
Route::middleware('guest:customer,staff')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');

    Route::get('/register', [RegisterController::class, 'show'])->name('register');
    Route::post('/register', [RegisterController::class, 'sendOtp'])->name('register.send-otp');
    Route::post('/register/verify', [RegisterController::class, 'verify'])->name('register.verify');
    Route::post('/register/resend', [RegisterController::class, 'resend'])->name('register.resend');
    Route::post('/register/reset', [RegisterController::class, 'reset'])->name('register.reset');

    Route::get('/forgot-password', [PasswordResetController::class, 'showForgot'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendLink'])->name('password.email');
    Route::get('/reset-password/{broker}/{token}', [PasswordResetController::class, 'showReset'])->name('password.reset');
    Route::post('/reset-password/{broker}', [PasswordResetController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

/*
|--------------------------------------------------------------------------
| Customer area
|--------------------------------------------------------------------------
*/
Route::middleware('auth:customer')->prefix('customer')->name('customer.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'customer'])->name('dashboard');

    // Phase 2: services, booking, checkout, bookings, upload payment, feedback, notifications, profile
});

/*
|--------------------------------------------------------------------------
| Staff-side areas (all use the "staff" login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:staff')->group(function () {

    Route::middleware('role:staff')->prefix('staff')->name('staff.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'staff'])->name('dashboard');

        // Phase 3: bookings, walk-in, attendance, availability, profile
    });

    Route::middleware('role:manager')->prefix('manager')->name('manager.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'manager'])->name('dashboard');

        // Phase 3: bookings, payments, reports, attendance, customers, staff, services, walk-in
        // (each protected with perm:module, for example perm:bookings,edit)
    });

    Route::middleware('role:owner')->prefix('owner')->name('owner.')->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'owner'])->name('dashboard');

        // Phase 4: everything above plus promos, policies, permissions, logs, staff management
    });
});
