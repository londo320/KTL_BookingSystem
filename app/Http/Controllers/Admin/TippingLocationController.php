<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Depot;
use App\Models\TippingLocation;
use Illuminate\Http\Request;

class TippingLocationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'function-access']);
    }

    public function index(Request $request)
    {
        $allowedDepotIds = $this->getAllowedDepotIds();
        
        // Get user's default depot for action restrictions
        $user = auth()->user();
        $defaultDepotId = $user->depot_id ?? $allowedDepotIds[0] ?? null;
        
        // Allow viewing all depots but note which is default for actions
        $selectedDepotId = $request->get('depot_id');
        
        // Show default depot initially, unless another depot is selected
        if ($selectedDepotId && in_array($selectedDepotId, $allowedDepotIds)) {
            $currentDepotId = $selectedDepotId;
        } elseif ($selectedDepotId === "") {
            // Explicitly selected "All Depots"
            $currentDepotId = null;
        } else {
            // Default to user's default depot
            $currentDepotId = $defaultDepotId;
        }

        $query = TippingLocation::with(['depot'])
            ->whereIn('depot_id', $allowedDepotIds);

        // Filter by current depot if specified
        if ($currentDepotId) {
            $query->where('depot_id', $currentDepotId);
        }

        $locations = $query->withCount(['activeBookings'])
            ->orderBy('depot_id')
            ->orderBy('name')
            ->paginate(20);

        // Get all depots for filter dropdown
        $allDepots = \App\Models\Depot::whereIn('id', $allowedDepotIds)->get();

        return view('admin.tipping-locations.index', compact(
            'locations', 
            'allDepots', 
            'currentDepotId',
            'defaultDepotId'
        ));
    }

    public function create()
    {
        $depots = $this->getAllowedDepots();

        return view('admin.tipping-locations.create', compact('depots'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'depot_id' => 'required|exists:depots,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:tipping_locations,code,NULL,id,depot_id,'.$request->depot_id,
            'description' => 'nullable|string',
            'location_type' => 'required|in:drop_zone,collection_zone,general',
            'capacity' => 'required|integer|min:1|max:50',
            'is_active' => 'boolean',
        ]);

        // Check depot access for depot-admin
        if (auth()->user()->hasRole('depot-admin')) {
            $allowedDepotIds = auth()->user()->depots()->pluck('depots.id')->toArray();
            if (! in_array($request->depot_id, $allowedDepotIds)) {
                return back()->withErrors(['depot_id' => 'You do not have access to this depot.']);
            }
        }

        TippingLocation::create($validated);

        return redirect()
            ->route('admin.tipping-locations.index')
            ->with('success', 'Tipping location created successfully.');
    }

    public function show(TippingLocation $tippingLocation)
    {
        $tippingLocation->load([
            'depot',
            'activeBookings' => function ($query) {
                $query->latest()->limit(20);
            },
            'activeBookings.customer',
        ]);

        // Get current occupancy status
        $currentOccupancy = $tippingLocation->getCurrentOccupancy();
        $availableCapacity = $tippingLocation->getAvailableCapacity();

        // Get user's default depot for action restrictions
        $allowedDepotIds = $this->getAllowedDepotIds();
        $user = auth()->user();
        $defaultDepotId = $user->depot_id ?? $allowedDepotIds[0] ?? null;

        return view('admin.tipping-locations.show', compact(
            'tippingLocation',
            'currentOccupancy',
            'availableCapacity',
            'defaultDepotId'
        ));
    }

    public function edit(TippingLocation $tippingLocation)
    {
        // Check depot access for depot-admin
        if (auth()->user()->hasRole('depot-admin')) {
            $allowedDepotIds = auth()->user()->depots()->pluck('depots.id')->toArray();
            if (! in_array($tippingLocation->depot_id, $allowedDepotIds)) {
                abort(403, 'You do not have access to this depot.');
            }
        }

        $depots = $this->getAllowedDepots();

        return view('admin.tipping-locations.edit', compact('tippingLocation', 'depots'));
    }

    public function update(Request $request, TippingLocation $tippingLocation)
    {
        $validated = $request->validate([
            'depot_id' => 'required|exists:depots,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:tipping_locations,code,'.$tippingLocation->id.',id,depot_id,'.$request->depot_id,
            'description' => 'nullable|string',
            'location_type' => 'required|in:drop_zone,collection_zone,general',
            'capacity' => 'required|integer|min:1|max:50',
            'is_active' => 'boolean',
        ]);

        // Check depot access for depot-admin
        if (auth()->user()->hasRole('depot-admin')) {
            $allowedDepotIds = auth()->user()->depots()->pluck('depots.id')->toArray();
            if (! in_array($request->depot_id, $allowedDepotIds) || ! in_array($tippingLocation->depot_id, $allowedDepotIds)) {
                return back()->withErrors(['depot_id' => 'You do not have access to this depot.']);
            }
        }

        $tippingLocation->update($validated);

        return redirect()
            ->route('admin.tipping-locations.show', $tippingLocation)
            ->with('success', 'Tipping location updated successfully.');
    }

    public function destroy(TippingLocation $tippingLocation)
    {
        // Check depot access for depot-admin
        if (auth()->user()->hasRole('depot-admin')) {
            $allowedDepotIds = auth()->user()->depots()->pluck('depots.id')->toArray();
            if (! in_array($tippingLocation->depot_id, $allowedDepotIds)) {
                abort(403, 'You do not have access to this depot.');
            }
        }

        // Check if location has active bookings
        if ($tippingLocation->activeBookings()->count() > 0) {
            return back()->withErrors(['delete' => 'Cannot delete location with active bookings.']);
        }
        
        // Check if location has any historical movements
        $movementsCount = \App\Models\Movement::where('tipping_location_id', $tippingLocation->id)->count();
        if ($movementsCount > 0) {
            // Soft delete: deactivate instead of deleting
            $tippingLocation->update(['is_active' => false]);
            return redirect()
                ->route('admin.tipping-locations.index')
                ->with('success', "Location '{$tippingLocation->name}' has been deactivated (soft deleted) due to {$movementsCount} historical movement records. You can reactivate it later if needed.");
        }

        try {
            $tippingLocation->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            if ($e->errorInfo[1] == 1451) { // Foreign key constraint error
                // Soft delete: deactivate instead of deleting
                $tippingLocation->update(['is_active' => false]);
                return redirect()
                    ->route('admin.tipping-locations.index')
                    ->with('success', "Location '{$tippingLocation->name}' has been deactivated (soft deleted) as it is still referenced by other records. You can reactivate it later if needed.");
            }
            throw $e; // Re-throw if it's a different error
        }

        return redirect()
            ->route('admin.tipping-locations.index')
            ->with('success', 'Tipping location deleted successfully.');
    }
    
    public function toggleActive(TippingLocation $tippingLocation)
    {
        // Check depot access
        if (auth()->user()->depot_id) {
            if ($tippingLocation->depot_id !== auth()->user()->depot_id) {
                abort(403, 'You do not have access to this depot.');
            }
        }
        
        $tippingLocation->update([
            'is_active' => !$tippingLocation->is_active
        ]);
        
        $status = $tippingLocation->is_active ? 'activated' : 'deactivated';
        
        return redirect()
            ->route('admin.tipping-locations.index')
            ->with('success', "Tipping location '{$tippingLocation->name}' has been {$status}.");
    }

    private function getAllowedDepots()
    {
        if (auth()->user()->hasRole('admin|site-admin')) {
            return Depot::orderBy('name')->get();
        }

        // For depot-admin, only show their assigned depots
        return auth()->user()->depots()->orderBy('name')->get();
    }

    private function getAllowedDepotIds()
    {
        $assignedDepotIds = auth()->user()->depots()->pluck('depots.id')->toArray();

        if (empty($assignedDepotIds) && auth()->user()->hasRole('admin|site-admin')) {
            return \App\Models\Depot::pluck('id')->toArray();
        }

        return $assignedDepotIds;
    }
}
