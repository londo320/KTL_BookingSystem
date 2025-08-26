<?php

namespace App\Modules\Outbound\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Outbound\Models\PhysicalLoadRegistration;
use App\Modules\Outbound\Models\WmsStagingOrder;
use App\Modules\Outbound\Services\OrderMatchingService;
use App\Models\Depot;
use Illuminate\Http\Request;

class PhysicalLoadController extends Controller
{
    /**
     * Show arrival dashboard
     */
    public function dashboard()
    {
        $todayArrivals = PhysicalLoadRegistration::today()
            ->with(['arrivalDepot', 'registeredBy'])
            ->orderBy('arrival_time', 'desc')
            ->get();

        $awaitingMatching = PhysicalLoadRegistration::awaiting()
            ->with(['arrivalDepot'])
            ->get();

        $statistics = [
            'today_arrivals' => PhysicalLoadRegistration::today()->count(),
            'awaiting_matching' => $awaitingMatching->count(),
            'pending_wms_orders' => WmsStagingOrder::pending()->count(),
            'matched_today' => PhysicalLoadRegistration::matched()->today()->count(),
        ];

        return view('outbound::admin.physical-loads.dashboard', compact(
            'todayArrivals', 'awaitingMatching', 'statistics'
        ));
    }

    /**
     * Show driver arrival form
     */
    public function create()
    {
        $depots = Depot::orderBy('name')->get();
        
        return view('outbound::admin.physical-loads.create', compact('depots'));
    }

    /**
     * Register driver arrival
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'load_reference' => 'required|string|max:50|unique:physical_load_registrations,load_reference',
            'driver_paperwork_ref' => 'nullable|string|max:100',
            'vehicle_registration' => 'required|string|max:20',
            'trailer_registration' => 'nullable|string|max:20',
            'carrier_company' => 'required|string|max:200',
            'driver_name' => 'required|string|max:100',
            'driver_phone' => 'nullable|string|max:20',
            'driver_license' => 'nullable|string|max:50',
            'arrival_depot_id' => 'required|exists:depots,id',
            'arrival_notes' => 'nullable|string',
            'expected_orders' => 'nullable|integer|min:0|max:1000',
        ]);

        $validated['arrival_time'] = now();
        $validated['registered_by'] = auth()->id();
        $validated['expected_orders'] = $validated['expected_orders'] ?? 0;

        $physicalLoad = PhysicalLoadRegistration::create($validated);

        // Trigger immediate matching attempt
        $this->attemptOrderMatching($physicalLoad);

        return redirect()
            ->route('outbound.physical-loads.show', $physicalLoad)
            ->with('success', "Driver arrival registered successfully. Load reference: {$physicalLoad->load_reference}");
    }

    /**
     * Show physical load details
     */
    public function show(PhysicalLoadRegistration $physicalLoad)
    {
        $physicalLoad->load([
            'arrivalDepot', 
            'registeredBy', 
            'outboundLoad.orders.customer',
            'stagingOrders'
        ]);

        $stagingOrders = $physicalLoad->stagingOrders()
            ->orderBy('processing_status')
            ->orderBy('uploaded_at')
            ->get();

        $matchingProgress = $physicalLoad->matching_progress;

        return view('outbound::admin.physical-loads.show', compact(
            'physicalLoad', 'stagingOrders', 'matchingProgress'
        ));
    }

    /**
     * Manually trigger order matching for a load
     */
    public function triggerMatching(PhysicalLoadRegistration $physicalLoad)
    {
        $results = $this->attemptOrderMatching($physicalLoad);

        $message = "Matching attempted. Matched: {$results['matched']}, Failed: {$results['failed']}";
        
        return back()->with('success', $message);
    }

    /**
     * Update load status
     */
    public function updateStatus(Request $request, PhysicalLoadRegistration $physicalLoad)
    {
        $validated = $request->validate([
            'status' => 'required|in:arrived,orders_matched,ready_for_collection,collecting,departed,cancelled',
            'notes' => 'nullable|string'
        ]);

        $physicalLoad->update([
            'status' => $validated['status']
        ]);

        if ($validated['notes']) {
            $physicalLoad->update([
                'arrival_notes' => $physicalLoad->arrival_notes . "\n\n" . now()->format('Y-m-d H:i') . ": " . $validated['notes']
            ]);
        }

        return back()->with('success', 'Load status updated successfully');
    }

    /**
     * Process all pending WMS orders
     */
    public function processAllPending()
    {
        $matchingService = new OrderMatchingService();
        $results = $matchingService->processPendingOrders();

        $message = "Processed {$results['processed']} orders. Matched: {$results['matched']}, Failed: {$results['failed']}";
        
        if (!empty($results['errors'])) {
            $message .= ". Errors: " . count($results['errors']);
        }

        return back()->with('success', $message);
    }

    /**
     * Attempt order matching for specific physical load
     */
    private function attemptOrderMatching(PhysicalLoadRegistration $physicalLoad): array
    {
        $matchingService = new OrderMatchingService();
        
        // Find staging orders for this load
        $stagingOrders = WmsStagingOrder::pending()
            ->forLoad($physicalLoad->load_reference)
            ->get();

        $results = ['matched' => 0, 'failed' => 0];

        foreach ($stagingOrders as $stagingOrder) {
            if ($matchingService->matchStagingOrder($stagingOrder)) {
                $results['matched']++;
            } else {
                $results['failed']++;
            }
        }

        // Update matching count
        $physicalLoad->updateMatchedCount();

        return $results;
    }
}