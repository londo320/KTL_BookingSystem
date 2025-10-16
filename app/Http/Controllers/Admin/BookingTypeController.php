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

        // Load existing depot durations and time restrictions
        $depotDurations = $bookingType->depots()
            ->get()
            ->pluck('pivot.duration_minutes', 'id')
            ->toArray();

        $depotStartTimes = $bookingType->depots()
            ->get()
            ->pluck('pivot.booking_start_time', 'id')
            ->toArray();

        $depotEndTimes = $bookingType->depots()
            ->get()
            ->pluck('pivot.booking_end_time', 'id')
            ->toArray();

        // Load existing customer durations and time restrictions
        $customerDurations = $bookingType->customers()
            ->get()
            ->mapWithKeys(function ($customer) {
                $key = $customer->id . '_' . ($customer->pivot->depot_id ?? 'all');
                return [$key => [
                    'customer_id' => $customer->id,
                    'depot_id' => $customer->pivot->depot_id,
                    'duration' => $customer->pivot->duration_minutes,
                    'start_time' => $customer->pivot->booking_start_time,
                    'end_time' => $customer->pivot->booking_end_time,
                ]];
            })
            ->toArray();

        return view('admin.booking_types.edit', compact('bookingType', 'depots', 'customers', 'depotDurations', 'depotStartTimes', 'depotEndTimes', 'customerDurations'));
    }

    public function update(Request $request, BookingType $bookingType)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:1',
            'booking_start_time' => 'nullable|date_format:H:i',
            'booking_end_time' => 'nullable|date_format:H:i|after:booking_start_time',
            'depot_durations' => 'nullable|array',
            'depot_durations.*' => 'nullable|integer|min:1',
            'depot_start_times' => 'nullable|array',
            'depot_start_times.*' => 'nullable|date_format:H:i',
            'depot_end_times' => 'nullable|array',
            'depot_end_times.*' => 'nullable|date_format:H:i',
            'customer_durations' => 'nullable|array',
            'customer_durations.*.*' => 'nullable|integer|min:1',
            'customer_start_times' => 'nullable|array',
            'customer_start_times.*.*' => 'nullable|date_format:H:i',
            'customer_end_times' => 'nullable|array',
            'customer_end_times.*.*' => 'nullable|date_format:H:i',
        ]);

        // Update basic info
        $bookingType->update([
            'name' => $data['name'],
            'duration_minutes' => $data['duration_minutes'],
            'booking_start_time' => $data['booking_start_time'] ?? null,
            'booking_end_time' => $data['booking_end_time'] ?? null,
        ]);

        // Sync depot durations and time restrictions
        $depotSync = [];
        if (!empty($data['depot_durations'])) {
            foreach ($data['depot_durations'] as $depotId => $duration) {
                if (!empty($duration) || !empty($data['depot_start_times'][$depotId]) || !empty($data['depot_end_times'][$depotId])) {
                    $depotSync[$depotId] = [
                        'duration_minutes' => $duration ?: null,
                        'booking_start_time' => $data['depot_start_times'][$depotId] ?? null,
                        'booking_end_time' => $data['depot_end_times'][$depotId] ?? null,
                    ];
                }
            }
        }
        $bookingType->depots()->sync($depotSync);

        // Sync customer durations and time restrictions
        $bookingType->customers()->detach(); // Clear all existing
        if (!empty($data['customer_durations'])) {
            foreach ($data['customer_durations'] as $customerId => $depotDurations) {
                foreach ($depotDurations as $depotKey => $duration) {
                    $startTime = $data['customer_start_times'][$customerId][$depotKey] ?? null;
                    $endTime = $data['customer_end_times'][$customerId][$depotKey] ?? null;

                    if (!empty($duration) || !empty($startTime) || !empty($endTime)) {
                        $bookingType->customers()->attach($customerId, [
                            'depot_id' => $depotKey === 'all' ? null : $depotKey,
                            'duration_minutes' => $duration ?: null,
                            'booking_start_time' => $startTime,
                            'booking_end_time' => $endTime,
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
