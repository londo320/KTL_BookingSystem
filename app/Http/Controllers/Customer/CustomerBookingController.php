<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Traits\NormalizeInputTrait;
use App\Models\Booking;
use App\Models\BookingHistory;
use App\Models\BookingType;
use App\Models\Carrier;
use App\Models\FactoryBooking;
use App\Models\Slot;
use App\Services\PDFService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class CustomerBookingController extends Controller
{
    use NormalizeInputTrait;
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Get regular bookings
        $regularBookings = $this->filteredBookings($request);
        
        // Get factory bookings with similar filtering
        $factoryBookings = $this->filteredFactoryBookings($request);
        
        // Combine and paginate both types
        $combinedBookings = $this->combineAndSortBookings($regularBookings, $factoryBookings, $request);
        
        $bookings = $this->paginateCombinedBookings($combinedBookings, $request, 20);

        // Get filter data for the view
        $depots = $user->depots;
        $customers = $user->customers;
        $types = BookingType::orderBy('name')->get();

        // Generate weeks data for dropdown
        $currentYear = Carbon::now()->year;
        $currentWeek = Carbon::now()->weekOfYear;
        $weeks = [];
        for ($week = 1; $week <= 52; $week++) {
            $weekStart = Carbon::now()->setISODate($currentYear, $week)->startOfWeek();
            $weekEnd = $weekStart->clone()->endOfWeek();
            $weeks[] = [
                'number' => $week,
                'start' => $weekStart->format('M d'),
                'end' => $weekEnd->format('M d'),
                'is_current' => $week === $currentWeek,
            ];
        }

        return view('customer.bookings.index', compact('bookings', 'depots', 'customers', 'types', 'weeks'));
    }

    public function create(Request $request)
    {
        $user = auth()->user();
        $userDepots = $user->depots;
        $userCustomers = $user->customers;

        $date = $request->input('date') ?? now()->format('Y-m-d');
        $depotId = $request->input('depot_id') ?? $userDepots->first()?->id;
        $customerId = $this->resolveSelectedCustomerId($request, $userCustomers);

        return view('customer.bookings.create', [
            'booking' => new Booking,
            'slots' => $depotId ? $this->getVisibleSlots($depotId, $date, null, $customerId) : collect(),
            'types' => BookingType::orderBy('name')->get(),
            'customers' => $userCustomers,
            'selectedCustomerId' => $customerId,
            'selectedDepotId' => $depotId,
            'selectedDate' => $date,
            'showSkuFields' => \App\Models\CustomerBookingConfig::skuFieldsEnabled($customerId, $depotId),
        ]);
    }

    /**
     * The booking-creation flow lets a user pick which of their accessible
     * customers a booking is for (bay assignments, SKU config, and restricted
     * slots can all differ per customer) — defaults to their only/first
     * customer when they don't have more than one to choose from.
     */
    private function resolveSelectedCustomerId(Request $request, $userCustomers): ?int
    {
        $requested = $request->input('customer_id');
        if ($requested && $userCustomers->contains('id', $requested)) {
            return (int) $requested;
        }

        return $userCustomers->first()?->id;
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $accessibleCustomerIds = $user->customers->pluck('id');

        $data = $request->validate([
            'customer_id' => ['required', 'integer', Rule::in($accessibleCustomerIds)],
            'slot_id' => 'required|exists:slots,id',
            'booking_type_id' => 'required|exists:booking_types,id',
            'carrier_id' => 'nullable|exists:carriers,id',
            'carrier_name' => 'required|string|max:100',
            'notes' => 'nullable|string',
            'vehicle_registration' => 'nullable|string|max:50',
            'container_number' => 'nullable|string|max:50',
            'bay_number' => 'nullable|string|max:10',
            'estimated_arrival' => 'nullable|date',
            'special_instructions' => 'nullable|string',
            
            // PO Numbers validation
            'po_numbers' => 'required|array|min:1',
            'po_numbers.*.po_number' => 'required|string|max:255',
            'po_numbers.*.lines' => 'required|array|min:1',
            'po_numbers.*.lines.*.line_number' => 'required|integer|min:1',
            'po_numbers.*.lines.*.pallet_entries' => 'nullable|array',
            'po_numbers.*.lines.*.pallet_entries.*.cases' => 'required|integer|min:0',
            'po_numbers.*.lines.*.pallet_entries.*.pallets' => 'required|integer|min:0',
            'po_numbers.*.lines.*.pallet_entries.*.type_id' => 'nullable|exists:pallet_types,id',
        ]);

        // Normalize input data
        if (!empty($data['vehicle_registration'])) {
            $data['vehicle_registration'] = $this->normalizeVehicleRegistration($data['vehicle_registration']);
        }
        if (!empty($data['container_number'])) {
            $data['container_number'] = $this->normalizeContainerNumber($data['container_number']);
        }
        if (!empty($data['carrier_name'])) {
            $data['carrier_name'] = $this->normalizeCarrierName($data['carrier_name']);
        }

        $slot = Slot::with('depot')->findOrFail($data['slot_id']);

        // Verify user has access to this depot
        if (! $user->depots->contains('id', $slot->depot_id)) {
            return back()->withErrors(['slot_id' => 'You do not have access to the depot for this slot.']);
        }
        if ($slot->locked_at && $slot->locked_at->isPast()) {
            return back()->withErrors(['slot_id' => 'That slot is no longer available (cut-off time passed).']);
        }

        // Transform PO data from new pallet_entries structure to legacy database structure
        if (isset($data['po_numbers'])) {
            foreach ($data['po_numbers'] as $poIndex => $poData) {
                if (isset($poData['lines'])) {
                    foreach ($poData['lines'] as $lineIndex => $lineData) {
                        if (!empty($lineData['pallet_entries'])) {
                            $totalCases = 0;
                            $totalPallets = 0;
                            $firstPalletTypeId = null;
                            
                            foreach ($lineData['pallet_entries'] as $entry) {
                                $totalCases += intval($entry['cases'] ?? 0);
                                $totalPallets += intval($entry['pallets'] ?? 0);
                                if (empty($firstPalletTypeId) && !empty($entry['type_id'])) {
                                    $firstPalletTypeId = $entry['type_id'];
                                }
                            }
                            
                            $data['po_numbers'][$poIndex]['lines'][$lineIndex]['expected_cases'] = $totalCases;
                            $data['po_numbers'][$poIndex]['lines'][$lineIndex]['expected_pallets'] = $totalPallets;
                            if ($firstPalletTypeId) {
                                $data['po_numbers'][$poIndex]['lines'][$lineIndex]['expected_pallet_type_id'] = $firstPalletTypeId;
                            }
                            
                            unset($data['po_numbers'][$poIndex]['lines'][$lineIndex]['pallet_entries']);
                        }
                    }
                }
            }
        }

        $data['user_id'] = auth()->id();
        $data['depot_id'] = $slot->depot_id;

        // Handle carrier creation/selection
        if (empty($data['carrier_id']) && !empty($data['carrier_name'])) {
            // Try to find existing carrier or create new one
            $carrier = Carrier::findOrReactivate($data['carrier_name']);
            if (!$carrier) {
                $carrier = Carrier::create([
                    'name' => $data['carrier_name'],
                    'is_active' => true,
                ]);
            }
            $data['carrier_id'] = $carrier->id;
        }

        DB::transaction(function () use ($data) {
            $booking = Booking::create($data);
            
            // Save PO numbers and lines
            if (isset($data['po_numbers'])) {
                foreach ($data['po_numbers'] as $poData) {
                    $poNumber = $booking->poNumbers()->create([
                        'po_number' => $poData['po_number'],
                    ]);
                    
                    if (isset($poData['lines'])) {
                        foreach ($poData['lines'] as $lineData) {
                            $poNumber->lines()->create([
                                'line_number' => $lineData['line_number'],
                                'expected_cases' => $lineData['expected_cases'] ?? 0,
                                'expected_pallets' => $lineData['expected_pallets'] ?? 0,
                                'expected_pallet_type_id' => $lineData['expected_pallet_type_id'] ?? null,
                            ]);
                        }
                    }
                }
            }
        });

        return redirect()->route('customer.bookings.index')->with('success', 'Booking created.');
    }

    public function show(Request $request, Booking $booking)
    {
        $this->authorize('view', $booking);

        return view('customer.bookings.show', [
            'booking' => $booking->load(['slot.depot', 'bookingType', 'customer']),
        ]);
    }

    public function edit(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);

        // Allow limited editing even after arrival/cutoff for transportation details
        // Full PO editing will be restricted in the view based on status
        
        $date = $request->input('date') ?? $booking->slot->start_at->format('Y-m-d');
        $depotId = $request->input('depot_id') ?? $booking->slot->depot_id;

        $slots = $this->getVisibleSlots($depotId, $date);

        if ($booking->slot && ! $slots->contains('id', $booking->slot_id)) {
            $slots->push($booking->slot);
        }

        return view('customer.bookings.edit', [
            'booking' => $booking->load([
                'slot.depot', 
                'bookingType', 
                'customer', 
                'carrier',
                'poNumbers.lines.expectedPalletType',
                'poNumbers.lines.actualPalletType'
            ]),
            'slots' => $slots->sortBy('start_at'),
            'types' => BookingType::orderBy('name')->get(),
            'selectedDepotId' => $depotId,
            'selectedDate' => $date,
        ]);
    }

    public function update(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);

        $hasArrived = $booking->arrived_at;
        $cutoffPassed = $booking->slot && $booking->slot->locked_at && $booking->slot->locked_at->isPast();
        $canEditPO = !$hasArrived && !$cutoffPassed;

        // Build validation rules based on booking status
        $rules = [
            'slot_id' => 'required|exists:slots,id',
            'vehicle_registration' => 'nullable|string|max:50',
            'container_number' => 'nullable|string|max:50',
            'carrier_id' => 'nullable|exists:carriers,id',
            'carrier_name' => 'nullable|string|max:100',
            'special_instructions' => 'nullable|string',
        ];

        // Only add arrival time validation if not arrived
        if (!$hasArrived) {
            $rules['estimated_arrival'] = 'nullable|date';
        }

        // Only validate PO numbers if editing is allowed
        if ($canEditPO) {
            $rules = array_merge($rules, [
                'po_numbers' => 'required|array|min:1',
                'po_numbers.*.po_number' => 'required|string|max:255',
                'po_numbers.*.lines' => 'required|array|min:1',
                'po_numbers.*.lines.*.line_number' => 'required|integer|min:1',
                'po_numbers.*.lines.*.pallet_entries' => 'nullable|array',
                'po_numbers.*.lines.*.pallet_entries.*.cases' => 'required|integer|min:0',
                'po_numbers.*.lines.*.pallet_entries.*.pallets' => 'required|integer|min:0',
                'po_numbers.*.lines.*.pallet_entries.*.type_id' => 'nullable|exists:pallet_types,id',
            ]);
        }

        $data = $request->validate($rules);

        // Debug: Log ETA data
        \Log::info('Customer booking update - ETA data:', [
            'booking_id' => $booking->id,
            'has_arrived' => $hasArrived,
            'request_eta' => $request->input('estimated_arrival'),
            'validated_eta' => $data['estimated_arrival'] ?? 'not_set',
            'current_eta' => $booking->estimated_arrival ? $booking->estimated_arrival->toDateTimeString() : 'null'
        ]);

        // Normalize input data
        if (!empty($data['vehicle_registration'])) {
            $data['vehicle_registration'] = $this->normalizeVehicleRegistration($data['vehicle_registration']);
        }
        if (!empty($data['container_number'])) {
            $data['container_number'] = $this->normalizeContainerNumber($data['container_number']);
        }
        if (!empty($data['carrier_name'])) {
            $data['carrier_name'] = $this->normalizeCarrierName($data['carrier_name']);
        }

        // Only validate slot changes if PO editing is allowed (before cutoff/arrival)
        if ($canEditPO) {
            $slot = Slot::findOrFail($data['slot_id']);
            if ($slot->locked_at && $slot->locked_at->isPast()) {
                return back()->withErrors(['slot_id' => 'That slot is locked and cannot be changed.']);
            }
        }

        // Transform PO data from new pallet_entries structure to legacy database structure
        // Only process PO data if editing is allowed
        if ($canEditPO && isset($data['po_numbers'])) {
            foreach ($data['po_numbers'] as $poIndex => $poData) {
                if (isset($poData['lines'])) {
                    foreach ($poData['lines'] as $lineIndex => $lineData) {
                        if (!empty($lineData['pallet_entries'])) {
                            $totalCases = 0;
                            $totalPallets = 0;
                            $firstPalletTypeId = null;
                            
                            foreach ($lineData['pallet_entries'] as $entry) {
                                $totalCases += intval($entry['cases'] ?? 0);
                                $totalPallets += intval($entry['pallets'] ?? 0);
                                if (empty($firstPalletTypeId) && !empty($entry['type_id'])) {
                                    $firstPalletTypeId = $entry['type_id'];
                                }
                            }
                            
                            $data['po_numbers'][$poIndex]['lines'][$lineIndex]['expected_cases'] = $totalCases;
                            $data['po_numbers'][$poIndex]['lines'][$lineIndex]['expected_pallets'] = $totalPallets;
                            if ($firstPalletTypeId) {
                                $data['po_numbers'][$poIndex]['lines'][$lineIndex]['expected_pallet_type_id'] = $firstPalletTypeId;
                            }
                            
                            unset($data['po_numbers'][$poIndex]['lines'][$lineIndex]['pallet_entries']);
                        }
                    }
                }
            }
        }

        // Handle carrier creation/selection
        if (empty($data['carrier_id']) && !empty($data['carrier_name'])) {
            // Try to find existing carrier or create new one
            $carrier = Carrier::findOrReactivate($data['carrier_name']);
            if (!$carrier) {
                $carrier = Carrier::create([
                    'name' => $data['carrier_name'],
                    'is_active' => true,
                ]);
            }
            $data['carrier_id'] = $carrier->id;
        }

        DB::transaction(function () use ($booking, $data, $canEditPO) {
            // Always update basic booking fields that are always editable
            $basicFields = [
                'carrier_name' => $data['carrier_name'] ?? $booking->carrier_name,
                'carrier_id' => $data['carrier_id'] ?? $booking->carrier_id,
            ];

            // vehicle_registration/container_number are shadowed by accessors
            // (Booking::getVehicleRegistrationAttribute/getContainerNumberAttribute)
            // that always read from vehicle_details, never the raw column — so
            // they must be written there too, or the edit silently appears not
            // to save even though the (unused) raw column did update.
            $vehicleDetails = $booking->vehicle_details ?? [];

            if (array_key_exists('vehicle_registration', $data)) {
                if (!empty($data['vehicle_registration'])) {
                    $vehicleDetails['vehicle_registration'] = $data['vehicle_registration'];
                } else {
                    unset($vehicleDetails['vehicle_registration']);
                }
            }

            if (array_key_exists('container_number', $data)) {
                if (!empty($data['container_number'])) {
                    $vehicleDetails['container_number'] = $data['container_number'];
                } else {
                    unset($vehicleDetails['container_number']);
                }
            }

            // Update special instructions
            if (isset($data['special_instructions'])) {
                if ($data['special_instructions']) {
                    $vehicleDetails['special_instructions'] = $data['special_instructions'];
                } else {
                    unset($vehicleDetails['special_instructions']);
                }
            }

            // Update estimated arrival (only if not already arrived)
            if (!$booking->arrived_at) {
                if ($data['estimated_arrival'] ?? null) {
                    $vehicleDetails['estimated_arrival'] = $data['estimated_arrival'];
                } else {
                    unset($vehicleDetails['estimated_arrival']);
                }
            }

            $basicFields['vehicle_details'] = $vehicleDetails;
            
            // Only update slot if PO editing is allowed (before cutoff/arrival)
            if ($canEditPO && isset($data['slot_id'])) {
                $basicFields['slot_id'] = $data['slot_id'];
                $basicFields['depot_id'] = Slot::find($data['slot_id'])?->depot_id ?? $booking->depot_id;
            }
            
            // Debug: Log what will be updated
            \Log::info('Customer booking update - basic fields:', [
                'booking_id' => $booking->id,
                'basic_fields' => $basicFields
            ]);
            
            $booking->update($basicFields);
            
            // Update PO numbers and lines only if editing is allowed
            if ($canEditPO && isset($data['po_numbers'])) {
                // Delete existing PO numbers and lines
                $booking->poNumbers()->delete();
                
                // Recreate PO numbers and lines
                foreach ($data['po_numbers'] as $poData) {
                    $poNumber = $booking->poNumbers()->create([
                        'po_number' => $poData['po_number'],
                    ]);
                    
                    if (isset($poData['lines'])) {
                        foreach ($poData['lines'] as $lineData) {
                            $poNumber->lines()->create([
                                'line_number' => $lineData['line_number'],
                                'expected_cases' => $lineData['expected_cases'] ?? 0,
                                'expected_pallets' => $lineData['expected_pallets'] ?? 0,
                                'expected_pallet_type_id' => $lineData['expected_pallet_type_id'] ?? null,
                            ]);
                        }
                    }
                }
            }
        });

        return redirect()->route('customer.bookings.index')->with('success', 'Booking updated.');
    }

    /**
     * Update just the ETA from the booking view page — available regardless
     * of whether the booking is locked/past cutoff for PO/slot editing,
     * since an updated arrival time doesn't touch any of that.
     */
    public function updateEta(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);

        if ($booking->arrived_at) {
            return back()->withErrors(['estimated_arrival' => 'Cannot update ETA once the vehicle has arrived.']);
        }

        if ($booking->isCancelled()) {
            return back()->withErrors(['estimated_arrival' => 'Cannot update ETA on a cancelled booking.']);
        }

        $data = $request->validate([
            'estimated_arrival' => 'nullable|date',
        ]);

        $vehicleDetails = $booking->vehicle_details ?? [];
        if (!empty($data['estimated_arrival'])) {
            $vehicleDetails['estimated_arrival'] = $data['estimated_arrival'];
        } else {
            unset($vehicleDetails['estimated_arrival']);
        }

        $booking->update(['vehicle_details' => $vehicleDetails]);

        return back()->with('success', 'Expected arrival time updated.');
    }

    /**
     * API endpoint to get availability overview for a depot
     */
    public function availability(Request $request)
    {
        $depotId = $request->input('depot_id');
        if (! $depotId) {
            return response()->json(['dates' => []]);
        }

        $user = auth()->user();
        $customerId = $this->resolveSelectedCustomerId($request, $user->customers);
        $bookingTypeId = $request->input('booking_type_id');

        // Check if user has access to this depot
        if (! $user->depots->contains('id', $depotId)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        // Get the next 30 days with available slots
        $dates = [];
        $startDate = now();
        $endDate = now()->addDays(30);

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateString = $date->format('Y-m-d');
            $availableSlots = $this->groupSlotsByTime($this->getVisibleSlots($depotId, $dateString, $bookingTypeId, $customerId))->count();

            if ($availableSlots > 0) {
                $dates[] = [
                    'date' => $dateString,
                    'available_slots' => $availableSlots,
                    'day_name' => $date->format('l'),
                ];
            }
        }

        return response()->json(['dates' => $dates]);
    }

    /**
     * API endpoint to get slots for a specific depot and date
     */
    public function slots(Request $request)
    {
        $depotId = $request->input('depot_id');
        $date = $request->input('date');
        $bookingTypeId = $request->input('booking_type_id');

        if (! $depotId || ! $date) {
            return response()->json(['slots' => []]);
        }

        $user = auth()->user();
        $customerId = $this->resolveSelectedCustomerId($request, $user->customers);

        // Check if user has access to this depot
        if (! $user->depots->contains('id', $depotId)) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $slots = $this->getVisibleSlots($depotId, $date, $bookingTypeId, $customerId);

        $formattedSlots = $this->groupSlotsByTime($slots)->values();

        return response()->json(['slots' => $formattedSlots]);
    }

    /**
     * Bay-based generation creates one Slot row per bay per time window, so
     * the same start/end time can appear many times (once per bay). Group
     * them so the picker shows each time once, backed by one representative
     * slot id (preferring a public one, since the customer doesn't choose
     * the bay) plus how many bays are available at that time.
     */
    private function groupSlotsByTime($slots)
    {
        return $slots
            ->groupBy(fn ($slot) => Carbon::parse($slot->start_at)->format('Y-m-d H:i:s').'|'.Carbon::parse($slot->end_at)->format('Y-m-d H:i:s'))
            ->map(function ($group) {
                $representative = $group->first(fn ($slot) => $slot->allowed_customers->count() === 0) ?? $group->first();

                $startAt = Carbon::parse($representative->start_at);
                $endAt = Carbon::parse($representative->end_at);
                $isRestricted = $representative->allowed_customers->count() > 0;

                $customersInfo = 'Public';
                if ($isRestricted) {
                    $customerNames = $representative->allowed_customers->pluck('name')->take(2);
                    $customersInfo = $customerNames->join(', ');
                    if ($representative->allowed_customers->count() > 2) {
                        $customersInfo .= ' +'.($representative->allowed_customers->count() - 2).' more';
                    }
                }

                return [
                    'id' => $representative->id,
                    'time_range' => $startAt->format('H:i').' - '.$endAt->format('H:i'),
                    'is_restricted' => $isRestricted,
                    'customers_info' => $customersInfo,
                    'depot_name' => $representative->depot->name,
                    'bays_available' => $group->count(),
                ];
            });
    }

    public function emailPDF(Request $request, Booking $booking)
    {
        $this->authorize('view', $booking);

        $request->validate([
            'email' => 'required|email',
            'message' => 'nullable|string|max:500',
        ]);

        try {
            $booking->load(['slot.depot', 'bookingType', 'customer', 'user']);

            \Log::info('Starting customer PDF generation for booking: '.$booking->id);

            // Generate PDF with mPDF for better emoji support
            $pdfService = new PDFService;
            $pdf = $pdfService->generateBookingPDF('customer.bookings.pdf', compact('booking'));

            \Log::info('Customer PDF generated successfully. Class: '.get_class($pdf));

            // Handle different PDF library outputs based on class type
            if ($pdf instanceof \Mpdf\Mpdf || $pdf instanceof \mPDF\mPDF) {
                // mPDF
                \Log::info('Using mPDF to extract content');
                $pdfContent = $pdf->Output('', 'S');
            } elseif ($pdf instanceof \Barryvdh\DomPDF\PDF) {
                // DomPDF - get raw PDF content using output()
                \Log::info('Using DomPDF to extract content');
                $pdfContent = $pdf->output();
            } else {
                throw new \Exception('Unknown PDF library for email: '.get_class($pdf));
            }

            \Log::info('Customer PDF content extracted. Size: '.strlen($pdfContent).' bytes');

            // Send email
            \Log::info('Sending customer email to: '.$request->input('email'));
            Mail::send('customer.bookings.email', [
                'booking' => $booking,
                'customMessage' => $request->input('message', ''),
            ], function ($mail) use ($request, $booking, $pdfContent) {
                $mail->to($request->input('email'))
                    ->subject('Your Booking Details #'.$booking->id)
                    ->attachData($pdfContent, "booking-{$booking->id}.pdf", [
                        'mime' => 'application/pdf',
                    ]);
            });

            \Log::info('Customer email sent successfully');

            return response()->json(['success' => true, 'message' => 'PDF sent successfully']);
        } catch (\Exception $e) {
            \Log::error('Customer PDF email error: '.$e->getMessage());
            \Log::error('Customer PDF email stack trace: '.$e->getTraceAsString());

            return response()->json(['success' => false, 'message' => 'Failed to send PDF: '.$e->getMessage()]);
        }
    }

    public function downloadPDF(Booking $booking)
    {
        $this->authorize('view', $booking);

        $booking->load(['slot.depot', 'bookingType', 'customer', 'user']);

        $pdfService = new PDFService;
        $mpdf = $pdfService->generateBookingPDF('customer.bookings.pdf', compact('booking'));

        $filename = "booking-{$booking->id}-".$booking->slot->start_at->format('Y-m-d').'.pdf';

        return response($mpdf->Output($filename, 'S'))
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
    }

    /**
     * Full (non-paginated) combined bookings list for the current filters —
     * shared by the CSV/Excel/PDF exports so they always match what the
     * index page shows for the same query string.
     */
    private function exportableBookings(Request $request)
    {
        $regularBookings = $this->filteredBookings($request);
        $factoryBookings = $this->filteredFactoryBookings($request);

        return $this->combineAndSortBookings($regularBookings, $factoryBookings, $request);
    }

    private function exportRowFor($booking): array
    {
        $isFactory = isset($booking->type) && $booking->type === 'factory';

        $expectedCases = 0;
        $expectedPallets = 0;
        $actualCases = 0;
        $actualPallets = 0;
        foreach ($booking->poNumbers as $po) {
            foreach ($po->lines as $line) {
                $expectedCases += $line->expected_cases ?? 0;
                $expectedPallets += $line->expected_pallets ?? 0;
                $actualCases += $line->actual_cases ?? 0;
                $actualPallets += $line->actual_pallets ?? 0;
            }
        }

        $status = 'Scheduled';
        if ($booking->cancelled_at) {
            $status = 'Cancelled';
        } elseif ($booking->arrived_at) {
            $status = $booking->departed_at ? 'Completed' : 'On-site';
        }

        return [
            $booking->booking_reference ?? 'N/A',
            optional($booking->customer)->name ?? '-',
            $booking->slot->depot->name,
            $booking->slot->start_at->format('Y-m-d H:i'),
            $booking->slot->end_at->format('Y-m-d H:i'),
            $isFactory ? 'Factory Delivery' : (optional($booking->bookingType)->name ?? 'N/A'),
            $booking->vehicle_registration ?? '-',
            $booking->container_number ?? '-',
            $expectedCases,
            $actualCases,
            $expectedPallets,
            $actualPallets,
            $status,
        ];
    }

    private function exportHeaders(): array
    {
        return [
            'Booking Ref', 'Customer', 'Depot', 'Start', 'End', 'Type',
            'Vehicle Registration', 'Container Number',
            'Expected Cases', 'Actual Cases', 'Expected Pallets', 'Actual Pallets', 'Status',
        ];
    }

    public function exportCsv(Request $request)
    {
        $bookings = $this->exportableBookings($request);
        $headers = $this->exportHeaders();

        $filename = 'my-bookings-'.now()->format('Y-m-d-H-i').'.csv';

        return response()->stream(function () use ($bookings, $headers) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM for Excel
            fputcsv($file, $headers);
            foreach ($bookings as $booking) {
                fputcsv($file, $this->exportRowFor($booking));
            }
            fclose($file);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function exportExcel(Request $request)
    {
        $bookings = $this->exportableBookings($request);
        $headers = $this->exportHeaders();

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('My Bookings');

        $sheet->fromArray($headers, null, 'A1');
        $sheet->getStyle('A1:M1')->getFont()->setBold(true);

        $row = 2;
        foreach ($bookings as $booking) {
            $sheet->fromArray($this->exportRowFor($booking), null, 'A'.$row);
            $row++;
        }

        foreach (range('A', 'M') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $filename = 'my-bookings-'.now()->format('Y-m-d-H-i').'.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function exportPdf(Request $request)
    {
        $bookings = $this->exportableBookings($request);

        $data = [
            'bookings' => $bookings,
            'customerName' => auth()->user()->name,
            'generatedAt' => now(),
            'totalBookings' => $bookings->count(),
        ];

        $pdfService = new PDFService;
        $pdf = $pdfService->generateBookingPDF('customer.bookings.export-pdf', $data);

        $filename = 'my-bookings-'.now()->format('Y-m-d-H-i').'.pdf';

        if ($pdf instanceof \Mpdf\Mpdf || $pdf instanceof \mPDF\mPDF) {
            return response($pdf->Output('', 'S'))
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
        }

        return $pdf->download($filename);
    }

    private function filteredBookings(Request $request)
    {
        $user = auth()->user();
        $accessibleCustomerIds = $user->getAccessibleCustomerIds();
        $accessibleDepotIds = $user->depots->pluck('id')->toArray();

        $query = Booking::with(['slot.depot', 'bookingType', 'customer'])
            ->whereIn('customer_id', $accessibleCustomerIds)
            ->whereNotNull('customer_id')
            // Exclude bookings that were cancelled due to rebooking (only show active + directly cancelled bookings)
            ->where(function ($q) {
                $q->whereNull('cancelled_at') // Active bookings
                    ->orWhere(function ($q2) {
                        $q2->whereNotNull('cancelled_at')
                            ->where(function ($q3) {
                                $q3->whereNull('cancellation_reason')
                                    ->orWhere('cancellation_reason', 'not like', '%Rebooked%');
                            });
                    });
            })
            ->join('slots', 'bookings.slot_id', '=', 'slots.id')
            ->orderBy('slots.start_at', 'asc')
            ->select('bookings.*');

        // Depot filter
        if ($depotId = $request->input('depot_id')) {
            $query->whereHas('slot', fn ($q) => $q->where('depot_id', $depotId));
        } else {
            // Restrict to user's accessible depots
            $query->whereHas('slot', fn ($q) => $q->whereIn('depot_id', $accessibleDepotIds));
        }

        // Booking type filter
        if ($bookingTypeId = $request->input('booking_type_id')) {
            $query->where('booking_type_id', $bookingTypeId);
        }

        // Quick date filters (handle both 'filter' and 'quick_filter' parameters)
        if ($quickFilter = $request->input('filter') ?: $request->input('quick_filter')) {
            if ($quickFilter === 'today') {
                $query->whereHas('slot', function ($q) {
                    $q->whereDate('start_at', Carbon::today());
                });
            } elseif ($quickFilter === 'tomorrow') {
                $query->whereHas('slot', function ($q) {
                    $q->whereDate('start_at', Carbon::tomorrow());
                });
            } elseif ($quickFilter === 'last_week') {
                $query->whereHas('slot', function ($q) {
                    $q->whereBetween('start_at', [
                        Carbon::now()->subWeek()->startOfWeek(),
                        Carbon::now()->subWeek()->endOfWeek(),
                    ]);
                });
            } elseif ($quickFilter === 'this_week') {
                $query->whereHas('slot', function ($q) {
                    $q->whereBetween('start_at', [
                        Carbon::now()->startOfWeek(),
                        Carbon::now()->endOfWeek(),
                    ]);
                });
            } elseif ($quickFilter === 'next_week') {
                $query->whereHas('slot', function ($q) {
                    $q->whereBetween('start_at', [
                        Carbon::now()->addWeek()->startOfWeek(),
                        Carbon::now()->addWeek()->endOfWeek(),
                    ]);
                });
            }
        }

        // Week number filter
        if ($weekNumber = $request->input('week_number')) {
            $query->whereHas('slot', function ($q) use ($weekNumber) {
                $year = Carbon::now()->year;
                $weekStart = Carbon::now()->setISODate($year, $weekNumber)->startOfWeek();
                $weekEnd = $weekStart->clone()->endOfWeek();
                $q->whereBetween('start_at', [$weekStart, $weekEnd]);
            });
        }

        // Date range filters
        if ($from = $request->input('from')) {
            $query->whereHas('slot', function ($q) use ($from) {
                $q->whereDate('start_at', '>=', $from);
            });
        }
        if ($to = $request->input('to')) {
            $query->whereHas('slot', function ($q) use ($to) {
                $q->whereDate('start_at', '<=', $to);
            });
        }

        // Arrival status filter
        if ($arrival = $request->input('arrival')) {
            if ($arrival === 'not_arrived') {
                $query->whereNull('arrived_at');
            } elseif ($arrival === 'late_runners') {
                $query->whereNull('arrived_at')
                    ->whereHas('slot', fn ($q) => $q->where('start_at', '<', now()));
            } elseif ($arrival === 'arrived') {
                $query->whereNotNull('arrived_at');
            } elseif ($arrival === 'on_time') {
                $query->whereNotNull('arrived_at')
                    ->whereRaw('arrived_at <= DATE_ADD((SELECT start_at FROM slots WHERE slots.id = bookings.slot_id), INTERVAL 30 MINUTE)');
            } elseif ($arrival === 'arrived_late') {
                $query->whereNotNull('arrived_at')
                    ->whereRaw('arrived_at > DATE_ADD((SELECT start_at FROM slots WHERE slots.id = bookings.slot_id), INTERVAL 30 MINUTE)');
            } elseif ($arrival === 'onsite') {
                $query->whereNotNull('arrived_at')->whereDoesntHave('movements', function ($q) {
                    $q->whereNotNull('trailer_collected_at');
                });
            } elseif ($arrival === 'completed') {
                $query->whereNotNull('arrived_at')->whereNotNull('departed_at');
            }
        }

        return $query->orderByDesc('id');
    }

    protected function getVisibleSlots($depotId, $date, $bookingTypeId = null, $customerId = null)
    {
        $user = auth()->user();
        $customerId = $customerId ?? $user->getCustomerId();

        $query = Slot::where('depot_id', $depotId)
            ->whereDate('start_at', $date)
            ->where('start_at', '>', now())
            ->where('is_blocked', false)

            // Only slots with no active (non-cancelled) bookings
            ->whereDoesntHave('bookings', function ($q) {
                $q->whereNull('cancelled_at');
            })

            ->where(function ($q) {
                // Not locked (booking cut-off has not passed)
                $q->whereNull('locked_at')
                    ->orWhere('locked_at', '>', now());
            });

        if (\App\Models\Setting::isUsingBayBasedSlots()) {
            // Bay-based generation doesn't use slot release rules — that
            // mechanism only applies to template-based generation. Instead,
            // restrict by the customer's bay assignments, if any exist.
            $allowedBayIds = $customerId ? \App\Models\CustomerBayAssignment::getAllowedBayIds($customerId, $depotId) : null;
            if ($allowedBayIds !== null) {
                $query->whereIn('tipping_bay_id', $allowedBayIds);
            }
        } else {
            $query->where(function ($query) use ($customerId) {
                $query->where(function ($q) use ($customerId) {
                    // Restricted slots (no released_at, but allowed_customers)
                    $q->whereNull('released_at')
                        ->whereHas('allowed_customers', fn ($q2) => $q2->where('customers.id', $customerId)
                        );
                })
                    ->orWhere(function ($q) {
                        // Public slots (released_at in the past)
                        $q->whereNotNull('released_at')
                            ->where('released_at', '<=', now());
                    });
            });
        }

        $slots = $query->with('allowed_customers', 'depot')
            ->orderBy('start_at')
            ->get();

        // Restrict to this booking type's allowed time window, if one is selected
        if ($bookingTypeId) {
            $bookingType = BookingType::find($bookingTypeId);
            if ($bookingType) {
                $slots = $slots->filter(
                    fn ($slot) => $bookingType->isAvailableAtTime($slot->start_at, $customerId, $depotId)
                )->values();
            }
        }

        return $slots;
    }

    /**
     * Show rebooking form
     */
    public function showRebook(Booking $booking)
    {
        \Log::info('CustomerBookingController::showRebook called for booking ID: '.$booking->id);

        // Temporarily disable authorization for testing
        // $this->authorize('update', $booking);

        // Allow rebooking even after arrival for rejection cases
        // if ($booking->arrived_at) {
        //     return redirect()->route('customer.bookings.show', $booking)
        //         ->with('error', 'Cannot rebook a booking that has already arrived.');
        // }

        // Temporarily disable these checks until migrations are run
        // if ($booking->isCancelled()) {
        //     return redirect()->route('customer.bookings.show', $booking)
        //         ->with('error', 'Cannot rebook a cancelled booking.');
        // }

        // Get max rebooks setting
        $maxRebooksPerBooking = \App\Models\CustomerBehaviorSetting::getCustomerSetting(
            $booking->customer_id,
            'max_rebooks_per_booking',
            3
        );

        // Check if this booking chain has reached max rebooks (skip if column doesn't exist)
        try {
            if (isset($booking->rebook_count) && $booking->rebook_count >= $maxRebooksPerBooking) {
                return redirect()->route('customer.bookings.show', $booking)
                    ->with('error', "Maximum number of rebooks ({$maxRebooksPerBooking}) reached for this booking chain.");
            }
        } catch (\Exception $e) {
            // Column doesn't exist yet, continue
        }

        // Get customer behavior data (simplified for now)
        try {
            $customerBehaviorData = $this->getCustomerBehaviorData($booking->customer_id);
        } catch (\Exception $e) {
            // If there's an error, use empty data
            $customerBehaviorData = [
                'recent_rebooks' => 0,
                'recent_cancellations' => 0,
                'last_minute_actions' => 0,
            ];
        }

        return view('customer.bookings.rebook', [
            'booking' => $booking,
            'customerBehaviorData' => $customerBehaviorData,
            'maxRebooksPerBooking' => $maxRebooksPerBooking,
        ]);
    }

    /**
     * Process rebooking
     */
    public function rebook(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);

        // Allow rebooking even after arrival for rejection cases
        // if ($booking->arrived_at) {
        //     return response()->json(['success' => false, 'message' => 'Cannot rebook a booking that has already arrived.'], 400);
        // }

        if ($booking->isCancelled()) {
            return response()->json(['success' => false, 'message' => 'Cannot rebook a cancelled booking.'], 400);
        }

        $request->validate([
            'new_slot_id' => 'required|exists:slots,id',
            'reason' => 'required|string|max:255',
        ]);

        $newSlot = Slot::findOrFail($request->new_slot_id);

        // Verify user has access to this depot
        $user = auth()->user();
        if (! $user->depots->contains('id', $newSlot->depot_id)) {
            return response()->json(['success' => false, 'message' => 'You do not have access to the depot for this slot.'], 403);
        }

        // Check if slot is available (only active bookings matter)
        if ($newSlot->bookings()->whereNull('cancelled_at')->exists()) {
            return response()->json(['success' => false, 'message' => 'Selected slot is no longer available.'], 400);
        }

        // Check if slot is locked
        if ($newSlot->locked_at && $newSlot->locked_at->isPast()) {
            return response()->json(['success' => false, 'message' => 'Selected slot is locked and cannot be booked.'], 400);
        }

        try {
            $newBooking = $booking->rebook($newSlot, $request->reason);

            return response()->json([
                'success' => true,
                'message' => 'Booking rebooked successfully!',
                'redirect' => route('customer.bookings.show', $newBooking),
            ]);
        } catch (\Exception $e) {
            \Log::error('Customer rebooking error: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Failed to rebook: '.$e->getMessage()], 500);
        }
    }

    /**
     * Cancel booking
     */
    public function cancel(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);

        // Allow cancellation even after arrival for rejection cases
        // if ($booking->arrived_at) {
        //     return response()->json(['success' => false, 'message' => 'Cannot cancel a booking that has already arrived.'], 400);
        // }

        if ($booking->isCancelled()) {
            return response()->json(['success' => false, 'message' => 'Booking is already cancelled.'], 400);
        }

        if ($booking->departed_at) {
            return response()->json(['success' => false, 'message' => 'Cannot cancel a booking that has already departed.'], 400);
        }

        $request->validate([
            'cancellation_reason' => 'required|string|max:255',
        ]);

        try {
            DB::transaction(function () use ($booking, $request) {
                // Release occupied slots
                $booking->occupiedSlots()->detach();

                // Mark booking as cancelled
                $booking->update([
                    'cancelled_at' => now(),
                    'cancellation_reason' => $request->cancellation_reason,
                ]);

                // Record in history
                BookingHistory::recordAction(
                    $booking,
                    'cancelled',
                    $request->cancellation_reason
                );
            });

            return response()->json(['success' => true, 'message' => 'Booking cancelled successfully!']);
        } catch (\Exception $e) {
            \Log::error('Customer booking cancellation error: '.$e->getMessage());

            return response()->json(['success' => false, 'message' => 'Failed to cancel booking: '.$e->getMessage()], 500);
        }
    }

    /**
     * Show booking history
     */
    public function history(Booking $booking)
    {
        $this->authorize('view', $booking);

        try {
            // Check if booking_history table exists
            if (! \Schema::hasTable('booking_history')) {
                throw new \Exception('Booking history table does not exist yet');
            }

            // Get complete history for the entire booking chain, but filter intelligently
            $bookingIds = [$booking->id];

            // Get all bookings in the chain (both predecessors and successors)
            $originalBookingId = $booking->original_booking_id ?: $booking->id;
            $chainBookings = Booking::where('original_booking_id', $originalBookingId)
                ->orWhere('id', $originalBookingId)
                ->pluck('id')
                ->toArray();

            $bookingIds = array_unique(array_merge($bookingIds, $chainBookings));

            $allHistory = BookingHistory::whereIn('booking_id', $bookingIds)
                ->with(['booking.slot.depot', 'originalSlot.depot', 'newSlot.depot', 'user', 'customer'])
                ->orderBy('created_at', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            // Filter to show clean timeline: only first creation + all other actions
            $history = collect();
            $creationShown = false;

            foreach ($allHistory as $item) {
                if ($item->action === 'created') {
                    // Only show the very first creation entry
                    if (! $creationShown) {
                        $history->push($item);
                        $creationShown = true;
                    }
                    // Skip all subsequent creation entries
                } else {
                    // Show ALL non-creation actions (rebooks, cancellations, etc.)
                    $history->push($item);
                }
            }
            $originalCreationShown = $creationShown;

            // Apply smart chronological sorting after filtering
            $history = $history->sortBy([
                function ($item) {
                    // Primary sort: timestamp, but ensure created actions are always first
                    $timestamp = $item->created_at->timestamp;
                    if ($item->action === 'created') {
                        $timestamp -= 3600; // Subtract 1 hour to ensure created comes first
                    }

                    return $timestamp;
                },
                function ($item) {
                    // Secondary sort: action priority for same timestamp
                    $order = ['created' => 1, 'rebooked' => 2, 'cancelled' => 3];

                    return $order[$item->action] ?? 4;
                },
            ]);

            // Convert back to collection with preserved order
            $history = collect($history->values());

            // If no history records exist OR no creation was found, show clean timeline from booking data
            if ($history->isEmpty() || ! $originalCreationShown) {
                $history = collect();

                // Find the original booking in the chain
                $originalBookingId = $booking->original_booking_id ?: $booking->id;
                $originalBooking = $booking;
                if ($booking->original_booking_id) {
                    $originalBooking = Booking::find($originalBookingId) ?: $booking;
                }

                // Add original creation entry if not already present
                if (! $originalCreationShown) {
                    $history->push((object) [
                        'id' => 'creation-entry',
                        'action' => 'created',
                        'reason' => 'Initial booking created',
                        'created_at' => $originalBooking->created_at,
                        'user' => null, // Hide user details from customers for GDPR compliance
                        'booking' => $originalBooking,
                        'originalSlot' => null,
                        'newSlot' => $originalBooking->slot,
                        'hours_before_slot' => null,
                        'is_last_minute' => false,
                        'customer_rebook_count_30days' => 0,
                        'customer_cancel_count_30days' => 0,
                        'changes' => null,
                    ]);
                }

                // Add rebook entry if this is a rebooked booking
                if ($booking->original_booking_id && $booking->id != $originalBookingId) {
                    $history->push((object) [
                        'id' => 'rebook-entry',
                        'action' => 'rebooked',
                        'reason' => $booking->rebook_reason ?? 'Booking rebooked',
                        'created_at' => $booking->created_at,
                        'user' => null,
                        'booking' => $booking,
                        'originalSlot' => $originalBooking->slot,
                        'newSlot' => $booking->slot,
                        'hours_before_slot' => null,
                        'is_last_minute' => false,
                        'customer_rebook_count_30days' => 0,
                        'customer_cancel_count_30days' => 0,
                        'changes' => null,
                    ]);
                }

                // Add cancellation if applicable
                if ($booking->cancelled_at) {
                    $history->push((object) [
                        'id' => 'cancel-entry',
                        'action' => 'cancelled',
                        'reason' => $booking->cancellation_reason ?: 'Booking cancelled',
                        'created_at' => $booking->cancelled_at,
                        'user' => null,
                        'booking' => $booking,
                        'originalSlot' => $booking->slot,
                        'newSlot' => null,
                        'hours_before_slot' => null,
                        'is_last_minute' => false,
                        'customer_rebook_count_30days' => 0,
                        'customer_cancel_count_30days' => 0,
                        'changes' => null,
                    ]);
                }
            }

        } catch (\Exception $e) {
            // If booking_history table doesn't exist or is incomplete, show a placeholder
            \Log::warning('Booking history error: '.$e->getMessage());

            // Create clean chronological history from booking chain data
            $history = collect([]);

            // Find the original booking in the chain
            $originalBookingId = $booking->original_booking_id ?: $booking->id;
            $originalBooking = $booking;
            if ($booking->original_booking_id) {
                try {
                    $originalBooking = Booking::find($originalBookingId) ?: $booking;
                } catch (\Exception $e) {
                    $originalBooking = $booking;
                }
            }

            // Always add original creation entry first
            $history->push((object) [
                'id' => 'placeholder-creation',
                'action' => 'created',
                'reason' => 'Initial booking created',
                'created_at' => $originalBooking->created_at,
                'user' => null, // Hide user details from customers for GDPR compliance
                'booking' => $originalBooking,
                'originalSlot' => null,
                'newSlot' => $originalBooking->slot,
                'hours_before_slot' => null,
                'is_last_minute' => false,
                'customer_rebook_count_30days' => 0,
                'customer_cancel_count_30days' => 0,
                'changes' => null,
            ]);

            // Add rebook entry if this is a rebooked booking
            if ($booking->original_booking_id && $booking->id != $originalBookingId) {
                $history->push((object) [
                    'id' => 'placeholder-rebook',
                    'action' => 'rebooked',
                    'reason' => $booking->rebook_reason ?? 'Booking rebooked',
                    'created_at' => $booking->created_at,
                    'user' => null,
                    'booking' => $booking,
                    'originalSlot' => $originalBooking->slot,
                    'newSlot' => $booking->slot,
                    'hours_before_slot' => null,
                    'is_last_minute' => false,
                    'customer_rebook_count_30days' => 0,
                    'customer_cancel_count_30days' => 0,
                    'changes' => null,
                ]);
            }

            // Add cancellation if current booking is cancelled
            if ($booking->cancelled_at) {
                $history->push((object) [
                    'id' => 'placeholder-cancel',
                    'action' => 'cancelled',
                    'reason' => $booking->cancellation_reason ?: 'Booking cancelled',
                    'created_at' => $booking->cancelled_at,
                    'user' => null,
                    'booking' => $booking,
                    'originalSlot' => $booking->slot,
                    'newSlot' => null,
                    'hours_before_slot' => null,
                    'is_last_minute' => false,
                    'customer_rebook_count_30days' => 0,
                    'customer_cancel_count_30days' => 0,
                    'changes' => null,
                ]);
            }

            // Sort chronologically (oldest to newest)
            $history = $history->sortBy(function ($item) {
                return $item->created_at->timestamp;
            });

            // Convert back to collection with preserved order
            $history = collect($history->values());
        }

        // Calculate actual rebook count from history
        $actualRebookCount = $history->where('action', 'rebooked')->count();

        // Calculate actual rebook count from filtered history
        $actualRebookCount = $history->where('action', 'rebooked')->count();

        // Handle sort order toggle with smart positioning of created
        $sortOrder = request('sort', 'asc'); // Default to ascending (oldest first)
        if ($sortOrder === 'desc') {
            // For descending order, we need to re-sort to put created last instead of first
            $history = $history->sortByDesc([
                function ($item) {
                    // Primary sort: timestamp, but ensure created actions are always last in desc order
                    $timestamp = $item->created_at->timestamp;
                    if ($item->action === 'created') {
                        $timestamp += 3600; // Add 1 hour to ensure created comes last
                    }

                    return $timestamp;
                },
                function ($item) {
                    // Secondary sort: action priority for same timestamp (reversed)
                    $order = ['cancelled' => 1, 'rebooked' => 2, 'created' => 3];

                    return $order[$item->action] ?? 4;
                },
            ]);
            $history = collect($history->values());
        }

        // Get max rebooks setting
        $maxRebooksPerBooking = \App\Models\CustomerBehaviorSetting::getCustomerSetting(
            $booking->customer_id,
            'max_rebooks_per_booking',
            3
        );

        return view('customer.bookings.history', [
            'booking' => $booking,
            'history' => $history,
            'sortOrder' => $sortOrder,
            'actualRebookCount' => $actualRebookCount,
            'maxRebooksPerBooking' => $maxRebooksPerBooking,
        ]);
    }

    /**
     * Get available slots for rebooking
     */
    /**
     * Get customer behavior data for warnings
     */
    private function getCustomerBehaviorData($customerId)
    {
        $thirtyDaysAgo = now()->subDays(30);

        return [
            'recent_rebooks' => BookingHistory::where('customer_id', $customerId)
                ->where('action', 'rebooked')
                ->where('created_at', '>=', $thirtyDaysAgo)
                ->count(),
            'recent_cancellations' => BookingHistory::where('customer_id', $customerId)
                ->where('action', 'cancelled')
                ->where('reason', 'NOT LIKE', '%Rebooked%')
                ->where('created_at', '>=', $thirtyDaysAgo)
                ->distinct('booking_id')
                ->count('booking_id'),
            'last_minute_actions' => BookingHistory::where('customer_id', $customerId)
                ->whereIn('action', ['rebooked', 'cancelled'])
                ->where('is_last_minute', true)
                ->where('created_at', '>=', $thirtyDaysAgo)
                ->count(),
        ];
    }

    /**
     * Get filtered factory bookings for customer
     */
    private function filteredFactoryBookings(Request $request)
    {
        $user = auth()->user();
        $accessibleCustomerIds = $user->getAccessibleCustomerIds();
        $accessibleDepotIds = $user->depots->pluck('id')->toArray();

        $query = FactoryBooking::with(['depot', 'customer', 'carrier'])
            ->whereIn('customer_id', $accessibleCustomerIds)
            ->whereNotNull('customer_id');

        // Depot filter
        if ($depotId = $request->input('depot_id')) {
            $query->where('depot_id', $depotId);
        } else {
            // Restrict to user's accessible depots
            $query->whereIn('depot_id', $accessibleDepotIds);
        }

        // Date range filters - factory bookings use arrived_at instead of slot dates
        if ($from = $request->input('from')) {
            $query->whereDate('arrived_at', '>=', $from);
        }
        if ($to = $request->input('to')) {
            $query->whereDate('arrived_at', '<=', $to);
        }

        // Quick date filters
        if ($quickFilter = $request->input('filter') ?: $request->input('quick_filter')) {
            if ($quickFilter === 'today') {
                $query->whereDate('arrived_at', Carbon::today());
            } elseif ($quickFilter === 'tomorrow') {
                $query->whereDate('arrived_at', Carbon::tomorrow());
            } elseif ($quickFilter === 'last_week') {
                $query->whereBetween('arrived_at', [
                    Carbon::now()->subWeek()->startOfWeek(),
                    Carbon::now()->subWeek()->endOfWeek(),
                ]);
            } elseif ($quickFilter === 'this_week') {
                $query->whereBetween('arrived_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek(),
                ]);
            } elseif ($quickFilter === 'next_week') {
                $query->whereBetween('arrived_at', [
                    Carbon::now()->addWeek()->startOfWeek(),
                    Carbon::now()->addWeek()->endOfWeek(),
                ]);
            }
        }

        // Week number filter
        if ($weekNumber = $request->input('week_number')) {
            $year = Carbon::now()->year;
            $weekStart = Carbon::now()->setISODate($year, $weekNumber)->startOfWeek();
            $weekEnd = $weekStart->clone()->endOfWeek();
            $query->whereBetween('arrived_at', [$weekStart, $weekEnd]);
        }

        // Arrival status filter - factory bookings don't have the same statuses, so adapt
        if ($arrival = $request->input('arrival')) {
            if ($arrival === 'arrived') {
                $query->whereNotNull('arrived_at');
            } elseif ($arrival === 'onsite') {
                $query->whereNotNull('arrived_at')->whereNull('completed_at');
            } elseif ($arrival === 'completed') {
                $query->whereNotNull('completed_at');
            }
            // Skip other filters that don't apply to factory bookings
        }

        return $query;
    }

    /**
     * Combine regular and factory bookings and sort them
     */
    private function combineAndSortBookings($regularBookingsQuery, $factoryBookingsQuery, Request $request)
    {
        // Get the actual collections
        $regularBookings = $regularBookingsQuery->get();
        $factoryBookings = $factoryBookingsQuery->get();

        // Transform factory bookings to look like regular bookings for consistency
        $transformedFactoryBookings = $factoryBookings->map(function ($factoryBooking) {
            // Create a pseudo-slot object for factory bookings
            $pseudoSlot = (object) [
                'id' => null,
                'start_at' => $factoryBooking->arrived_at,
                'end_at' => $factoryBooking->completed_at ?? $factoryBooking->arrived_at->copy()->addHour(),
                'depot' => $factoryBooking->depot,
            ];

            return (object) [
                'id' => $factoryBooking->id,
                'booking_reference' => $factoryBooking->reference,
                'reference' => $factoryBooking->reference,
                'slot' => $pseudoSlot,
                'bookingType' => (object) ['name' => 'Factory Delivery'],
                'customer' => $factoryBooking->customer,
                'vehicle_registration' => $factoryBooking->vehicle_registration,
                'trailer_registration' => $factoryBooking->trailer_registration,
                'container_number' => null,
                'arrived_at' => $factoryBooking->arrived_at,
                'departed_at' => $factoryBooking->completed_at,
                'cancelled_at' => null,
                'cancellation_reason' => null,
                'status' => $factoryBooking->status,
                'estimated_arrival' => null,
                'poNumbers' => collect(), // Factory bookings don't have PO structure like regular bookings
                'type' => 'factory', // Mark as factory booking
                'original_factory_booking' => $factoryBooking, // Keep reference to original
            ];
        });

        // Combine both collections
        $combined = $regularBookings->concat($transformedFactoryBookings);

        // Sort by start_at (regular bookings) or arrived_at (factory bookings)
        $combined = $combined->sortByDesc(function ($booking) {
            if (isset($booking->type) && $booking->type === 'factory') {
                return $booking->slot->start_at->timestamp;
            }
            return $booking->slot->start_at->timestamp;
        });

        return $combined->values();
    }

    /**
     * Paginate combined bookings collection
     */
    private function paginateCombinedBookings($combinedBookings, Request $request, $perPage = 20)
    {
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $perPage;
        
        $items = $combinedBookings->slice($offset, $perPage)->values();
        
        return new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $combinedBookings->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'pageName' => 'page',
                'query' => $request->query(),
            ]
        );
    }
}
