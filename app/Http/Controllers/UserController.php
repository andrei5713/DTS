<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    /**
     * Get all users for autocomplete functionality
     */
    public function index(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        
        $users = User::with('unit')
            ->select('id', 'name', 'username', 'email', 'unit_id', 'role', 'created_at')
            ->where('role', '!=', 'admin') // Exclude admin users since they can't receive files
            ->when($query, function ($q) use ($query) {
                return $q->where('name', 'like', "%{$query}%")
                    ->orWhere('username', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            })
            ->orderBy('created_at', 'desc') // Show recently added accounts first
            ->get() // Remove limit to fetch all accounts
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'unit_name' => $user->unit->full_name ?? 'N/A',
                    'unit_id' => $user->unit_id,
                    'role' => $user->role,
                    'created_at' => $user->created_at
                ];
            });
        
        return response()->json($users);
    }
}
