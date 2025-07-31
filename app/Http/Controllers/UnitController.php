<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::where('is_active', true)->get();
        return response()->json($units);
    }
}
