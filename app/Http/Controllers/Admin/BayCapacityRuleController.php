<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BayCapacityRule;
use App\Models\BookingType;
use App\Models\Depot;
use App\Models\TippingBay;
use Illuminate\Http\Request;

class BayCapacityRuleController extends Controller
{
    public function index()
    {
        $rules = BayCapacityRule::with(['depot', 'bookingType'])
            ->orderBy('depot_id')
            ->orderBy('booking_type_id')
            ->orderBy('time_start')
            ->get();

        return view('admin.bay_capacity_rules.index', compact('rules'));
    }

    public function create()
    {
        $depots = Depot::orderBy('name')->get();
        $bookingTypes = BookingType::orderBy('name')->get();

        return view('admin.bay_capacity_rules.create', compact('depots', 'bookingTypes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'depot_id' => 'required|exists:depots,id',
            'booking_type_id' => 'nullable|exists:booking_types,id',
            'time_start' => 'required|date_format:H:i',
            'time_end' => 'required|date_format:H:i|after:time_start',
            'days_of_week' => 'nullable|array',
            'days_of_week.*' => 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'max_concurrent_bookings' => 'required|integer|min:1|max:100',
            'applicable_bay_ids' => 'nullable|array',
            'applicable_bay_ids.*' => 'exists:tipping_bays,id',
            'capacity_weight' => 'nullable|numeric|min:0.1|max:10',
            'is_active' => 'boolean',
        ]);

        $validated['capacity_weight'] = $validated['capacity_weight'] ?? 1.0;
        $validated['is_active'] = $validated['is_active'] ?? true;

        BayCapacityRule::create($validated);

        return redirect()
            ->route('app.bay-capacity-rules.index')
            ->with('success', 'Bay capacity rule created successfully!');
    }

    public function edit(BayCapacityRule $bayCapacityRule)
    {
        $depots = Depot::orderBy('name')->get();
        $bookingTypes = BookingType::orderBy('name')->get();
        $bays = TippingBay::where('depot_id', $bayCapacityRule->depot_id)
            ->orderBy('name')
            ->get();

        return view('admin.bay_capacity_rules.edit', compact('bayCapacityRule', 'depots', 'bookingTypes', 'bays'));
    }

    public function update(Request $request, BayCapacityRule $bayCapacityRule)
    {
        $validated = $request->validate([
            'depot_id' => 'required|exists:depots,id',
            'booking_type_id' => 'nullable|exists:booking_types,id',
            'time_start' => 'required|date_format:H:i',
            'time_end' => 'required|date_format:H:i|after:time_start',
            'days_of_week' => 'nullable|array',
            'days_of_week.*' => 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'max_concurrent_bookings' => 'required|integer|min:1|max:100',
            'applicable_bay_ids' => 'nullable|array',
            'applicable_bay_ids.*' => 'exists:tipping_bays,id',
            'capacity_weight' => 'nullable|numeric|min:0.1|max:10',
            'is_active' => 'boolean',
        ]);

        $validated['capacity_weight'] = $validated['capacity_weight'] ?? 1.0;

        $bayCapacityRule->update($validated);

        return redirect()
            ->route('app.bay-capacity-rules.index')
            ->with('success', 'Bay capacity rule updated successfully!');
    }

    public function destroy(BayCapacityRule $bayCapacityRule)
    {
        $bayCapacityRule->delete();

        return redirect()
            ->route('app.bay-capacity-rules.index')
            ->with('success', 'Bay capacity rule deleted successfully!');
    }

    /**
     * AJAX endpoint to get bays for a depot
     */
    public function getBaysForDepot(Request $request)
    {
        $depotId = $request->input('depot_id');
        $bays = TippingBay::where('depot_id', $depotId)
            ->orderBy('name')
            ->get(['id', 'name', 'code']);

        return response()->json($bays);
    }
}
