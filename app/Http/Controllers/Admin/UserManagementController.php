<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $users = User::with('unit')->latest()->get();
        $units = Unit::where('is_active', true)->get();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'units' => $units,
        ]);
    }

    public function pendingUsers()
    {
        $pendingUsers = User::where('role', 'pending')->latest()->get();
        $units = Unit::where('is_active', true)->get();
        
        return Inertia::render('Admin/PendingUsers', [
            'pendingUsers' => $pendingUsers,
            'units' => $units,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:encoder,viewer',
            'unit_id' => 'nullable|exists:units,id',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'unit_id' => $request->unit_id,
        ]);

        return redirect()->back()->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user)
    {
        // Handle partial updates (e.g., role-only updates)
        if ($request->has('role') && !$request->has('name')) {
            $request->validate([
                'role' => 'required|in:encoder,viewer',
            ]);

            $user->update([
                'role' => $request->role,
            ]);

            return redirect()->back()->with('success', 'User role updated successfully.');
        }

        // Handle full user updates
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->id,
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:encoder,viewer',
            'unit_id' => 'nullable|exists:units,id',
        ]);

        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'role' => $request->role,
            'unit_id' => $request->unit_id,
        ]);

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();
        return redirect()->back()->with('success', 'User deleted successfully.');
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->back()->with('success', 'Password reset successfully.');
    }

    public function approveUser(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:encoder,viewer',
            'unit_id' => 'required|exists:units,id',
        ]);

        $user->update([
            'role' => $request->role,
            'unit_id' => $request->unit_id,
        ]);

        return redirect()->back()->with('success', 'User approved successfully');
    }

    public function bulkApprove(Request $request)
    {
        $request->validate([
            'users' => 'required|array',
            'users.*.id' => 'required|exists:users,id',
            'users.*.role' => 'required|in:encoder,viewer',
            'users.*.unit_id' => 'required|exists:units,id',
        ]);

        foreach ($request->users as $userData) {
            $user = User::find($userData['id']);
            if ($user && $user->role === 'pending') {
                $user->update([
                    'role' => $userData['role'],
                    'unit_id' => $userData['unit_id'],
                ]);
            }
        }

        return redirect()->back()->with('success', 'Users approved successfully');
    }

    public function bulkReject(Request $request)
    {
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'required|exists:users,id',
        ]);

        User::whereIn('id', $request->user_ids)->delete();

        return redirect()->back()->with('success', 'Users rejected successfully');
    }
}
