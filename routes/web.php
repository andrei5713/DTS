<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\GoogleController;
use Inertia\Inertia;

// Protected Routes
Route::middleware(['web'])->group(function () {
    Route::get('/home', function () {
        return Inertia::render('Home');
    })->name('home');

    Route::get('/', [LoginController::class, 'showLogin'])->name('login');
    Route::get('/login', [LoginController::class, 'showLogin']);
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


    // Google OAuth
    Route::get('/auth/google', [LoginController::class, 'redirectToGoogle'])->name('google.login');
    Route::get('/auth/google/callback', [LoginController::class, 'handleGoogleCallback']);
});
