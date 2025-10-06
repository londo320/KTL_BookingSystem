<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingType;
use Illuminate\Http\Request;

class BookingTypeController extends Controller
{
    public function index()
    {
        $types = BookingType::all();

        return view('admin.booking_types.index', compact('types'));
    }

    public function create()
    {
        return view('admin.booking_types.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        BookingType::create($data);

        return redirect()->route('admin.booking-types.index')->with('success', 'Booking type created.');
    }

    public function edit(BookingType $bookingType)
    {
        $depots = \App\Models\Depot::all();
        $customers = \App\Models\Customer::all();

        // Load existing depot durations
        $depotDurations = $bookingType->depots()
            ->get()
            ->pluck('pivot.duration_minutes', 'id')
            ->toArray();

        // Load existing customer durations
        $customerDurations = $bookingType->customers()
            ->get()
            ->mapWithKeys(function ($customer) {
                $key = $customer->id . '_' . ($customer->pivot->depot_id ?? 'all');
                return [$key => [
                    'customer_id' => $customer->id,
                    'depot_id' => $customer->pivot->depot_id,
                    'duration' => $customer->pivot->duration_minutes
                ]];
            })
            ->toArray();

        return view('admin.booking_types.edit', compact('bookingType', 'depots', 'customers', 'depotDurations', 'customerDurations'));
    }

    public function update(Request $request, BookingType $bookingType)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:1',
            'depot_durations' => 'nullable|array',
            'depot_durations.*' => 'nullable|integer|min:1',
            'customer_durations' => 'nullable|array',
            'customer_durations.*.*' => 'nullable|integer|min:1',
        ]);

        // Update basic info
        $bookingType->update([
            'name' => $data['name'],
            'duration_minutes' => $data['duration_minutes'],
        ]);

        // Sync depot durations
        $depotSync = [];
        if (!empty($data['depot_durations'])) {
            foreach ($data['depot_durations'] as $depotId => $duration) {
                if (!empty($duration)) {
                    $depotSync[$depotId] = ['duration_minutes' => $duration];
                }
            }
        }
        $bookingType->depots()->sync($depotSync);

        // Sync customer durations
        $bookingType->customers()->detach(); // Clear all existing
        if (!empty($data['customer_durations'])) {
            foreach ($data['customer_durations'] as $customerId => $depotDurations) {
                foreach ($depotDurations as $depotKey => $duration) {
                    if (!empty($duration)) {
                        $bookingType->customers()->attach($customerId, [
                            'depot_id' => $depotKey === 'all' ? null : $depotKey,
                            'duration_minutes' => $duration,
                        ]);
                    }
                }
            }
        }

        return redirect()->route('app.booking-types.index')->with('success', 'Booking type updated.');
    }

    public function destroy(BookingType $bookingType)
    {
        $bookingType->delete();

        return redirect()->route('admin.booking-types.index')->with('success', 'Booking type deleted.');
    }
}
