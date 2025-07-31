<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Unit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'admin_users' => User::where('role', 'admin')->count(),
            'department_users' => User::where('role', 'department')->count(),
            'regular_users' => User::where('role', 'user')->count(),
            'pending_users' => User::where('role', 'pending')->count(),
            'total_units' => Unit::count(),
            'active_units' => Unit::where('is_active', true)->count(),
        ];

        $recent_users = User::with('unit')
            ->latest()
            ->take(5)
            ->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recent_users' => $recent_users,
        ]);
    }
}
