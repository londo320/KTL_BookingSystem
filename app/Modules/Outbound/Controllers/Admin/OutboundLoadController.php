<?php

namespace App\Modules\Outbound\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Outbound\Models\OutboundLoad;
use App\Modules\Outbound\Models\OutboundOrder;
use App\Modules\Outbound\Models\CustomerAddress;
use App\Models\Customer;
use App\Models\Depot;
use App\Models\Vehicle;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OutboundLoadController extends Controller
{
    public function index(Request $request)
    {
        $query = OutboundLoad::with(['orders.customer', 'plannedVehicle', 'assignedDriver']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by load reference
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('load_reference', 'like', "%{$search}%")
                  ->orWhere('load_name', 'like', "%{$search}%")
                  ->orWhereHas('orders', function ($orderQuery) use ($search) {
                      $orderQuery->where('order_reference', 'like', "%{$search}%")
                                 ->orWhere('po_number', 'like', "%{$search}%");
                  });
            });
        }

        $loads = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get summary statistics
        $stats = [
            'total_loads' => OutboundLoad::count(),
            'active_loads' => OutboundLoad::active()->count(),
            'planning_loads' => OutboundLoad::byStatus('planning')->count(),
            'in_transit_loads' => OutboundLoad::whereIn('status', ['collecting', 'in_transit', 'delivering'])->count(),
        ];

        return view('outbound::admin.loads.index', compact('loads', 'stats'));
    }

    public function show(OutboundLoad $load)
    {
        $load->load([
            'orders.customer',
            'orders.customerAddress',
            'orders.collectionDepot',
            'orders.palletDetails.palletType',
            'collections.depot',
            'plannedVehicle',
            'assignedDriver'
        ]);

        return view('outbound::admin.loads.show', compact('load'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get();
        $depots = Depot::orderBy('name')->get();
        $vehicles = Vehicle::active()->orderBy('registration')->get();
        // Get warehouse staff who can be assigned to loads
        $drivers = User::role(['warehouse', 'depot-admin'])->orderBy('name')->get();

        return view('outbound::admin.loads.create', compact('customers', 'depots', 'vehicles', 'drivers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'load_name' => 'nullable|string|max:100',
            'planned_vehicle_id' => 'nullable|exists:vehicles,id',
            'assigned_driver_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
            'orders' => 'required|array|min:1',
            'orders.*.customer_id' => 'required|exists:customers,id',
            'orders.*.customer_address_id' => 'required|exists:customer_addresses,id',
            'orders.*.collection_depot_id' => 'required|exists:depots,id',
            'orders.*.order_reference' => 'required|string|max:100',
            'orders.*.po_number' => 'nullable|string|max:100',
            'orders.*.expected_pallets' => 'required|integer|min:0',
            'orders.*.expected_cases' => 'required|integer|min:0',
            'orders.*.expected_units' => 'required|integer|min:0',
            'orders.*.expected_weight_kg' => 'nullable|numeric|min:0',
            'orders.*.delivery_notes' => 'nullable|string',
            'orders.*.planned_delivery_date' => 'nullable|date',
            'orders.*.delivery_priority' => 'nullable|in:standard,priority,urgent',
        ]);

        DB::transaction(function () use ($validated) {
            // Create the load
            $load = OutboundLoad::create([
                'load_name' => $validated['load_name'],
                'planned_vehicle_id' => $validated['planned_vehicle_id'],
                'assigned_driver_id' => $validated['assigned_driver_id'],
                'notes' => $validated['notes'],
                'created_by' => auth()->id(),
                'created_from' => 'manual',
            ]);

            // Create orders
            foreach ($validated['orders'] as $orderData) {
                OutboundOrder::create([
                    'outbound_load_id' => $load->id,
                    'customer_id' => $orderData['customer_id'],
                    'customer_address_id' => $orderData['customer_address_id'],
                    'collection_depot_id' => $orderData['collection_depot_id'],
                    'order_reference' => $orderData['order_reference'],
                    'po_number' => $orderData['po_number'] ?? null,
                    'expected_pallets' => $orderData['expected_pallets'],
                    'expected_cases' => $orderData['expected_cases'],
                    'expected_units' => $orderData['expected_units'],
                    'expected_weight_kg' => $orderData['expected_weight_kg'] ?? null,
                    'delivery_notes' => $orderData['delivery_notes'] ?? null,
                    'planned_delivery_date' => $orderData['planned_delivery_date'] ?? null,
                    'delivery_priority' => $orderData['delivery_priority'] ?? 'standard',
                ]);
            }

            // Create collection records for each depot
            $this->createCollectionRecords($load);

            return $load;
        });

        return redirect()->route('outbound.admin.loads.index')
            ->with('success', 'Outbound load created successfully');
    }

    public function edit(OutboundLoad $load)
    {
        if (!in_array($load->status, ['planning', 'ready_for_collection'])) {
            return back()->with('error', 'Load cannot be edited in current status');
        }

        $load->load(['orders.customer', 'orders.customerAddress', 'orders.collectionDepot']);

        $customers = Customer::orderBy('name')->get();
        $depots = Depot::orderBy('name')->get();
        $vehicles = Vehicle::active()->orderBy('registration')->get();
        // Get warehouse staff who can be assigned to loads
        $drivers = User::role(['warehouse', 'depot-admin'])->orderBy('name')->get();

        return view('outbound::admin.loads.edit', compact('load', 'customers', 'depots', 'vehicles', 'drivers'));
    }

    public function update(Request $request, OutboundLoad $load)
    {
        if (!in_array($load->status, ['planning', 'ready_for_collection'])) {
            return back()->with('error', 'Load cannot be updated in current status');
        }

        $validated = $request->validate([
            'load_name' => 'nullable|string|max:100',
            'planned_vehicle_id' => 'nullable|exists:vehicles,id',
            'assigned_driver_id' => 'nullable|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $load->update($validated);

        return back()->with('success', 'Load updated successfully');
    }

    public function destroy(OutboundLoad $load)
    {
        if ($load->status !== 'planning') {
            return back()->with('error', 'Only loads in planning status can be deleted');
        }

        $load->delete();

        return redirect()->route('outbound.admin.loads.index')
            ->with('success', 'Load deleted successfully');
    }

    public function timingAnalysis(OutboundLoad $load)
    {
        $orders = $load->orders()->with(['customerAddress', 'customer'])->get();
        
        $timingAnalysis = [];
        foreach ($orders as $order) {
            // Calculate latest arrival time
            $customerAddress = $order->customerAddress;
            $latestDelivery = $customerAddress->latest_delivery_time ?? '17:00';
            $processingTime = $customerAddress->unloading_duration_minutes ?? 30;
            $bufferTime = $customerAddress->delivery_buffer_minutes ?? 15;
            
            $latestArrival = now()->setTimeFromTimeString($latestDelivery)
                ->subMinutes($processingTime + $bufferTime);

            $timingAnalysis[] = [
                'order' => $order,
                'customer_name' => $order->customer->name,
                'delivery_address' => $customerAddress->company_name ?? $customerAddress->address_line_1,
                'latest_delivery_time' => $latestDelivery,
                'latest_arrival_time' => $latestArrival->format('H:i'),
                'processing_time_minutes' => $processingTime,
                'buffer_time_minutes' => $bufferTime,
                'delivery_constraints' => $customerAddress->delivery_constraints,
            ];
        }

        return view('outbound::admin.loads.timing-analysis', compact('load', 'timingAnalysis'));
    }

    protected function createCollectionRecords(OutboundLoad $load)
    {
        $depotIds = $load->orders()->distinct('collection_depot_id')->pluck('collection_depot_id');
        $sequence = 1;

        foreach ($depotIds as $depotId) {
            $collection = $load->collections()->create([
                'depot_id' => $depotId,
                'collection_sequence' => $sequence++,
                'planned_collection_time' => now()->addHours(24), // Default to tomorrow
                'estimated_duration_minutes' => 30,
                'status' => 'pending',
            ]);

            // Update depot totals
            $collection->updateDepotTotals();
        }
    }

    // AJAX endpoints for dynamic form updates
    public function getCustomerAddresses(Customer $customer)
    {
        $addresses = $customer->customerAddresses()->active()->get();
        return response()->json($addresses);
    }

    public function getAddressDetails(CustomerAddress $address)
    {
        return response()->json([
            'address' => $address,
            'delivery_constraints' => $address->delivery_constraints,
            'formatted_hours' => $address->formatted_delivery_hours,
        ]);
    }
}