<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\GoogleController;
use Inertia\Inertia;
use Illuminate\Http\Request;

Route::middleware(['web'])->group(function () {
    // Main pages
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
        return Inertia::location('/login'); // ✅ Fixes Inertia logout
    });

    // ✅ Google OAuth — now using GoogleController
    Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);
});