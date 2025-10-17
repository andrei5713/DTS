<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\UnitManagementController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DepartmentHeadController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

// Public Routes (Guest Middleware)
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
    Route::get('/login', [LoginController::class, 'showLoginForm']);
    Route::post('/', [LoginController::class, 'login']); // Add POST route for root
    Route::post('/login', [LoginController::class, 'login']);

    // Registration
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    // Password Reset Request
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

    // Password Reset Form
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Protected Routes (Auth Middleware)
Route::middleware('auth')->group(function () {
    Route::get('/home', function () {
        $user = Auth::user();
        
        if ($user->role === 'admin') {
            // Include admin data for admin users
            $stats = [
                'total_users' => \App\Models\User::count(),
                'admin_users' => \App\Models\User::where('role', 'admin')->count(),
                'encoder_users' => \App\Models\User::where('role', 'encoder')->count(),
                'viewer_users' => \App\Models\User::where('role', 'viewer')->count(),
                'pending_users' => \App\Models\User::where('role', 'pending')->count(),
                'total_units' => \App\Models\Unit::count(),
                'active_units' => \App\Models\Unit::where('is_active', true)->count(),
            ];

            $recent_users = \App\Models\User::with('unit')
                ->latest()
                ->take(5)
                ->get();

            $all_users = \App\Models\User::with('unit')->latest()->get();
            
            $pending_users = \App\Models\User::where('role', 'pending')->latest()->get();
            $units = \App\Models\Unit::where('is_active', true)->get();

            return Inertia::render('Home', [
                'stats' => $stats,
                'recent_users' => $recent_users,
                'users' => $all_users,
                'pendingUsers' => $pending_users,
                'units' => $units,
            ]);
        } elseif ($user->role === 'encoder') {
            // Encoders will see their dashboard in the Home page
            return Inertia::render('Home');
        }
        
        return Inertia::render('Home');
    })->name('home');

    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Units API
    Route::get('/api/units', [UnitController::class, 'index'])->name('api.units');
    
    // Users API
    Route::get('/api/users', [UserController::class, 'index'])->name('api.users');
    
    // Document routes
    Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
    Route::get('/documents/incoming', [DocumentController::class, 'incoming'])->name('documents.incoming');
    Route::get('/documents/outgoing', [DocumentController::class, 'outgoing'])->name('documents.outgoing');
    Route::get('/documents/archived', [DocumentController::class, 'archived'])->name('documents.archived');
    Route::get('/api/documents/{document}', [DocumentController::class, 'show'])->name('api.documents.show');
    Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
    Route::post('/documents/{document}/receive', [DocumentController::class, 'receive'])->name('documents.receive');
    Route::get('/documents/{document}/related', [DocumentController::class, 'getRelatedDocuments'])->name('documents.related');
    Route::post('/documents/{document}/forward', [DocumentController::class, 'forward'])->name('documents.forward');
    Route::post('/documents/{document}/reject', [DocumentController::class, 'reject'])->name('documents.reject');
    Route::post('/documents/{document}/comply', [DocumentController::class, 'comply'])->name('documents.comply');
    Route::post('/documents/{document}/archive', [DocumentController::class, 'archive'])->name('documents.archive');
    Route::post('/documents/{document}/unarchive', [DocumentController::class, 'unarchive'])->name('documents.unarchive');
    Route::get('/api/can-upload', [DocumentController::class, 'canUserUpload'])->name('api.can-upload');
    Route::get('/api/documents/{document}/can-perform-action', [DocumentController::class, 'canUserPerformAction'])->name('api.can-perform-action');
    Route::get('/api/debug-user-documents', [DocumentController::class, 'debugUserDocuments'])->name('api.debug-user-documents');
    
    // Document Response routes
    Route::get('/api/documents/{document}/responses', [DocumentController::class, 'getResponses'])->name('api.documents.responses');
    Route::post('/api/documents/{document}/responses', [DocumentController::class, 'storeResponse'])->name('api.documents.responses.store');
    Route::get('/api/responses/unread-count', [DocumentController::class, 'getUnreadResponseCount'])->name('api.responses.unread-count');
    
    // Notification routes
    Route::get('/api/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('api.notifications');
    Route::post('/api/notifications/{notification}/read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('api.notifications.read');
    Route::post('/api/notifications/read-all', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('api.notifications.read-all');
    Route::get('/api/notifications/unread-count', [App\Http\Controllers\NotificationController::class, 'unreadCount'])->name('api.notifications.unread-count');

    // Admin Routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::get('/pending-users', [UserManagementController::class, 'pendingUsers'])->name('pending-users.index');
        
        // User Management
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
        Route::put('/users/{user}/approve', [UserManagementController::class, 'approveUser'])->name('users.approve');
        Route::post('/users/bulk-approve', [UserManagementController::class, 'bulkApprove'])->name('users.bulk-approve');
        Route::post('/users/bulk-reject', [UserManagementController::class, 'bulkReject'])->name('users.bulk-reject');
        Route::post('/users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.reset-password');
        
        // Unit Management
        Route::get('/units', [UnitManagementController::class, 'index'])->name('units.index');
        Route::post('/units', [UnitManagementController::class, 'store'])->name('units.store');
        Route::put('/units/{unit}', [UnitManagementController::class, 'update'])->name('units.update');
        Route::delete('/units/{unit}', [UnitManagementController::class, 'destroy'])->name('units.destroy');
    });

    // Encoder Routes (handled in Home page now)
    // Route::middleware('encoder')->prefix('encoder')->name('encoder.')->group(function () {
    //     Route::get('/dashboard', [DepartmentHeadController::class, 'dashboard'])->name('dashboard');
    // });
});

// Optional: Google OAuth (if you want to keep it)
// Route::get('/auth/google', [LoginController::class, 'redirectToGoogle'])->name('google.login');
// Route::get('/auth/google/callback', [LoginController::class, 'handleGoogleCallback']);
