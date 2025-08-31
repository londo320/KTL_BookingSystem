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
            'current_status' => 'in_parking',
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
            'current_status' => 'unloading',
            'moved_to_bay_at' => now(),
            'unloading_started_at' => now(), // Auto-start tipping timer
            'operation_notes' => $request->notes ? ($movement->operation_notes ? $movement->operation_notes."\n".$request->notes : $request->notes) : $movement->operation_notes,
        ]);
        $bay->markOccupied();

        return back()->with('success', 'Trailer moved to '.$bay->name.' and tipping timer started');
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
            $validationRules['po_lines.*.actual_cases'] = 'required|integer|min:0';
            $validationRules['po_lines.*.actual_pallets'] = 'required|integer|min:0';
            $validationRules['po_lines.*.actual_pallet_type_id'] = 'nullable|exists:pallet_types,id';
        }

        $request->validate($validationRules);

        $issues = $request->issues ? array_filter($request->issues) : null;

        \DB::transaction(function () use ($request, $factoryBooking, $issues) {
            // Update PO line actual quantities if provided
            if ($request->has('po_lines')) {
                foreach ($request->po_lines as $lineId => $lineData) {
                    $poLine = PoLine::findOrFail($lineId);
                    
                    // Update actual quantities directly on the line
                    $poLine->update([
                        'actual_cases' => $lineData['actual_cases'],
                        'actual_pallets' => $lineData['actual_pallets'],
                        'actual_pallet_type_id' => $lineData['actual_pallet_type_id'] ?? null,
                    ]);
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
            'current_status' => 'in_parking',
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
            'departure_scenario' => 'required|in:completed_with_trailer,completed_dropped_trailer',
            'departure_notes' => 'nullable|string|max:1000',
        ]);

        $movement = $factoryBooking->getOrCreateMovement();
        
        // Build operation notes
        $operationNotes = $movement->operation_notes ?: '';
        $scenarioText = $request->departure_scenario === 'completed_with_trailer' 
            ? 'Departed with trailer' 
            : 'Departed - trailer dropped';
        
        if ($request->departure_notes) {
            $operationNotes .= ($operationNotes ? "\n" : '') . $scenarioText . ': ' . $request->departure_notes;
        } else {
            $operationNotes .= ($operationNotes ? "\n" : '') . $scenarioText;
        }
        
        $movement->update([
            'current_status' => 'departed',
            'departed_at' => now(),
            'operation_notes' => $operationNotes,
        ]);

        $factoryBooking->update([
            'status' => 'departed', 
            'departed_at' => now()
        ]);

        // Free up location and bay
        if ($movement->tippingLocation) {
            $movement->tippingLocation->markAvailable();
        }
        if ($movement->tippingBay) {
            $movement->tippingBay->markAvailable();
        }

        return back()->with('success', 'Vehicle departure recorded successfully - ' . $scenarioText);
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

    public function addPoLine(Request $request, FactoryBooking $factoryBooking)
    {
        $request->validate([
            'po_id' => 'required|integer|exists:booking_po_numbers,id',
            'expected_cases' => 'nullable|integer|min:0',
            'expected_pallets' => 'nullable|integer|min:0',
            'expected_pallet_type_id' => 'nullable|exists:pallet_types,id',
            'actual_cases' => 'nullable|integer|min:0',
            'actual_pallets' => 'nullable|integer|min:0',
            'actual_pallet_type_id' => 'nullable|exists:pallet_types,id',
        ]);

        $poNumber = $factoryBooking->poNumbers()->find($request->po_id);
        if (!$poNumber) {
            return response()->json(['success' => false, 'message' => 'PO number not found']);
        }

        // Get the next line number
        $lineNumber = $poNumber->lines()->max('line_number') + 1;

        $poNumber->lines()->create([
            'line_number' => $lineNumber,
            'expected_cases' => $request->expected_cases ?: 0,
            'expected_pallets' => $request->expected_pallets ?: 0,
            'expected_pallet_type_id' => $request->expected_pallet_type_id ?: null,
            'actual_cases' => $request->actual_cases ?: null,
            'actual_pallets' => $request->actual_pallets ?: null,
            'actual_pallet_type_id' => $request->actual_pallet_type_id ?: null,
        ]);

        return response()->json(['success' => true, 'message' => 'PO line added successfully']);
    }

    public function updatePoLine(Request $request, FactoryBooking $factoryBooking)
    {
        $request->validate([
            'line_id' => 'required|integer|exists:po_lines,id',
            'actual_cases' => 'nullable|integer|min:0',
            'actual_pallets' => 'nullable|integer|min:0',
            'actual_pallet_type_id' => 'nullable|exists:pallet_types,id',
        ]);

        // Find the line and verify it belongs to this factory booking
        $line = \App\Models\PoLine::find($request->line_id);
        if (!$line || !$factoryBooking->poNumbers()->where('id', $line->booking_po_number_id)->exists()) {
            return response()->json(['success' => false, 'message' => 'PO line not found']);
        }

        $line->update([
            'actual_cases' => $request->actual_cases ?: null,
            'actual_pallets' => $request->actual_pallets ?: null,
            'actual_pallet_type_id' => $request->actual_pallet_type_id ?: null,
        ]);

        return response()->json(['success' => true, 'message' => 'PO line updated successfully']);
    }

    public function moveTrailer(Request $request, FactoryBooking $factoryBooking)
    {
        $this->authorizeBookingAccess($factoryBooking);
        
        $request->validate([
            'action' => 'required|in:move_to_location,move_to_bay,move_to_collection,depart',
            'location_id' => 'nullable|exists:tipping_locations,id',
            'bay_id' => 'nullable|exists:tipping_bays,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $movement = $factoryBooking->getOrCreateMovement();
        
        // Check if trailer has already departed
        if ($movement->current_status === 'departed') {
            return back()->withErrors(['error' => 'Trailer has already departed and cannot be moved.']);
        }

        switch ($request->action) {
            case 'move_to_location':
                return $this->handleLocationMove($request, $factoryBooking, $movement);
            case 'move_to_bay':
                return $this->handleBayMove($request, $factoryBooking, $movement);
            case 'move_to_collection':
                return $this->handleCollectionMove($request, $factoryBooking, $movement);
            case 'depart':
                return $this->handleDeparture($request, $factoryBooking, $movement);
        }
    }

    private function handleLocationMove(Request $request, FactoryBooking $factoryBooking, $movement)
    {
        $location = \App\Models\TippingLocation::findOrFail($request->location_id);
        
        if ($location->depot_id !== $factoryBooking->depot_id) {
            return back()->withErrors(['error' => 'Location must be within the same depot.']);
        }
        
        if (!$location->isAvailable()) {
            return back()->withErrors(['error' => 'Selected location is not available.']);
        }

        // Free up current locations/bays
        if ($movement->tippingLocation && $movement->tippingLocation->id != $location->id) {
            $movement->tippingLocation->markAvailable();
        }
        if ($movement->tippingBay) {
            $movement->tippingBay->markAvailable();
        }

        $movement->update([
            'tipping_location_id' => $location->id,
            'tipping_bay_id' => null,
            'current_status' => 'in_parking',
            'moved_to_location_at' => now(),
            'operation_notes' => $request->notes ? ($movement->operation_notes ? $movement->operation_notes."\n".$request->notes : $request->notes) : $movement->operation_notes,
        ]);
        
        $location->markOccupied();
        
        return back()->with('success', 'Trailer moved to location: ' . $location->name);
    }

    private function handleBayMove(Request $request, FactoryBooking $factoryBooking, $movement)
    {
        $bay = \App\Models\TippingBay::findOrFail($request->bay_id);
        
        if ($bay->depot_id !== $factoryBooking->depot_id) {
            return back()->withErrors(['error' => 'Bay must be within the same depot.']);
        }
        
        if (!$bay->isAvailable()) {
            return back()->withErrors(['error' => 'Selected bay is not available.']);
        }

        // Free up current bay if different
        if ($movement->tippingBay && $movement->tippingBay->id != $bay->id) {
            $movement->tippingBay->markAvailable();
        }

        $movement->update([
            'tipping_bay_id' => $bay->id,
            'current_status' => 'at_bay',
            'moved_to_bay_at' => now(),
            'operation_notes' => $request->notes ? ($movement->operation_notes ? $movement->operation_notes."\n".$request->notes : $request->notes) : $movement->operation_notes,
        ]);
        
        $bay->markOccupied();
        
        return back()->with('success', 'Trailer moved to bay: ' . $bay->name);
    }

    private function handleCollectionMove(Request $request, FactoryBooking $factoryBooking, $movement)
    {
        // Free up current locations/bays when moving to collection
        if ($movement->tippingLocation) {
            $movement->tippingLocation->markAvailable();
        }
        if ($movement->tippingBay) {
            $movement->tippingBay->markAvailable();
        }

        $movement->update([
            'tipping_location_id' => null,
            'tipping_bay_id' => null,
            'current_status' => 'back_to_parking',
            'moved_to_location_at' => now(),
            'operation_notes' => $request->notes ? ($movement->operation_notes ? $movement->operation_notes."\n".$request->notes : $request->notes) : $movement->operation_notes,
        ]);
        
        return back()->with('success', 'Trailer moved to collection zone. Bay and location freed.');
    }

    private function handleDeparture(Request $request, FactoryBooking $factoryBooking, $movement)
    {
        // Free up all occupied locations/bays when departing
        if ($movement->tippingLocation) {
            $movement->tippingLocation->markAvailable();
        }
        if ($movement->tippingBay) {
            $movement->tippingBay->markAvailable();
        }

        $movement->update([
            'current_status' => 'departed',
            'actual_departure' => now(),
            'tipping_location_id' => null,
            'tipping_bay_id' => null,
            'operation_notes' => $request->notes ? ($movement->operation_notes ? $movement->operation_notes."\n".$request->notes : $request->notes) : $movement->operation_notes,
        ]);

        $factoryBooking->update([
            'status' => 'departed',
            'departed_at' => now()
        ]);
        
        return back()->with('success', 'Trailer departure recorded. All bays and locations freed.');
    }
}