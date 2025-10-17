<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class DepartmentHeadController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        return Inertia::render('DepartmentHead/Dashboard', [
            'user' => $user,
            'unit' => $user->unit,
        ]);
    }
}
