<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\TippingBay;
use App\Models\TippingLocation;
use Illuminate\Http\Request;

class OperationsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin|depot-admin|site-admin|warehouse']);
    }

    /**
     * Assign a trailer to a drop zone
     */
    public function assignDropZone(Request $request, Booking $booking)
    {
        $request->validate([
            'location_id' => 'required|exists:tipping_locations,id',
        ]);

        $location = TippingLocation::findOrFail($request->location_id);
        $movement = $booking->getOrCreateMovement();

        $movement->update([
            'tipping_location_id' => $location->id,
            'current_status' => 'in_parking',
            'moved_to_location_at' => now(),
        ]);

        $location->markOccupied($booking);

        return response()->json([
            'success' => true,
            'message' => "Trailer assigned to {$location->name}",
            'next_action' => 'Record unit departure'
        ]);
    }

    /**
     * Record unit departure (driver leaves, trailer stays)
     */
    public function unitDepart(Request $request, Booking $booking)
    {
        $request->validate([
            'unit_registration' => 'required|string|max:50',
        ]);

        $movement = $booking->getOrCreateMovement();

        $movement->update([
            'unit_departed_at' => now(),
            'current_status' => 'trailer_dropped',
            'operation_notes' => "Unit {$request->unit_registration} departed, trailer dropped",
        ]);

        return response()->json([
            'success' => true,
            'message' => "Unit {$request->unit_registration} departed",
            'next_action' => 'Shunt trailer to bay'
        ]);
    }

    /**
     * Shunt trailer to tipping bay (ONLY for loaded trailers)
     */
    public function shuntToBay(Request $request, Booking $booking)
    {
        $request->validate([
            'bay_id' => 'required|exists:tipping_bays,id',
        ]);

        $movement = $booking->getOrCreateMovement();
        
        // WORKFLOW RULE: Only loaded trailers can go to tipping bays
        if (in_array($movement->current_status, ['empty', 'departed'])) {
            return response()->json([
                'success' => false,
                'error' => 'Empty trailers cannot be moved to tipping bays. Move to collection zone instead.'
            ], 422);
        }

        $bay = TippingBay::findOrFail($request->bay_id);

        // Check bay is available
        if (!$bay->isAvailable()) {
            return response()->json([
                'success' => false,
                'error' => "Bay {$bay->name} is currently occupied"
            ], 422);
        }

        // Free up current location
        if ($movement->tippingLocation) {
            $movement->tippingLocation->markAvailable();
        }

        $movement->update([
            'tipping_bay_id' => $bay->id,
            'tipping_location_id' => null,
            'current_status' => 'at_bay',
            'moved_to_bay_at' => now(),
        ]);

        $bay->markOccupied($booking);

        return response()->json([
            'success' => true,
            'message' => "Loaded trailer shunted to {$bay->name}",
            'next_action' => 'Start tipping'
        ]);
    }

    /**
     * Start tipping process (supports both Live Tip and Drop workflows)
     */
    public function startTipping(Request $request, Booking $booking)
    {
        $movement = $booking->getOrCreateMovement();
        
        // Handle different tipping types
        $tippingType = $booking->tipping_type ?? 'live_tip';
        
        if ($tippingType === 'live_tip') {
            // Live Tip workflow: Trailer must be at bay
            if ($movement->current_status !== 'at_bay') {
                return response()->json([
                    'success' => false,
                    'error' => 'Live Tip: Trailer must be at tipping bay to start tipping'
                ], 422);
            }
        } else {
            // Drop workflow: Can tip from various statuses
            if (!in_array($movement->current_status, ['arrived', 'in_parking'])) {
                return response()->json([
                    'success' => false,
                    'error' => 'Drop: Booking is not ready to start tipping (current status: ' . $movement->current_status . ')'
                ], 422);
            }
        }
        
        // Cannot tip empty trailers
        if (in_array($movement->current_status, ['empty', 'departed'])) {
            return response()->json([
                'success' => false,
                'error' => 'Cannot tip empty trailer'
            ], 422);
        }
        
        // Start tipping process
        $booking->update([
            'tipping_started_at' => now(),
            'tipping_status' => 'tipping_in_progress'
        ]);
        
        $movement->update([
            'current_status' => 'unloading',
            'unloading_started_at' => now(),
        ]);
        
        // Record appropriate history message
        $historyMessage = $tippingType === 'live_tip' 
            ? 'Live Tip tipping started - vehicle remains connected'
            : 'Drop workflow tipping started - trailer processing independently';
            
        \App\Models\BookingHistory::recordAction(
            $booking,
            'tipping_started',
            $historyMessage
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Tipping started successfully',
            'workflow' => $tippingType,
            'next_action' => 'Complete tipping when finished'
        ]);
    }

    /**
     * Complete tipping process
     */
    public function completeTipping(Request $request, Booking $booking)
    {
        $movement = $booking->getOrCreateMovement();

        $movement->update([
            'current_status' => 'empty',
            'unloading_completed_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tipping completed - trailer is now empty',
            'next_action' => 'Move to collection zone'
        ]);
    }

    /**
     * Move empty trailer to collection zone (ONLY for empty trailers)
     */
    public function moveToCollectionZone(Request $request, Booking $booking)
    {
        $request->validate([
            'location_id' => 'required|exists:tipping_locations,id',
        ]);

        $location = TippingLocation::findOrFail($request->location_id);
        $movement = $booking->getOrCreateMovement();

        // WORKFLOW RULE: Only empty trailers can go to collection zones
        if ($movement->current_status !== 'empty') {
            return response()->json([
                'success' => false,
                'error' => 'Only empty trailers can be moved to collection zones. Tip the trailer first.'
            ], 422);
        }

        // Free up current bay
        if ($movement->tippingBay) {
            $movement->tippingBay->markAvailable($booking);
        }

        $movement->update([
            'tipping_location_id' => $location->id,
            'tipping_bay_id' => null,
            'current_status' => 'back_to_parking',
            'moved_to_location_at' => now(),
        ]);

        $location->markOccupied($booking);

        return response()->json([
            'success' => true,
            'message' => "Empty trailer moved to {$location->name}",
            'next_action' => 'Await collection unit'
        ]);
    }

    /**
     * Move trailer between drop zones (loaded or empty)
     */
    public function moveToDropZone(Request $request, Booking $booking)
    {
        $request->validate([
            'location_id' => 'required|exists:tipping_locations,id',
        ]);

        $location = TippingLocation::findOrFail($request->location_id);
        $movement = $booking->getOrCreateMovement();

        // Free up current location/bay
        if ($movement->tippingLocation && $movement->tippingLocation->id != $location->id) {
            $movement->tippingLocation->markAvailable();
        }
        if ($movement->tippingBay) {
            $movement->tippingBay->markAvailable($booking);
        }

        // Determine status based on trailer state  
        $newStatus = in_array($movement->current_status, ['empty']) ? 'back_to_parking' : 'in_parking';

        $movement->update([
            'tipping_location_id' => $location->id,
            'tipping_bay_id' => null,
            'current_status' => $newStatus,
            'moved_to_location_at' => now(),
        ]);

        $location->markOccupied($booking);

        $trailerState = $movement->current_status === 'empty' ? 'Empty' : 'Loaded';
        
        return response()->json([
            'success' => true,
            'message' => "{$trailerState} trailer moved to {$location->name}",
            'next_action' => $movement->current_status === 'empty' ? 'Await collection' : 'Shunt to bay when ready'
        ]);
    }

    /**
     * Record collection unit arrival and departure
     */
    public function recordCollection(Request $request, Booking $booking)
    {
        $request->validate([
            'collection_registration' => 'required|string|max:50',
            'driver_name' => 'nullable|string|max:100',
        ]);

        $movement = $booking->getOrCreateMovement();

        $movement->update([
            'collection_unit_arrived_at' => now(),
            'collection_unit_departed_at' => now()->addMinutes(5), // Assume 5 minute collection time
            'collection_unit_registration' => $request->collection_registration,
            'collection_driver_name' => $request->driver_name,
            'current_status' => 'departed',
        ]);

        // Complete the booking
        $booking->update(['status' => 'completed']);

        // Free up location
        if ($movement->tippingLocation) {
            $movement->tippingLocation->markAvailable();
        }

        return response()->json([
            'success' => true,
            'message' => "Collection by {$request->collection_registration} completed",
            'next_action' => 'Booking completed'
        ]);
    }

    /**
     * Get available locations for dropdown
     */
    public function getAvailableLocations(Request $request)
    {
        $type = $request->get('type', 'drop'); // drop or collection
        
        $locations = TippingLocation::where('is_active', true)
            ->where('location_type', 'parking') // Simplified: all locations are now parking areas
            ->available()
            ->get(['id', 'name', 'code']);

        return response()->json($locations);
    }

    /**
     * Get available bays for dropdown
     */
    public function getAvailableBays(Request $request)
    {
        $query = TippingBay::where('is_active', true)
            ->available();

        // Filter by depot if specified
        if ($request->has('depot_id') && $request->depot_id) {
            $query->where('depot_id', $request->depot_id);
        }

        $bays = $query->get(['id', 'name', 'code']);

        return response()->json($bays);
    }
}