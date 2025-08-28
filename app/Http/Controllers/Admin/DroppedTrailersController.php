<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Depot;
use Illuminate\Http\Request;

class DroppedTrailersController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin|depot-admin|site-admin']);
    }

    /**
     * Display list of dropped trailers currently on site
     */
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

        $query = Booking::with(['customer', 'slot.depot', 'movements.tippingLocation', 'movements.tippingBay'])
            ->whereHas('slot', fn ($q) => $q->whereIn('depot_id', $allowedDepotIds))
            ->whereNotNull('arrived_at')
            ->whereNotNull('departed_at')        // Unit has departed
            ->whereDoesntHave('movements', function ($q) {
                $q->whereNotNull('trailer_collected_at'); // But trailer not collected yet
            });

        // Filter by specific depot if selected, otherwise show all
        if ($currentDepotId) {
            $query->whereHas('slot', fn ($q) => $q->where('depot_id', $currentDepotId));
        }

        // Filter by movement status
        if ($request->status) {
            $query->whereHas('movements', fn ($q) => $q->where('current_status', $request->status));
        }

        $droppedTrailers = $query->orderBy('trailer_dropped_at', 'desc')
            ->paginate(20)
            ->appends($request->only(['depot_id', 'status']));

        // Get all depots for filter dropdown
        $allDepots = \App\Models\Depot::whereIn('id', $allowedDepotIds)->get();

        $statusOptions = [
            'trailer_dropped' => 'Trailer Dropped',
            'at_bay' => 'In Bay',
            'unloading' => 'Unloading in Progress',
            'empty' => 'Unloading Complete',
        ];

        return view('admin.dropped-trailers.index', compact(
            'droppedTrailers', 
            'allDepots', 
            'statusOptions', 
            'currentDepotId',
            'defaultDepotId'
        ));
    }

    /**
     * Show trailer reconnection form
     */
    public function reconnect(Request $request, Booking $booking)
    {
        $allowedDepotIds = $this->getAllowedDepotIds();

        // Check if user has access to this booking's depot
        if (! in_array($booking->slot->depot_id, $allowedDepotIds)) {
            abort(403, 'You do not have access to this booking.');
        }

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'vehicle_registration' => 'required|string|max:50',
                'departure_notes' => 'nullable|string|max:500',
            ]);

            // Store departure vehicle details in movement custom fields
            $movement = $booking->getOrCreateMovement();
            $movement->update([
                'custom_fields' => array_merge(
                    $movement->custom_fields ?? [],
                    [
                        'departure_vehicle_registration' => $validated['vehicle_registration'],
                    ]
                ),
            ]);

            // Use the existing trailer depart method from the model
            if ($booking->trailerDepart($validated['departure_notes'])) {
                return redirect()
                    ->route('admin.dropped-trailers.index')
                    ->with('success', "Trailer reconnected to vehicle {$validated['vehicle_registration']} and departed successfully.");
            } else {
                return back()->withErrors(['reconnect' => 'Unable to complete trailer departure. Check booking status.']);
            }
        }

        return view('admin.dropped-trailers.reconnect', compact('booking'));
    }

    /**
     * Get depot IDs that the current user can access based on their role
     */
    private function getAllowedDepotIds()
    {
        $user = auth()->user();

        // All roles (including admin) respect depot pivot assignments
        $allowedDepotIds = $user->depots()->pluck('depots.id')->toArray();

        // If no depots assigned, they can't access any (return impossible condition)
        if (empty($allowedDepotIds)) {
            return [0]; // No depot will have ID 0
        }

        return $allowedDepotIds;
    }

    /**
     * Get depots that the current user can access based on their role
     */
    private function getAllowedDepots()
    {
        $user = auth()->user();

        // All roles (including admin) respect depot pivot assignments
        return $user->depots()->orderBy('name')->get();
    }
}
