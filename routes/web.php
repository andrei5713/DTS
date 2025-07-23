<!-- web.php -->
<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use Inertia\Inertia;
use Illuminate\Http\Request;

// Protected Routes
Route::middleware(['web'])->group(function () {
    Route::get('/home', function () {
        return Inertia::render('Home');
    })->name('home');

    Route::get('/', [LoginController::class, 'showLogin'])->name('login');
    Route::get('/login', [LoginController::class, 'showLogin']);
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Instead of a full HTTP redirect, do this:
        return Inertia::location('/login'); // ✅ Fixes the toString error
    });


    // Google OAuth
    Route::get('/auth/google', [LoginController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('/auth/google/callback', [LoginController::class, 'handleGoogleCallback']);
});
