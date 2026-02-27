<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingType;
use App\Models\CustomerBayAssignment;
use App\Models\Slot;
use App\Services\SlotAvailabilityService;
use App\Services\SlotBookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['slot.depot', 'bookingType', 'poNumbers'])
            ->where('customer_id', auth()->user()->getCustomerId())
            ->latest()
            ->paginate(20);

        return view('customer.bookings.index', compact('bookings'));
    }

    public function create()
    {
        return view('customer.bookings.create', [
            'booking' => new Booking,
            'slots' => $this->getVisibleSlots(),
            'types' => BookingType::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request, SlotBookingService $slotBookingService)
    {
        $data = $request->validate([
            'slot_id' => 'required|exists:slots,id',
            'booking_type_id' => 'required|exists:booking_types,id',
            'container_size' => 'nullable|integer|min:0',
            'container_number' => 'nullable|string|max:50',
            'seal_number' => 'nullable|string|max:100',
            'vehicle_registration' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'po_numbers' => 'nullable|array',
            'po_numbers.*.po_number' => 'required|string|max:255',
            'po_numbers.*.lines' => 'nullable|array',
            'po_numbers.*.lines.*.line_number' => 'required|integer|min:1',
            'po_numbers.*.lines.*.expected_cases' => 'nullable|integer|min:0',
            'po_numbers.*.lines.*.expected_pallets' => 'nullable|integer|min:0',
            'po_numbers.*.lines.*.expected_pallet_type_id' => 'nullable|exists:pallet_types,id',
            'po_numbers.*.lines.*.actual_cases' => 'nullable|integer|min:0',
            'po_numbers.*.lines.*.actual_pallets' => 'nullable|integer|min:0',
            'po_numbers.*.lines.*.actual_pallet_type_id' => 'nullable|exists:pallet_types,id',
            'po_numbers.*.lines.*.sku' => 'nullable|string|max:255',
            'po_numbers.*.lines.*.description' => 'nullable|string',
        ]);

        $slot = Slot::findOrFail($data['slot_id']);
        $bookingTypeId = $data['booking_type_id'];
        $customerId = auth()->user()->getCustomerId();

        // Check if booking can be made (include customer_id for customer-specific durations)
        $availability = $slotBookingService->checkAvailability($slot, $bookingTypeId, $customerId);

        if (!$availability['can_book']) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['slot_id' => $availability['message']]);
        }

        $data['user_id'] = auth()->id();
        $data['customer_id'] = auth()->user()->getCustomerId();

        // Remove po_numbers from main data before creating booking
        $poNumbers = $data['po_numbers'] ?? [];
        unset($data['po_numbers']);

        try {
            // Create booking and occupy slots
            $booking = $slotBookingService->createBooking($data, $slot, $bookingTypeId);

            // Create PO numbers and lines if provided
            if (! empty($poNumbers)) {
                foreach ($poNumbers as $poData) {
                    $po = $booking->poNumbers()->create([
                        'po_number' => $poData['po_number'],
                    ]);

                    if (! empty($poData['lines'])) {
                        foreach ($poData['lines'] as $lineData) {
                            // Auto-create product if SKU provided and doesn't exist
                            if (!empty($lineData['sku']) && !empty($lineData['description'])) {
                                $customerId = $data['customer_id'];
                                \App\Models\Product::firstOrCreate(
                                    [
                                        'customer_id' => $customerId,
                                        'sku' => $lineData['sku']
                                    ],
                                    [
                                        'description' => $lineData['description'],
                                        'default_case_count' => null,
                                        'default_pallets' => null
                                    ]
                                );
                            }

                            $po->lines()->create($lineData);
                        }
                    }
                }
            }

            return redirect()->route('customer.bookings.index')->with('success', 'Booking created. ' . $availability['message']);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['slot_id' => $e->getMessage()]);
        }
    }

    public function edit(Booking $booking)
    {
        $this->authorize('update', $booking);
        if ($booking->arrived_at) {
            return redirect()->route('customer.bookings.index')->with('error', 'Cannot edit once arrived.');
        }

        // Load PO numbers for the booking
        $booking->load('poNumbers');

        return view('customer.bookings.edit', [
            'booking' => $booking,
            'slots' => $this->getVisibleSlots(),
            'types' => BookingType::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Booking $booking, SlotBookingService $slotBookingService)
    {
        $this->authorize('update', $booking);
        if ($booking->arrived_at) {
            return redirect()->route('customer.bookings.index')->with('error', 'Booking already arrived.');
        }

        $data = $request->validate([
            'slot_id' => 'required|exists:slots,id',
            'booking_type_id' => 'required|exists:booking_types,id',
            'container_size' => 'nullable|integer|min:0',
            'container_number' => 'nullable|string|max:50',
            'seal_number' => 'nullable|string|max:100',
            'vehicle_registration' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'po_numbers' => 'nullable|array',
            'po_numbers.*.po_number' => 'required|string|max:255',
            'po_numbers.*.lines' => 'nullable|array',
            'po_numbers.*.lines.*.line_number' => 'required|integer|min:1',
            'po_numbers.*.lines.*.expected_cases' => 'nullable|integer|min:0',
            'po_numbers.*.lines.*.expected_pallets' => 'nullable|integer|min:0',
            'po_numbers.*.lines.*.expected_pallet_type_id' => 'nullable|exists:pallet_types,id',
            'po_numbers.*.lines.*.actual_cases' => 'nullable|integer|min:0',
            'po_numbers.*.lines.*.actual_pallets' => 'nullable|integer|min:0',
            'po_numbers.*.lines.*.actual_pallet_type_id' => 'nullable|exists:pallet_types,id',
            'po_numbers.*.lines.*.sku' => 'nullable|string|max:255',
            'po_numbers.*.lines.*.description' => 'nullable|string',
        ]);

        // Handle PO numbers separately
        $poNumbers = $data['po_numbers'] ?? [];
        unset($data['po_numbers']);

        // Check if slot or booking type changed
        $slotChanged = $data['slot_id'] != $booking->slot_id;
        $typeChanged = $data['booking_type_id'] != $booking->booking_type_id;

        if ($slotChanged || $typeChanged) {
            // Use SlotBookingService to update slots
            $newSlot = Slot::findOrFail($data['slot_id']);
            $newBookingTypeId = $data['booking_type_id'];

            try {
                $slotBookingService->updateBooking($booking, $data, $newSlot, $newBookingTypeId);
            } catch (\Exception $e) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['slot_id' => $e->getMessage()]);
            }
        } else {
            // Just update the booking data
            $booking->update($data);
        }

        // Update PO numbers and lines - delete existing and recreate
        if (array_key_exists('po_numbers', $request->all())) {
            $booking->poNumbers()->delete();

            if (! empty($poNumbers)) {
                foreach ($poNumbers as $poData) {
                    $po = $booking->poNumbers()->create([
                        'po_number' => $poData['po_number'],
                    ]);

                    if (! empty($poData['lines'])) {
                        foreach ($poData['lines'] as $lineData) {
                            // Auto-create product if SKU provided and doesn't exist
                            if (!empty($lineData['sku']) && !empty($lineData['description'])) {
                                $customerId = $booking->customer_id;
                                \App\Models\Product::firstOrCreate(
                                    [
                                        'customer_id' => $customerId,
                                        'sku' => $lineData['sku']
                                    ],
                                    [
                                        'description' => $lineData['description'],
                                        'default_case_count' => null,
                                        'default_pallets' => null
                                    ]
                                );
                            }

                            $po->lines()->create($lineData);
                        }
                    }
                }
            }
        }

        return redirect()->route('customer.bookings.index')->with('success', 'Booking updated.');
    }

    protected function getVisibleSlots()
    {
        $customerId = auth()->user()->getCustomerId();

        return Slot::where('depot_id', auth()->user()->depot_id)
            ->where('start_at', '>', now())
            ->whereNotNull('released_at')
            ->where('released_at', '<=', now())
            ->where(function ($query) {
                $query->whereNull('locked_at')->orWhere('locked_at', '>', now());
            })
            ->where(function ($query) use ($customerId) {
                $query->whereDoesntHave('allowed_customers')
                    ->orWhereHas('allowed_customers', function ($q) use ($customerId) {
                        $q->where('customers.id', $customerId);
                    });
            })
            ->orderBy('start_at')
            ->get();
    }

    /**
     * Get available dates with slot counts for the date picker sidebar
     */
    public function getAvailability(Request $request, SlotAvailabilityService $slotService)
    {
        $depotId = $request->get('depot_id');
        $customerId = auth()->user()->getCustomerId();

        if (!$depotId) {
            return response()->json([
                'success' => false,
                'dates' => [],
            ]);
        }

        // Get customer's booking type (default to first available)
        $bookingType = BookingType::first();
        if (!$bookingType) {
            return response()->json([
                'success' => false,
                'dates' => [],
            ]);
        }

        $daysAhead = 30; // Show 30 days ahead

        // Get customer's allowed bays
        $allowedBayIds = CustomerBayAssignment::where('customer_id', $customerId)
            ->where('is_active', true)
            ->pluck('tipping_bay_id')
            ->toArray();

        // Build query
        $query = Slot::select('start_at')
            ->where('start_at', '>=', now())
            ->where('start_at', '<=', now()->addDays($daysAhead))
            ->where('is_blocked', false)
            ->where('depot_id', $depotId);

        if (!empty($allowedBayIds)) {
            $query->whereIn('tipping_bay_id', $allowedBayIds);
        }

        $slots = $query->get();

        // Group by date and count available slots per date
        $dateGroups = [];
        foreach ($slots as $slot) {
            // Check availability using service
            $availability = $slotService->isSlotAvailable(
                $slot,
                $customerId,
                $bookingType->id
            );

            if ($availability['available']) {
                $dateKey = $slot->start_at->format('Y-m-d');
                if (!isset($dateGroups[$dateKey])) {
                    $dateGroups[$dateKey] = [
                        'date' => $dateKey,
                        'available_slots' => 0,
                    ];
                }
                $dateGroups[$dateKey]['available_slots']++;
            }
        }

        // Convert to array and sort
        $result = array_values($dateGroups);
        usort($result, function ($a, $b) {
            return strcmp($a['date'], $b['date']);
        });

        return response()->json([
            'success' => true,
            'dates' => $result,
        ]);
    }

    /**
     * Get available slots for a specific date
     */
    public function getSlots(Request $request, SlotAvailabilityService $slotService)
    {
        $depotId = $request->get('depot_id');
        $date = $request->get('date');
        $customerId = auth()->user()->getCustomerId();

        if (!$depotId || !$date) {
            return response()->json([
                'success' => false,
                'slots' => [],
            ]);
        }

        // Get customer's booking type (default to first available)
        $bookingType = BookingType::first();
        if (!$bookingType) {
            return response()->json([
                'success' => false,
                'slots' => [],
            ]);
        }

        // Get customer's allowed bays
        $allowedBayIds = CustomerBayAssignment::where('customer_id', $customerId)
            ->where('is_active', true)
            ->pluck('tipping_bay_id')
            ->toArray();

        // Build query for specific date
        $query = Slot::with(['depot', 'tippingBay'])
            ->whereDate('start_at', $date)
            ->where('start_at', '>=', now())
            ->where('is_blocked', false)
            ->where('depot_id', $depotId);

        if (!empty($allowedBayIds)) {
            $query->whereIn('tipping_bay_id', $allowedBayIds);
        }

        $slots = $query->orderBy('start_at')->get();

        // Format slots for the UI
        $formattedSlots = [];
        foreach ($slots as $slot) {
            // Check availability
            $availability = $slotService->isSlotAvailable(
                $slot,
                $customerId,
                $bookingType->id
            );

            if ($availability['available']) {
                $formattedSlots[] = [
                    'id' => $slot->id,
                    'time_range' => $slot->start_at->format('H:i') . ' - ' . $slot->end_at->format('H:i'),
                    'is_restricted' => !empty($allowedBayIds),
                    'customers_info' => $slot->tippingBay ? $slot->tippingBay->name : 'No Bay',
                ];
            }
        }

        return response()->json([
            'success' => true,
            'slots' => $formattedSlots,
        ]);
    }
}
