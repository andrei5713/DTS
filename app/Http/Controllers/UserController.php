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
        
        $users = User::select('id', 'name', 'username', 'email')
            ->when($query, function ($q) use ($query) {
                return $q->where('name', 'like', "%{$query}%")
                    ->orWhere('username', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            })
            ->limit(10)
            ->get();
        
        return response()->json($users);
    }
}
