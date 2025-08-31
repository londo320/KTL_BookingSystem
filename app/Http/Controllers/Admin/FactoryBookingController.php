<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FactoryBooking;
use App\Models\Customer;
use App\Models\Carrier;
use App\Models\Depot;
use App\Models\TrailerType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FactoryBookingController extends Controller
{
    public function index(Request $request)
    {
        // Allow admin or users with factory-bookings.view function
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasFunction('factory-bookings.view')) {
            abort(403, 'You do not have permission to view factory bookings.');
        }
        
        $query = FactoryBooking::with(['customer', 'carrier', 'depot', 'registeredBy'])
            ->orderBy('priority', 'desc')
            ->orderBy('arrived_at', 'asc');

        // Filter by depot if specified
        if ($request->depot_id) {
            $query->where('depot_id', $request->depot_id);
        }

        // Filter by status if specified
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('vehicle_registration', 'like', "%{$search}%")
                  ->orWhere('trailer_registration', 'like', "%{$search}%")
                  ->orWhere('driver_name', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($customerQuery) use ($search) {
                      $customerQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $factoryBookings = $query->paginate(20);
        $depots = Depot::orderBy('name')->get();

        return view('warehouse.factory-bookings.index', compact('factoryBookings', 'depots'));
    }

    public function show(FactoryBooking $factoryBooking)
    {
        $factoryBooking->load(['customer', 'carrier', 'depot', 'registeredBy', 'movements.tippingLocation', 'movements.tippingBay', 'poNumbers']);
        
        // Get available locations and bays for trailer movement
        $availableLocations = \App\Models\TippingLocation::forDepot($factoryBooking->depot_id)
            ->available()
            ->get();
            
        $availableBays = \App\Models\TippingBay::forDepot($factoryBooking->depot_id)
            ->available()
            ->get();
        
        return view('admin.factory-bookings.show', compact('factoryBooking', 'availableLocations', 'availableBays'));
    }

    public function create()
    {
        // Allow admin or users with factory-bookings.create function
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasFunction('factory-bookings.create')) {
            abort(403, 'You do not have permission to create factory bookings.');
        }
        
        $customers = Customer::orderBy('name')->get();
        $carriers = Carrier::orderBy('name')->get();
        $trailerTypes = TrailerType::orderBy('name')->get();
        
        // Get user's accessible depots
        $user = Auth::user();
        $depots = $user->depots()->orderBy('name')->get();
        
        // If user has no depots assigned, get all depots (for super admin)
        if ($depots->isEmpty()) {
            $depots = Depot::orderBy('name')->get();
        }

        return view('warehouse.factory-bookings.create', compact('customers', 'carriers', 'trailerTypes', 'depots'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'depot_id' => 'required|exists:depots,id',
            'customer_id' => 'required|exists:customers,id',
            'carrier_id' => 'nullable|exists:carriers,id',
            'carrier_name' => 'required|string|max:255',
            'trailer_type_id' => 'required|exists:trailer_types,id',
            'tipping_type' => 'required|in:live_tip,drop',
            'vehicle_registration' => 'required|string|max:50',
            'trailer_registration' => 'nullable|string|max:50',
            'delivery_notes' => 'nullable|string|max:1000',
            'priority' => 'nullable|integer|min:0|max:100',
            'gate_notes' => 'nullable|string|max:1000',
            'po_numbers' => 'required|array|min:1',
            'po_numbers.*.po_number' => 'required|string|max:100',
            'po_numbers.*.lines' => 'required|array|min:1',
            'po_numbers.*.lines.*.expected_cases' => 'required|integer|min:0',
            'po_numbers.*.lines.*.expected_pallets' => 'required|integer|min:0',
            'po_numbers.*.lines.*.line_number' => 'required|integer|min:1',
        ]);

        // Verify user has access to selected depot
        if (!$user->depots->contains($validated['depot_id']) && !$user->hasRole('super_admin')) {
            return back()->withErrors(['depot_id' => 'You do not have access to this depot.']);
        }

        $factoryBooking = DB::transaction(function () use ($validated, $user) {
            // Handle carrier creation/selection
            $carrierId = $validated['carrier_id'];
            if (!$carrierId && !empty($validated['carrier_name'])) {
                // Create new carrier if name provided but no ID
                $carrier = Carrier::firstOrCreate(
                    ['name' => trim($validated['carrier_name'])],
                    ['is_active' => true]
                );
                $carrierId = $carrier->id;
            }

            $factoryBooking = FactoryBooking::create([
                ...collect($validated)->except(['po_numbers', 'carrier_name'])->toArray(),
                'carrier_id' => $carrierId,
                'registered_by' => $user->id,
                'priority' => $validated['priority'] ?? 50,
                'arrived_at' => now(),
                'status' => 'arrived',
            ]);

            // Create initial movement record
            $factoryBooking->getOrCreateMovement();
            
            // Create PO numbers and lines from form data
            foreach ($validated['po_numbers'] as $poData) {
                $poNumber = $factoryBooking->poNumbers()->create([
                    'po_number' => $poData['po_number'],
                ]);

                // Create lines for this PO
                if (!empty($poData['lines'])) {
                    foreach ($poData['lines'] as $lineData) {
                        $poNumber->lines()->create([
                            'line_number' => $lineData['line_number'],
                            'expected_cases' => $lineData['expected_cases'],
                            'expected_pallets' => $lineData['expected_pallets'],
                        ]);
                    }
                }
            }
            
            return $factoryBooking;
        });

        return redirect()->route('admin.factory-bookings.index')
            ->with('success', 'Factory booking registered successfully! Reference: ' . $factoryBooking->reference);
    }

    public function edit(FactoryBooking $factoryBooking)
    {
        $customers = Customer::orderBy('name')->get();
        $carriers = Carrier::orderBy('name')->get();
        $trailerTypes = TrailerType::orderBy('name')->get();
        
        $user = Auth::user();
        $depots = $user->depots()->orderBy('name')->get();
        
        if ($depots->isEmpty()) {
            $depots = Depot::orderBy('name')->get();
        }

        return view('warehouse.factory-bookings.edit', compact('factoryBooking', 'customers', 'carriers', 'trailerTypes', 'depots'));
    }

    public function update(Request $request, FactoryBooking $factoryBooking)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'carrier_id' => 'nullable|exists:carriers,id',
            'carrier_name' => 'required|string|max:255',
            'trailer_type_id' => 'nullable|exists:trailer_types,id',
            'vehicle_registration' => 'required|string|max:50',
            'trailer_registration' => 'nullable|string|max:50',
            'delivery_notes' => 'nullable|string|max:1000',
            'priority' => 'nullable|integer|min:0|max:100',
            'gate_notes' => 'nullable|string|max:1000',
            'po_numbers' => 'nullable|array',
            'po_numbers.*.po_number' => 'required_with:po_numbers.*|string|max:100',
            'po_numbers.*.lines' => 'nullable|array',
            'po_numbers.*.lines.*.line_number' => 'nullable|integer|min:1',
            'po_numbers.*.lines.*.expected_cases' => 'nullable|integer|min:0',
            'po_numbers.*.lines.*.expected_pallets' => 'nullable|integer|min:0',
            'po_numbers.*.lines.*.expected_pallet_type_id' => 'nullable|exists:pallet_types,id',
            'po_numbers.*.lines.*.actual_cases' => 'nullable|integer|min:0',
            'po_numbers.*.lines.*.actual_pallets' => 'nullable|integer|min:0',
            'po_numbers.*.lines.*.actual_pallet_type_id' => 'nullable|exists:pallet_types,id',
            'po_numbers.*.lines.*.pallet_entries' => 'nullable|array',
        ]);

        DB::transaction(function () use ($request, $factoryBooking, $validated) {
            // Handle carrier creation/selection
            $carrierId = $validated['carrier_id'];
            if (!$carrierId && !empty($validated['carrier_name'])) {
                // Create new carrier if name provided but no ID
                $carrier = Carrier::firstOrCreate(
                    ['name' => trim($validated['carrier_name'])],
                    ['is_active' => true]
                );
                $carrierId = $carrier->id;
            }

            // Update factory booking basic info
            $updateData = collect($validated)->except(['po_numbers', 'carrier_name'])->toArray();
            $updateData['carrier_id'] = $carrierId;
            $factoryBooking->update($updateData);

            // Clear existing PO numbers and lines
            $factoryBooking->poNumbers()->delete();

            // Handle PO numbers from component format
            if (!empty($validated['po_numbers'])) {
                foreach ($validated['po_numbers'] as $poData) {
                    if (empty($poData['po_number'])) continue;
                    
                    // Create PO number
                    $poNumber = $factoryBooking->poNumbers()->create([
                        'po_number' => $poData['po_number']
                    ]);

                    // Handle lines for this PO
                    if (!empty($poData['lines'])) {
                        foreach ($poData['lines'] as $lineData) {
                            // Skip empty lines
                            if (empty($lineData['expected_cases']) && empty($lineData['expected_pallets']) && 
                                empty($lineData['actual_cases']) && empty($lineData['actual_pallets'])) {
                                continue;
                            }

                            $poNumber->lines()->create([
                                'line_number' => $lineData['line_number'] ?: 1,
                                'expected_cases' => $lineData['expected_cases'] ?: 0,
                                'expected_pallets' => $lineData['expected_pallets'] ?: 0,
                                'expected_pallet_type_id' => $lineData['expected_pallet_type_id'] ?: null,
                                'actual_cases' => $lineData['actual_cases'] ?: null,
                                'actual_pallets' => $lineData['actual_pallets'] ?: null,
                                'actual_pallet_type_id' => $lineData['actual_pallet_type_id'] ?: null,
                            ]);
                        }
                    }
                }
            }
        });

        return redirect()->route('app.factory-bookings.show', $factoryBooking)
            ->with('success', 'Factory booking and PO details updated successfully.');
    }

    public function destroy(FactoryBooking $factoryBooking)
    {
        // Only allow deletion if not started processing
        if ($factoryBooking->status !== 'arrived') {
            return back()->withErrors(['error' => 'Cannot delete factory booking that has started processing.']);
        }

        $factoryBooking->delete();

        return redirect()->route('admin.factory-bookings.index')
            ->with('success', 'Factory booking deleted successfully.');
    }

    public function startProcessing(FactoryBooking $factoryBooking)
    {
        if ($factoryBooking->status !== 'arrived') {
            return back()->withErrors(['error' => 'Factory booking is not in arrived status.']);
        }

        $factoryBooking->update([
            'status' => 'processing',
            'processing_started_at' => now(),
        ]);

        return back()->with('success', 'Factory booking processing started.');
    }

    public function complete(FactoryBooking $factoryBooking)
    {
        if (!in_array($factoryBooking->status, ['processing', 'arrived'])) {
            return back()->withErrors(['error' => 'Factory booking cannot be completed from current status.']);
        }

        $factoryBooking->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Factory booking marked as completed.');
    }

    public function markDeparted(FactoryBooking $factoryBooking)
    {
        if ($factoryBooking->status !== 'completed') {
            return back()->withErrors(['error' => 'Factory booking must be completed before departure.']);
        }

        $factoryBooking->update([
            'status' => 'departed',
            'departed_at' => now(),
        ]);

        return back()->with('success', 'Factory booking departure recorded.');
    }

    public function addPoNumbers(Request $request, FactoryBooking $factoryBooking)
    {
        $request->validate([
            'po_numbers' => 'required|array|min:1',
            'po_numbers.*' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($request, $factoryBooking) {
            foreach ($request->po_numbers as $poNumber) {
                $poNumber = trim($poNumber);
                if (empty($poNumber)) {
                    continue;
                }

                // Check if PO number already exists for this factory booking
                $existingPo = $factoryBooking->poNumbers()->where('po_number', $poNumber)->first();
                if (!$existingPo) {
                    $factoryBooking->poNumbers()->create([
                        'po_number' => $poNumber,
                    ]);
                }
            }
        });

        return back()->with('success', 'PO numbers added successfully. You can now add expected cases/pallets to each PO.');
    }

    public function history(FactoryBooking $factoryBooking, Request $request)
    {
        $factoryBooking->load(['depot', 'customer', 'movements.tippingLocation', 'movements.tippingBay']);

        // Get sorting preference
        $sortOrder = $request->get('sort', 'desc');
        
        // Get all history for this factory booking via movements
        $history = collect();
        
        // Add factory booking creation
        $history->push((object)[
            'id' => 'created-' . $factoryBooking->id,
            'action' => 'created',
            'reason' => 'Factory booking registered',
            'created_at' => $factoryBooking->created_at,
        ]);
        
        // Add movement history if available
        foreach ($factoryBooking->movements as $movement) {
            if ($movement->actual_arrival) {
                $history->push((object)[
                    'id' => 'arrival-' . $movement->id,
                    'action' => 'arrival',
                    'reason' => 'Vehicle arrived on site',
                    'created_at' => $movement->actual_arrival,
                ]);
            }
            
            if ($movement->moved_to_location_at) {
                $location = $movement->tippingLocation;
                $history->push((object)[
                    'id' => 'location-' . $movement->id,
                    'action' => 'modified',
                    'reason' => 'Moved to location: ' . ($location ? $location->name : 'Unknown'),
                    'created_at' => $movement->moved_to_location_at,
                ]);
            }
            
            if ($movement->moved_to_bay_at) {
                $bay = $movement->tippingBay;
                $history->push((object)[
                    'id' => 'bay-' . $movement->id,
                    'action' => 'modified',
                    'reason' => 'Moved to bay: ' . ($bay ? $bay->name : 'Unknown'),
                    'created_at' => $movement->moved_to_bay_at,
                ]);
            }
            
            if ($movement->unloading_started_at) {
                $history->push((object)[
                    'id' => 'tipping-start-' . $movement->id,
                    'action' => 'modified',
                    'reason' => 'Tipping started',
                    'created_at' => $movement->unloading_started_at,
                ]);
            }
            
            if ($movement->unloading_completed_at) {
                $duration = null;
                if ($movement->unloading_started_at) {
                    $durationMinutes = round($movement->unloading_started_at->diffInMinutes($movement->unloading_completed_at));
                    if ($durationMinutes >= 10080) {
                        $weeks = floor($durationMinutes / 10080);
                        $days = floor(($durationMinutes % 10080) / 1440);
                        $duration = $weeks . 'w' . ($days > 0 ? ' ' . $days . 'd' : '');
                    } elseif ($durationMinutes >= 1440) {
                        $days = floor($durationMinutes / 1440);
                        $hours = floor(($durationMinutes % 1440) / 60);
                        $mins = $durationMinutes % 60;
                        $duration = $days . 'd ' . ($hours > 0 ? $hours . 'h ' : '') . ($mins > 0 ? $mins . 'm' : '');
                    } elseif ($durationMinutes >= 60) {
                        $hours = floor($durationMinutes / 60);
                        $mins = $durationMinutes % 60;
                        $duration = $hours . 'h ' . ($mins > 0 ? $mins . 'm' : '');
                    } else {
                        $duration = $durationMinutes . ' min';
                    }
                }
                
                $history->push((object)[
                    'id' => 'tipping-complete-' . $movement->id,
                    'action' => 'modified',
                    'reason' => 'Tipping completed' . ($duration ? ' in ' . $duration : ''),
                    'created_at' => $movement->unloading_completed_at,
                ]);
            }
            
            if ($movement->actual_departure) {
                $history->push((object)[
                    'id' => 'departure-' . $movement->id,
                    'action' => 'completed',
                    'reason' => 'Vehicle departed from site',
                    'created_at' => $movement->actual_departure,
                ]);
            }
        }
        
        // Sort by timestamp
        $history = $sortOrder === 'asc' 
            ? $history->sortBy('created_at')
            : $history->sortByDesc('created_at');

        return view('admin.factory-bookings.history', compact('factoryBooking', 'history', 'sortOrder'));
    }
}
