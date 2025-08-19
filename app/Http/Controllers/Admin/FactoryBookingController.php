<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FactoryBooking;
use App\Models\Customer;
use App\Models\Carrier;
use App\Models\Depot;
use App\Models\TrailerType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FactoryBookingController extends Controller
{
    public function index(Request $request)
    {
        $query = FactoryBooking::with(['customer', 'carrier', 'depot', 'registeredBy'])
            ->orderBy('priority', 'desc')
            ->orderBy('arrived_at', 'asc');

        // Filter by depot if specified
        if ($request->depot_id) {
            $query->where('depot_id', $request->depot_id);
        }

        // Filter by status if specified
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('vehicle_registration', 'like', "%{$search}%")
                  ->orWhere('trailer_registration', 'like', "%{$search}%")
                  ->orWhere('driver_name', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($customerQuery) use ($search) {
                      $customerQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $factoryBookings = $query->paginate(20);
        $depots = Depot::orderBy('name')->get();

        return view('admin.factory-bookings.index', compact('factoryBookings', 'depots'));
    }

    public function show(FactoryBooking $factoryBooking)
    {
        $factoryBooking->load(['customer', 'carrier', 'depot', 'registeredBy', 'movements', 'poNumbers']);
        
        return view('admin.factory-bookings.show', compact('factoryBooking'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $carriers = Carrier::orderBy('name')->get();
        $trailerTypes = TrailerType::orderBy('name')->get();
        
        // Get user's accessible depots
        $user = Auth::user();
        $depots = $user->depots()->orderBy('name')->get();
        
        // If user has no depots assigned, get all depots (for super admin)
        if ($depots->isEmpty()) {
            $depots = Depot::orderBy('name')->get();
        }

        return view('admin.factory-bookings.create', compact('customers', 'carriers', 'trailerTypes', 'depots'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'depot_id' => 'required|exists:depots,id',
            'customer_id' => 'required|exists:customers,id',
            'carrier_id' => 'nullable|exists:carriers,id',
            'trailer_type_id' => 'nullable|exists:trailer_types,id',
            'vehicle_registration' => 'required|string|max:50',
            'trailer_registration' => 'nullable|string|max:50',
            'driver_name' => 'nullable|string|max:100',
            'driver_phone' => 'nullable|string|max:20',
            'delivery_notes' => 'nullable|string|max:1000',
            'priority' => 'nullable|integer|min:0|max:100',
            'gate_notes' => 'nullable|string|max:1000',
        ]);

        // Verify user has access to selected depot
        if (!$user->depots->contains($validated['depot_id']) && !$user->hasRole('super_admin')) {
            return back()->withErrors(['depot_id' => 'You do not have access to this depot.']);
        }

        $factoryBooking = DB::transaction(function () use ($validated, $user) {
            $factoryBooking = FactoryBooking::create([
                ...$validated,
                'registered_by' => $user->id,
                'priority' => $validated['priority'] ?? 50,
                'arrived_at' => now(),
                'status' => 'arrived',
            ]);

            // Create initial movement record
            $factoryBooking->getOrCreateMovement();
            
            return $factoryBooking;
        });

        return redirect()->route('admin.factory-bookings.index')
            ->with('success', 'Factory booking registered successfully! Reference: ' . $factoryBooking->reference);
    }

    public function edit(FactoryBooking $factoryBooking)
    {
        $customers = Customer::orderBy('name')->get();
        $carriers = Carrier::orderBy('name')->get();
        $trailerTypes = TrailerType::orderBy('name')->get();
        
        $user = Auth::user();
        $depots = $user->depots()->orderBy('name')->get();
        
        if ($depots->isEmpty()) {
            $depots = Depot::orderBy('name')->get();
        }

        return view('admin.factory-bookings.edit', compact('factoryBooking', 'customers', 'carriers', 'trailerTypes', 'depots'));
    }

    public function update(Request $request, FactoryBooking $factoryBooking)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'carrier_id' => 'nullable|exists:carriers,id',
            'trailer_type_id' => 'nullable|exists:trailer_types,id',
            'vehicle_registration' => 'required|string|max:50',
            'trailer_registration' => 'nullable|string|max:50',
            'driver_name' => 'nullable|string|max:100',
            'driver_phone' => 'nullable|string|max:20',
            'delivery_notes' => 'nullable|string|max:1000',
            'priority' => 'nullable|integer|min:0|max:100',
            'gate_notes' => 'nullable|string|max:1000',
        ]);

        $factoryBooking->update($validated);

        return redirect()->route('admin.factory-bookings.show', $factoryBooking)
            ->with('success', 'Factory booking updated successfully.');
    }

    public function destroy(FactoryBooking $factoryBooking)
    {
        // Only allow deletion if not started processing
        if ($factoryBooking->status !== 'arrived') {
            return back()->withErrors(['error' => 'Cannot delete factory booking that has started processing.']);
        }

        $factoryBooking->delete();

        return redirect()->route('admin.factory-bookings.index')
            ->with('success', 'Factory booking deleted successfully.');
    }

    public function startProcessing(FactoryBooking $factoryBooking)
    {
        if ($factoryBooking->status !== 'arrived') {
            return back()->withErrors(['error' => 'Factory booking is not in arrived status.']);
        }

        $factoryBooking->update([
            'status' => 'processing',
            'processing_started_at' => now(),
        ]);

        return back()->with('success', 'Factory booking processing started.');
    }

    public function complete(FactoryBooking $factoryBooking)
    {
        if (!in_array($factoryBooking->status, ['processing', 'arrived'])) {
            return back()->withErrors(['error' => 'Factory booking cannot be completed from current status.']);
        }

        $factoryBooking->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Factory booking marked as completed.');
    }

    public function markDeparted(FactoryBooking $factoryBooking)
    {
        if ($factoryBooking->status !== 'completed') {
            return back()->withErrors(['error' => 'Factory booking must be completed before departure.']);
        }

        $factoryBooking->update([
            'status' => 'departed',
            'departed_at' => now(),
        ]);

        return back()->with('success', 'Factory booking departure recorded.');
    }
}
