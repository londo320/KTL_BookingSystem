<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\FactoryBooking;
use App\Models\PalletType;
use App\Models\PoLine;
use App\Models\PoLineActualPallet;
use App\Models\Setting;
use App\Models\TippingBay;
use App\Models\TippingLocation;
use App\Models\User;
use Illuminate\Http\Request;

class TippingWorkflowController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin|depot-admin|site-admin']);
    }

    public function show(Booking $booking)
    {
        $this->authorizeBookingAccess($booking);

        $booking->load([
            'slot.depot',
            'customer',
            'tippingLocation',
            'tippingBay',
            'tippingOperator',
            'bayAssignedBy',
            'poNumbers.lines.expectedPalletType',
            'poNumbers.lines.actualPalletType',
            'poNumbers.lines.actualPallets.palletType',
        ]);

        // Get available locations and bays for this depot
        $availableLocations = TippingLocation::forDepot($booking->slot->depot_id)
            ->available()
            ->get();

        // Get collection zones for empty trailers
        $collectionZones = TippingLocation::forDepot($booking->slot->depot_id)
            ->collectionZones()
            ->available()
            ->get();

        $availableBays = TippingBay::forDepot($booking->slot->depot_id)
            ->available()
            ->get();

        // Get tipping operators (depot staff)
        $operators = User::whereHas('depots', function ($query) use ($booking) {
            $query->where('depots.id', $booking->slot->depot_id);
        })->get();

        $workflowEnabled = Setting::isTippingWorkflowEnabled();
        
        // Get all active pallet types for the tipping completion form
        $palletTypes = PalletType::active()->orderBy('name')->get();

        return view('admin.tipping-workflow.show', compact(
            'booking',
            'availableLocations',
            'collectionZones',
            'availableBays',
            'operators',
            'workflowEnabled',
            'palletTypes'
        ));
    }


    public function dropTrailer(Request $request, Booking $booking)
    {
        $this->authorizeBookingAccess($booking);

        $request->validate([
            'tipping_location_id' => 'required|exists:tipping_locations,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $location = TippingLocation::findOrFail($request->tipping_location_id);

        // Verify location is available and in same depot
        if ($location->depot_id !== $booking->slot->depot_id) {
            return back()->withErrors(['tipping_location_id' => 'Location must be within the same depot as the booking: ' . $booking->slot->depot->name]);
        }
        
        if (! $location->isAvailable()) {
            return back()->withErrors(['tipping_location_id' => 'Selected location is not currently available.']);
        }

        // Check workflow enforcement
        if (Setting::isTippingWorkflowEnabled()) {
            if (! $booking->dropTrailer($location, $request->notes)) {
                return back()->withErrors(['error' => 'Cannot drop trailer at this stage.']);
            }
        } else {
            // Manual mode - allow action without strict workflow enforcement
            $movement = $booking->getOrCreateMovement();
            $movement->update([
                'tipping_location_id' => $location->id,
                'current_status' => 'in_location', // Changed from 'trailer_dropped' to indicate still attached
                'moved_to_location_at' => now(),
                'operation_notes' => $request->notes ? ($movement->operation_notes ? $movement->operation_notes."\n".$request->notes : $request->notes) : $movement->operation_notes,
            ]);
            $location->markOccupied($booking);
        }

        return back()->with('success', 'Vehicle parked successfully at '.$location->name);
    }

    public function moveToBay(Request $request, Booking $booking)
    {
        $this->authorizeBookingAccess($booking);

        $request->validate([
            'tipping_bay_id' => 'required|exists:tipping_bays,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $bay = TippingBay::findOrFail($request->tipping_bay_id);

        // Verify bay is available and in same depot
        if ($bay->depot_id !== $booking->slot->depot_id) {
            return back()->withErrors(['tipping_bay_id' => 'Bay must be within the same depot as the booking: ' . $booking->slot->depot->name]);
        }
        
        if (! $bay->isAvailable()) {
            return back()->withErrors(['tipping_bay_id' => 'Selected bay is not currently available.']);
        }

        // Check workflow enforcement
        if (Setting::isTippingWorkflowEnabled()) {
            if (! $booking->moveToBay($bay, $request->notes)) {
                return back()->withErrors(['error' => 'Cannot move trailer to bay at this stage.']);
            }
        } else {
            // Manual mode - allow action without strict workflow enforcement
            $movement = $booking->getOrCreateMovement();
            $movement->update([
                'tipping_bay_id' => $bay->id,
                'current_status' => 'at_bay',
                'moved_to_bay_at' => now(),
                'operation_notes' => $request->notes ? ($movement->operation_notes ? $movement->operation_notes."\n".$request->notes : $request->notes) : $movement->operation_notes,
            ]);
            $bay->markOccupied($booking);
        }

        return back()->with('success', 'Trailer moved to '.$bay->name);
    }

    public function startTipping(Request $request, Booking $booking)
    {
        $this->authorizeBookingAccess($booking);

        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $movement = $booking->getOrCreateMovement();
        
        // Prevent multiple tipping sessions - check if tipping was already completed
        if ($movement->unloading_completed_at) {
            return back()->withErrors(['error' => 'Tipping has already been completed for this trailer. Cannot restart tipping process.']);
        }

        // Check workflow enforcement
        if (Setting::isTippingWorkflowEnabled()) {
            if (! $booking->startTipping(null, $request->notes)) {
                return back()->withErrors(['error' => 'Cannot start tipping at this stage.']);
            }
        } else {
            // Manual mode - allow action without strict workflow enforcement
            $movement->update([
                'current_status' => 'unloading',
                'unloading_started_at' => now(),
                'operation_notes' => $request->notes ? ($movement->operation_notes ? $movement->operation_notes."\n".$request->notes : $request->notes) : $movement->operation_notes,
            ]);
        }

        return back()->with('success', 'Tipping started - complete when finished to record actual quantities.');
    }

    public function completeTipping(Request $request, Booking $booking)
    {
        $this->authorizeBookingAccess($booking);

        $movement = $booking->getOrCreateMovement();
        
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
        if ($booking->poNumbers()->exists()) {
            $validationRules['po_lines'] = 'required|array';
            $validationRules['po_lines.*.actual_cases'] = 'required|integer|min:1';
            $validationRules['po_lines.*.actual_pallets'] = 'required|array|min:1';
            $validationRules['po_lines.*.actual_pallets.*.pallet_type_id'] = 'required|exists:pallet_types,id';
            $validationRules['po_lines.*.actual_pallets.*.quantity'] = 'required|integer|min:1';
        }

        $request->validate($validationRules);

        $issues = $request->issues ? array_filter($request->issues) : null;

        \DB::transaction(function () use ($request, $booking, $issues) {
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

            // Check workflow enforcement
            if (Setting::isTippingWorkflowEnabled()) {
                if (! $booking->completeTipping($request->notes, $issues)) {
                    throw new \Exception('Cannot complete tipping at this stage.');
                }
            } else {
                // Manual mode - allow action without strict workflow enforcement
                $movement = $booking->getOrCreateMovement();
                
                // Free up current bay if any
                if ($movement->tippingBay) {
                    $movement->tippingBay->markAvailable($booking);
                }
                
                $movement->update([
                    'current_status' => 'empty',
                    'tipping_bay_id' => null, // Clear bay when tipping is completed
                    'unloading_completed_at' => now(),
                    'operation_notes' => $request->notes ? ($movement->operation_notes ? $movement->operation_notes."\n".$request->notes : $request->notes) : $movement->operation_notes,
                ]);
            }
        });

        return back()->with('success', 'Tipping completed successfully with actual quantities recorded.');
    }

    public function moveToLocation(Request $request, Booking $booking)
    {
        $this->authorizeBookingAccess($booking);

        $request->validate([
            'tipping_location_id' => 'required|exists:tipping_locations,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $location = TippingLocation::findOrFail($request->tipping_location_id);

        // Verify location is available and in same depot
        if ($location->depot_id !== $booking->slot->depot_id) {
            return back()->withErrors(['tipping_location_id' => 'Location must be within the same depot as the booking: ' . $booking->slot->depot->name]);
        }
        
        if (! $location->isAvailable()) {
            return back()->withErrors(['tipping_location_id' => 'Selected location is not currently available.']);
        }

        $movement = $booking->getOrCreateMovement();
        
        // Free up current location if any
        if ($movement->tippingLocation && $movement->tippingLocation->id != $location->id) {
            $movement->tippingLocation->markAvailable();
        }
        
        // Free up current bay if moving out of bay
        if ($movement->tippingBay) {
            $movement->tippingBay->markAvailable($booking);
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
        
        $location->markOccupied($booking);

        $statusMessage = $isEmptyMovement ? 'Empty unit moved to ' : 'Vehicle moved to ';
        return back()->with('success', $statusMessage . $location->name);
    }

    public function dropTrailerDetached(Request $request, Booking $booking)
    {
        $this->authorizeBookingAccess($booking);

        $request->validate([
            'tipping_location_id' => 'required|exists:tipping_locations,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $location = TippingLocation::findOrFail($request->tipping_location_id);

        // Verify location is available and in same depot
        if ($location->depot_id !== $booking->slot->depot_id) {
            return back()->withErrors(['tipping_location_id' => 'Location must be within the same depot as the booking: ' . $booking->slot->depot->name]);
        }
        
        if (! $location->isAvailable()) {
            return back()->withErrors(['tipping_location_id' => 'Selected location is not currently available.']);
        }

        $movement = $booking->getOrCreateMovement();
        
        // Free up current bay if any
        if ($movement->tippingBay) {
            $movement->tippingBay->markAvailable($booking);
        }

        $movement->update([
            'tipping_location_id' => $location->id,
            'tipping_bay_id' => null, // Clear bay when dropping trailer
            'current_status' => 'trailer_dropped',
            'trailer_dropped_at' => now(),
            'unit_departed_at' => now(), // Record unit departure time
            'operation_notes' => $request->notes ? ($movement->operation_notes ? $movement->operation_notes."\n".$request->notes : $request->notes) : $movement->operation_notes,
        ]);
        
        $location->markOccupied($booking);

        return back()->with('success', 'Trailer dropped (detached) at '.$location->name);
    }

    public function trailerDepart(Request $request, Booking $booking)
    {
        $this->authorizeBookingAccess($booking);

        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        // Check workflow enforcement
        if (Setting::isTippingWorkflowEnabled()) {
            if (! $booking->trailerDepart($request->notes)) {
                return back()->withErrors(['error' => 'Cannot process trailer departure at this stage.']);
            }
        } else {
            // Manual mode - allow action without strict workflow enforcement
            $movement = $booking->getOrCreateMovement();
            $movement->update([
                'current_status' => 'departed',
                'departed_at' => now(),
                'operation_notes' => $request->notes ? ($movement->operation_notes ? $movement->operation_notes."\n".$request->notes : $request->notes) : $movement->operation_notes,
            ]);

            $booking->update(['status' => 'completed']);

            // Free up location and bay
            if ($movement->tippingLocation) {
                $movement->tippingLocation->markAvailable();
            }
            if ($movement->tippingBay) {
                $movement->tippingBay->markAvailable($booking);
            }
        }

        return back()->with('success', 'Trailer departure recorded successfully.');
    }

    public function unitDepart(Request $request, Booking $booking)
    {
        $this->authorizeBookingAccess($booking);

        $request->validate([
            'notes' => 'nullable|string|max:1000',
            'collect_different_trailer' => 'nullable|boolean',
        ]);

        $movement = $booking->getOrCreateMovement();
        $collectingDifferentTrailer = $request->boolean('collect_different_trailer');
        
        // Record unit departure
        $movement->update([
            'unit_departed_at' => now(),
            'operation_notes' => $request->notes ? ($movement->operation_notes ? $movement->operation_notes."\n".$request->notes : $request->notes) : $movement->operation_notes,
        ]);

        // If trailer is empty and unit is leaving, mark as waiting for collection
        if (in_array($movement->current_status, ['empty'])) {
            $movement->update(['current_status' => 'trailer_dropped']);
        }

        // If unit is collecting a different trailer, mark this booking as completed
        if ($collectingDifferentTrailer) {
            $booking->update(['status' => 'completed']);
            
            // Free up location and bay
            if ($movement->tippingLocation) {
                $movement->tippingLocation->markAvailable();
            }
            if ($movement->tippingBay) {
                $movement->tippingBay->markAvailable($booking);
            }
            
            return back()->with('success', 'Unit departed to collect different trailer. Booking completed.');
        }

        return back()->with('success', 'Unit departure recorded. Trailer awaiting collection.');
    }

    public function collectionArrival(Request $request, Booking $booking)
    {
        $this->authorizeBookingAccess($booking);

        $request->validate([
            'collection_unit_registration' => 'required|string|max:50',
            'collection_driver_name' => 'nullable|string|max:100',
            'collection_driver_phone' => 'nullable|string|max:20',
            'notes' => 'nullable|string|max:1000',
        ]);

        $movement = $booking->getOrCreateMovement();
        
        $movement->update([
            'collection_unit_arrived_at' => now(),
            'collection_unit_registration' => $request->collection_unit_registration,
            'collection_driver_name' => $request->collection_driver_name,
            'collection_driver_phone' => $request->collection_driver_phone,
            'collection_notes' => $request->notes,
            'current_status' => 'trailer_collected',
        ]);

        return back()->with('success', 'Collection unit arrived and trailer collected.');
    }

    public function collectionDepart(Request $request, Booking $booking)
    {
        $this->authorizeBookingAccess($booking);

        $request->validate([
            'notes' => 'nullable|string|max:1000',
        ]);

        $movement = $booking->getOrCreateMovement();
        
        $movement->update([
            'collection_unit_departed_at' => now(),
            'current_status' => 'departed',
            'collection_notes' => $request->notes ? ($movement->collection_notes ? $movement->collection_notes."\n".$request->notes : $request->notes) : $movement->collection_notes,
        ]);

        // Update booking status to completed
        $booking->update(['status' => 'completed']);

        // Free up location and bay
        if ($movement->tippingLocation) {
            $movement->tippingLocation->markAvailable();
        }
        if ($movement->tippingBay) {
            $movement->tippingBay->markAvailable($booking);
        }

        return back()->with('success', 'Collection unit departed. Booking completed.');
    }

    public function moveToCollectionZone(Request $request, Booking $booking)
    {
        $this->authorizeBookingAccess($booking);

        $request->validate([
            'tipping_location_id' => 'required|exists:tipping_locations,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $location = TippingLocation::findOrFail($request->tipping_location_id);

        // Verify location is a collection zone and in same depot
        if ($location->depot_id !== $booking->slot->depot_id) {
            return back()->withErrors(['tipping_location_id' => 'Location must be within the same depot as the booking: ' . $booking->slot->depot->name]);
        }

        if ($location->location_type !== TippingLocation::TYPE_COLLECTION_ZONE) {
            return back()->withErrors(['tipping_location_id' => 'Selected location must be a collection zone.']);
        }
        
        if (! $location->isAvailable()) {
            return back()->withErrors(['tipping_location_id' => 'Selected collection zone is not currently available.']);
        }

        $movement = $booking->getOrCreateMovement();
        
        // Free up current location if any
        if ($movement->tippingLocation && $movement->tippingLocation->id != $location->id) {
            $movement->tippingLocation->markAvailable();
        }
        
        // Free up current bay if moving out of bay
        if ($movement->tippingBay) {
            $movement->tippingBay->markAvailable($booking);
        }

        $movement->update([
            'tipping_location_id' => $location->id,
            'tipping_bay_id' => null,
            'current_status' => 'awaiting_collection',
            'moved_to_location_at' => now(),
            'operation_notes' => $request->notes ? ($movement->operation_notes ? $movement->operation_notes."\n".$request->notes : $request->notes) : $movement->operation_notes,
        ]);
        
        $location->markOccupied($booking);

        return back()->with('success', '✅ Empty trailer successfully moved to collection zone: ' . $location->name . ($location->code ? ' (' . $location->code . ')' : '') . ' - Ready for pickup');
    }

    public function dashboard(Request $request)
    {
        // Apply depot access restrictions for depot-admin
        $allowedDepotIds = $this->getAllowedDepotIds();
        
        // Get user's default depot or first allowed depot
        $user = auth()->user();
        $defaultDepotId = $user->depot_id ?? $allowedDepotIds[0] ?? null;
        
        // Allow depot selection via request parameter
        $selectedDepotId = $request->get('depot_id');
        
        // Validate selected depot is in allowed list
        if ($selectedDepotId && in_array($selectedDepotId, $allowedDepotIds)) {
            $currentDepotId = $selectedDepotId;
        } else {
            $currentDepotId = $defaultDepotId;
        }

        if ($currentDepotId) {
            $depots = [\App\Models\Depot::find($currentDepotId)];
        } else {
            $depots = \App\Models\Depot::whereIn('id', $allowedDepotIds)->get();
        }

        $tippingData = [];
        $accessibleCustomerIds = auth()->user()->getAccessibleCustomerIds();

        foreach ($depots as $depot) {
            // Get tipping locations with current occupancy - filtered by accessible customers
            $locations = TippingLocation::forDepot($depot->id)
                ->with(['activeBookings' => function ($query) use ($accessibleCustomerIds) {
                    if (! auth()->user()->canSeeAllCustomers()) {
                        $query->whereIn('customer_id', $accessibleCustomerIds);
                    }
                }, 'activeBookings.customer', 'activeBookings.poNumbers'])
                ->get();

            // Get tipping bays with current status - filtered by accessible customers
            $bays = TippingBay::forDepot($depot->id)->get();
            
            // Load current bookings separately for each bay to get movement data
            foreach ($bays as $bay) {
                $currentBooking = $bay->currentBooking();
                if ($currentBooking && (auth()->user()->canSeeAllCustomers() || in_array($currentBooking->customer_id, $accessibleCustomerIds))) {
                    $currentBooking->load(['customer', 'poNumbers', 'movements']);
                    $bay->setRelation('currentBooking', $currentBooking);
                }
            }

            // Get bookings by status - filtered by accessible customers
            $bookingsQuery = Booking::whereHas('slot', function ($query) use ($depot) {
                $query->where('depot_id', $depot->id);
            })
                ->where('arrived_at', '!=', null) // Only arrived bookings
                ->whereNull('cancelled_at');

            // Filter by accessible customers if user doesn't have access to all
            if (! auth()->user()->canSeeAllCustomers()) {
                $bookingsQuery->whereIn('customer_id', $accessibleCustomerIds);
            }

            $bookingsByStatus = $bookingsQuery
                ->join('movements', 'bookings.id', '=', 'movements.booking_id')
                ->selectRaw('movements.current_status, COUNT(*) as count')
                ->groupBy('movements.current_status')
                ->pluck('count', 'current_status')
                ->toArray();

            // Get factory bookings by status for this depot
            $factoryBookingsQuery = FactoryBooking::where('factory_bookings.depot_id', $depot->id)
                ->onSite(); // Only factory bookings currently on site

            // Filter by accessible customers if user doesn't have access to all
            if (! auth()->user()->canSeeAllCustomers()) {
                $factoryBookingsQuery->whereIn('factory_bookings.customer_id', $accessibleCustomerIds);
            }

            $factoryBookingsByStatus = $factoryBookingsQuery
                ->join('movements', 'factory_bookings.id', '=', 'movements.factory_booking_id')
                ->selectRaw('movements.current_status, COUNT(*) as count')
                ->groupBy('movements.current_status')
                ->pluck('count', 'current_status')
                ->toArray();

            // Combine regular bookings and factory bookings stats
            $combinedStats = [
                'not_started' => ($bookingsByStatus['scheduled'] ?? 0) + ($factoryBookingsByStatus['arrived'] ?? 0),
                'in_location' => ($bookingsByStatus['in_location'] ?? 0),
                'trailer_dropped' => ($bookingsByStatus['trailer_dropped'] ?? 0),
                'moved_to_bay' => ($bookingsByStatus['at_bay'] ?? 0),
                'tipping_in_progress' => ($bookingsByStatus['unloading'] ?? 0) + ($factoryBookingsByStatus['unloading'] ?? 0),
                'tipping_completed' => ($bookingsByStatus['empty'] ?? 0) + ($factoryBookingsByStatus['empty'] ?? 0),
                'trailer_departed' => ($bookingsByStatus['departed'] ?? 0) + ($factoryBookingsByStatus['departed'] ?? 0),
                'factory_bookings' => [
                    'total' => $factoryBookingsQuery->count(),
                    'arrived' => $factoryBookingsByStatus['arrived'] ?? 0,
                    'processing' => $factoryBookingsByStatus['unloading'] ?? 0,
                    'completed' => $factoryBookingsByStatus['empty'] ?? 0,
                ],
            ];

            $tippingData[] = [
                'depot' => $depot,
                'locations' => $locations,
                'bays' => $bays,
                'stats' => $combinedStats,
            ];
        }

        $allDepots = \App\Models\Depot::whereIn('id', $allowedDepotIds)->get();

        return view('admin.tipping-workflow.dashboard', compact('tippingData', 'allDepots', 'currentDepotId'));
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

    private function authorizeBookingAccess(Booking $booking): void
    {
        // Check if user can access this booking's customer
        if (! auth()->user()->canAccessCustomer($booking->customer_id)) {
            abort(403, 'You do not have access to this customer\'s bookings.');
        }

        // Check if user can access this booking's depot
        if (! in_array($booking->slot->depot_id, $this->getAllowedDepotIds())) {
            abort(403, 'You do not have access to this depot.');
        }
    }
}
