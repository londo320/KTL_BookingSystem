<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingType;
use App\Models\BookingTypeDurationRule;
use App\Models\Customer;
use App\Models\Depot;
use Illuminate\Http\Request;

class BookingTypeDurationRuleController extends Controller
{
    public function index()
    {
        $rules = BookingTypeDurationRule::with(['bookingType', 'depot', 'customer'])
            ->orderBy('booking_type_id')
            ->orderByDesc('priority')
            ->orderBy('min_cases')
            ->get();

        return view('admin.duration_rules.index', compact('rules'));
    }

    public function create()
    {
        $bookingTypes = BookingType::orderBy('name')->get();
        $depots = Depot::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();

        return view('admin.duration_rules.create', compact('bookingTypes', 'depots', 'customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_type_id' => 'required|exists:booking_types,id',
            'depot_id' => 'nullable|exists:depots,id',
            'customer_id' => 'nullable|exists:customers,id',
            'min_cases' => 'required|integer|min:0',
            'max_cases' => 'nullable|integer|min:0|gt:min_cases',
            'duration_minutes' => 'required|integer|min:30|max:1440',
            'priority' => 'nullable|integer|min:0|max:100',
        ]);

        $validated['priority'] = $validated['priority'] ?? 0;

        BookingTypeDurationRule::create($validated);

        return redirect()
            ->route('app.duration-rules.index')
            ->with('success', 'Duration rule created successfully!');
    }

    public function edit(BookingTypeDurationRule $durationRule)
    {
        $bookingTypes = BookingType::orderBy('name')->get();
        $depots = Depot::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();

        return view('admin.duration_rules.edit', compact('durationRule', 'bookingTypes', 'depots', 'customers'));
    }

    public function update(Request $request, BookingTypeDurationRule $durationRule)
    {
        $validated = $request->validate([
            'booking_type_id' => 'required|exists:booking_types,id',
            'depot_id' => 'nullable|exists:depots,id',
            'customer_id' => 'nullable|exists:customers,id',
            'min_cases' => 'required|integer|min:0',
            'max_cases' => 'nullable|integer|min:0|gt:min_cases',
            'duration_minutes' => 'required|integer|min:30|max:1440',
            'priority' => 'nullable|integer|min:0|max:100',
        ]);

        $validated['priority'] = $validated['priority'] ?? 0;

        $durationRule->update($validated);

        return redirect()
            ->route('app.duration-rules.index')
            ->with('success', 'Duration rule updated successfully!');
    }

    public function destroy(BookingTypeDurationRule $durationRule)
    {
        $durationRule->delete();

        return redirect()
            ->route('app.duration-rules.index')
            ->with('success', 'Duration rule deleted successfully!');
    }
}
