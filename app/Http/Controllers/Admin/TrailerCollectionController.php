<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\FactoryBooking;
use Illuminate\Http\Request;

class TrailerCollectionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin|depot-admin|site-admin']);
    }

    public function index(Request $request)
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

        $accessibleCustomerIds = auth()->user()->getAccessibleCustomerIds();

        // Get trailers awaiting collection (both empty after tipping and full rejected/dropped)
        $trailersQuery = Booking::with([
            'customer',
            'slot.depot',
            'movements.tippingLocation',
            'poNumbers'
        ])
        ->whereHas('slot', function ($query) use ($currentDepotId) {
            if ($currentDepotId) {
                $query->where('depot_id', $currentDepotId);
            }
        })
        ->whereHas('movements', function ($query) {
            $query->whereIn('current_status', [
                'awaiting_collection',
                'trailer_dropped',
                'empty'
            ]);
        })
        ->whereNull('cancelled_at');

        // Filter by accessible customers if user doesn't have access to all
        if (! auth()->user()->canSeeAllCustomers()) {
            $trailersQuery->whereIn('customer_id', $accessibleCustomerIds);
        }

        $trailers = $trailersQuery->get();

        // Get factory bookings awaiting collection
        $factoryTrailersQuery = FactoryBooking::with([
            'customer',
            'depot',
            'movements.tippingLocation'
        ])
        ->where('depot_id', $currentDepotId)
        ->whereHas('movements', function ($query) {
            $query->whereIn('current_status', [
                'awaiting_collection',
                'trailer_dropped',
                'empty'
            ]);
        })
        ->whereNull('cancelled_at');

        if (! auth()->user()->canSeeAllCustomers()) {
            $factoryTrailersQuery->whereIn('customer_id', $accessibleCustomerIds);
        }

        $factoryTrailers = $factoryTrailersQuery->get();

        $allDepots = \App\Models\Depot::whereIn('id', $allowedDepotIds)->get();

        return view('admin.trailer-collection.index', compact(
            'trailers',
            'factoryTrailers',
            'allDepots',
            'currentDepotId'
        ));
    }

    public function collect(Request $request, Booking $booking)
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
            'current_status' => 'collected',
        ]);

        // Update booking status to completed
        $booking->update(['status' => 'completed']);

        // Free up location
        if ($movement->tippingLocation) {
            $movement->tippingLocation->markAvailable();
        }

        return back()->with('success', 'Trailer collected successfully.');
    }

    private function getAllowedDepotIds(): array
    {
        $assignedDepotIds = auth()->user()->depots()->pluck('depots.id')->toArray();

        if (empty($assignedDepotIds) && auth()->user()->hasRole('admin|site-admin')) {
            return \App\Models\Depot::pluck('id')->toArray();
        }

        return $assignedDepotIds;
    }

    private function authorizeBookingAccess(Booking $booking): void
    {
        if (! auth()->user()->canAccessCustomer($booking->customer_id)) {
            abort(403, 'You do not have access to this customer\'s bookings.');
        }

        if (! in_array($booking->slot->depot_id, $this->getAllowedDepotIds())) {
            abort(403, 'You do not have access to this depot.');
        }
    }
}