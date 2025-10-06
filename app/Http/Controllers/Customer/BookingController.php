<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingType;
use App\Models\Slot;
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

        // Check if booking can be made
        $availability = $slotBookingService->checkAvailability($slot, $bookingTypeId);

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

    public function update(Request $request, Booking $booking)
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

        $booking->update($data);

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
}
