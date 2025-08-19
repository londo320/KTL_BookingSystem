<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingHistory;
use App\Models\Customer;
use App\Models\Slot;
use Illuminate\Http\Request;

class BookingRebookController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin|depot-admin|site-admin']);
    }

    public function show(Booking $booking)
    {
        $booking->load(['slot', 'customer', 'history.originalSlot', 'history.newSlot']);

        // Get available slots for rebooking (same depot, future dates)
        $availableSlots = Slot::where('depot_id', $booking->slot->depot_id)
            ->where('start_at', '>', now())
            ->whereRaw('(
                SELECT COUNT(*) FROM bookings 
                WHERE bookings.slot_id = slots.id 
                AND bookings.cancelled_at IS NULL
            ) < slots.capacity')
            ->orderBy('start_at')
            ->take(20)
            ->get();

        // Customer behavior analysis
        $customerStats = $this->getCustomerBehaviorStats($booking->customer_id);

        // Check rebooking restrictions
        $restrictions = $this->checkRebookingRestrictions($booking);

        return view('admin.bookings.rebook', compact(
            'booking',
            'availableSlots',
            'customerStats',
            'restrictions'
        ));
    }

    public function store(Request $request, Booking $booking)
    {
        $request->validate([
            'new_slot_id' => 'required|exists:slots,id',
            'reason' => 'required|string|max:500',
        ]);

        // Validate rebooking restrictions
        $restrictions = $this->checkRebookingRestrictions($booking);
        if (! empty($restrictions['blocked'])) {
            return back()->withErrors(['rebook' => $restrictions['blocked']]);
        }

        $newSlot = Slot::findOrFail($request->new_slot_id);

        // Check slot capacity
        $currentBookings = $newSlot->bookings()->active()->count();
        if ($currentBookings >= $newSlot->capacity) {
            return back()->withErrors(['new_slot_id' => 'Selected slot is fully booked.']);
        }

        // Check depot access
        $allowedDepotIds = $this->getAllowedDepotIds();
        if (! in_array($newSlot->depot_id, $allowedDepotIds)) {
            return back()->withErrors(['new_slot_id' => 'You do not have access to this depot.']);
        }

        try {
            $newBooking = $booking->rebook($newSlot, $request->reason);

            // Add warning if this is excessive rebooking
            if ($restrictions['warning']) {
                session()->flash('warning', $restrictions['warning']);
            }

            return redirect()
                ->route('admin.bookings.show', $newBooking)
                ->with('success', 'Booking successfully rebooked to '.$newSlot->start_at->format('M j, Y g:i A'));

        } catch (\Exception $e) {
            return back()->withErrors(['rebook' => 'Failed to rebook: '.$e->getMessage()]);
        }
    }

    public function cancel(Request $request, Booking $booking)
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:500',
        ]);

        try {
            $booking->cancel($request->cancellation_reason);

            return redirect()
                ->route('admin.bookings.index')
                ->with('success', 'Booking cancelled successfully.');

        } catch (\Exception $e) {
            return back()->withErrors(['cancel' => 'Failed to cancel booking: '.$e->getMessage()]);
        }
    }

    public function history(Booking $booking)
    {
        try {
            // Check if booking_history table exists
            if (! \Schema::hasTable('booking_history')) {
                throw new \Exception('Booking history table does not exist yet');
            }

            // Get complete history for the entire booking chain, but filter intelligently
            $bookingIds = [$booking->id];

            // Get all bookings in the chain (both predecessors and successors)
            $originalBookingId = $booking->original_booking_id ?: $booking->id;
            $chainBookings = Booking::where('original_booking_id', $originalBookingId)
                ->orWhere('id', $originalBookingId)
                ->pluck('id')
                ->toArray();

            $bookingIds = array_unique(array_merge($bookingIds, $chainBookings));

            $allHistory = BookingHistory::whereIn('booking_id', $bookingIds)
                ->with(['booking.slot.depot', 'originalSlot.depot', 'newSlot.depot', 'user', 'customer'])
                ->orderBy('created_at', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            // Filter to show clean timeline: only first creation + all other actions
            $history = collect();
            $creationShown = false;

            foreach ($allHistory as $item) {
                if ($item->action === 'created') {
                    // Only show the very first creation entry
                    if (! $creationShown) {
                        $history->push($item);
                        $creationShown = true;
                    }
                    // Skip all subsequent creation entries
                } else {
                    // Show ALL non-creation actions (rebooks, cancellations, etc.)
                    $history->push($item);
                }
            }
            $originalCreationShown = $creationShown;

            // Apply smart chronological sorting after filtering
            $history = $history->sortBy([
                function ($item) {
                    // Primary sort: timestamp, but ensure created actions are always first
                    $timestamp = $item->created_at->timestamp;
                    if ($item->action === 'created') {
                        $timestamp -= 3600; // Subtract 1 hour to ensure created comes first
                    }

                    return $timestamp;
                },
                function ($item) {
                    // Secondary sort: action priority for same timestamp
                    $order = ['created' => 1, 'rebooked' => 2, 'cancelled' => 3];

                    return $order[$item->action] ?? 4;
                },
            ]);

            // Convert back to collection with preserved order
            $history = collect($history->values());

            // If no history records exist OR no creation was found, show clean timeline from booking data
            if ($history->isEmpty() || ! $originalCreationShown) {
                $history = collect();

                // Find the original booking in the chain
                $originalBookingId = $booking->original_booking_id ?: $booking->id;
                $originalBooking = $booking;
                if ($booking->original_booking_id) {
                    $originalBooking = Booking::find($originalBookingId) ?: $booking;
                }

                // Add original creation entry if not already present
                if (! $originalCreationShown) {
                    $history->push((object) [
                        'id' => 'creation-entry',
                        'action' => 'created',
                        'reason' => 'Initial booking created',
                        'created_at' => $originalBooking->created_at,
                        'user' => $originalBooking->user ?? auth()->user(),
                        'booking' => $originalBooking,
                        'originalSlot' => null,
                        'newSlot' => $originalBooking->slot,
                        'hours_before_slot' => null,
                        'is_last_minute' => false,
                        'customer_rebook_count_30days' => 0,
                        'customer_cancel_count_30days' => 0,
                        'changes' => null,
                    ]);
                }

                // Add rebook entry if this is a rebooked booking
                if ($booking->original_booking_id && $booking->id != $originalBookingId) {
                    $history->push((object) [
                        'id' => 'rebook-entry',
                        'action' => 'rebooked',
                        'reason' => $booking->rebook_reason ?? 'Booking rebooked',
                        'created_at' => $booking->created_at,
                        'user' => $booking->user ?? auth()->user(),
                        'booking' => $booking,
                        'originalSlot' => $originalBooking->slot,
                        'newSlot' => $booking->slot,
                        'hours_before_slot' => null,
                        'is_last_minute' => false,
                        'customer_rebook_count_30days' => 0,
                        'customer_cancel_count_30days' => 0,
                        'changes' => null,
                    ]);
                }

                // Add cancellation if applicable
                if ($booking->cancelled_at) {
                    $history->push((object) [
                        'id' => 'cancel-entry',
                        'action' => 'cancelled',
                        'reason' => $booking->cancellation_reason ?: 'Booking cancelled',
                        'created_at' => $booking->cancelled_at,
                        'user' => auth()->user(),
                        'booking' => $booking,
                        'originalSlot' => $booking->slot,
                        'newSlot' => null,
                        'hours_before_slot' => null,
                        'is_last_minute' => false,
                        'customer_rebook_count_30days' => 0,
                        'customer_cancel_count_30days' => 0,
                        'changes' => null,
                    ]);
                }
            }

        } catch (\Exception $e) {
            // If booking_history table doesn't exist or is incomplete, show a placeholder
            \Log::warning('Admin booking history error: '.$e->getMessage());

            // Create clean chronological history from booking chain data
            $history = collect([]);

            // Find the original booking in the chain
            $originalBookingId = $booking->original_booking_id ?: $booking->id;
            $originalBooking = $booking;
            if ($booking->original_booking_id) {
                try {
                    $originalBooking = Booking::find($originalBookingId) ?: $booking;
                } catch (\Exception $e) {
                    $originalBooking = $booking;
                }
            }

            // Always add original creation entry first
            $history->push((object) [
                'id' => 'placeholder-creation',
                'action' => 'created',
                'reason' => 'Initial booking created',
                'created_at' => $originalBooking->created_at,
                'user' => $originalBooking->user ?? auth()->user(),
                'booking' => $originalBooking,
                'originalSlot' => null,
                'newSlot' => $originalBooking->slot,
                'hours_before_slot' => null,
                'is_last_minute' => false,
                'customer_rebook_count_30days' => 0,
                'customer_cancel_count_30days' => 0,
                'changes' => null,
            ]);

            // Add rebook entry if this is a rebooked booking
            if ($booking->original_booking_id && $booking->id != $originalBookingId) {
                $history->push((object) [
                    'id' => 'placeholder-rebook',
                    'action' => 'rebooked',
                    'reason' => $booking->rebook_reason ?? 'Booking rebooked',
                    'created_at' => $booking->created_at,
                    'user' => $booking->user ?? auth()->user(),
                    'booking' => $booking,
                    'originalSlot' => $originalBooking->slot,
                    'newSlot' => $booking->slot,
                    'hours_before_slot' => null,
                    'is_last_minute' => false,
                    'customer_rebook_count_30days' => 0,
                    'customer_cancel_count_30days' => 0,
                    'changes' => null,
                ]);
            }

            // Add cancellation if current booking is cancelled
            if ($booking->cancelled_at) {
                $history->push((object) [
                    'id' => 'placeholder-cancel',
                    'action' => 'cancelled',
                    'reason' => $booking->cancellation_reason ?: 'Booking cancelled',
                    'created_at' => $booking->cancelled_at,
                    'user' => auth()->user(),
                    'booking' => $booking,
                    'originalSlot' => $booking->slot,
                    'newSlot' => null,
                    'hours_before_slot' => null,
                    'is_last_minute' => false,
                    'customer_rebook_count_30days' => 0,
                    'customer_cancel_count_30days' => 0,
                    'changes' => null,
                ]);
            }

            // Sort chronologically (oldest to newest) with created first
            $history = $history->sortBy([
                function ($item) {
                    // Primary sort: timestamp, but ensure created actions are always first
                    $timestamp = $item->created_at->timestamp;
                    if ($item->action === 'created') {
                        $timestamp -= 3600; // Subtract 1 hour to ensure created comes first
                    }

                    return $timestamp;
                },
                function ($item) {
                    // Secondary sort: action priority for same timestamp
                    $order = ['created' => 1, 'rebooked' => 2, 'cancelled' => 3];

                    return $order[$item->action] ?? 4;
                },
            ]);

            // Convert back to collection with preserved order
            $history = collect($history->values());
        }

        // Calculate actual rebook count from history
        $actualRebookCount = $history->where('action', 'rebooked')->count();

        // Calculate actual rebook count from filtered history
        $actualRebookCount = $history->where('action', 'rebooked')->count();

        // Handle sort order toggle with smart positioning of created
        $sortOrder = request('sort', 'asc'); // Default to ascending (oldest first)
        if ($sortOrder === 'desc') {
            // For descending order, we need to re-sort to put created last instead of first
            $history = $history->sortByDesc([
                function ($item) {
                    // Primary sort: timestamp, but ensure created actions are always last in desc order
                    $timestamp = $item->created_at->timestamp;
                    if ($item->action === 'created') {
                        $timestamp += 3600; // Add 1 hour to ensure created comes last
                    }

                    return $timestamp;
                },
                function ($item) {
                    // Secondary sort: action priority for same timestamp (reversed)
                    $order = ['cancelled' => 1, 'rebooked' => 2, 'created' => 3];

                    return $order[$item->action] ?? 4;
                },
            ]);
            $history = collect($history->values());
        }

        return view('admin.bookings.history', compact('booking', 'history'))->with([
            'sortOrder' => $sortOrder,
            'actualRebookCount' => $actualRebookCount,
        ]);
    }

    private function checkRebookingRestrictions(Booking $booking): array
    {
        $restrictions = [
            'blocked' => null,
            'warning' => null,
        ];

        // Check if already cancelled
        if ($booking->isCancelled()) {
            $restrictions['blocked'] = 'Cannot rebook a cancelled booking.';

            return $restrictions;
        }

        // Check if slot has already started
        if ($booking->slot->start_at <= now()) {
            $restrictions['blocked'] = 'Cannot rebook a slot that has already started.';

            return $restrictions;
        }

        // Get customer's custom behavior settings (or defaults)
        $maxRebooksPerBooking = \App\Models\CustomerBehaviorSetting::getCustomerSetting(
            $booking->customer_id,
            'max_rebooks_per_booking',
            3
        );
        $maxLastMinuteRebooks30Days = \App\Models\CustomerBehaviorSetting::getCustomerSetting(
            $booking->customer_id,
            'max_last_minute_rebooks_30days',
            5
        );
        $maxTotalRebooks30Days = \App\Models\CustomerBehaviorSetting::getCustomerSetting(
            $booking->customer_id,
            'max_total_rebooks_30days',
            10
        );

        // Check rebook count limits (use custom limit)
        if ($booking->rebook_count >= $maxRebooksPerBooking) {
            $restrictions['blocked'] = "This booking has already been rebooked {$maxRebooksPerBooking} times. Maximum limit reached.";

            return $restrictions;
        }

        // Get customer behavior stats
        $customerStats = $this->getCustomerBehaviorStats($booking->customer_id);

        // Check for excessive last-minute rebooking (use custom limit)
        if ($customerStats['last_minute_rebooks_30days'] >= $maxLastMinuteRebooks30Days) {
            $restrictions['blocked'] = "Customer has exceeded the limit of {$maxLastMinuteRebooks30Days} last-minute rebooks in 30 days.";

            return $restrictions;
        }

        // Warning for frequent rebooking (use custom limit)
        if ($customerStats['total_rebooks_30days'] >= $maxTotalRebooks30Days) {
            $restrictions['warning'] = "Warning: Customer has {$customerStats['total_rebooks_30days']} rebooks in the last 30 days (limit: {$maxTotalRebooks30Days}).";
        }

        // Warning for last-minute rebooking
        if ($booking->isLastMinuteAction()) {
            $restrictions['warning'] = ($restrictions['warning'] ?? '').' This is a last-minute rebook (<24hrs).';
        }

        return $restrictions;
    }

    private function getCustomerBehaviorStats($customerId): array
    {
        $stats = BookingHistory::where('customer_id', $customerId)
            ->recentActivity(30)
            ->selectRaw('
                COUNT(*) as total_actions,
                SUM(CASE WHEN action = "rebooked" THEN 1 ELSE 0 END) as total_rebooks_30days,
                SUM(CASE WHEN action = "cancelled" THEN 1 ELSE 0 END) as total_cancellations_30days,
                SUM(CASE WHEN action IN ("rebooked", "cancelled") AND is_last_minute = 1 THEN 1 ELSE 0 END) as last_minute_rebooks_30days,
                AVG(CASE WHEN action IN ("rebooked", "cancelled") THEN hours_before_slot END) as avg_hours_notice
            ')
            ->first();

        return [
            'total_rebooks_30days' => $stats->total_rebooks_30days ?? 0,
            'total_cancellations_30days' => $stats->total_cancellations_30days ?? 0,
            'last_minute_rebooks_30days' => $stats->last_minute_rebooks_30days ?? 0,
            'avg_hours_notice' => round($stats->avg_hours_notice ?? 0, 1),
        ];
    }

    private function getAllowedDepotIds(): array
    {
        $user = auth()->user();
        $allowedDepotIds = $user->depots()->pluck('depots.id')->toArray();

        if (empty($allowedDepotIds)) {
            return [0]; // No depot will have ID 0
        }

        return $allowedDepotIds;
    }
}
