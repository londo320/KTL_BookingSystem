<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingType;
use App\Models\Depot;
use App\Models\Slot;
use Illuminate\Http\Request;

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

        $query = Slot::with('depot')
            ->withCount('bookings')
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

        $slots = $query->orderBy('start_at')->paginate(30);

        // Get all depots for filter dropdown
        $allDepots = \App\Models\Depot::whereIn('id', $allowedDepotIds)->get();

        return view('admin.slots.index', compact(
            'slots', 
            'allDepots', 
            'currentDepotId', 
            'defaultDepotId'
        ));
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

        $slot->update($data);

        return redirect()->route('admin.slots.index')->with('success', 'Slot updated successfully.');
    }

    public function destroy(Slot $slot)
    {
        $slot->delete();

        return back()->with('success', 'Slot deleted.');
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
