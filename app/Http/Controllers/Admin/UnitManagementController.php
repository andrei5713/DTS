<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UnitManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Unit::withCount('users');

        // Search functionality
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%')
                  ->orWhere('full_name', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by status
        if ($request->status !== null) {
            $query->where('is_active', $request->status);
        }

        $units = $query->latest()->paginate(10);

        return Inertia::render('Admin/Units/Index', [
            'units' => $units,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:units',
            'full_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        Unit::create([
            'name' => $request->name,
            'code' => $request->code,
            'full_name' => $request->full_name,
            'description' => $request->description,
            'is_active' => $request->is_active ?? true,
        ]);

        return redirect()->back()->with('success', 'Unit created successfully.');
    }

    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:units,code,' . $unit->id,
            'full_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $unit->update([
            'name' => $request->name,
            'code' => $request->code,
            'full_name' => $request->full_name,
            'description' => $request->description,
            'is_active' => $request->is_active,
        ]);

        return redirect()->back()->with('success', 'Unit updated successfully.');
    }

    public function destroy(Unit $unit)
    {
        if ($unit->users()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete unit with assigned users.');
        }

        $unit->delete();
        return redirect()->back()->with('success', 'Unit deleted successfully.');
    }
}
