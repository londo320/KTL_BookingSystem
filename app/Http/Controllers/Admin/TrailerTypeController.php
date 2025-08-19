<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TrailerType;
use Illuminate\Http\Request;

class TrailerTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $showDeleted = $request->boolean('show_deleted', false);
        
        if ($showDeleted) {
            $query = TrailerType::withTrashed();
        } else {
            $query = TrailerType::query();
        }
        
        $query->withCount('bookings');

        // Filter by status
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                    $query->where('is_active', true);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'deleted':
                    $query->whereNotNull('deleted_at');
                    if (!$showDeleted) {
                        $query = TrailerType::withTrashed()->withCount('bookings');
                        $query->whereNotNull('deleted_at');
                        $showDeleted = true;
                    }
                    break;
            }
        }

        // Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $trailerTypes = $query->orderBy('name')->paginate(20);

        // Get statistics
        if ($showDeleted) {
            $stats = [
                'total' => TrailerType::withTrashed()->count(),
                'active' => TrailerType::withTrashed()->where('is_active', true)->whereNull('deleted_at')->count(),
                'inactive' => TrailerType::withTrashed()->where('is_active', false)->whereNull('deleted_at')->count(),
                'deleted' => TrailerType::onlyTrashed()->count(),
                'with_bookings' => TrailerType::withTrashed()->has('bookings')->count(),
            ];
        } else {
            $stats = [
                'total' => TrailerType::count(),
                'active' => TrailerType::where('is_active', true)->count(),
                'inactive' => TrailerType::where('is_active', false)->count(),
                'with_bookings' => TrailerType::has('bookings')->count(),
            ];
        }

        return view('admin.trailer-types.index', compact('trailerTypes', 'stats', 'showDeleted'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.trailer-types.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:trailer_types,name',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        TrailerType::create([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.trailer-types.index')
            ->with('success', 'Trailer type created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $trailerType = TrailerType::withTrashed()->withCount('bookings')->findOrFail($id);
        
        // Get recent bookings using this trailer type
        $recentBookings = $trailerType->bookings()
            ->with(['slot.depot', 'customer'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.trailer-types.show', compact('trailerType', 'recentBookings'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $trailerType = TrailerType::withTrashed()->findOrFail($id);
        return view('admin.trailer-types.edit', compact('trailerType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $trailerType = TrailerType::withTrashed()->findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:trailer_types,name,' . $trailerType->id,
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ]);

        $trailerType->update([
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.trailer-types.index')
            ->with('success', 'Trailer type updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $trailerType = TrailerType::withTrashed()->findOrFail($id);
        
        if (!$trailerType->canBeDeleted()) {
            return back()->withErrors(['error' => 'Cannot delete trailer type with existing bookings. Deactivate instead.']);
        }

        $trailerType->delete();

        return redirect()->route('admin.trailer-types.index')
            ->with('success', 'Trailer type deleted successfully.');
    }

    /**
     * Restore a soft-deleted trailer type
     */
    public function restore($id)
    {
        $trailerType = TrailerType::withTrashed()->findOrFail($id);
        $trailerType->restore();

        return back()->with('success', 'Trailer type restored successfully.');
    }

    /**
     * Toggle active status
     */
    public function toggle($id)
    {
        $trailerType = TrailerType::withTrashed()->findOrFail($id);
        $trailerType->update(['is_active' => !$trailerType->is_active]);

        $status = $trailerType->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Trailer type {$status} successfully.");
    }
}
