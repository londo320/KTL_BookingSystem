<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Depot;
use App\Models\TippingBay;
use Illuminate\Http\Request;

class TippingBayController extends Controller
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
        
        // Show default depot initially, unless another depot is selected
        if ($selectedDepotId && in_array($selectedDepotId, $allowedDepotIds)) {
            $currentDepotId = $selectedDepotId;
        } elseif ($selectedDepotId === "") {
            // Explicitly selected "All Depots"
            $currentDepotId = null;
        } else {
            // Default to user's default depot
            $currentDepotId = $defaultDepotId;
        }

        $query = TippingBay::with(['depot'])
            ->whereIn('depot_id', $allowedDepotIds);

        // Filter by current depot if specified
        if ($currentDepotId) {
            $query->where('depot_id', $currentDepotId);
        }

        $bays = $query->orderBy('depot_id')
            ->orderBy('name')
            ->paginate(20);

        // Load current bookings for each bay (since currentBooking is a method, not a relationship)
        foreach ($bays as $bay) {
            $currentBooking = $bay->currentBooking();
            if ($currentBooking) {
                $currentBooking->load('customer');
                $bay->setRelation('currentBooking', $currentBooking);
            }
        }

        // Get all depots for filter dropdown
        $allDepots = \App\Models\Depot::whereIn('id', $allowedDepotIds)->get();

        return view('admin.tipping-bays.index', compact(
            'bays', 
            'allDepots', 
            'currentDepotId',
            'defaultDepotId'
        ));
    }

    public function create()
    {
        $depots = $this->getAllowedDepots();

        return view('admin.tipping-bays.create', compact('depots'));
    }

    public function store(Request $request)
    {
        $allowedDepotIds = $this->getAllowedDepotIds();
        $user = auth()->user();
        $defaultDepotId = $user->depot_id ?? $allowedDepotIds[0] ?? null;
        
        $validated = $request->validate([
            'depot_id' => 'required|exists:depots,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:tipping_bays,code,NULL,id,depot_id,'.$request->depot_id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'show_on_map' => 'boolean',
            'map_x' => 'nullable|numeric|min:0|max:100',
            'map_y' => 'nullable|numeric|min:0|max:100',
            'map_width' => 'nullable|integer|min:20|max:300',
            'map_height' => 'nullable|integer|min:15|max:200',
            'map_rotation' => 'nullable|numeric|min:0|max:360',
            'text_size' => 'nullable|in:xs,sm,md,lg',
            'text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'equipment' => 'nullable|array',
            'equipment.*' => 'nullable|string|max:255',
        ]);

        // Check depot access and default depot restrictions
        if (! in_array($request->depot_id, $allowedDepotIds)) {
            return back()->withErrors(['depot_id' => 'You do not have access to this depot.']);
        }
        
        // Check if user can create in this depot (admins can create in any accessible depot, others need it to be their default)
        if (!$user->hasRole('admin') && $request->depot_id !== $defaultDepotId) {
            return back()->withErrors(['depot_id' => 'You can only create tipping bays in your default depot.']);
        }

        $validated['equipment'] = array_filter($validated['equipment'] ?? []);

        TippingBay::create($validated);

        return redirect()
            ->route('admin.tipping-bays.index')
            ->with('success', 'Tipping bay created successfully.');
    }

    public function show(TippingBay $tippingBay)
    {
        $tippingBay->load([
            'depot',
            'bookings' => function ($query) {
                $query->latest()->limit(20);
            },
            'bookings.customer',
        ]);

        // Load current booking separately since it's now a method
        $currentBooking = $tippingBay->currentBooking();
        if ($currentBooking) {
            $currentBooking->load(['customer', 'movements']);
        }

        // Get user's default depot for action restrictions
        $allowedDepotIds = $this->getAllowedDepotIds();
        $user = auth()->user();
        $defaultDepotId = $user->depot_id ?? $allowedDepotIds[0] ?? null;

        return view('admin.tipping-bays.show', compact(
            'tippingBay', 
            'currentBooking',
            'defaultDepotId'
        ));
    }

    public function edit(TippingBay $tippingBay)
    {
        $allowedDepotIds = $this->getAllowedDepotIds();
        $user = auth()->user();
        $defaultDepotId = $user->depot_id ?? $allowedDepotIds[0] ?? null;

        // Check depot access
        if (! in_array($tippingBay->depot_id, $allowedDepotIds)) {
            abort(403, 'You do not have access to this depot.');
        }

        // Check if user can edit this depot (admins can edit any accessible depot, others need it to be their default)
        if (!$user->hasRole('admin') && $tippingBay->depot_id !== $defaultDepotId) {
            abort(403, 'You can only edit tipping bays in your default depot. Please change your default depot in your profile if needed.');
        }

        $depots = $this->getAllowedDepots();
        $equipmentTypes = \App\Models\EquipmentType::active()->ordered()->get();

        return view('admin.tipping-bays.edit', compact('tippingBay', 'depots', 'equipmentTypes'));
    }

    public function update(Request $request, TippingBay $tippingBay)
    {
        $allowedDepotIds = $this->getAllowedDepotIds();
        $user = auth()->user();
        $defaultDepotId = $user->depot_id ?? $allowedDepotIds[0] ?? null;
        
        $validated = $request->validate([
            'depot_id' => 'required|exists:depots,id',
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:tipping_bays,code,'.$tippingBay->id.',id,depot_id,'.$request->depot_id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'show_on_map' => 'boolean',
            'map_x' => 'nullable|numeric|min:0|max:100',
            'map_y' => 'nullable|numeric|min:0|max:100',
            'map_width' => 'nullable|integer|min:20|max:300',
            'map_height' => 'nullable|integer|min:15|max:200',
            'map_rotation' => 'nullable|numeric|min:0|max:360',
            'text_size' => 'nullable|in:xs,sm,md,lg',
            'text_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'equipment' => 'nullable|array',
            'equipment.*' => 'nullable|string|max:255',
        ]);

        // Check depot access and default depot restrictions
        if (! in_array($request->depot_id, $allowedDepotIds) || ! in_array($tippingBay->depot_id, $allowedDepotIds)) {
            return back()->withErrors(['depot_id' => 'You do not have access to this depot.']);
        }
        
        // Check if user can edit this depot (admins can edit any accessible depot, others need it to be their default)
        if (!$user->hasRole('admin') && ($tippingBay->depot_id !== $defaultDepotId || $request->depot_id !== $defaultDepotId)) {
            return back()->withErrors(['depot_id' => 'You can only edit tipping bays in your default depot.']);
        }

        $validated['equipment'] = array_filter($validated['equipment'] ?? []);

        $tippingBay->update($validated);

        return redirect()
            ->route('admin.tipping-bays.show', $tippingBay)
            ->with('success', 'Tipping bay updated successfully.');
    }

    public function destroy(TippingBay $tippingBay)
    {
        $allowedDepotIds = $this->getAllowedDepotIds();
        $user = auth()->user();
        $defaultDepotId = $user->depot_id ?? $allowedDepotIds[0] ?? null;
        
        // Check depot access
        if (! in_array($tippingBay->depot_id, $allowedDepotIds)) {
            abort(403, 'You do not have access to this depot.');
        }
        
        // Check if user can delete this depot's bay (must be their default depot)
        if ($tippingBay->depot_id !== $defaultDepotId) {
            abort(403, 'You can only delete tipping bays in your default depot.');
        }

        // Check if bay is occupied
        if ($tippingBay->is_occupied || $tippingBay->currentBooking()) {
            return back()->withErrors(['delete' => 'Cannot delete bay that is currently occupied.']);
        }

        $tippingBay->delete();

        return redirect()
            ->route('admin.tipping-bays.index')
            ->with('success', 'Tipping bay deleted successfully.');
    }

    public function markAvailable(TippingBay $tippingBay)
    {
        // Check depot access for depot-admin
        if (auth()->user()->hasRole('depot-admin')) {
            $allowedDepotIds = auth()->user()->depots()->pluck('depots.id')->toArray();
            if (! in_array($tippingBay->depot_id, $allowedDepotIds)) {
                abort(403, 'You do not have access to this depot.');
            }
        }

        $tippingBay->markAvailable();

        return back()->with('success', 'Bay marked as available.');
    }

    private function getAllowedDepots()
    {
        if (auth()->user()->hasRole('admin|site-admin')) {
            return Depot::orderBy('name')->get();
        }

        // For depot-admin, only show their assigned depots
        return auth()->user()->depots()->orderBy('name')->get();
    }

    private function getAllowedDepotIds()
    {
        $assignedDepotIds = auth()->user()->depots()->pluck('depots.id')->toArray();

        if (empty($assignedDepotIds) && auth()->user()->hasRole('admin|site-admin')) {
            return \App\Models\Depot::pluck('id')->toArray();
        }

        return $assignedDepotIds;
    }
}
