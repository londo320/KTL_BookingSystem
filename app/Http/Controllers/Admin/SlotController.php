<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\DeleteEmptySlotsJob;
use App\Models\BookingType;
use App\Models\Depot;
use App\Models\Slot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SlotController extends Controller
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

        // Show all allowed depots for viewing, but track default for actions
        if ($selectedDepotId && in_array($selectedDepotId, $allowedDepotIds)) {
            $currentDepotId = $selectedDepotId;
        } else {
            $currentDepotId = null; // Show all depots
        }

        // Check if user wants grouped view
        $groupedView = $request->has('grouped');

        $query = Slot::with(['depot', 'tippingBay'])
            ->withCount('occupyingBookings')
            ->whereIn('depot_id', $allowedDepotIds);

        if (! $request->has('show_past')) {
            $query->where('end_at', '>=', now());
        }

        if ($currentDepotId) {
            $query->where('depot_id', $currentDepotId);
        }

        if ($request->filled('date')) {
            $query->whereDate('start_at', $request->date);
        }

        $slots = $query->orderBy('start_at')->get();

        // Group slots if requested
        $groupedSlots = null;
        if ($groupedView) {
            $groupedSlots = $this->groupSlotsByDateTime($slots);
        }

        // Get all depots for filter dropdown
        $allDepots = \App\Models\Depot::whereIn('id', $allowedDepotIds)->get();

        return view('admin.slots.index', compact(
            'slots',
            'groupedSlots',
            'groupedView',
            'allDepots',
            'currentDepotId',
            'defaultDepotId'
        ));
    }

    /**
     * Group slots by date/time to show capacity overview
     */
    protected function groupSlotsByDateTime($slots)
    {
        $grouped = [];

        foreach ($slots as $slot) {
            $date = $slot->start_at->format('Y-m-d');
            $time = $slot->start_at->format('H:i');
            $depotId = $slot->depot_id;
            $key = "{$depotId}_{$date}_{$time}";

            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'depot_id' => $depotId,
                    'depot_name' => $slot->depot->name,
                    'date' => $date,
                    'time' => $time,
                    'start_at' => $slot->start_at,
                    'end_at' => $slot->end_at,
                    'total_capacity' => 0,
                    'total_used' => 0,
                    'bays' => [],
                    'slot_ids' => [],
                ];
            }

            $grouped[$key]['total_capacity'] += $slot->capacity;
            $grouped[$key]['total_used'] += $slot->occupying_bookings_count;
            $grouped[$key]['bays'][] = [
                'id' => $slot->tipping_bay_id,
                'name' => $slot->tippingBay?->name ?? 'No Bay',
                'capacity' => $slot->capacity,
                'used' => $slot->occupying_bookings_count,
            ];
            $grouped[$key]['slot_ids'][] = $slot->id;
        }

        return collect($grouped)->sortBy('start_at')->values();
    }

    public function create()
    {
        $depots = Depot::all();
        $types = BookingType::all();

        return view('admin.slots.create', compact('depots', 'types'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'depot_id' => 'required|exists:depots,id',
            'booking_type_id' => 'required|exists:booking_types,id',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'is_blocked' => 'sometimes|boolean',
        ]);

        Slot::create($data);

        return back()->with('success', 'Slot created successfully.');
    }

    public function edit(Slot $slot)
    {
        $depots = Depot::all();
        $types = BookingType::all();

        return view('admin.slots.edit', compact('slot', 'depots', 'types'));
    }

    public function update(Request $request, Slot $slot)
    {
        $data = $request->validate([
            'depot_id' => 'required|exists:depots,id',
            'booking_type_id' => 'required|exists:booking_types,id',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'is_blocked' => 'sometimes|boolean',
        ]);

        // Auto-lock feature: if blocking this slot, block all other slots at same depot/time
        if (isset($data['is_blocked']) && $data['is_blocked'] && !$slot->is_blocked) {
            $this->autoLockRelatedSlots($slot);
        }

        // Auto-unlock feature: if unblocking this slot, unblock all other slots at same depot/time
        if (isset($data['is_blocked']) && !$data['is_blocked'] && $slot->is_blocked) {
            $this->autoUnlockRelatedSlots($slot);
        }

        $slot->update($data);

        return redirect()->route('admin.slots.index')->with('success', 'Slot updated successfully.');
    }

    /**
     * Auto-lock all slots at the same depot and time when one bay is blocked
     */
    private function autoLockRelatedSlots(Slot $slot): void
    {
        Slot::where('depot_id', $slot->depot_id)
            ->where('start_at', $slot->start_at)
            ->where('id', '!=', $slot->id)
            ->update(['is_blocked' => true]);
    }

    /**
     * Auto-unlock all slots at the same depot and time when a bay is unblocked
     */
    private function autoUnlockRelatedSlots(Slot $slot): void
    {
        Slot::where('depot_id', $slot->depot_id)
            ->where('start_at', $slot->start_at)
            ->where('id', '!=', $slot->id)
            ->update(['is_blocked' => false]);
    }

    public function destroy(Slot $slot)
    {
        $slot->delete();

        return back()->with('success', 'Slot deleted.');
    }

    /**
     * Bulk delete slots by date (only empty slots without bookings)
     */
    public function bulkDeleteByDate(Request $request)
    {
        try {
            $request->validate([
                'date' => 'required|date',
            ]);

            $user = auth()->user();
            $defaultDepotId = $user->depot_id;

            if (!$defaultDepotId) {
                return back()->with('error', 'No default depot assigned.');
            }

            // Count how many slots will be deleted
            $count = Slot::where('depot_id', $defaultDepotId)
                ->whereDate('start_at', $request->date)
                ->whereDoesntHave('occupyingBookings')
                ->count();

            if ($count === 0) {
                return back()->with('info', "No empty slots found on " . $request->date);
            }

            // For small numbers, delete immediately
            if ($count <= 50) {
                $deletedCount = Slot::where('depot_id', $defaultDepotId)
                    ->whereDate('start_at', $request->date)
                    ->whereDoesntHave('occupyingBookings')
                    ->delete();

                return back()->with('success', "Deleted {$deletedCount} empty slot(s) on " . $request->date);
            }

            // For large numbers, use background job to avoid timeout
            DeleteEmptySlotsJob::dispatch($defaultDepotId, $request->date);

            Log::info("Queued deletion of {$count} empty slots for depot {$defaultDepotId} on {$request->date}");

            return back()->with('success', "Deletion of {$count} empty slots has been queued. This will complete in the background. Check the logs for progress.");
        } catch (\Exception $e) {
            Log::error("Error during bulk delete by date: " . $e->getMessage(), [
                'exception' => $e,
                'depot_id' => $defaultDepotId ?? null,
                'date' => $request->date ?? null,
            ]);

            return back()->with('error', 'Error deleting slots: ' . $e->getMessage());
        }
    }

    /**
     * Bulk delete selected slots (only empty slots without bookings)
     */
    public function bulkDeleteSelected(Request $request)
    {
        try {
            Log::info('Bulk delete selected started', [
                'slot_ids' => $request->slot_ids ?? [],
                'user_id' => auth()->id(),
            ]);

            $request->validate([
                'slot_ids' => 'required|array',
                'slot_ids.*' => 'exists:slots,id',
            ]);

            $user = auth()->user();
            $defaultDepotId = $user->depot_id;

            if (!$defaultDepotId) {
                Log::warning('No default depot assigned for user', ['user_id' => auth()->id()]);
                return back()->with('error', 'No default depot assigned.');
            }

            Log::info('Attempting to delete slots', [
                'depot_id' => $defaultDepotId,
                'slot_ids_count' => count($request->slot_ids),
                'slot_ids' => $request->slot_ids,
            ]);

            // Only delete slots that:
            // 1. Are in the selected IDs
            // 2. Belong to the user's default depot
            // 3. Have no bookings
            $deletedCount = Slot::whereIn('id', $request->slot_ids)
                ->where('depot_id', $defaultDepotId)
                ->whereDoesntHave('occupyingBookings')
                ->delete();

            $attemptedCount = count($request->slot_ids);
            $skippedCount = $attemptedCount - $deletedCount;

            Log::info('Bulk delete completed', [
                'deleted' => $deletedCount,
                'attempted' => $attemptedCount,
                'skipped' => $skippedCount,
            ]);

            $message = "Deleted {$deletedCount} slot(s)";
            if ($skippedCount > 0) {
                $message .= " ({$skippedCount} skipped - had bookings or wrong depot)";
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            Log::error('Error in bulk delete selected', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Error deleting slots: ' . $e->getMessage());
        }
    }

    /**
     * Get depot IDs that the current user can access based on their role
     */
    private function getAllowedDepotIds()
    {
        $assignedDepotIds = auth()->user()->depots()->pluck('depots.id')->toArray();

        if (empty($assignedDepotIds) && auth()->user()->hasRole('admin|site-admin')) {
            return \App\Models\Depot::pluck('id')->toArray();
        }

        return $assignedDepotIds;
    }
}
