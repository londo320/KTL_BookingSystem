<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Movement;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PrioritySettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin|depot-admin|site-admin']);
    }

    /**
     * Show priority settings modal for queue management
     */
    public function index(Request $request)
    {
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
        
        // Filter movements by selected depot only
        $depotIds = $currentDepotId ? [$currentDepotId] : $allowedDepotIds;

        // Get active trailers in queue
        $activeBookings = Booking::with(['customer', 'slot.depot', 'movements'])
            ->whereHas('slot', fn($q) => $q->whereIn('depot_id', $depotIds))
            ->whereHas('movements', function($q) {
                $q->whereIn('current_status', ['arrived', 'in_waiting', 'trailer_dropped'])
                  ->whereNull('actual_departure')
                  ->whereNull('collection_unit_departed_at');
            })
            ->get();

        // Get customers that have active bookings
        $customers = Customer::whereIn('id', $activeBookings->pluck('customer_id')->unique())
            ->orderBy('name')
            ->get();

        return view('admin.operations.priority-settings', compact(
            'activeBookings',
            'customers',
            'currentDepotId'
        ));
    }

    /**
     * Update customer priority level
     */
    public function updateCustomerPriority(Request $request, Customer $customer)
    {
        $request->validate([
            'priority_level' => 'required|integer|min:0|max:10',
            'priority_notes' => 'nullable|string|max:500'
        ]);

        $customer->update([
            'priority_level' => $request->priority_level,
            'priority_notes' => $request->priority_notes
        ]);

        return response()->json([
            'success' => true,
            'message' => "Updated priority for {$customer->name}"
        ]);
    }

    /**
     * Update booking-specific settings
     */
    public function updateBookingPriority(Request $request, Booking $booking)
    {
        $request->validate([
            'collection_scheduled_at' => 'nullable|date|after:now',
            'manual_priority_boost' => 'nullable|integer|min:-100|max:200',
            'priority_notes' => 'nullable|string|max:500'
        ]);

        $booking->update([
            'collection_scheduled_at' => $request->collection_scheduled_at ? 
                Carbon::parse($request->collection_scheduled_at) : null,
            'manual_priority_boost' => $request->manual_priority_boost ?? 0,
            'priority_notes' => $request->priority_notes
        ]);

        return response()->json([
            'success' => true,
            'message' => "Updated priority settings for {$booking->booking_reference}"
        ]);
    }

    /**
     * Set tipping type for a booking
     */
    public function setTippingType(Request $request, Booking $booking)
    {
        $request->validate([
            'tipping_type' => 'required|in:live_tip,drop'
        ]);

        $booking->update([
            'tipping_type' => $request->tipping_type
        ]);

        $typeLabels = [
            'live_tip' => 'Live Tip (Unit Stays)',
            'drop' => 'Drop (Unit Leaves)'
        ];

        return response()->json([
            'success' => true,
            'message' => "Set tipping type to: {$typeLabels[$request->tipping_type]}"
        ]);
    }

    /**
     * Reset all manual priority boosts
     */
    public function resetAllPriorities(Request $request)
    {
        $allowedDepotIds = $this->getAllowedDepotIds();
        $selectedDepotId = $request->get('depot_id');
        
        $depotIds = $selectedDepotId && in_array($selectedDepotId, $allowedDepotIds) 
            ? [$selectedDepotId] 
            : $allowedDepotIds;

        $updated = Booking::whereHas('slot', fn($q) => $q->whereIn('depot_id', $depotIds))
            ->where('manual_priority_boost', '!=', 0)
            ->update([
                'manual_priority_boost' => 0,
                'priority_notes' => null
            ]);

        return response()->json([
            'success' => true,
            'message' => "Reset priority boosts for {$updated} bookings"
        ]);
    }

    private function getAllowedDepotIds(): array
    {
        $assignedDepotIds = auth()->user()->depots()->pluck('depots.id')->toArray();

        if (empty($assignedDepotIds) && auth()->user()->hasRole('admin|site-admin')) {
            return \App\Models\Depot::pluck('id')->toArray();
        }

        return $assignedDepotIds;
    }
}