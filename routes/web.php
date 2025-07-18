<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Auth\LoginController;

Route::post('/login', [LoginController::class, 'login']);

// 👇 Show login page
Route::get('/login', function () {
    return Inertia::render('Login');
})->name('login');

// 👇 Redirect root "/" to login
Route::get('/', function () {
    return redirect('/login');
});

// 👇 Handle login post
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => ['required'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credentials, $request->remember)) {
        $request->session()->regenerate();
        return redirect()->intended('/dashboard');
    }

    return back()->withErrors([
        'email' => 'Invalid credentials.',
    ]);
});

// ✅ Protected Routes (middleware: auth)
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', fn() => Inertia::render('Dashboard'))->name('dashboard');

    // Other Vue sections
    Route::get('/incoming', fn() => Inertia::render('Sections/Incoming'))->name('incoming');
    Route::get('/outgoing', fn() => Inertia::render('Sections/Outgoing'))->name('outgoing');
    Route::get('/travel', fn() => Inertia::render('Sections/Travel'))->name('travel');
    Route::get('/bur', fn() => Inertia::render('Sections/BUR'))->name('bur');
    Route::get('/settings', fn() => Inertia::render('Sections/Settings'))->name('settings');

    // Logout
    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');

    Route::get('/ictsd-outgoing', fn() => Inertia::render('Outgoing/ICTSD'))->name('ictsd-outgoing');
    Route::get('/cpd-outgoing', fn() => Inertia::render('Outgoing/CPD'))->name('cpd-outgoing');
});
