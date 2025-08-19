<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Traits\NormalizeInputTrait;
use App\Models\Carrier;
use App\Models\Depot;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CarrierController extends Controller
{
    use NormalizeInputTrait;
    public function index(Request $request)
    {
        // Show deleted carriers only if explicitly requested
        $showDeleted = $request->boolean('show_deleted', false);
        
        if ($showDeleted) {
            $query = Carrier::withTrashed();
        } else {
            $query = Carrier::query();
        }
        
        $query->withCount('bookings')->with('depots');

        // Filter by status
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                    $query->where('is_active', true);
                    if ($showDeleted) {
                        $query->whereNull('deleted_at');
                    }
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    if ($showDeleted) {
                        $query->whereNull('deleted_at');
                    }
                    break;
                case 'deleted':
                    $query->whereNotNull('deleted_at');
                    if (!$showDeleted) {
                        // Force show deleted when this filter is selected
                        $query = Carrier::withTrashed()->withCount('bookings')->with('depots');
                        $query->whereNotNull('deleted_at');
                        $showDeleted = true;
                    }
                    break;
                case 'pending':
                    $query->where('requires_approval', true);
                    if ($showDeleted) {
                        $query->whereNull('deleted_at');
                    }
                    break;
                case 'merged':
                    // Show carriers that have been merged (inactive with specific naming pattern)
                    $query->where('is_active', false)
                          ->where('name', 'like', '%(MERGED INTO:%');
                    if ($showDeleted) {
                        $query->whereNull('deleted_at');
                    }
                    break;
            }
        }

        // Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Sort
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('direction', 'asc');
        
        if ($sortField === 'bookings_count') {
            $query->orderBy('bookings_count', $sortDirection);
        } elseif ($sortField === 'last_used_at') {
            $query->orderBy('last_used_at', $sortDirection);
        } else {
            $query->orderBy($sortField, $sortDirection);
        }

        $carriers = $query->paginate(20);

        // Get statistics based on current view mode
        if ($showDeleted) {
            $stats = [
                'total' => Carrier::withTrashed()->count(),
                'active' => Carrier::withTrashed()->where('is_active', true)->whereNull('deleted_at')->count(),
                'inactive' => Carrier::withTrashed()->where('is_active', false)->whereNull('deleted_at')->count(),
                'deleted' => Carrier::onlyTrashed()->count(),
                'merged' => Carrier::withTrashed()->where('is_active', false)->where('name', 'like', '%(MERGED INTO:%')->whereNull('deleted_at')->count(),
                'with_bookings' => Carrier::withTrashed()->has('bookings')->count(),
                'pending_approval' => Carrier::withTrashed()->where('requires_approval', true)->whereNull('deleted_at')->count(),
            ];
        } else {
            $stats = [
                'total' => Carrier::count(),
                'active' => Carrier::where('is_active', true)->count(),
                'inactive' => Carrier::where('is_active', false)->count(),
                'merged' => Carrier::where('is_active', false)->where('name', 'like', '%(MERGED INTO:%')->count(),
                'with_bookings' => Carrier::has('bookings')->count(),
                'pending_approval' => Carrier::where('requires_approval', true)->count(),
            ];
        }

        return view('admin.carriers.index', compact('carriers', 'stats', 'showDeleted'));
    }

    public function show($id)
    {
        // Find carrier including soft-deleted ones
        $carrier = Carrier::withTrashed()->findOrFail($id);
        
        $carrier->load([
            'bookings.slot.depot',
            'depots' => function($query) {
                $query->withPivot(['is_enabled', 'auto_disable_unused', 'auto_disable_months', 'allowed_customer_ids']);
            },
            'mergesAsSource.targetCarrier',
            'mergesAsTarget.sourceCarrier'
        ]);

        // Get booking statistics by depot
        $bookingsByDepot = $carrier->bookings()
            ->join('slots', 'bookings.slot_id', '=', 'slots.id')
            ->join('depots', 'slots.depot_id', '=', 'depots.id')
            ->select('depots.name', DB::raw('count(*) as count'))
            ->groupBy('depots.id', 'depots.name')
            ->get();

        // Get recent bookings
        $recentBookings = $carrier->bookings()
            ->with(['slot.depot', 'customer'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.carriers.show', compact('carrier', 'bookingsByDepot', 'recentBookings'));
    }

    public function create()
    {
        $depots = Depot::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        
        return view('admin.carriers.create', compact('depots', 'customers'));
    }

    public function store(Request $request)
    {
        // First check if a soft-deleted carrier with this name exists
        $existingCarrier = Carrier::withTrashed()->where('name', $request->name)->first();
        
        if ($existingCarrier && $existingCarrier->trashed()) {
            // Restore the deleted carrier and update its details
            return $this->restoreAndUpdateCarrier($request, $existingCarrier);
        }
        
        // Normalize carrier name to title case
        $request->merge([
            'name' => $this->normalizeCarrierName($request->name)
        ]);
        
        // Standard validation for new carriers (only check against non-deleted ones)
        $request->validate([
            'name' => 'required|string|max:255|unique:carriers,name,NULL,id,deleted_at,NULL',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        DB::transaction(function() use ($request) {
            $carrier = Carrier::create([
                'name' => $request->name,
                'contact_email' => $request->contact_email,
                'contact_phone' => $request->contact_phone,
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Configure depot relationships
            $this->configureDepotRelationships($carrier, $request);
        });

        return redirect()->route('admin.carriers.index')
            ->with('success', 'Carrier created successfully.');
    }
    
    /**
     * Restore a soft-deleted carrier and update its details
     */
    private function restoreAndUpdateCarrier(Request $request, Carrier $carrier)
    {
        // Normalize carrier name to title case
        $request->merge([
            'name' => $this->normalizeCarrierName($request->name)
        ]);
        
        // Validate the input (excluding unique name check since we're restoring)
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        DB::transaction(function() use ($request, $carrier) {
            // Restore the carrier
            $carrier->restore();
            
            // Update the carrier details
            $carrier->update([
                'name' => $request->name,
                'contact_email' => $request->contact_email,
                'contact_phone' => $request->contact_phone,
                'is_active' => $request->boolean('is_active', true),
            ]);

            // Clear existing depot relationships and configure new ones
            $carrier->depots()->detach();
            $this->configureDepotRelationships($carrier, $request);
        });

        return redirect()->route('admin.carriers.index')
            ->with('success', 'Deleted carrier "' . $carrier->name . '" has been restored and updated with new details.');
    }
    
    /**
     * Configure depot relationships for a carrier
     */
    private function configureDepotRelationships(Carrier $carrier, Request $request)
    {
        if ($request->has('depot_configs')) {
            foreach ($request->depot_configs as $depotId => $config) {
                if (!empty($config['enabled'])) {
                    $carrier->depots()->attach($depotId, [
                        'is_enabled' => true,
                        'auto_disable_unused' => $config['auto_disable_unused'] ?? true,
                        'auto_disable_months' => $config['auto_disable_months'] ?? 6,
                        'allowed_customer_ids' => !empty($config['allowed_customer_ids']) 
                            ? json_encode($config['allowed_customer_ids']) 
                            : null,
                    ]);
                }
            }
        }
    }

    public function edit($id)
    {
        // Find carrier including soft-deleted ones
        $carrier = Carrier::withTrashed()->findOrFail($id);
        
        $carrier->load('depots');
        $depots = Depot::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        
        return view('admin.carriers.edit', compact('carrier', 'depots', 'customers'));
    }

    public function update(Request $request, $id)
    {
        // Find carrier including soft-deleted ones
        $carrier = Carrier::withTrashed()->findOrFail($id);
        
        // Normalize carrier name to title case
        $request->merge([
            'name' => $this->normalizeCarrierName($request->name)
        ]);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:carriers,name,' . $carrier->id,
            'contact_email' => 'nullable|email',
            'contact_phone' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        DB::transaction(function() use ($request, $carrier) {
            $carrier->update([
                'name' => $request->name,
                'contact_email' => $request->contact_email,
                'contact_phone' => $request->contact_phone,
                'is_active' => $request->boolean('is_active'),
            ]);

            // Update depot relationships
            $carrier->depots()->detach();
            
            if ($request->has('depot_configs')) {
                foreach ($request->depot_configs as $depotId => $config) {
                    if (!empty($config['enabled'])) {
                        $carrier->depots()->attach($depotId, [
                            'is_enabled' => true,
                            'auto_disable_unused' => $config['auto_disable_unused'] ?? true,
                            'auto_disable_months' => $config['auto_disable_months'] ?? 6,
                            'allowed_customer_ids' => !empty($config['allowed_customer_ids']) 
                                ? json_encode($config['allowed_customer_ids']) 
                                : null,
                        ]);
                    }
                }
            }
        });

        return redirect()->route('admin.carriers.index')
            ->with('success', 'Carrier updated successfully.');
    }

    public function destroy($id)
    {
        // Find carrier including soft-deleted ones
        $carrier = Carrier::withTrashed()->findOrFail($id);
        
        if ($carrier->bookings()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete carrier with existing bookings. Deactivate instead.']);
        }

        $carrier->delete();

        return redirect()->route('admin.carriers.index')
            ->with('success', 'Carrier deleted successfully.');
    }

    public function restore($id)
    {
        $carrier = Carrier::withTrashed()->findOrFail($id);
        $carrier->restore();

        return back()->with('success', 'Carrier restored successfully.');
    }

    public function toggle($id)
    {
        // Find carrier including soft-deleted ones
        $carrier = Carrier::withTrashed()->findOrFail($id);
        
        $carrier->update(['is_active' => !$carrier->is_active]);

        $status = $carrier->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Carrier {$status} successfully.");
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'carrier_ids' => 'required|array',
            'carrier_ids.*' => 'exists:carriers,id',
        ]);

        $carriers = Carrier::whereIn('id', $request->carrier_ids);

        switch ($request->action) {
            case 'activate':
                $carriers->update(['is_active' => true]);
                $message = 'Carriers activated successfully.';
                break;
            case 'deactivate':
                $carriers->update(['is_active' => false]);
                $message = 'Carriers deactivated successfully.';
                break;
            case 'delete':
                // Only delete carriers without bookings
                $carriersToDelete = $carriers->doesntHave('bookings')->get();
                $carriersWithBookings = $carriers->has('bookings')->count();
                
                foreach ($carriersToDelete as $carrier) {
                    $carrier->delete();
                }
                
                $message = count($carriersToDelete) . ' carriers deleted successfully.';
                if ($carriersWithBookings > 0) {
                    $message .= " {$carriersWithBookings} carriers with bookings were not deleted.";
                }
                break;
        }

        return back()->with('success', $message);
    }

    public function cleanup()
    {
        // Auto-disable carriers based on depot rules
        $disabledCount = 0;
        
        DB::table('depot_carrier')
            ->where('auto_disable_unused', true)
            ->where('is_enabled', true)
            ->get()
            ->each(function($depotCarrier) use (&$disabledCount) {
                $carrier = Carrier::find($depotCarrier->carrier_id);
                
                if ($carrier && $carrier->shouldAutoDisable($depotCarrier->auto_disable_months)) {
                    DB::table('depot_carrier')
                        ->where('depot_id', $depotCarrier->depot_id)
                        ->where('carrier_id', $depotCarrier->carrier_id)
                        ->update(['is_enabled' => false]);
                    
                    $disabledCount++;
                }
            });

        return back()->with('success', "Auto-disabled {$disabledCount} depot-carrier relationships based on inactivity rules.");
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (strlen($query) < 2) {
            return response()->json([
                'carriers' => [],
                'total' => 0,
                'has_more' => false
            ]);
        }
        
        $page = $request->get('page', 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        
        // Search active carriers first, then inactive ones
        $activeCarriers = Carrier::where('is_active', true)
            ->where('name', 'like', '%' . $query . '%')
            ->orderBy('name')
            ->get(['id', 'name', 'is_active']);
            
        $inactiveCarriers = Carrier::where('is_active', false)
            ->where('name', 'like', '%' . $query . '%')
            ->orderBy('name')
            ->get(['id', 'name', 'is_active']);
        
        $allCarriers = $activeCarriers->concat($inactiveCarriers);
        $total = $allCarriers->count();
        
        // Paginate results
        $carriers = $allCarriers->slice($offset, $perPage)->values();
        
        // Check for exact match
        $exactMatch = $allCarriers->firstWhere('name', $query);
        
        return response()->json([
            'carriers' => $carriers,
            'total' => $total,
            'has_more' => $total > ($offset + $perPage),
            'exact_match' => $exactMatch !== null,
            'page' => $page,
            'per_page' => $perPage
        ]);
    }

    public function quickCreate(Request $request)
    {
        // Normalize carrier name to title case
        $request->merge([
            'name' => $this->normalizeCarrierName($request->name)
        ]);
        
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Check if carrier already exists (case-insensitive, including soft-deleted)
        $existing = Carrier::withTrashed()->whereRaw('LOWER(name) = ?', [strtolower($request->name)])->first();
        
        if ($existing) {
            if ($existing->trashed()) {
                // Restore soft-deleted carrier
                $existing->restore();
                $existing->update(['is_active' => true]);
                
                return response()->json([
                    'success' => true,
                    'carrier' => $existing,
                    'message' => 'Deleted carrier restored and activated'
                ]);
            } else {
                // Reactivate if it exists but is inactive
                if (!$existing->is_active) {
                    $existing->update(['is_active' => true]);
                }
                
                return response()->json([
                    'success' => true,
                    'carrier' => $existing,
                    'message' => $existing->is_active ? 'Carrier already exists' : 'Carrier reactivated'
                ]);
            }
        }

        // Create new carrier
        $carrier = Carrier::create([
            'name' => $request->name,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'carrier' => $carrier,
            'message' => 'Carrier created successfully'
        ]);
    }
}