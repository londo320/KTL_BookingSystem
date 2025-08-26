<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FactoryBooking;
use App\Models\PalletType;
use App\Models\PoLine;
use App\Models\PoLineActualPallet;
use App\Models\Setting;
use App\Models\TippingBay;
use App\Models\TippingLocation;
use App\Models\User;
use Illuminate\Http\Request;

class FactoryBookingWorkflowController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'function-access']);
    }

    public function show(FactoryBooking $factoryBooking)
    {
        $this->authorizeBookingAccess($factoryBooking);

        $factoryBooking->load([
            'depot',
            'customer',
            'carrier',
            'movements.tippingLocation',
            'movements.tippingBay',
            'poNumbers.lines.expectedPalletType',
            'poNumbers.lines.actualPalletType',
            'poNumbers.lines.actualPallets.palletType',
        ]);

        // Get available locations and bays for this depot
        $availableLocations = TippingLocation::forDepot($factoryBooking->depot_id)
            ->available()
            ->get();

        $availableBays = TippingBay::forDepot($factoryBooking->depot_id)
            ->available()
            ->get();

        // Get tipping operators (depot staff)
        $operators = User::whereHas('depots', function ($query) use ($factoryBooking) {
            $query->where('depots.id', $factoryBooking->depot_id);
        })->get();

        $workflowEnabled = Setting::isTippingWorkflowEnabled();
        
        // Get all active pallet types for the tipping completion form
        $palletTypes = PalletType::active()->orderBy('name')->get();

        // Use the same view but pass factory booking as 'booking' for compatibility
        return view('admin.factory-booking-workflow.show', compact(
            'factoryBooking',
            'availableLocations',
            'availableBays',
            'operators',
            'workflowEnabled',
            'palletTypes'
        ));
    }

    public function dropTrailer(Request $request, FactoryBooking $factoryBooking)
    {
        $this->authorizeBookingAccess($factoryBooking);

        $request->validate([
            'tipping_location_id' => 'required|exists:tipping_locations,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $location = TippingLocation::findOrFail($request->tipping_location_id);

        // Verify location is available and in same depot
        if ($location->depot_id !== $factoryBooking->depot_id) {
            return back()->withErrors(['tipping_location_id' => 'Location must be within the same depot: ' . $factoryBooking->depot->name]);
        }
        
        if (! $location->isAvailable()) {
            return back()->withErrors(['tipping_location_id' => 'Selected location is not currently available.']);
        }

        // Manual mode - allow action without strict workflow enforcement
        $movement = $factoryBooking->getOrCreateMovement();
        $movement->update([
            'tipping_location_id' => $location->id,
            'current_status' => 'in_location',
            'moved_to_location_at' => now(),
            'operation_notes' => $request->notes ? ($movement->operation_notes ? $movement->operation_notes."\n".$request->notes : $request->notes) : $movement->operation_notes,
        ]);
        $location->markOccupied();

        return back()->with('success', 'Vehicle parked successfully at '.$location->name);
    }

    public function moveToBay(Request $request, FactoryBooking $factoryBooking)
    {
        $this->authorizeBookingAccess($factoryBooking);

        $request->validate([
            'tipping_bay_id' => 'required|exists:tipping_bays,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $bay = TippingBay::findOrFail($request->tipping_bay_id);

        // Verify bay is available and in same depot
        if ($bay->depot_id !== $factoryBooking->depot_id) {
            return back()->withErrors(['tipping_bay_id' => 'Bay must be within the same depot: ' . $factoryBooking->depot->name]);
        }
        
        if (! $bay->isAvailable()) {
            return back()->withErrors(['tipping_bay_id' => 'Selected bay is not currently available.']);
        }

        // Manual mode - allow action without strict workflow enforcement
        $movement = $factoryBooking->getOrCreateMovement();
        $movement->update([
            'tipping_bay_id' => $bay->id,
            'current_status' => 'at_bay',
            'moved_to_bay_at' => now(),
            'operation_notes' => $request->notes ? ($movement->operation_notes ? $movement->operation_notes."\n".$request->notes : $request->notes) : $movement->operation_notes,
        ]);
        $bay->markOccupied();

        return back()->with('success', 'Trailer moved to '.$bay->name);
    }

    public function startTipping(Request $request, FactoryBooking $factoryBooking)
    {
        $this->authorizeBookingAccess($factoryBooking);

        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $movement = $factoryBooking->getOrCreateMovement();
        
        // Prevent multiple tipping sessions - check if tipping was already completed
        if ($movement->unloading_completed_at) {
            return back()->withErrors(['error' => 'Tipping has already been completed for this trailer. Cannot restart tipping process.']);
        }

        // Manual mode - allow action without strict workflow enforcement
        $movement->update([
            'current_status' => 'unloading',
            'unloading_started_at' => now(),
            'operation_notes' => $request->notes ? ($movement->operation_notes ? $movement->operation_notes."\n".$request->notes : $request->notes) : $movement->operation_notes,
        ]);

        return back()->with('success', 'Tipping started - complete when finished to record actual quantities.');
    }

    public function completeTipping(Request $request, FactoryBooking $factoryBooking)
    {
        $this->authorizeBookingAccess($factoryBooking);

        $movement = $factoryBooking->getOrCreateMovement();
        
        // Prevent multiple tipping completions
        if ($movement->unloading_completed_at) {
            return back()->withErrors(['error' => 'Tipping has already been completed for this trailer.']);
        }

        $validationRules = [
            'notes' => 'nullable|string|max:1000',
            'issues' => 'nullable|array',
            'issues.*' => 'nullable|string|max:255',
        ];

        // Add validation rules for PO lines if they exist
        if ($factoryBooking->poNumbers()->exists()) {
            $validationRules['po_lines'] = 'required|array';
            $validationRules['po_lines.*.actual_cases'] = 'required|integer|min:1';
            $validationRules['po_lines.*.actual_pallets'] = 'required|array|min:1';
            $validationRules['po_lines.*.actual_pallets.*.pallet_type_id'] = 'required|exists:pallet_types,id';
            $validationRules['po_lines.*.actual_pallets.*.quantity'] = 'required|integer|min:1';
        }

        $request->validate($validationRules);

        $issues = $request->issues ? array_filter($request->issues) : null;

        \DB::transaction(function () use ($request, $factoryBooking, $issues) {
            // Update PO line actual quantities if provided
            if ($request->has('po_lines')) {
                foreach ($request->po_lines as $lineId => $lineData) {
                    $poLine = PoLine::findOrFail($lineId);
                    
                    // Update actual cases
                    $poLine->update([
                        'actual_cases' => $lineData['actual_cases'],
                    ]);
                    
                    // Clear existing actual pallets and create new ones
                    $poLine->actualPallets()->delete();
                    
                    foreach ($lineData['actual_pallets'] as $palletData) {
                        if (!empty($palletData['pallet_type_id']) && !empty($palletData['quantity'])) {
                            PoLineActualPallet::create([
                                'po_line_id' => $poLine->id,
                                'pallet_type_id' => $palletData['pallet_type_id'],
                                'quantity' => $palletData['quantity'],
                            ]);
                        }
                    }
                }
            }

            // Manual mode - allow action without strict workflow enforcement
            $movement = $factoryBooking->getOrCreateMovement();
            $movement->update([
                'current_status' => 'empty',
                'unloading_completed_at' => now(),
                'operation_notes' => $request->notes ? ($movement->operation_notes ? $movement->operation_notes."\n".$request->notes : $request->notes) : $movement->operation_notes,
            ]);
        });

        return back()->with('success', 'Tipping completed successfully with actual quantities recorded.');
    }

    public function moveToLocation(Request $request, FactoryBooking $factoryBooking)
    {
        $this->authorizeBookingAccess($factoryBooking);

        $request->validate([
            'tipping_location_id' => 'required|exists:tipping_locations,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $location = TippingLocation::findOrFail($request->tipping_location_id);

        // Verify location is available and in same depot
        if ($location->depot_id !== $factoryBooking->depot_id) {
            return back()->withErrors(['tipping_location_id' => 'Location must be within the same depot: ' . $factoryBooking->depot->name]);
        }
        
        if (! $location->isAvailable()) {
            return back()->withErrors(['tipping_location_id' => 'Selected location is not currently available.']);
        }

        $movement = $factoryBooking->getOrCreateMovement();
        
        // Free up current location if any
        if ($movement->tippingLocation && $movement->tippingLocation->id != $location->id) {
            $movement->tippingLocation->markAvailable();
        }
        
        // Free up current bay if moving out of bay
        if ($movement->tippingBay) {
            $movement->tippingBay->markAvailable();
        }

        // Determine if this is an empty trailer movement (after tipping completion)
        $isEmptyMovement = $movement->unloading_completed_at && in_array($movement->current_status, ['empty', 'at_bay']);
        
        $movement->update([
            'tipping_location_id' => $location->id,
            'tipping_bay_id' => null, // Clear bay when moving to location
            'current_status' => 'in_location',
            'moved_to_location_at' => now(),
            'operation_notes' => $request->notes ? ($movement->operation_notes ? $movement->operation_notes."\n".$request->notes : $request->notes) : $movement->operation_notes,
        ]);
        
        $location->markOccupied();

        $statusMessage = $isEmptyMovement ? 'Empty unit moved to ' : 'Vehicle moved to ';
        return back()->with('success', $statusMessage . $location->name);
    }

    public function dropTrailerDetached(Request $request, FactoryBooking $factoryBooking)
    {
        $this->authorizeBookingAccess($factoryBooking);

        $request->validate([
            'tipping_location_id' => 'required|exists:tipping_locations,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $location = TippingLocation::findOrFail($request->tipping_location_id);

        // Verify location is available and in same depot
        if ($location->depot_id !== $factoryBooking->depot_id) {
            return back()->withErrors(['tipping_location_id' => 'Location must be within the same depot: ' . $factoryBooking->depot->name]);
        }
        
        if (! $location->isAvailable()) {
            return back()->withErrors(['tipping_location_id' => 'Selected location is not currently available.']);
        }

        $movement = $factoryBooking->getOrCreateMovement();
        
        // Free up current bay if any
        if ($movement->tippingBay) {
            $movement->tippingBay->markAvailable();
        }

        $movement->update([
            'tipping_location_id' => $location->id,
            'tipping_bay_id' => null, // Clear bay when dropping trailer
            'current_status' => 'trailer_dropped',
            'trailer_dropped_at' => now(),
            'operation_notes' => $request->notes ? ($movement->operation_notes ? $movement->operation_notes."\n".$request->notes : $request->notes) : $movement->operation_notes,
        ]);
        
        $location->markOccupied();

        return back()->with('success', 'Trailer dropped (detached) at '.$location->name);
    }

    public function trailerDepart(Request $request, FactoryBooking $factoryBooking)
    {
        $this->authorizeBookingAccess($factoryBooking);

        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        // Manual mode - allow action without strict workflow enforcement
        $movement = $factoryBooking->getOrCreateMovement();
        $movement->update([
            'current_status' => 'departed',
            'departed_at' => now(),
            'operation_notes' => $request->notes ? ($movement->operation_notes ? $movement->operation_notes."\n".$request->notes : $request->notes) : $movement->operation_notes,
        ]);

        $factoryBooking->update(['status' => 'departed', 'departed_at' => now()]);

        // Free up location and bay
        if ($movement->tippingLocation) {
            $movement->tippingLocation->markAvailable();
        }
        if ($movement->tippingBay) {
            $movement->tippingBay->markAvailable();
        }

        return back()->with('success', 'Trailer departure recorded successfully.');
    }

    private function authorizeBookingAccess(FactoryBooking $factoryBooking): void
    {
        // Check if user can access this factory booking's customer
        if (! auth()->user()->canAccessCustomer($factoryBooking->customer_id)) {
            abort(403, 'You do not have access to this customer\'s bookings.');
        }

        // Check if user can access this factory booking's depot
        $allowedDepotIds = $this->getAllowedDepotIds();
        if (! in_array($factoryBooking->depot_id, $allowedDepotIds)) {
            abort(403, 'You do not have access to this depot.');
        }
    }

    private function getAllowedDepotIds(): array
    {
        // Always use depot assignments from depot_user pivot table
        // Roles determine permissions within those depots, not which depots they can see
        $assignedDepotIds = auth()->user()->depots()->pluck('depots.id')->toArray();

        // If no depot assignments and user is admin/site-admin, they can see all depots
        // This is a fallback for super admin users
        if (empty($assignedDepotIds) && auth()->user()->hasRole('admin|site-admin')) {
            return \App\Models\Depot::pluck('id')->toArray();
        }

        return $assignedDepotIds;
    }
}