<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Http\Traits\NormalizeInputTrait;
use App\Models\Booking;
use App\Models\BookingType;
use App\Models\Carrier;
use App\Models\Customer;
use App\Models\Depot;
use App\Models\FactoryBooking;
use App\Models\Movement;
use App\Models\Slot;
use App\Models\TippingBay;
use App\Models\TippingLocation;
use App\Models\TrailerType;
use App\Services\PDFService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    use NormalizeInputTrait;
    
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin|depot-admin|site-admin|warehouse']);
    }

    /**
     * Get depot IDs that the current user can access based on their role
     */
    private function getAllowedDepotIds()
    {
        $user = auth()->user();

        // All roles (including admin) respect depot pivot assignments
        $allowedDepotIds = $user->depots()->pluck('depots.id')->toArray();

        // If no depots assigned, they can't access any (return impossible condition)
        if (empty($allowedDepotIds)) {
            return [0]; // No depot will have ID 0
        }

        return $allowedDepotIds;
    }

    /**
     * Get depots that the current user can access based on their role
     */
    private function getAllowedDepots()
    {
        $user = auth()->user();

        // All roles (including admin) respect depot pivot assignments
        return $user->depots()->orderBy('name')->get();
    }

    /**
     * Get customers that the current user can access based on their role
     */
    private function getAllowedCustomers()
    {
        $user = auth()->user();
        $allowedCustomerIds = $this->getAllowedCustomerIds();
        
        if ($allowedCustomerIds === null) {
            // User can access all customers
            return Customer::orderBy('name')->get();
        }
        
        // User has restricted customer access
        return Customer::whereIn('id', $allowedCustomerIds)->orderBy('name')->get();
    }

    /**
     * Check if user has depot access, throw 403 if not
     */
    private function ensureDepotAccess()
    {
        $user = auth()->user();

        if (! $user->hasRole('admin')) {
            $isDepotUser = DB::table('depot_user')->where('user_id', $user->id)->exists();
            if (! $isDepotUser) {
                abort(403, 'Unauthorized access — you are not linked to any depot.');
            }
        }
    }

    /**
     * Get customer IDs that the current user can access based on their role and assignments
     * Returns null if user can see all customers, or array of specific customer IDs
     */
    private function getAllowedCustomerIds()
    {
        $user = auth()->user();

        // Check if user has specific customer assignments
        $assignedCustomerIds = $user->customers()->pluck('customers.id')->toArray();

        if ($user->hasRole('admin')) {
            // Admin: show all customers ONLY if no customers assigned in pivot
            if (! empty($assignedCustomerIds)) {
                // If specific customers assigned, filter to those only
                return $assignedCustomerIds;
            }

            // If no customer assignments, admin can see all customers (return null)
            return null;
        }

        if ($user->hasRole('customer')) {
            // Customer role: only their assigned customers
            return empty($assignedCustomerIds) ? [0] : $assignedCustomerIds;
        }

        // Depot-admin/site-admin: if they have customer assignments, filter to those
        // If no assignments, they can see all customers (return null)
        return empty($assignedCustomerIds) ? null : $assignedCustomerIds;
    }

    /**
     * Store current filters in session for persistence across actions
     */
    private function storeFiltersInSession(Request $request)
    {
        $filters = $request->only([
            'depot_id', 'customer_id', 'booking_type_id', 'from', 'to',
            'arrival', 'status', 'quick_filter', 'filter', 'week_number', 'year', 'search',
        ]);

        // Remove empty filters
        $filters = array_filter($filters, function ($value) {
            return $value !== null && $value !== '';
        });

        if (! empty($filters)) {
            $request->session()->put('booking_filters', $filters);
        }
    }

    /**
     * Get filters from session
     */
    private function getFiltersFromSession(Request $request)
    {
        return $request->session()->get('booking_filters', []);
    }

    /**
     * Redirect back to bookings index with stored filters
     */
    private function redirectWithFilters(Request $request, $message)
    {
        $filters = $this->getFiltersFromSession($request);

        return redirect()->route('app.bookings.index', $filters)->with('success', $message);
    }

    public function index(Request $request)
    {
        $this->ensureDepotAccess();
        $allowedDepotIds = $this->getAllowedDepotIds();
        $allowedCustomerIds = $this->getAllowedCustomerIds();

        // Apply filters from session if none provided
        $sessionFilters = $this->getFiltersFromSession($request);
        if (! $request->hasAny(['depot_id', 'customer_id', 'booking_type_id', 'from', 'to', 'arrival', 'quick_filter', 'filter', 'week_number', 'year', 'search', 'status']) && ! empty($sessionFilters)) {
            $request->merge($sessionFilters);
        }
        
        // Set default filters if no session filters and no parameters provided
        if (empty($sessionFilters) && !$request->hasAny(['depot_id', 'customer_id', 'booking_type_id', 'from', 'to', 'arrival', 'quick_filter', 'filter', 'week_number', 'year', 'search', 'status'])) {
            $request->merge([
                'filter' => 'today',
                'status' => 'outstanding'
            ]);
        }
        
        // If no status is specified, default to outstanding
        if (!$request->has('status')) {
            $request->merge(['status' => 'outstanding']);
        }

        // Store current filters in session for persistence
        $this->storeFiltersInSession($request);

        // Base query restricted to user's depots - sort by slot time (oldest first)
        $query = Booking::with([
            'slot.depot',
            'bookingType',
            'customer',
            'movements.tippingLocation',
            'movements.tippingBay',
            'poNumbers.lines.expectedPalletType',
            'poNumbers.lines.actualPalletType',
        ])
            ->whereHas('slot', fn ($q) => $q->whereIn('depot_id', $allowedDepotIds))
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

        // Apply customer filtering if user has restricted access
        if ($allowedCustomerIds !== null) {
            $query->whereIn('customer_id', $allowedCustomerIds)->whereNotNull('customer_id');
        }

        // Depot filter
        if ($depotId = $request->input('depot_id')) {
            $query->whereHas('slot', fn ($q) => $q->where('depot_id', $depotId));
        }

        // Customer filter
        if ($customerId = $request->input('customer_id')) {
            $query->where('customer_id', $customerId);
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
            } elseif ($quickFilter === 'yesterday') {
                $query->whereHas('slot', function ($q) {
                    $q->whereDate('start_at', Carbon::yesterday());
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
            if ($year = $request->input('year', Carbon::now()->year)) {
                $startOfWeek = Carbon::now()->setISODate($year, $weekNumber)->startOfWeek();
                $endOfWeek = $startOfWeek->clone()->endOfWeek();

                $query->whereHas('slot', function ($q) use ($startOfWeek, $endOfWeek) {
                    $q->whereBetween('start_at', [$startOfWeek, $endOfWeek]);
                });
            }
        }

        // Date filters (existing functionality)
        if ($from = $request->input('from')) {
            if (! $to = $request->input('to')) {
                $query->whereHas('slot', function ($q) use ($from) {
                    $q->whereDate('start_at', $from);
                });
            } else {
                $query->whereHas('slot', function ($q) use ($from, $to) {
                    $q->whereBetween('start_at', [$from, $to]);
                });
            }
        }

        // Arrival filter - expanded options
        if ($arr = $request->arrival) {
            if ($arr === 'not_arrived') {
                $query->whereNull('arrived_at');
            } elseif ($arr === 'arrived') {
                $query->whereNotNull('arrived_at');
            } elseif ($arr === 'onsite') {
                $query->whereNotNull('arrived_at')->whereDoesntHave('movements', function ($q) {
                    $q->whereNotNull('trailer_collected_at');
                });
            } elseif ($arr === 'completed') {
                $query->whereNotNull('arrived_at')->whereNotNull('departed_at');
            } elseif ($arr === 'late_runners') {
                $query->whereNull('arrived_at')
                    ->whereHas('slot', function ($q) {
                        $q->where('start_at', '<', Carbon::now());
                    });
            } elseif ($arr === 'on_time') {
                $query->whereNotNull('arrived_at')
                    ->whereRaw('arrived_at <= (SELECT start_at FROM slots WHERE slots.id = bookings.slot_id)');
            } elseif ($arr === 'arrived_late') {
                $query->whereNotNull('arrived_at')
                    ->whereRaw('arrived_at > (SELECT start_at FROM slots WHERE slots.id = bookings.slot_id)');
            }
        }

        // Quick search functionality - search across multiple fields
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('booking_reference', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('movements', function ($q) use ($search) {
                      $q->where('additional_data->vehicle_registration', 'like', "%{$search}%")
                        ->orWhere('additional_data->container_number', 'like', "%{$search}%")
                        ->orWhere('additional_data->carrier_company', 'like', "%{$search}%");
                  })
                  // Search in booking vehicle_details JSON field
                  ->orWhere('vehicle_details->vehicle_registration', 'like', "%{$search}%")
                  ->orWhere('vehicle_details->container_number', 'like', "%{$search}%")
                  ->orWhere('vehicle_details->carrier_company', 'like', "%{$search}%");
            });
        }
        
        // Status filter - improved default behavior
        $statusFilter = $request->input('status', 'outstanding'); // Default to outstanding
        if ($statusFilter === 'all') {
            // Show all statuses - no additional filter
        } elseif ($statusFilter === 'outstanding') {
            // Show only outstanding bookings (not completed/cancelled/departed)
            $query->where(function ($q) {
                $q->whereNull('departed_at')
                  ->whereNotIn('status', ['completed', 'cancelled'])
                  ->whereNull('cancelled_at');
            });
        } elseif ($statusFilter === 'completed') {
            // Show completed bookings
            $query->where(function ($q) {
                $q->whereNotNull('departed_at')
                  ->orWhere('status', 'completed');
            });
        } elseif ($statusFilter) {
            // Filter by specific status
            $query->where('status', $statusFilter);
        }

        // Get regular bookings as collection
        $regularBookings = $query->get();

        // Get factory bookings with similar filtering
        $factoryBookingsQuery = FactoryBooking::with(['depot', 'customer', 'carrier', 'movements', 'poNumbers.lines.expectedPalletType', 'poNumbers.lines.actualPalletType'])
            ->whereIn('depot_id', $allowedDepotIds);
        
        if ($allowedCustomerIds !== null) {
            $factoryBookingsQuery->whereIn('customer_id', $allowedCustomerIds)->whereNotNull('customer_id');
        }
        
        // Apply same filters as regular bookings
        if ($depotId = $request->input('depot_id')) {
            $factoryBookingsQuery->where('depot_id', $depotId);
        }
        
        if ($customerId = $request->input('customer_id')) {
            $factoryBookingsQuery->where('customer_id', $customerId);
        }
        
        // Apply date filters (factory bookings use arrived_at instead of slot dates)
        if ($quickFilter = $request->input('filter') ?: $request->input('quick_filter')) {
            if ($quickFilter === 'today') {
                $factoryBookingsQuery->whereDate('arrived_at', Carbon::today());
            } elseif ($quickFilter === 'yesterday') {
                $factoryBookingsQuery->whereDate('arrived_at', Carbon::yesterday());
            } elseif ($quickFilter === 'tomorrow') {
                $factoryBookingsQuery->whereDate('arrived_at', Carbon::tomorrow());
            } elseif ($quickFilter === 'this_week') {
                $factoryBookingsQuery->whereBetween('arrived_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek(),
                ]);
            } elseif ($quickFilter === 'next_week') {
                $factoryBookingsQuery->whereBetween('arrived_at', [
                    Carbon::now()->addWeek()->startOfWeek(),
                    Carbon::now()->addWeek()->endOfWeek(),
                ]);
            } elseif ($quickFilter === 'last_week') {
                $factoryBookingsQuery->whereBetween('arrived_at', [
                    Carbon::now()->subWeek()->startOfWeek(),
                    Carbon::now()->subWeek()->endOfWeek(),
                ]);
            }
        }

        // Date range filters
        if ($from = $request->input('from')) {
            $factoryBookingsQuery->whereDate('arrived_at', '>=', $from);
        }
        if ($to = $request->input('to')) {
            $factoryBookingsQuery->whereDate('arrived_at', '<=', $to);
        }

        // Week number filter
        if ($weekNumber = $request->input('week_number')) {
            $year = $request->input('year') ?: Carbon::now()->year;
            $weekStart = Carbon::now()->setISODate($year, $weekNumber)->startOfWeek();
            $weekEnd = $weekStart->clone()->endOfWeek();
            $factoryBookingsQuery->whereBetween('arrived_at', [$weekStart, $weekEnd]);
        }

        // Search filter
        if ($search = $request->input('search')) {
            $factoryBookingsQuery->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('vehicle_registration', 'like', "%{$search}%")
                  ->orWhere('trailer_registration', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter adaptation for factory bookings
        if ($statusFilter = $request->input('status')) {
            if ($statusFilter === 'outstanding') {
                // Factory bookings that haven't completed yet
                $factoryBookingsQuery->whereNull('completed_at');
            } elseif ($statusFilter === 'completed') {
                // Factory bookings that are completed
                $factoryBookingsQuery->whereNotNull('completed_at');
            }
            // For other statuses, factory bookings might not apply, so we can exclude them or adapt
        }

        $factoryBookings = $factoryBookingsQuery->get();

        // Transform factory bookings to look like regular bookings for consistent display
        $transformedFactoryBookings = $factoryBookings->map(function ($factoryBooking) {
            // Create a pseudo-slot object for factory bookings
            $pseudoSlot = (object) [
                'id' => null,
                'start_at' => $factoryBooking->arrived_at,
                'end_at' => $factoryBooking->completed_at ?? $factoryBooking->arrived_at->copy()->addHour(),
                'depot' => $factoryBooking->depot,
                'depot_id' => $factoryBooking->depot_id,
            ];

            // Calculate PO totals similar to Booking model accessors
            $totalExpectedCases = $factoryBooking->poNumbers->sum(function ($po) {
                return $po->total_expected_cases;
            });
            $totalActualCases = $factoryBooking->poNumbers->sum(function ($po) {
                return $po->total_actual_cases;
            });
            $totalExpectedPallets = $factoryBooking->poNumbers->sum(function ($po) {
                return $po->total_expected_pallets;
            });
            $totalActualPallets = $factoryBooking->poNumbers->sum(function ($po) {
                return $po->total_actual_pallets;
            });

            return (object) [
                'id' => $factoryBooking->id,
                'booking_reference' => $factoryBooking->reference,
                'reference' => $factoryBooking->reference,
                'slot' => $pseudoSlot,
                'bookingType' => (object) ['name' => 'Factory Delivery', 'id' => null],
                'customer' => $factoryBooking->customer,
                'customer_id' => $factoryBooking->customer_id,
                'vehicle_registration' => $factoryBooking->vehicle_registration,
                'trailer_registration' => $factoryBooking->trailer_registration,
                'container_number' => null,
                'arrived_at' => $factoryBooking->arrived_at,
                'departed_at' => $factoryBooking->completed_at,
                'cancelled_at' => null,
                'cancellation_reason' => null,
                'status' => $factoryBooking->status,
                'estimated_arrival' => null,
                'poNumbers' => $factoryBooking->poNumbers, // Factory bookings have PO numbers too
                // Get factory booking movements for proper status
                'movements' => $factoryBooking->movements,
                'type' => 'factory', // Mark as factory booking
                'original_factory_booking' => $factoryBooking, // Keep reference to original
                'tipping_status' => $this->getFactoryBookingTippingStatus($factoryBooking),
                'special_instructions' => $factoryBooking->delivery_notes,
                // Add PO total properties that the view expects
                'total_expected_cases' => $totalExpectedCases,
                'total_actual_cases' => $totalActualCases,
                'total_case_variance' => $totalActualCases - $totalExpectedCases,
                'total_expected_pallets' => $totalExpectedPallets,
                'total_actual_pallets' => $totalActualPallets,
                'total_pallet_variance' => $totalActualPallets - $totalExpectedPallets,
                // Add location properties for factory bookings
                'current_location' => $factoryBooking->completed_at ? 'departed' : 'on_site',
                'waiting_area_location' => null,
                'tipping_bay_id' => null,
                'trailer_left_on_site' => false, // Factory bookings don't typically leave trailers
                'trailer_collected_at' => $factoryBooking->completed_at, // Same as completion for factory bookings
                'tipping_status_badge' => $factoryBooking->tipping_status_badge, // Use factory booking's own badge
            ];
        });

        // Combine both collections
        $combinedBookings = $regularBookings->concat($transformedFactoryBookings);

        // Sort by slot start time (regular bookings) or arrived_at (factory bookings)
        $combinedBookings = $combinedBookings->sortByDesc(function ($booking) {
            return $booking->slot->start_at->timestamp;
        });

        // Paginate the combined collection manually
        $currentPage = $request->input('page', 1);
        $perPage = 30;
        $offset = ($currentPage - 1) * $perPage;
        
        $paginatedBookings = $combinedBookings->slice($offset, $perPage)->values();
        
        $bookings = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedBookings,
            $combinedBookings->count(),
            $perPage,
            $currentPage,
            [
                'path' => $request->url(),
                'pageName' => 'page',
                'query' => $request->only(['depot_id', 'customer_id', 'booking_type_id', 'from', 'to', 'arrival', 'status', 'quick_filter', 'filter', 'week_number', 'year', 'search']),
            ]
        );

        // Load needed data for filters in view
        $depots = $this->getAllowedDepots();

        // Filter customers based on user permissions
        if ($allowedCustomerIds !== null) {
            $customers = Customer::whereIn('id', $allowedCustomerIds)->orderBy('name')->get();
        } else {
            $customers = Customer::orderBy('name')->get();
        }

        $types = BookingType::orderBy('name')->get();

        // Build summary by depot and customer using filtered bookings
        $filteredBookings = (clone $query)->get();

        $summaryByDepotCustomer = [];
        foreach ($filteredBookings as $b) {
            $dn = $b->slot->depot->name;
            $cn = $b->customer ? $b->customer->name : 'No Customer';
            $data = &$summaryByDepotCustomer[$dn][$cn];
            if (! isset($data)) {
                $data = [
                    'arrived' => 0, 'late' => 0, 'outstanding' => 0,
                    'expected_cases' => 0, 'actual_cases' => 0, 'case_variance' => 0,
                    'expected_pallets' => 0, 'actual_pallets' => 0, 'pallet_variance' => 0,
                    'late_duration_minutes' => 0, // total late minutes for depot/customer group
                ];
            }
            $data['expected_cases'] += $b->expected_cases ?? 0;
            $data['actual_cases'] += $b->actual_cases ?? 0;
            $data['expected_pallets'] += $b->expected_pallets ?? 0;
            $data['actual_pallets'] += $b->actual_pallets ?? 0;

            $now = Carbon::now();
            $slotStart = Carbon::parse($b->slot->start_at);

            if ($b->arrived_at) {
                $data['arrived']++;

                $arrivedAt = Carbon::parse($b->arrived_at);

                // Check if arrived late
                if ($arrivedAt->gt($slotStart)) {
                    $data['late']++;
                    // Calculate late duration (minutes) from slot start to arrival
                    $lateMinutes = $arrivedAt->diffInMinutes($slotStart);
                    $data['late_duration_minutes'] += $lateMinutes;
                }
            } else {
                // Not arrived yet
                if ($now->gt($slotStart)) {
                    // Late (not arrived but past slot start)
                    $data['late']++;

                    // Calculate late duration from slot start to now
                    $lateMinutes = $now->diffInMinutes($slotStart);
                    $data['late_duration_minutes'] += $lateMinutes;
                }
                $data['outstanding']++;
            }
        }

        // Compute variances and depot totals
        foreach ($summaryByDepotCustomer as $dn => $custs) {
            $totals = [
                'arrived' => 0, 'late' => 0, 'outstanding' => 0,
                'expected_cases' => 0, 'actual_cases' => 0, 'case_variance' => 0,
                'expected_pallets' => 0, 'actual_pallets' => 0, 'pallet_variance' => 0,
            ];
            foreach ($custs as $cn => $sum) {
                $summaryByDepotCustomer[$dn][$cn]['case_variance'] =
                    $sum['actual_cases'] - $sum['expected_cases'];
                $summaryByDepotCustomer[$dn][$cn]['pallet_variance'] =
                    $sum['actual_pallets'] - $sum['expected_pallets'];
                foreach (['arrived', 'late', 'outstanding', 'expected_cases', 'actual_cases', 'expected_pallets', 'actual_pallets'] as $key) {
                    $totals[$key] += $sum[$key];
                }
            }
            $totals['case_variance'] = $totals['actual_cases'] - $totals['expected_cases'];
            $totals['pallet_variance'] = $totals['actual_pallets'] - $totals['expected_pallets'];
            $summaryByDepotCustomer[$dn]['_totals'] = $totals;
        }

        // Generate week numbers for current year
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

        // Get tipping locations and bays for the modal
        $tippingLocations = TippingLocation::whereIn('depot_id', $allowedDepotIds)->get();
        $tippingBays = TippingBay::whereIn('depot_id', $allowedDepotIds)->get();
        
        // Get trailer types for filtering
        $trailerTypes = TrailerType::active()->orderBy('name')->get();
        
        // Get carriers for arrival form
        $carriers = Carrier::active()->orderBy('name')->get();
        
        // Get user's default depot for action restrictions
        $user = auth()->user();
        $defaultDepotId = $user->depot_id ?? $allowedDepotIds[0] ?? null;
        
        // Current depot selection (for display purposes)
        $currentDepotId = $request->get('depot_id');
        
        // Rename depots to allDepots for consistency with other views
        $allDepots = $depots;

        return view('warehouse.bookings.index', compact(
            'bookings', 'allDepots', 'customers', 'types', 'summaryByDepotCustomer', 'weeks', 'currentYear',
            'tippingLocations', 'tippingBays', 'trailerTypes', 'carriers', 'defaultDepotId', 'currentDepotId'
        ));
    }

    /**
     * Streamlined booking index for operators - live updates, comprehensive filters with session memory
     */
    public function indexStreamlined(Request $request)
    {
        $this->ensureDepotAccess();
        $allowedDepotIds = $this->getAllowedDepotIds();
        $allowedCustomerIds = $this->getAllowedCustomerIds();

        // Apply filters from session if none provided
        $sessionFilters = $this->getFiltersFromSession($request);
        if (! $request->hasAny(['depot_id', 'customer_id', 'booking_type_id', 'from', 'to', 'arrival', 'filter', 'week_number', 'search', 'status']) && ! empty($sessionFilters)) {
            $request->merge($sessionFilters);
        }
        
        // Set default filters if no session filters and no parameters provided
        if (empty($sessionFilters) && !$request->hasAny(['depot_id', 'customer_id', 'booking_type_id', 'from', 'to', 'arrival', 'filter', 'week_number', 'search', 'status'])) {
            $request->merge([
                'filter' => 'today',
                'status' => 'outstanding'
            ]);
        }
        
        // Store current filters in session for persistence
        $this->storeFiltersInSession($request);

        // Build comprehensive query with all the same filtering logic as the main index
        $query = Booking::with([
            'slot.depot',
            'bookingType',
            'customer',
            'movements.tippingLocation',
            'movements.tippingBay',
            'poNumbers.lines.expectedPalletType',
            'poNumbers.lines.actualPalletType',
        ])
            ->whereHas('slot', fn ($q) => $q->whereIn('depot_id', $allowedDepotIds))
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

        // Apply customer filtering if user has restricted access
        if ($allowedCustomerIds !== null) {
            $query->whereIn('customer_id', $allowedCustomerIds)->whereNotNull('customer_id');
        }

        // Depot filter
        if ($depotId = $request->input('depot_id')) {
            $query->whereHas('slot', fn ($q) => $q->where('depot_id', $depotId));
        }

        // Customer filter
        if ($customerId = $request->input('customer_id')) {
            $query->where('customer_id', $customerId);
        }

        // Quick date filters (handle both 'filter' and 'quick_filter' parameters)
        if ($quickFilter = $request->input('filter')) {
            if ($quickFilter === 'today') {
                $query->whereHas('slot', function ($q) {
                    $q->whereDate('start_at', Carbon::today());
                });
            } elseif ($quickFilter === 'yesterday') {
                $query->whereHas('slot', function ($q) {
                    $q->whereDate('start_at', Carbon::yesterday());
                });
            } elseif ($quickFilter === 'tomorrow') {
                $query->whereHas('slot', function ($q) {
                    $q->whereDate('start_at', Carbon::tomorrow());
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
            $year = $request->input('year', Carbon::now()->year);
            $weekStart = Carbon::now()->setISODate($year, $weekNumber)->startOfWeek();
            $weekEnd = $weekStart->clone()->endOfWeek();
            
            $query->whereHas('slot', function ($q) use ($weekStart, $weekEnd) {
                $q->whereBetween('start_at', [$weekStart, $weekEnd]);
            });
        }

        // Custom date range filter
        if ($from = $request->input('from')) {
            $query->whereHas('slot', fn ($q) => $q->whereDate('start_at', '>=', $from));
        }
        if ($to = $request->input('to')) {
            $query->whereHas('slot', fn ($q) => $q->whereDate('start_at', '<=', $to));
        }

        // Arrival status filter
        if ($arrivalStatus = $request->input('arrival')) {
            if ($arrivalStatus === 'not_arrived') {
                $query->whereNull('arrived_at');
            } elseif ($arrivalStatus === 'late_runners') {
                $query->whereNull('arrived_at')
                    ->whereHas('slot', fn ($q) => $q->where('start_at', '<', Carbon::now()));
            } elseif ($arrivalStatus === 'arrived') {
                $query->whereNotNull('arrived_at');
            } elseif ($arrivalStatus === 'on_time') {
                $query->whereNotNull('arrived_at')
                    ->whereRaw('arrived_at <= (SELECT start_at FROM slots WHERE slots.id = bookings.slot_id)');
            } elseif ($arrivalStatus === 'arrived_late') {
                $query->whereNotNull('arrived_at')
                    ->whereRaw('arrived_at > (SELECT start_at FROM slots WHERE slots.id = bookings.slot_id)');
            } elseif ($arrivalStatus === 'onsite') {
                $query->whereNotNull('arrived_at')->whereDoesntHave('movements', function ($q) {
                    $q->whereNotNull('trailer_collected_at');
                });
            }
        }

        // Booking status filter
        $status = $request->input('status');
        if ($status === 'outstanding' || (!$status && !$request->input('arrival'))) {
            // Outstanding = not cancelled AND not departed
            $query->whereNull('cancelled_at')->whereNull('departed_at');
        } elseif ($status === 'completed') {
            $query->whereNotNull('departed_at');
        } elseif ($status === 'cancelled') {
            $query->whereNotNull('cancelled_at');
        } elseif ($status === 'pending') {
            $query->where('status', 'pending');
        } elseif ($status === 'confirmed') {
            $query->where('status', 'confirmed');
        } elseif ($status === 'in_progress') {
            $query->whereNotNull('arrived_at')->whereNull('trailer_collected_at');
        }
        // For 'all' status, don't add any additional filter

        // Search filtering
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('booking_reference', 'like', "%{$search}%")
                  ->orWhere('vehicle_registration', 'like', "%{$search}%")
                  ->orWhere('container_number', 'like', "%{$search}%")
                  ->orWhere('carrier_company', 'like', "%{$search}%")
                  ->orWhere('reference', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $bookings = $query->paginate(50)->appends($request->query());

        // Get dropdown data
        $depots = $this->getAllowedDepots();
        $customers = $this->getAllowedCustomers();
        $types = BookingType::orderBy('name')->get();

        // Get data for modals
        $tippingLocations = TippingLocation::whereIn('depot_id', $allowedDepotIds)->where('is_active', true)->get();
        $tippingBays = TippingBay::whereIn('depot_id', $allowedDepotIds)->where('is_active', true)->get();

        // For AJAX requests, return just the table rows
        if ($request->ajax()) {
            return view('warehouse.bookings.partials.streamlined-rows', compact('bookings'))->render();
        }

        return view('warehouse.bookings.index-streamlined', compact(
            'bookings', 'depots', 'customers', 'types', 'tippingLocations', 'tippingBays'
        ));
    }

    public function create()
    {
        $this->ensureDepotAccess();
        $allowedDepotIds = $this->getAllowedDepotIds();
        $depots = $this->getAllowedDepots();

        $slots = Slot::with(['depot', 'allowed_customers'])
            ->whereIn('depot_id', $allowedDepotIds)
            ->whereDate('start_at', '>=', now()->toDateString())
            ->get()
            ->filter(fn ($slot) => $slot->bookings()->count() < $slot->capacity);

        $types = BookingType::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();

        // Get tipping locations and bays for the allowed depots
        $tippingLocations = \App\Models\TippingLocation::with('depot')
            ->whereIn('depot_id', $allowedDepotIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $tippingBays = \App\Models\TippingBay::with('depot')
            ->whereIn('depot_id', $allowedDepotIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $trailerTypes = TrailerType::active()->orderBy('name')->get();

        $booking = new Booking; // 👈 avoids undefined variable errors

        return view('warehouse.bookings.create', compact('slots', 'types', 'depots', 'customers', 'booking', 'tippingLocations', 'tippingBays', 'trailerTypes'));
    }

    public function show(Booking $booking)
    {
        $this->ensureDepotAccess();
        $allowedDepotIds = $this->getAllowedDepotIds();

        // Check if user has access to this booking's depot
        if (! in_array($booking->slot->depot_id, $allowedDepotIds)) {
            abort(403, 'You do not have access to this booking.');
        }

        return view('warehouse.bookings.show', [
            'booking' => $booking->load([
                'slot.depot',
                'bookingType',
                'customer',
                'user',
                'movements',
                'poNumbers.lines.expectedPalletType',
                'poNumbers.lines.actualPalletType',
                'poNumbers.lines.actualPallets.palletType',
            ]),
        ]);
    }

    public function store(Request $request)
    {
        // Debug: Log the request data
        \Log::info('Booking store method called', [
            'request_data' => $request->all(),
            'user_id' => auth()->id(),
            'user_email' => auth()->user()->email ?? 'unknown'
        ]);

        try {
            $data = $request->validate([
            'slot_id' => 'required|exists:slots,id',
            'booking_type_id' => 'required|exists:booking_types,id',
            'customer_id' => 'required|exists:customers,id',
            'carrier_id' => 'nullable|exists:carriers,id',
            'carrier_name' => 'required|string|max:100',
            'container_size' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            // Vehicle details
            'vehicle_registration' => 'nullable|string|max:50',
            'container_number' => 'nullable|string|max:50', 
            'gate_number' => 'nullable|string|max:20',
            'trailer_type_id' => 'nullable|exists:trailer_types,id',
            'estimated_arrival' => 'nullable|date',
            'special_instructions' => 'nullable|string|max:500',
            'tipping_location_id' => 'nullable|exists:tipping_locations,id',
            'tipping_bay_id' => 'nullable|exists:tipping_bays,id',
            'tipping_type' => 'nullable|in:live_tip,drop',
            // PO numbers
            'po_numbers' => 'required|array|min:1',
            'po_numbers.*.po_number' => 'required|string|max:255',
            'po_numbers.*.lines' => 'required|array|min:1',
            'po_numbers.*.lines.*.line_number' => 'required|integer|min:1',
            // Support new pallet_entries structure
            'po_numbers.*.lines.*.pallet_entries' => 'nullable|array',
            'po_numbers.*.lines.*.pallet_entries.*.cases' => 'nullable|integer|min:0',
            'po_numbers.*.lines.*.pallet_entries.*.pallets' => 'nullable|integer|min:0',
            'po_numbers.*.lines.*.pallet_entries.*.type_id' => 'nullable|exists:pallet_types,id',
            // Legacy support for old structure
            'po_numbers.*.lines.*.expected_cases' => 'nullable|integer|min:0',
            'po_numbers.*.lines.*.expected_pallets' => 'nullable|integer|min:0',
            'po_numbers.*.lines.*.expected_pallet_type_id' => 'nullable|exists:pallet_types,id',
            'po_numbers.*.lines.*.actual_cases' => 'nullable|integer|min:0',
            'po_numbers.*.lines.*.actual_pallets' => 'nullable|integer|min:0',
            'po_numbers.*.lines.*.actual_pallet_type_id' => 'nullable|exists:pallet_types,id',
        ]);
        
        // Custom validation: Ensure at least one PO line has cases > 0
        $hasCases = false;
        foreach ($data['po_numbers'] as $po) {
            foreach ($po['lines'] as $line) {
                // Check new pallet_entries structure first
                if (!empty($line['pallet_entries'])) {
                    foreach ($line['pallet_entries'] as $entry) {
                        if (!empty($entry['cases']) && $entry['cases'] > 0) {
                            $hasCases = true;
                            break 3;
                        }
                    }
                }
                // Fallback to old structure
                elseif (!empty($line['expected_cases']) && $line['expected_cases'] > 0) {
                    $hasCases = true;
                    break 2;
                }
            }
        }
        
        if (!$hasCases) {
            return back()->withErrors(['po_numbers' => 'At least one PO line must have cases greater than 0.'])->withInput();
        }

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

        $data['user_id'] = auth()->id();

        // Extract vehicle details and store in JSON field
        $vehicleDetails = [];
        $vehicleFields = ['vehicle_registration', 'container_number', 'carrier_company', 
                         'gate_number', 'trailer_type_id', 'estimated_arrival', 'special_instructions'];
        
        foreach ($vehicleFields as $field) {
            if (!empty($data[$field])) {
                $vehicleDetails[$field] = $data[$field];
            }
            unset($data[$field]); // Remove from main booking data
        }
        
        if (!empty($vehicleDetails)) {
            $data['vehicle_details'] = $vehicleDetails;
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

        // Remove po_numbers from main data before creating booking
        $poNumbers = $data['po_numbers'] ?? [];
        unset($data['po_numbers']);

        $booking = Booking::create($data);

        // Create PO numbers and lines if provided
        if (! empty($poNumbers)) {
            foreach ($poNumbers as $poData) {
                $po = $booking->poNumbers()->create([
                    'po_number' => $poData['po_number'],
                ]);

                if (! empty($poData['lines'])) {
                    foreach ($poData['lines'] as $lineData) {
                        // Transform new pallet_entries structure to legacy format for database storage
                        if (!empty($lineData['pallet_entries'])) {
                            // Calculate totals from pallet_entries
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
                            
                            // Set legacy fields for database compatibility
                            $lineData['expected_cases'] = $totalCases;
                            $lineData['expected_pallets'] = $totalPallets;
                            if ($firstPalletTypeId) {
                                $lineData['expected_pallet_type_id'] = $firstPalletTypeId;
                            }
                            
                            // Remove pallet_entries as it's not a database field
                            unset($lineData['pallet_entries']);
                        }
                        
                        $po->lines()->create($lineData);
                    }
                }
            }
        }

            $this->recalculateSlot($booking);

            \Log::info('Booking created successfully', ['booking_id' => $booking->id]);

            return $this->redirectWithFilters($request, 'Booking created.');
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Booking validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            throw $e; // Re-throw to show validation errors
        } catch (\Exception $e) {
            \Log::error('Booking creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return back()->withInput()->withErrors([
                'general' => 'An error occurred while creating the booking: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Redirect to rebook form (handled by BookingRebookController)
     */
    public function rebookShow(Booking $booking)
    {
        return redirect()->route('app.bookings.rebook.show', $booking);
    }

    /**
     * Show booking history (delegated to BookingRebookController)
     */
    public function history(Booking $booking)
    {
        $rebookController = new \App\Http\Controllers\Warehouse\BookingRebookController();
        return $rebookController->history($booking);
    }

    //    public function edit(Booking $booking)
    //    {
    //        $slots     = Slot::with('depot')->orderBy('start_at')->get();
    //        $types     = BookingType::orderBy('name')->get();
    //        $depots    = auth()->user()->depots()->orderBy('name')->get();
    //        $customers = Customer::orderBy('name')->get();

    //        return view('warehouse.bookings.edit', compact('booking','slots','types','depots','customers'));
    //    }

    public function edit(Booking $booking)
    {
        $this->ensureDepotAccess();
        $allowedDepotIds = $this->getAllowedDepotIds();
        $depots = $this->getAllowedDepots();
        $types = BookingType::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();

        $availableSlots = Slot::with(['depot', 'allowed_customers'])
            ->whereIn('depot_id', $allowedDepotIds)
            ->whereDate('start_at', '>=', now()->toDateString())
            ->get()
            ->filter(function ($slot) use ($booking) {
                return $slot->bookings()->where('id', '!=', $booking->id)->count() < $slot->capacity;
            });

        // Ensure current slot is visible even if full or past
        if ($booking->slot && ! $availableSlots->contains('id', $booking->slot->id)) {
            $booking->slot->load(['depot', 'allowed_customers']);
            $availableSlots->push($booking->slot);
        }

        $slots = $availableSlots->sortBy('start_at');

        // Get tipping locations and bays for the allowed depots
        $tippingLocations = \App\Models\TippingLocation::with('depot')
            ->whereIn('depot_id', $allowedDepotIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $tippingBays = \App\Models\TippingBay::with('depot')
            ->whereIn('depot_id', $allowedDepotIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $trailerTypes = TrailerType::active()->orderBy('name')->get();

        // Load PO numbers for the booking
        $booking->load('poNumbers');

        return view('warehouse.bookings.edit', compact('booking', 'slots', 'types', 'depots', 'customers', 'tippingLocations', 'tippingBays', 'trailerTypes'));
    }

    public function update(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'slot_id' => 'nullable|exists:slots,id',
            'booking_type_id' => 'required|exists:booking_types,id',
            'customer_id' => 'required|exists:customers,id',
            'carrier_id' => 'nullable|exists:carriers,id',
            'carrier_name' => 'required|string|max:100',
            'container_size' => 'nullable|integer|min:0',
            'notes' => 'nullable|string',
            'vehicle_registration' => 'nullable|string|max:50',
            'container_number' => 'nullable|string|max:50',
            'gate_number' => 'nullable|string|max:20',
            'trailer_type_id' => 'nullable|exists:trailer_types,id',
            'estimated_arrival' => 'nullable|date',
            'tipping_location_id' => 'nullable|exists:tipping_locations,id',
            'tipping_bay_id' => 'nullable|exists:tipping_bays,id',
            'special_instructions' => 'nullable|string|max:500',
            'tipping_type' => 'nullable|in:live_tip,drop',
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

        if (isset($data['slot_id']) && $data['slot_id'] != $booking->slot_id) {
            $this->ensureSlotIsAvailable($data['slot_id'], $booking->id);
            $booking->slot_id = $data['slot_id'];
        }

        // Extract vehicle details and store in JSON field
        $vehicleDetails = $booking->vehicle_details ?? [];
        $vehicleFields = ['vehicle_registration', 'container_number', 'carrier_company', 
                         'gate_number', 'trailer_type_id', 'estimated_arrival', 'special_instructions'];
        
        foreach ($vehicleFields as $field) {
            if (array_key_exists($field, $data)) {
                if (!empty($data[$field])) {
                    $vehicleDetails[$field] = $data[$field];
                } else {
                    unset($vehicleDetails[$field]);
                }
                unset($data[$field]); // Remove from main booking data
            }
        }
        
        $data['vehicle_details'] = $vehicleDetails;

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
        
        // Handle PO numbers separately
        $poNumbers = $data['po_numbers'] ?? [];
        unset($data['po_numbers']);

        $booking->update($data);

        // Update PO numbers and lines - delete existing and recreate
        if (array_key_exists('po_numbers', $request->all())) {
            // Custom validation for update: Ensure at least one PO line has cases > 0
            if (!empty($poNumbers)) {
                $hasCases = false;
                foreach ($poNumbers as $po) {
                    foreach ($po['lines'] as $line) {
                        // Check new pallet_entries structure first
                        if (!empty($line['pallet_entries'])) {
                            foreach ($line['pallet_entries'] as $entry) {
                                if (!empty($entry['cases']) && $entry['cases'] > 0) {
                                    $hasCases = true;
                                    break 3;
                                }
                            }
                        }
                        // Fallback to old structure
                        elseif (!empty($line['expected_cases']) && $line['expected_cases'] > 0) {
                            $hasCases = true;
                            break 2;
                        }
                    }
                }
                
                if (!$hasCases) {
                    return back()->withErrors(['po_numbers' => 'At least one PO line must have cases greater than 0.'])->withInput();
                }
            }
            
            $booking->poNumbers()->delete();

            if (! empty($poNumbers)) {
                foreach ($poNumbers as $poData) {
                    $po = $booking->poNumbers()->create([
                        'po_number' => $poData['po_number'],
                    ]);

                    if (! empty($poData['lines'])) {
                        foreach ($poData['lines'] as $lineData) {
                            // Transform new pallet_entries structure to legacy format for database storage
                            if (!empty($lineData['pallet_entries'])) {
                                // Calculate totals from pallet_entries
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
                                
                                // Set legacy fields for database compatibility
                                $lineData['expected_cases'] = $totalCases;
                                $lineData['expected_pallets'] = $totalPallets;
                                if ($firstPalletTypeId) {
                                    $lineData['expected_pallet_type_id'] = $firstPalletTypeId;
                                }
                                
                                // Remove pallet_entries as it's not a database field
                                unset($lineData['pallet_entries']);
                            }
                            
                            $po->lines()->create($lineData);
                        }
                    }
                }
            }
        }

        $this->recalculateSlot($booking);

        return $this->redirectWithFilters($request, 'Booking updated.');
    }

    public function destroy(Request $request, Booking $booking)
    {
        $booking->delete();

        return $this->redirectWithFilters($request, 'Booking deleted.');
    }

    public function assignBayFromWaiting(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'tipping_bay_id' => 'required|exists:tipping_bays,id',
            'assignment_notes' => 'nullable|string|max:255',
        ]);

        $tippingBay = \App\Models\TippingBay::findOrFail($validated['tipping_bay_id']);

        if (! $tippingBay->isAvailable()) {
            return back()->withErrors(['tipping_bay_id' => 'Selected bay is not available.']);
        }

        // Store original values before update
        $movement = $booking->getOrCreateMovement();
        $originalBayId = $movement->tipping_bay_id;
        $originalBayName = $movement->tippingBay?->name ?? 'None';
        $originalLocation = $booking->current_location;

        // TODO: Update to use movements system instead of booking fields
        // Use the booking's movement system to move to bay
        $tippingBay = TippingBay::findOrFail($validated['tipping_bay_id']);

        $success = $booking->moveToBay($tippingBay, $request->get('notes'));

        if (! $success) {
            return redirect()->back()->with('error', 'Unable to move trailer to bay. Check if bay is available and trailer status allows this action.');
        }

        if ($movement->wasRecentlyCreated === false) {
            $movement->update([
                'current_status' => 'at_bay',
                'tipping_bay_id' => $validated['tipping_bay_id'],
            ]);
        }

        // Record bay assignment in history with detailed changes
        $message = 'Vehicle moved from waiting area '.$booking->waiting_area_location.' to bay '.$tippingBay->name;
        if (! empty($validated['assignment_notes'])) {
            $message .= '. Notes: '.$validated['assignment_notes'];
        }

        $changes = [
            'tipping_bay_id' => [
                'old' => $originalBayId,
                'new' => $validated['tipping_bay_id'],
            ],
            'bay_name' => [
                'old' => $originalBayName,
                'new' => $tippingBay->name,
            ],
            'current_location' => [
                'old' => $originalLocation,
                'new' => 'tipping_bay',
            ],
            'movement_status' => [
                'old' => $booking->getCurrentMovementStatus(),
                'new' => 'at_bay',
            ],
        ];

        \App\Models\BookingHistory::recordAction($booking, 'modified', $message, null, null, $changes);

        return redirect()->route('app.bookings.index')->with('success', 'Vehicle assigned to bay '.$tippingBay->name.' successfully.');
    }

    public function markArrived(Request $request, Booking $booking)
    {
        // If this is a POST request with vehicle details, validate and update
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'vehicle_registration' => 'required|string|max:50',
                'container_number' => 'nullable|string|max:50',
                'carrier_id' => 'nullable|exists:carriers,id',
                'carrier_name' => 'required|string|max:100',
                'trailer_type_id' => 'required|exists:trailer_types,id',
                'tipping_location_id' => 'nullable|exists:tipping_locations,id',
                'tipping_bay_id' => 'nullable|exists:tipping_bays,id',
                'tipping_type' => 'required|in:live_tip,drop',
            ]);

            // Normalize input data
            if (!empty($validated['vehicle_registration'])) {
                $validated['vehicle_registration'] = $this->normalizeVehicleRegistration($validated['vehicle_registration']);
            }
            if (!empty($validated['container_number'])) {
                $validated['container_number'] = $this->normalizeContainerNumber($validated['container_number']);
            }
            if (!empty($validated['carrier_name'])) {
                $validated['carrier_name'] = $this->normalizeCarrierName($validated['carrier_name']);
            }

            // Optional: Can assign to waiting area or bay later in the workflow
            // No validation requirement - vehicle can be marked as simply "arrived on site"

            // Update booking basic info only (no location/status fields)
            $arrivalTime = now();
            $basicBookingData = [
                'arrived_at' => $arrivalTime,
                'status' => 'in_progress',
                'vehicle_registration' => $validated['vehicle_registration'],
                'container_number' => $validated['container_number'] ?? null,
                'carrier_id' => $validated['carrier_id'] ?? null,
                'tipping_location_id' => $validated['tipping_location_id'] ?? null,
                'tipping_bay_id' => $validated['tipping_bay_id'] ?? null,
                'trailer_type_id' => $validated['trailer_type_id'] ?? null,
                'tipping_type' => $validated['tipping_type'],
            ];

            $booking->update($basicBookingData);

            // Record arrival status using configurable arrival time rules
            \App\Models\BookingHistory::recordArrival($booking, $arrivalTime, 'Vehicle arrived on site');

            // Get or create movement for this booking
            $movement = $booking->getOrCreateMovement();

            // Update movement with arrival and vehicle details
            $movementData = [
                'actual_arrival' => now(),
                'current_status' => 'arrived',
            ];

            // Handle carrier creation/selection (same logic as create/update)
            if (empty($validated['carrier_id']) && !empty($validated['carrier_name'])) {
                // Try to find existing carrier or create new one
                $carrier = Carrier::findOrReactivate($validated['carrier_name']);
                if (!$carrier) {
                    $carrier = Carrier::create([
                        'name' => $validated['carrier_name'],
                        'is_active' => true,
                    ]);
                }
                $validated['carrier_id'] = $carrier->id;
            }
            
            // Get carrier name from carrier_id
            $carrierName = null;
            if (!empty($validated['carrier_id'])) {
                $carrier = Carrier::find($validated['carrier_id']);
                $carrierName = $carrier ? $carrier->name : null;
            }
            
            // Store vehicle details in movement - merge with any booking creation data
            $vehicleData = [
                'vehicle_registration' => $validated['vehicle_registration'],
                'container_number' => $validated['container_number'] ?? null,
                'carrier_company' => $carrierName,
                'trailer_type_id' => $validated['trailer_type_id'] ?? null,
            ];
            
            $movementData['additional_data'] = array_merge(
                $movement->additional_data ?? [],
                $vehicleData
            );

            // Handle waiting area in movement custom fields
            if (! empty($validated['waiting_area_location'])) {
                $movementData['current_status'] = 'in_parking';
                $movementData['custom_fields'] = array_merge(
                    $movement->custom_fields ?? [],
                    [
                        'waiting_area_location' => $validated['waiting_area_location'],
                        'entered_waiting_area_at' => now()->toDateTimeString(),
                    ]
                );
            }

            $movement->update($movementData);

            // Determine arrival message
            $arrivalMessage = 'Vehicle arrived on site - status changed to in_progress';
            if (! empty($validated['waiting_area_location'])) {
                $arrivalMessage .= ' and entered waiting area '.$validated['waiting_area_location'];
            }

            // Record arrival in booking history
            \App\Models\BookingHistory::recordAction($booking, 'modified', $arrivalMessage);

            // Handle workflow based on assignment type
            if ($validated['tipping_bay_id']) {
                // Express workflow: Direct bay assignment
                $tippingBay = \App\Models\TippingBay::find($validated['tipping_bay_id']);
                if ($tippingBay && $tippingBay->isAvailable()) {
                    // Use the booking's moveToBay method for proper workflow
                    $success = $booking->moveToBay($tippingBay, 'Express delivery - direct bay assignment on arrival');

                    if ($success) {
                        return $this->redirectWithFilters($request, 'Vehicle arrived and moved directly to bay '.$tippingBay->name.' (express workflow).');
                    } else {
                        return back()->withErrors(['tipping_bay_id' => 'Failed to assign to bay. Bay may no longer be available.']);
                    }
                } else {
                    return back()->withErrors(['tipping_bay_id' => 'Selected bay is not available.']);
                }
            } elseif (! empty($validated['waiting_area_location'])) {
                // Standard workflow: Waiting area first
                $waitingArea = $validated['waiting_area_location'];

                // If they also pre-assigned a drop location, note it
                $message = 'Vehicle arrived and assigned to waiting area '.$waitingArea.'.';
                if (! empty($validated['tipping_location_id'])) {
                    $tippingLocation = \App\Models\TippingLocation::find($validated['tipping_location_id']);
                    $message .= ' Pre-assigned drop location: '.$tippingLocation->name.'.';
                }

                return $this->redirectWithFilters($request, $message);
            } elseif (! empty($validated['tipping_location_id'])) {
                // Direct to drop location workflow
                $tippingLocation = \App\Models\TippingLocation::find($validated['tipping_location_id']);
                
                // Verify location is available and in same depot
                if (! $tippingLocation->isAvailable() || $tippingLocation->depot_id !== $booking->slot->depot_id) {
                    return back()->withErrors(['tipping_location_id' => 'Selected drop location is not available.']);
                }

                // Use the booking's dropTrailer method for proper workflow
                $success = $booking->dropTrailer($tippingLocation, 'Vehicle arrived and directly assigned to drop location');

                if ($success) {
                    return $this->redirectWithFilters($request, 'Vehicle arrived and moved to drop location: '.$tippingLocation->name);
                } else {
                    return back()->withErrors(['tipping_location_id' => 'Failed to assign to drop location. Location may no longer be available.']);
                }
            }

            return $this->redirectWithFilters($request, 'Vehicle arrived on site - marked as in progress.');
        }

        // If GET request, show the arrival form based on user role
        $allowedDepotIds = $this->getAllowedDepotIds();
        $bookingDepotId = $booking->slot->depot_id;

        // Ensure user has access to this booking's depot
        if (!in_array($bookingDepotId, $allowedDepotIds)) {
            abort(403, 'Access denied to this booking\'s depot');
        }

        // Get tipping locations and bays for the booking's specific depot only
        $tippingLocations = \App\Models\TippingLocation::with('depot')
            ->where('depot_id', $bookingDepotId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $tippingBays = \App\Models\TippingBay::with('depot')
            ->where('depot_id', $bookingDepotId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $trailerTypes = \App\Models\TrailerType::where('is_active', true)
            ->orderBy('name')
            ->get();

        $user = auth()->user();
        if ($user->hasRole('site-admin')) {
            return view('site-admin.arrival-form', compact('booking', 'tippingLocations', 'tippingBays', 'trailerTypes'));
        } elseif ($user->hasRole('depot-admin')) {
            return view('depot-admin.bookings.arrival-form', compact('booking', 'tippingLocations', 'tippingBays', 'trailerTypes'));
        } else {
            return view('warehouse.bookings.arrival-form', compact('booking', 'tippingLocations', 'tippingBays', 'trailerTypes'));
        }
    }

    public function markDeparted(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'departure_scenario' => 'required|in:completed_with_trailer,completed_dropped_trailer,trailer_swap,emergency_departure',
            'departure_notes' => 'nullable|string|max:1000',
            'dropped_trailer_location' => 'nullable|string|max:50',
            'collected_trailer_number' => 'nullable|string|max:100',
        ]);

        // TIPPING WORKFLOW ENFORCEMENT
        // Check if tipping is required and has been completed
        if ($this->isTippingRequired($booking) && ! $this->isTippingCompleted($booking)) {
            // Allow emergency departures but warn
            if ($validated['departure_scenario'] === 'emergency_departure') {
                // Emergency departure allowed - log warning
                \App\Models\BookingHistory::recordAction(
                    $booking,
                    'warning',
                    'Emergency departure authorized without tipping completion - requires follow-up'
                );
            } else {
                // Standard departures require tipping completion
                return back()->withErrors([
                    'tipping' => 'Vehicle cannot depart - tipping process must be completed first. Use emergency departure if absolutely necessary.',
                ])->withInput();
            }
        }

        // Determine if trailer is being left on site
        $trailerLeftOnSite = in_array($validated['departure_scenario'], ['completed_dropped_trailer', 'trailer_swap']);

        // Determine departure time - if tipping just completed, use that time
        $departureTime = now();
        if ($booking->tipping_completed_at && $booking->tipping_completed_at->diffInMinutes(now()) < 5) {
            // If tipping completed within the last 5 minutes, use tipping completion time as departure time
            $departureTime = $booking->tipping_completed_at;
        }

        // Get movement for this booking
        $movement = $booking->getOrCreateMovement();

        // Prepare departure notes with scenario info
        $departureNotes = $validated['departure_notes'] ?? '';
        $trailerLocationName = null;
        
        if (! empty($validated['dropped_trailer_location'])) {
            // Parse the new location format (DROP_1, BAY_2, WAITING_A1, etc.)
            $locationValue = $validated['dropped_trailer_location'];
            
            if (str_starts_with($locationValue, 'DROP_')) {
                $locationId = str_replace('DROP_', '', $locationValue);
                $tippingLocation = \App\Models\TippingLocation::find($locationId);
                $trailerLocationName = $tippingLocation ? $tippingLocation->name : $locationValue;
            } elseif (str_starts_with($locationValue, 'BAY_')) {
                $locationId = str_replace('BAY_', '', $locationValue);
                $tippingBay = \App\Models\TippingBay::find($locationId);
                $trailerLocationName = $tippingBay ? $tippingBay->name : $locationValue;
            } elseif (str_starts_with($locationValue, 'WAITING_')) {
                $waitingArea = str_replace('WAITING_', '', $locationValue);
                $trailerLocationName = 'Waiting Area ' . $waitingArea;
            } else {
                // Fallback for any other format
                $trailerLocationName = $locationValue;
            }
            
            $locationNote = 'Trailer left at: ' . $trailerLocationName;
            $departureNotes = $departureNotes ? $departureNotes."\n".$locationNote : $locationNote;
        }

        // Prepare departure custom fields
        $departureFields = [
            'departure_scenario' => $validated['departure_scenario'],
            'trailer_left_on_site' => $trailerLeftOnSite,
            'dropped_trailer_location' => $trailerLocationName ?? $validated['dropped_trailer_location'] ?? null,
            'trailer_status' => $trailerLeftOnSite ?
                ($booking->tipping_completed_at ? 'empty_available' : 'awaiting_collection') :
                'departed_with_vehicle',
        ];

        // Add trailer swap specific information
        if ($validated['departure_scenario'] === 'trailer_swap' && !empty($validated['collected_trailer_number'])) {
            $departureFields['collected_trailer_number'] = $validated['collected_trailer_number'];
            $departureFields['trailer_swapped'] = true;
        }

        // Add departure scenario to movement custom fields
        $movement->update([
            'custom_fields' => array_merge(
                $movement->custom_fields ?? [],
                $departureFields
            ),
        ]);

        // Use the booking's trailerDepart method for proper workflow
        $success = $booking->trailerDepart($departureNotes);

        if (! $success) {
            return back()->withErrors([
                'departure' => 'Unable to process departure. Check booking status allows departure.',
            ])->withInput();
        }

        // Create detailed departure message based on scenario
        $scenarioDescriptions = [
            'completed_with_trailer' => 'Vehicle departed with trailer - standard completion',
            'completed_dropped_trailer' => 'Vehicle departed solo - trailer left on site',
            'trailer_swap' => 'Vehicle departed with different trailer',
            'emergency_departure' => 'Emergency/problem departure recorded',
        ];

        $message = $scenarioDescriptions[$validated['departure_scenario']] ?? 'Vehicle departed';

        // Add specific details based on scenario
        if ($validated['departure_scenario'] === 'completed_dropped_trailer' && !empty($validated['dropped_trailer_location'])) {
            $status = $booking->tipping_completed_at ? 'empty and ready for collection' : 'full and awaiting tipping';
            $message .= ' at '.$validated['dropped_trailer_location'].' ('.$status.')';
        } elseif ($validated['departure_scenario'] === 'trailer_swap') {
            if (!empty($validated['collected_trailer_number'])) {
                $message .= ' (collected: '.$validated['collected_trailer_number'].')';
            }
            if (!empty($validated['dropped_trailer_location'])) {
                $status = $booking->tipping_completed_at ? 'empty and ready for collection' : 'full and awaiting tipping';
                $message .= ' - original trailer left at '.$validated['dropped_trailer_location'].' ('.$status.')';
            }
        }

        if ($validated['departure_notes']) {
            $message .= '. Notes: '.$validated['departure_notes'];
        }

        // Record departure/completion in booking history
        \App\Models\BookingHistory::recordAction(
            $booking,
            'completed',
            $message
        );

        return $this->redirectWithFilters($request, 'Booking completed - vehicle departed.');
    }

    public function emptyUnitCollection(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'vehicle_registration' => 'required|string|max:50',
                'carrier_name' => 'nullable|string|max:100',
                'carrier_id' => 'nullable|exists:carriers,id',
                'collected_trailer_number' => 'nullable|string|max:100',
                'collected_from_booking_id' => 'required|exists:bookings,id',
            ]);

            // If collecting from a specific booking, update that movement
            if (! empty($validated['collected_from_booking_id'])) {
                $sourceBooking = Booking::findOrFail($validated['collected_from_booking_id']);
                $sourceMovement = $sourceBooking->getOrCreateMovement();

                // Update movement status to collected
                $sourceMovement->update([
                    'current_status' => 'trailer_collected',
                    'trailer_collected_at' => now(),
                    'custom_fields' => array_merge(
                        $sourceMovement->custom_fields ?? [],
                        [
                            'collected_by_vehicle' => $validated['vehicle_registration'],
                            'collection_carrier' => $validated['carrier_name'],
                            'collection_carrier_id' => $validated['carrier_id'],
                        ]
                    ),
                ]);

                \App\Models\BookingHistory::recordAction(
                    $sourceBooking,
                    'modified',
                    'Trailer collected by '.$validated['vehicle_registration'].' ('.($validated['carrier_name'] ?: 'Unknown carrier').')'
                );
            }

            // Log the collection event
            \Log::info('Unit collection completed', [
                'vehicle' => $validated['vehicle_registration'],
                'booking_id' => $validated['collected_from_booking_id'],
                'carrier' => $validated['carrier_name'],
                'collected_at' => now(),
            ]);

            return redirect()->route('app.bookings.index')->with('success',
                'Unit collection recorded - Vehicle '.$validated['vehicle_registration'].' collected trailer from booking');
        }

        // Get user's allowed depot IDs and default depot
        $allowedDepotIds = $this->getAllowedDepotIds();
        $user = auth()->user();
        $defaultDepotId = $user->depot_id ?? $allowedDepotIds[0] ?? null;
        
        // Filter by default depot only for empty unit collection to reduce complexity
        $targetDepotIds = $defaultDepotId ? [$defaultDepotId] : $allowedDepotIds;
        
        // Get available parking areas and tipping bays for location dropdown (from user's default depot)
        $parkingAreas = \App\Models\TippingLocation::whereIn('depot_id', $targetDepotIds)
            ->where('location_type', \App\Models\TippingLocation::TYPE_PARKING)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
            
        $tippingBays = \App\Models\TippingBay::whereIn('depot_id', $targetDepotIds)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        // Get available trailers for collection (from user's default depot only)
        $availableTrailersMovements = Movement::with(['booking.slot.depot', 'booking.customer', 'trailer', 'tippingLocation', 'tippingBay'])
            ->whereNotNull('booking_id')
            ->whereHas('booking.slot', function($q) use ($targetDepotIds) {
                $q->whereIn('depot_id', $targetDepotIds);
            })
            ->whereIn('current_status', ['empty', 'in_parking', 'awaiting_collection', 'back_to_parking', 'trailer_dropped']) // Include all trailers ready for collection
            ->whereNull('trailer_collected_at')
            ->get();

        // Convert to bookings for view compatibility and add detailed info
        $availableTrailers = $availableTrailersMovements->map(function ($movement) {
            $booking = $movement->booking;
            
            // Determine current location
            $currentLocation = 'Unknown';
            if ($movement->tippingBay) {
                $currentLocation = $movement->tippingBay->name . ' (Tipping Bay)';
            } elseif ($movement->tippingLocation) {
                $currentLocation = $movement->tippingLocation->name . ' (' . ucwords(str_replace('_', ' ', $movement->tippingLocation->location_type)) . ')';
            } elseif ($movement->current_status === 'empty') {
                $currentLocation = 'Ready for Collection (Location TBD)';
            } elseif ($movement->current_status === 'in_parking') {
                $currentLocation = 'Dropped on Site (Location TBD)';
            } elseif ($movement->current_status === 'back_to_parking') {
                $currentLocation = 'Empty Trailer in Parking Area';
            } elseif ($movement->current_status === 'trailer_dropped') {
                $currentLocation = 'Trailer Dropped (Unit Departed)';
            }
            
            // Add enhanced info to booking
            $booking->location_display = $currentLocation;
            $booking->movement_status = $movement->current_status;
            $booking->customer_name = $booking->customer ? $booking->customer->name : 'Unknown Customer';
            $booking->depot_name = $booking->slot && $booking->slot->depot ? $booking->slot->depot->name : 'Unknown Depot';
            
            // Add trailer display number for sorting
            $booking->trailer_display_number = $booking->container_number ?? 'TRAILER-' . $booking->id;
            
            return $booking;
        })->sortBy('trailer_display_number'); // Sort alphabetically by trailer number

        return view('warehouse.bookings.empty-unit-collection', compact('availableTrailers', 'parkingAreas', 'tippingBays'));
    }

    public function trailerLocationReport(Request $request)
    {
        $this->ensureDepotAccess();
        $allowedDepotIds = $this->getAllowedDepotIds();
        
        // Get user's default depot or first allowed depot
        $user = auth()->user();
        $defaultDepotId = $user->depot_id ?? $allowedDepotIds[0] ?? null;
        
        // Allow depot selection via request parameter
        $selectedDepotId = $request->get('depot_id');
        
        // Show all allowed depots for viewing, but track default for actions
        if ($selectedDepotId && in_array($selectedDepotId, $allowedDepotIds)) {
            $currentDepotId = $selectedDepotId;
        } else {
            $currentDepotId = null; // Show all depots
        }
        
        // Filter movements by selected depot or show all
        $depotIds = $currentDepotId ? [$currentDepotId] : $allowedDepotIds;

        // Get movements with trailers still on site - including factory bookings
        $movementsOnSite = Movement::with([
            'booking.slot.depot', 
            'booking.customer', 
            'booking.poNumbers',
            'factoryBooking.depot',
            'factoryBooking.customer',
            'factoryBooking.poNumbers',
            'tippingBay', 
            'tippingLocation'
        ])
            ->where(function ($query) use ($depotIds) {
                // Regular bookings
                $query->whereNotNull('booking_id')
                    ->whereHas('booking.slot', fn($q) => $q->whereIn('depot_id', $depotIds))
                    // Factory bookings
                    ->orWhere(function ($subQuery) use ($depotIds) {
                        $subQuery->whereNotNull('factory_booking_id')
                            ->whereHas('factoryBooking', fn($q) => $q->whereIn('depot_id', $depotIds));
                    });
            })
            ->whereIn('current_status', ['arrived', 'in_parking', 'in_parking', 'in_parking', 'at_bay', 'unloading', 'empty'])
            ->orderBy('moved_to_location_at', 'asc') // Oldest first
            ->get();

        // Enhanced grouping by status
        $waitingToTip = $movementsOnSite->whereIn('current_status', ['in_parking', 'arrived', 'in_parking']); // Need to start tipping
        $currentlyTipping = $movementsOnSite->whereIn('current_status', ['at_bay', 'unloading']); // Actively being tipped
        $emptyTrailers = $movementsOnSite->where('current_status', 'empty'); // Tipped and ready for collection
        $generalWaiting = $movementsOnSite->where('current_status', 'in_parking'); // In waiting areas
        
        // Calculate time on site for all trailers
        $trailersWithTime = $movementsOnSite->map(function ($movement) {
            // Get the bookable model (booking or factoryBooking)
            $bookable = $movement->bookable;
            $arrival = $movement->actual_arrival ?? $bookable?->arrived_at;
            $timeOnSite = $arrival ? now()->diffInHours($arrival) : 0;
            $movement->time_on_site_hours = $timeOnSite;
            
            // Determine if tipping is completed
            $movement->tipping_completed = $movement->current_status === 'empty';
            $movement->needs_tipping = in_array($movement->current_status, ['in_parking', 'arrived']);
            $movement->being_tipped = in_array($movement->current_status, ['at_bay', 'unloading']);
            
            // Check if this is a factory booking and if it's overdue (>60 minutes)
            $movement->is_factory_booking = $movement->factory_booking_id !== null;
            $movement->factory_overdue = $movement->is_factory_booking && $timeOnSite > 1; // 60 minutes = 1 hour
            
            // Priority classification
            if ($movement->factory_overdue) {
                $movement->priority_class = 'urgent-factory';
            } elseif ($movement->is_factory_booking) {
                $movement->priority_class = 'factory';
            } elseif ($timeOnSite > 4) {
                $movement->priority_class = 'overdue';
            } else {
                $movement->priority_class = 'normal';
            }
            
            return $movement;
        });

        // Summary statistics
        $stats = [
            'total_on_site' => $movementsOnSite->count(),
            'awaiting_collection' => $waitingToTip->count(),
            'empty_available' => $emptyTrailers->count(),
            'being_tipped' => $currentlyTipping->count(),
            'in_parking_areas' => $generalWaiting->count(),
            'overdue_collections' => $emptyTrailers->filter(fn($m) => $m->time_on_site_hours > 24)->count(),
            'factory_bookings' => $trailersWithTime->where('is_factory_booking', true)->count(),
            'factory_overdue' => $trailersWithTime->where('factory_overdue', true)->count(),
        ];

        // Get allowed depots for filter dropdown
        $allDepots = \App\Models\Depot::whereIn('id', $allowedDepotIds)->get();

        return view('warehouse.bookings.trailer-location-report', compact(
            'movementsOnSite',
            'trailersWithTime',
            'waitingToTip',
            'currentlyTipping',
            'emptyTrailers',
            'generalWaiting',
            'stats',
            'allDepots',
            'currentDepotId',
            'defaultDepotId'
        ));
    }

    public function getAvailableTrailers(Request $request)
    {
        // Get movements with trailers currently on site available for collection
        $availableMovements = Movement::with(['booking:id,booking_reference', 'trailer'])
            ->whereNotNull('booking_id')
            ->whereIn('current_status', ['empty']) // Empty trailers awaiting collection
            ->whereNull('trailer_collected_at')
            ->orderBy('created_at', 'asc')
            ->get();

        // Transform to API response format
        $availableTrailers = $availableMovements->map(function ($movement) {
            return [
                'id' => $movement->booking->id,
                'booking_reference' => $movement->booking->booking_reference,
                'container_number' => $movement->additional_data['container_number'] ?? $movement->trailer?->trailer_number ?? 'N/A',
                'dropped_trailer_location' => $movement->custom_fields['dropped_trailer_location'] ?? 'Unknown',
                'dropped_trailer_status' => $movement->custom_fields['trailer_status'] ?? 'awaiting_collection',
                'trailer_collection_scheduled' => $movement->custom_fields['trailer_collection_scheduled'] ?? null,
                'movement_id' => $movement->id,
            ];
        });

        return response()->json($availableTrailers);
    }

    protected function recalculateSlot(Booking $booking): void
    {
        $slot = $booking->slot;
        $length = 60;
        if ($booking->bookingType->name === 'Handball') {
            $s = $booking->container_size;
            $length = $s < 3000 ? 180 : ($s <= 6000 ? 240 : 300);
        }
        $slot->update(['end_at' => $slot->start_at->copy()->addMinutes($length)]);
    }

    /**
     * Check if tipping is required for this booking type/depot
     */
    private function isTippingRequired(Booking $booking): bool
    {
        // For now, assume all bookings require tipping unless it's a collection-only booking
        // This can be expanded to check booking type or depot settings
        return ! in_array($booking->bookingType->name ?? '', ['Collection Only', 'Document Collection']);
    }

    /**
     * Check if tipping has been completed for this booking
     */
    private function isTippingCompleted(Booking $booking): bool
    {
        // Check if tipping was completed via the new movement system
        $movement = $booking->movements()->first();
        if ($movement) {
            return in_array($movement->current_status, ['loaded', 'ready_to_depart', 'departed']) ||
                   $movement->unloading_completed_at !== null;
        }

        // Fallback: check old system fields that might still exist
        return $booking->tipping_completed_at !== null;
    }

    protected function ensureSlotIsAvailable(int $slotId, ?int $ignoreBookingId = null): void
    {
        $slot = Slot::findOrFail($slotId);
        $end = $slot->start_at->copy()->addMinutes($slot->capacity ?? 60);
        $blocks = Slot::where('depot_id', $slot->depot_id)
            ->whereBetween('start_at', [$slot->start_at, $end->subSecond()])->get();

        foreach ($blocks as $b) {
            $count = $b->bookings()->when($ignoreBookingId, fn ($q) => $q->where('id', '!=', $ignoreBookingId))->count();
            if ($count >= $b->capacity) {
                abort(422, 'Time blocks full.');
            }
        }
    }

    public function emailPDF(Request $request, Booking $booking)
    {
        $this->ensureDepotAccess();
        $allowedDepotIds = $this->getAllowedDepotIds();

        // Check if user has access to this booking's depot
        if (! in_array($booking->slot->depot_id, $allowedDepotIds)) {
            return response()->json(['success' => false, 'message' => 'Access denied'], 403);
        }

        $request->validate([
            'email' => 'required|email',
            'message' => 'nullable|string|max:500',
        ]);

        try {
            $booking->load(['slot.depot', 'bookingType', 'customer', 'user']);

            \Log::info('Starting PDF generation for booking: '.$booking->id);

            // Generate PDF
            $pdfService = new PDFService;
            $pdf = $pdfService->generateBookingPDF('admin.bookings.pdf', compact('booking'));

            \Log::info('PDF generated successfully. Class: '.get_class($pdf));

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

            \Log::info('PDF content extracted. Size: '.strlen($pdfContent).' bytes');

            // Send email
            \Log::info('Sending email to: '.$request->input('email'));
            Mail::send('admin.bookings.email', [
                'booking' => $booking,
                'customMessage' => $request->input('message', ''),
            ], function ($mail) use ($request, $booking, $pdfContent) {
                $mail->to($request->input('email'))
                    ->subject('Booking Details #'.$booking->id)
                    ->attachData($pdfContent, "booking-{$booking->id}.pdf", [
                        'mime' => 'application/pdf',
                    ]);
            });

            \Log::info('Email sent successfully');

            return response()->json(['success' => true, 'message' => 'PDF sent successfully']);
        } catch (\Exception $e) {
            \Log::error('PDF email error: '.$e->getMessage());
            \Log::error('PDF email stack trace: '.$e->getTraceAsString());

            return response()->json(['success' => false, 'message' => 'Failed to send PDF: '.$e->getMessage()]);
        }
    }

    public function downloadPDF(Booking $booking)
    {
        $this->ensureDepotAccess();
        $allowedDepotIds = $this->getAllowedDepotIds();

        // Check if user has access to this booking's depot
        if (! in_array($booking->slot->depot_id, $allowedDepotIds)) {
            abort(403, 'You do not have access to this booking.');
        }

        $booking->load(['slot.depot', 'bookingType', 'customer', 'user']);

        $pdfService = new PDFService;
        $pdf = $pdfService->generateBookingPDF('admin.bookings.pdf', compact('booking'));

        $customerName = $booking->customer ? $booking->customer->name : 'no-customer';
        $filename = "booking-{$booking->id}-{$customerName}-".$booking->slot->start_at->format('Y-m-d').'.pdf';

        // Handle different PDF library outputs based on class type
        $class = get_class($pdf);

        if ($pdf instanceof \Mpdf\Mpdf || $pdf instanceof \mPDF\mPDF) {
            // mPDF
            return response($pdf->Output('', 'S'))
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
        } elseif ($pdf instanceof \Barryvdh\DomPDF\PDF) {
            // DomPDF - use download method directly
            return $pdf->download($filename);
        } else {
            // Unknown PDF library - throw error with class info
            throw new \Exception('Unknown PDF library class: '.$class.'. Available methods: '.implode(', ', get_class_methods($pdf)));
        }
    }

    /**
     * Display live arrivals page for depot-admin
     */
    public function arrivals(Request $request)
    {
        $this->ensureDepotAccess();
        $allowedDepotIds = $this->getAllowedDepotIds();

        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        // Get bookings for today filtered by depot access using the same logic as index - sort by slot time (oldest first)
        $query = Booking::with(['slot.depot', 'bookingType', 'customer', 'poNumbers.lines.expectedPalletType', 'poNumbers.lines.actualPalletType'])
            ->whereHas('slot', fn ($q) => $q->whereIn('depot_id', $allowedDepotIds))
            ->join('slots', 'bookings.slot_id', '=', 'slots.id')
            ->orderBy('slots.start_at', 'asc')
            ->select('bookings.*');

        // Apply filters if provided
        if ($depotId = $request->input('depot_id')) {
            $query->whereHas('slot', fn ($q) => $q->where('depot_id', $depotId));
        }

        if ($customerId = $request->input('customer_id')) {
            $query->where('customer_id', $customerId);
        }

        // Date filter - default to today if not specified
        $from = $request->input('from', $today->format('Y-m-d'));
        $to = $request->input('to', $tomorrow->format('Y-m-d'));

        if ($from && $to) {
            $query->whereHas('slot', fn ($q) => $q->whereBetween('start_at', [$from, $to.' 23:59:59']));
        }

        // Arrival status filter
        if ($arrivalStatus = $request->input('arrival')) {
            switch ($arrivalStatus) {
                case 'not_arrived':
                    $query->whereNull('arrived_at');
                    break;
                case 'arrived':
                    $query->whereNotNull('arrived_at')->whereDoesntHave('movements', function ($q) {
                    $q->whereNotNull('trailer_collected_at');
                });
                    break;
                case 'onsite':
                    $query->whereNotNull('arrived_at')->whereDoesntHave('movements', function ($q) {
                    $q->whereNotNull('trailer_collected_at');
                });
                    break;
            }
        }

        $bookings = $query->paginate(15)->withQueryString();

        // Get data for filters
        $depots = $this->getAllowedDepots();
        $customers = Customer::whereHas('bookings.slot', fn ($q) => $q->whereIn('depot_id', $allowedDepotIds))
            ->orderBy('name')->get();
        $bookingTypes = BookingType::orderBy('name')->get();

        return view('depot-admin.bookings.index', compact('bookings', 'depots', 'customers', 'bookingTypes'));
    }

    /**
     * Export bookings summary report as PDF
     */
    public function exportPDF(Request $request)
    {
        // Allow admin or users with bookings.export.pdf function
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasFunction('bookings.export.pdf')) {
            abort(403, 'You do not have permission to export bookings to PDF.');
        }
        
        $this->ensureDepotAccess();
        $allowedDepotIds = $this->getAllowedDepotIds();

        // Use the same query logic as index method with enhanced relationships
        $query = $this->buildBookingsQuery($request, $allowedDepotIds);
        $bookings = $query->with([
            'poNumbers.lines.expectedPalletType',
            'poNumbers.lines.actualPalletType',
            'movements',
        ])->get();

        // Build summary data
        $summaryByDepotCustomer = $this->buildSummaryData($bookings);
        $summaryData = $this->buildSummaryStats($bookings, $summaryByDepotCustomer);

        $filterDescription = $this->buildFilterDescription($request);

        $data = [
            'bookings' => $bookings,
            'summaryByDepotCustomer' => $summaryByDepotCustomer,
            'summaryData' => $summaryData,
            'filterDescription' => $filterDescription,
            'generatedAt' => Carbon::now(),
            'totalBookings' => $bookings->count(),
        ];

        $pdfService = new PDFService;
        $pdf = $pdfService->generateBookingPDF('admin.bookings.report-summary', $data);

        $filename = '📊-bookings-detailed-'.Carbon::now()->format('Y-m-d-H-i').'.pdf';

        if ($pdf instanceof \Mpdf\Mpdf || $pdf instanceof \mPDF\mPDF) {
            return response($pdf->Output('', 'S'))
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="'.$filename.'"');
        } elseif ($pdf instanceof \Barryvdh\DomPDF\PDF) {
            return $pdf->download($filename);
        }
    }

    /**
     * Export bookings as CSV
     */
    public function exportCSV(Request $request)
    {
        // Allow admin or users with bookings.export.csv function
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasFunction('bookings.export.csv')) {
            abort(403, 'You do not have permission to export bookings to CSV.');
        }
        
        $this->ensureDepotAccess();
        $allowedDepotIds = $this->getAllowedDepotIds();

        $query = $this->buildBookingsQuery($request, $allowedDepotIds);
        $bookings = $query->with(['poNumbers.lines.expectedPalletType', 'poNumbers.lines.actualPalletType'])->get();

        $filename = '📊-bookings-export-'.Carbon::now()->format('Y-m-d-H-i').'.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($bookings) {
            $file = fopen('php://output', 'w');

            // CSV Headers with emojis
            fputcsv($file, [
                '🆔 Booking ID', '📋 Reference', '🏢 Depot', '👤 Customer', '📦 Booking Type',
                '📅 Slot Start', '📅 Slot End', '🚛 Tipping Status', '📍 Current Status',
                '📝 PO Numbers', '🔢 Total Expected Units', '🔢 Total Actual Units', '📊 Unit Variance',
                '📦 Total Expected Pallets', '📦 Total Actual Pallets', '📊 Pallet Variance',
                '📏 Container Size', '🚗 Vehicle Registration', '📦 Container Number',
                '⏰ Arrived At', '🚪 Departed At', '📊 Status', '📝 Notes', '⚠️ Has Variance',
            ]);

            // Data rows
            foreach ($bookings as $booking) {
                // Determine status
                $status = '⏳ Scheduled';
                if ($booking->cancelled_at) {
                    $status = '❌ Cancelled';
                } elseif ($booking->arrived_at) {
                    if ($booking->departed_at) {
                        $status = '✅ Completed';
                    } else {
                        $status = '🏢 On-site';
                    }
                }

                // Get tipping status with emoji
                $tippingStatus = $booking->getCurrentMovementStatus();
                $tippingStatusWithEmoji = match ($tippingStatus) {
                    'scheduled' => '⏳ Scheduled',
                    'en_route' => '🚛 En Route',
                    'arrived' => '📍 Arrived',
                    'in_parking' => '⏸️ Waiting',
                    'in_parking' => '📍 Trailer Dropped',
                    'at_bay' => '🚛 At Bay',
                    'unloading' => '⚡ Unloading',
                    'empty' => '✅ Empty',
                    'loading' => '⚡ Loading',
                    'loaded' => '📦 Loaded',
                    'ready_to_depart' => '🚀 Ready',
                    'departed' => '🏁 Departed',
                    'trailer_collected' => '🔄 Collected',
                    default => '❓ Unknown'
                };

                // Collect PO numbers and details
                $poNumbers = '';
                $totalExpectedUnits = 0;
                $totalActualUnits = 0;
                $totalExpectedPallets = 0;
                $totalActualPallets = 0;
                $hasVariance = false;

                if ($booking->poNumbers && $booking->poNumbers->count() > 0) {
                    $poList = [];
                    foreach ($booking->poNumbers as $po) {
                        $poSummary = $po->po_number;
                        if ($po->lines->count() > 0) {
                            $poSummary .= " ({$po->lines->count()} lines, {$po->total_expected_units} units, {$po->total_expected_pallets} pallets)";
                        }
                        if ($po->hasVariance()) {
                            $poSummary .= ' ⚠️';
                            $hasVariance = true;
                        }
                        $poList[] = $poSummary;

                        $totalExpectedUnits += $po->total_expected_units;
                        $totalActualUnits += $po->total_actual_units;
                        $totalExpectedPallets += $po->total_expected_pallets;
                        $totalActualPallets += $po->total_actual_pallets;
                    }
                    $poNumbers = implode('; ', $poList);
                }

                // Calculate variances
                $unitVariance = $totalActualUnits - $totalExpectedUnits;
                $palletVariance = $totalActualPallets - $totalExpectedPallets;

                fputcsv($file, [
                    $booking->id,
                    $booking->booking_reference ?? 'N/A',
                    $booking->slot->depot->name,
                    $booking->customer ? $booking->customer->name : '❌ No Customer',
                    $booking->bookingType->name ?? 'N/A',
                    $booking->slot->start_at->format('Y-m-d H:i'),
                    $booking->slot->end_at->format('Y-m-d H:i'),
                    $tippingStatusWithEmoji,
                    $status,
                    $poNumbers ?: '❌ No PO Numbers',
                    $totalExpectedUnits,
                    $totalActualUnits,
                    $unitVariance != 0 ? ($unitVariance > 0 ? '+' : '').$unitVariance : '✅ 0',
                    $totalExpectedPallets,
                    $totalActualPallets,
                    $palletVariance != 0 ? ($palletVariance > 0 ? '+' : '').$palletVariance : '✅ 0',
                    $booking->container_size ? $booking->container_size.'ft' : 'N/A',
                    $booking->departure_vehicle_registration ?? 'N/A',
                    $booking->departure_notes ?? 'N/A',
                    $booking->arrived_at ? $booking->arrived_at->format('Y-m-d H:i') : '❌ Not arrived',
                    $booking->departed_at ? $booking->departed_at->format('Y-m-d H:i') : '❌ Not departed',
                    $status,
                    $booking->notes ?: 'No notes',
                    $hasVariance ? '⚠️ Yes' : '✅ No',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export bookings summary as Excel (using CSV format for simplicity)
     */
    public function exportExcel(Request $request)
    {
        // Allow admin or users with bookings.export.excel function
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasFunction('bookings.export.excel')) {
            abort(403, 'You do not have permission to export bookings to Excel.');
        }
        
        $this->ensureDepotAccess();
        $allowedDepotIds = $this->getAllowedDepotIds();

        $query = $this->buildBookingsQuery($request, $allowedDepotIds);
        $bookings = $query->with(['poNumbers.lines.expectedPalletType', 'poNumbers.lines.actualPalletType'])->get();
        $summaryByDepotCustomer = $this->buildSummaryData($bookings);

        $filename = '📊-bookings-detailed-'.Carbon::now()->format('Y-m-d-H-i').'.csv';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($summaryByDepotCustomer, $bookings) {
            $file = fopen('php://output', 'w');

            // Enhanced Summary Report Header with emojis
            fputcsv($file, ['📊 COMPREHENSIVE BOOKING REPORT']);
            fputcsv($file, ['🕐 Generated: '.Carbon::now()->format('Y-m-d H:i:s')]);
            fputcsv($file, ['📦 Total Bookings: '.$bookings->count()]);
            fputcsv($file, []); // Empty row

            // Detailed Booking List
            fputcsv($file, ['📋 DETAILED BOOKING LIST']);
            fputcsv($file, [
                '🆔 ID', '📋 Reference', '🏢 Depot', '👤 Customer', '📦 Type',
                '📅 Start Time', '📅 End Time', '🚛 Tipping Status', '📍 Status',
                '📝 PO Numbers Summary', '📊 PO Details',
                '🔢 Total Expected Units', '🔢 Total Actual Units', '📊 Unit Variance',
                '📦 Total Expected Pallets', '📦 Total Actual Pallets', '📊 Pallet Variance',
                '⚠️ Has Variance', '📝 Notes',
            ]);

            foreach ($bookings as $booking) {
                // Status determination
                $status = '⏳ Scheduled';
                if ($booking->cancelled_at) {
                    $status = '❌ Cancelled';
                } elseif ($booking->arrived_at) {
                    if ($booking->departed_at) {
                        $status = '✅ Completed';
                    } else {
                        $status = '🏢 On-site';
                    }
                }

                // Tipping status with emoji
                $tippingStatus = $booking->getCurrentMovementStatus();
                $tippingStatusWithEmoji = match ($tippingStatus) {
                    'scheduled' => '⏳ Scheduled',
                    'en_route' => '🚛 En Route',
                    'arrived' => '📍 Arrived',
                    'in_parking' => '⏸️ Waiting',
                    'in_parking' => '📍 Trailer Dropped',
                    'at_bay' => '🚛 At Bay',
                    'unloading' => '⚡ Unloading',
                    'empty' => '✅ Empty',
                    'loading' => '⚡ Loading',
                    'loaded' => '📦 Loaded',
                    'ready_to_depart' => '🚀 Ready',
                    'departed' => '🏁 Departed',
                    'trailer_collected' => '🔄 Collected',
                    default => '❓ Unknown'
                };

                // Collect PO information
                $poSummary = '';
                $poDetails = '';
                $totalExpectedUnits = 0;
                $totalActualUnits = 0;
                $totalExpectedPallets = 0;
                $totalActualPallets = 0;
                $hasVariance = false;

                if ($booking->poNumbers && $booking->poNumbers->count() > 0) {
                    $poSummaryList = [];
                    $poDetailsList = [];

                    foreach ($booking->poNumbers as $po) {
                        $poSummaryList[] = $po->po_number." ({$po->lines->count()} lines)";

                        // Detailed PO breakdown
                        $poDetail = $po->po_number.': ';
                        $lineDetails = [];
                        foreach ($po->lines as $line) {
                            $lineDetail = "Line {$line->line_number}: {$line->expected_cases} → {$line->actual_cases} units";
                            if ($line->expected_pallets > 0 || $line->actual_pallets > 0) {
                                $lineDetail .= ", {$line->expected_pallets} → {$line->actual_pallets} pallets";
                            }
                            if ($line->hasVariance()) {
                                $lineDetail .= ' ⚠️';
                                $hasVariance = true;
                            }
                            $lineDetails[] = $lineDetail;
                        }
                        $poDetail .= implode(' | ', $lineDetails);
                        $poDetailsList[] = $poDetail;

                        $totalExpectedUnits += $po->total_expected_units;
                        $totalActualUnits += $po->total_actual_units;
                        $totalExpectedPallets += $po->total_expected_pallets;
                        $totalActualPallets += $po->total_actual_pallets;
                    }

                    $poSummary = implode('; ', $poSummaryList);
                    $poDetails = implode(' || ', $poDetailsList);
                } else {
                    $poSummary = '❌ No PO Numbers';
                    $poDetails = 'No PO details available';
                }

                // Calculate variances
                $unitVariance = $totalActualUnits - $totalExpectedUnits;
                $palletVariance = $totalActualPallets - $totalExpectedPallets;

                fputcsv($file, [
                    $booking->id,
                    $booking->booking_reference ?? 'N/A',
                    $booking->slot->depot->name,
                    $booking->customer ? $booking->customer->name : '❌ No Customer',
                    $booking->bookingType->name ?? 'N/A',
                    $booking->slot->start_at->format('Y-m-d H:i'),
                    $booking->slot->end_at->format('Y-m-d H:i'),
                    $tippingStatusWithEmoji,
                    $status,
                    $poSummary,
                    $poDetails,
                    $totalExpectedUnits,
                    $totalActualUnits,
                    $unitVariance != 0 ? ($unitVariance > 0 ? '+' : '').$unitVariance : '✅ 0',
                    $totalExpectedPallets,
                    $totalActualPallets,
                    $palletVariance != 0 ? ($palletVariance > 0 ? '+' : '').$palletVariance : '✅ 0',
                    $hasVariance ? '⚠️ Yes' : '✅ No',
                    $booking->notes ?: 'No notes',
                ]);
            }

            fputcsv($file, []); // Empty row
            fputcsv($file, ['📊 SUMMARY BY DEPOT & CUSTOMER']);

            // Summary by depot and customer with emojis
            fputcsv($file, [
                '🏢 Depot', '👤 Customer', '✅ Arrived', '⏰ Late', '📋 Outstanding',
                '🔢 Expected Cases', '🔢 Actual Cases', '📊 Case Variance',
                '📦 Expected Pallets', '📦 Actual Pallets', '📊 Pallet Variance',
            ]);

            foreach ($summaryByDepotCustomer as $depotName => $customers) {
                foreach ($customers as $customerName => $data) {
                    if ($customerName === '_totals') {
                        continue;
                    }

                    fputcsv($file, [
                        $depotName,
                        $customerName,
                        $data['arrived'],
                        $data['late'],
                        $data['outstanding'],
                        $data['expected_cases'],
                        $data['actual_cases'],
                        $data['case_variance'] != 0 ? ($data['case_variance'] > 0 ? '+' : '').$data['case_variance'] : '✅ 0',
                        $data['expected_pallets'],
                        $data['actual_pallets'],
                        $data['pallet_variance'] != 0 ? ($data['pallet_variance'] > 0 ? '+' : '').$data['pallet_variance'] : '✅ 0',
                    ]);
                }

                // Add totals row with emoji
                if (isset($customers['_totals'])) {
                    $totals = $customers['_totals'];
                    fputcsv($file, [
                        '📊 '.$depotName.' TOTALS',
                        '',
                        $totals['arrived'],
                        $totals['late'],
                        $totals['outstanding'],
                        $totals['expected_cases'],
                        $totals['actual_cases'],
                        $totals['case_variance'] != 0 ? ($totals['case_variance'] > 0 ? '+' : '').$totals['case_variance'] : '✅ 0',
                        $totals['expected_pallets'],
                        $totals['actual_pallets'],
                        $totals['pallet_variance'] != 0 ? ($totals['pallet_variance'] > 0 ? '+' : '').$totals['pallet_variance'] : '✅ 0',
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Build the bookings query based on request filters
     */
    private function buildBookingsQuery(Request $request, array $allowedDepotIds)
    {
        $allowedCustomerIds = $this->getAllowedCustomerIds();

        $query = Booking::with(['slot.depot', 'bookingType', 'customer'])
            ->whereHas('slot', fn ($q) => $q->whereIn('depot_id', $allowedDepotIds))
            ->join('slots', 'bookings.slot_id', '=', 'slots.id')
            ->orderBy('slots.start_at', 'asc')
            ->select('bookings.*');

        // Apply customer filtering if user has restricted access
        if ($allowedCustomerIds !== null) {
            $query->whereIn('customer_id', $allowedCustomerIds)->whereNotNull('customer_id');
        }

        // Apply the same filters as index method
        if ($depotId = $request->input('depot_id')) {
            $query->whereHas('slot', fn ($q) => $q->where('depot_id', $depotId));
        }

        if ($customerId = $request->input('customer_id')) {
            $query->where('customer_id', $customerId);
        }

        if ($bookingTypeId = $request->input('booking_type_id')) {
            $query->where('booking_type_id', $bookingTypeId);
        }

        // Quick date filters (handle both 'filter' and 'quick_filter' parameters)
        if ($quickFilter = $request->input('filter') ?: $request->input('quick_filter')) {
            if ($quickFilter === 'today') {
                $query->whereHas('slot', function ($q) {
                    $q->whereDate('start_at', Carbon::today());
                });
            } elseif ($quickFilter === 'yesterday') {
                $query->whereHas('slot', function ($q) {
                    $q->whereDate('start_at', Carbon::yesterday());
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
            if ($year = $request->input('year', Carbon::now()->year)) {
                $startOfWeek = Carbon::now()->setISODate($year, $weekNumber)->startOfWeek();
                $endOfWeek = $startOfWeek->clone()->endOfWeek();

                $query->whereHas('slot', function ($q) use ($startOfWeek, $endOfWeek) {
                    $q->whereBetween('start_at', [$startOfWeek, $endOfWeek]);
                });
            }
        }

        // Date filters
        if ($from = $request->input('from')) {
            if (! $to = $request->input('to')) {
                $query->whereHas('slot', function ($q) use ($from) {
                    $q->whereDate('start_at', $from);
                });
            } else {
                $query->whereHas('slot', function ($q) use ($from, $to) {
                    $q->whereBetween('start_at', [$from, $to]);
                });
            }
        }

        // Arrival filter - expanded options
        if ($arr = $request->arrival) {
            if ($arr === 'not_arrived') {
                $query->whereNull('arrived_at');
            } elseif ($arr === 'arrived') {
                $query->whereNotNull('arrived_at');
            } elseif ($arr === 'onsite') {
                $query->whereNotNull('arrived_at')->whereDoesntHave('movements', function ($q) {
                    $q->whereNotNull('trailer_collected_at');
                });
            } elseif ($arr === 'completed') {
                $query->whereNotNull('arrived_at')->whereNotNull('departed_at');
            } elseif ($arr === 'late_runners') {
                $query->whereNull('arrived_at')
                    ->whereHas('slot', function ($q) {
                        $q->where('start_at', '<', Carbon::now());
                    });
            } elseif ($arr === 'on_time') {
                $query->whereNotNull('arrived_at')
                    ->whereRaw('arrived_at <= (SELECT start_at FROM slots WHERE slots.id = bookings.slot_id)');
            } elseif ($arr === 'arrived_late') {
                $query->whereNotNull('arrived_at')
                    ->whereRaw('arrived_at > (SELECT start_at FROM slots WHERE slots.id = bookings.slot_id)');
            }
        }

        return $query;
    }

    /**
     * Build summary data from bookings
     */
    private function buildSummaryData($bookings)
    {
        $summaryByDepotCustomer = [];

        foreach ($bookings as $b) {
            $dn = $b->slot->depot->name;
            $cn = $b->customer ? $b->customer->name : 'No Customer';
            $data = &$summaryByDepotCustomer[$dn][$cn];

            if (! isset($data)) {
                $data = [
                    'arrived' => 0, 'late' => 0, 'outstanding' => 0,
                    'expected_cases' => 0, 'actual_cases' => 0, 'case_variance' => 0,
                    'expected_pallets' => 0, 'actual_pallets' => 0, 'pallet_variance' => 0,
                    'late_duration_minutes' => 0,
                ];
            }

            $data['expected_cases'] += $b->expected_cases ?? 0;
            $data['actual_cases'] += $b->actual_cases ?? 0;
            $data['expected_pallets'] += $b->expected_pallets ?? 0;
            $data['actual_pallets'] += $b->actual_pallets ?? 0;

            $now = Carbon::now();
            $slotStart = Carbon::parse($b->slot->start_at);

            if ($b->arrived_at) {
                $data['arrived']++;
                $arrivedAt = Carbon::parse($b->arrived_at);
                if ($arrivedAt->gt($slotStart)) {
                    $data['late']++;
                    $lateMinutes = $arrivedAt->diffInMinutes($slotStart);
                    $data['late_duration_minutes'] += $lateMinutes;
                }
            } else {
                if ($now->gt($slotStart)) {
                    $data['late']++;
                    $lateMinutes = $now->diffInMinutes($slotStart);
                    $data['late_duration_minutes'] += $lateMinutes;
                }
                $data['outstanding']++;
            }
        }

        // Calculate variances and depot totals
        foreach ($summaryByDepotCustomer as $dn => $custs) {
            $totals = [
                'arrived' => 0, 'late' => 0, 'outstanding' => 0,
                'expected_cases' => 0, 'actual_cases' => 0, 'case_variance' => 0,
                'expected_pallets' => 0, 'actual_pallets' => 0, 'pallet_variance' => 0,
            ];
            foreach ($custs as $cn => $sum) {
                $summaryByDepotCustomer[$dn][$cn]['case_variance'] =
                    $sum['actual_cases'] - $sum['expected_cases'];
                $summaryByDepotCustomer[$dn][$cn]['pallet_variance'] =
                    $sum['actual_pallets'] - $sum['expected_pallets'];
                foreach (['arrived', 'late', 'outstanding', 'expected_cases', 'actual_cases', 'expected_pallets', 'actual_pallets'] as $key) {
                    $totals[$key] += $sum[$key];
                }
            }
            $totals['case_variance'] = $totals['actual_cases'] - $totals['expected_cases'];
            $totals['pallet_variance'] = $totals['actual_pallets'] - $totals['expected_pallets'];
            $summaryByDepotCustomer[$dn]['_totals'] = $totals;
        }

        return $summaryByDepotCustomer;
    }

    /**
     * Build filter description for reports
     */
    private function buildFilterDescription(Request $request)
    {
        $filters = [];

        if ($request->input('depot_id')) {
            $depot = Depot::find($request->input('depot_id'));
            if ($depot) {
                $filters[] = "Depot: {$depot->name}";
            }
        }

        if ($request->input('customer_id')) {
            $customer = Customer::find($request->input('customer_id'));
            if ($customer) {
                $filters[] = "Customer: {$customer->name}";
            }
        }

        if ($request->input('booking_type_id')) {
            $type = BookingType::find($request->input('booking_type_id'));
            if ($type) {
                $filters[] = "Type: {$type->name}";
            }
        }

        if ($quickFilter = $request->input('filter') ?: $request->input('quick_filter')) {
            $filters[] = 'Quick Filter: '.ucfirst(str_replace('_', ' ', $quickFilter));
        }

        if ($weekNumber = $request->input('week_number')) {
            $year = $request->input('year', Carbon::now()->year);
            $filters[] = "Week: {$weekNumber} ({$year})";
        }

        if ($from = $request->input('from')) {
            $to = $request->input('to');
            $filters[] = $to ? "Date: {$from} to {$to}" : "Date: {$from}";
        }

        if ($arrival = $request->input('arrival')) {
            $filters[] = 'Status: '.ucfirst(str_replace('_', ' ', $arrival));
        }

        return empty($filters) ? 'All bookings' : implode(', ', $filters);
    }

    /**
     * Build summary statistics for PDF reports
     */
    private function buildSummaryStats($bookings, $summaryByDepotCustomer)
    {
        $totalBookings = $bookings->count();
        $arrivedCount = $bookings->where('arrived_at', '!=', null)->count();
        $totalExpectedCases = $bookings->sum('expected_cases') ?? 0;
        $totalActualCases = $bookings->sum('actual_cases') ?? 0;
        $totalExpectedPallets = $bookings->sum('expected_pallets') ?? 0;
        $totalActualPallets = $bookings->sum('actual_pallets') ?? 0;

        // Count late arrivals and outstanding
        $now = Carbon::now();
        $lateCount = 0;
        $outstandingCount = 0;

        foreach ($bookings as $booking) {
            $slotStart = Carbon::parse($booking->slot->start_at);

            if ($booking->arrived_at) {
                $arrivedAt = Carbon::parse($booking->arrived_at);
                if ($arrivedAt->gt($slotStart)) {
                    $lateCount++;
                }
            } else {
                $outstandingCount++;
                if ($now->gt($slotStart)) {
                    $lateCount++;
                }
            }
        }

        $arrivedPercentage = $totalBookings > 0 ? ($arrivedCount / $totalBookings) * 100 : 0;
        $latePercentage = $totalBookings > 0 ? ($lateCount / $totalBookings) * 100 : 0;
        $outstandingPercentage = $totalBookings > 0 ? ($outstandingCount / $totalBookings) * 100 : 0;

        // Build depot breakdown for charts
        $depotBreakdown = [];
        foreach ($summaryByDepotCustomer as $depotName => $customers) {
            if (isset($customers['_totals'])) {
                $totals = $customers['_totals'];
                $depotBreakdown[$depotName] = [
                    'total' => $totals['arrived'] + $totals['late'] + $totals['outstanding'],
                    'arrived' => $totals['arrived'],
                    'late' => $totals['late'],
                    'outstanding' => $totals['outstanding'],
                    'expectedCases' => $totals['expected_cases'],
                    'actualCases' => $totals['actual_cases'],
                    'caseVariance' => $totals['case_variance'],
                    'expectedPallets' => $totals['expected_pallets'],
                    'actualPallets' => $totals['actual_pallets'],
                    'palletVariance' => $totals['pallet_variance'],
                ];
            }
        }

        return [
            'totalBookings' => $totalBookings,
            'arrivedCount' => $arrivedCount,
            'lateCount' => $lateCount,
            'outstandingCount' => $outstandingCount,
            'arrivedPercentage' => $arrivedPercentage,
            'latePercentage' => $latePercentage,
            'outstandingPercentage' => $outstandingPercentage,
            'totalExpectedCases' => $totalExpectedCases,
            'totalActualCases' => $totalActualCases,
            'totalCaseVariance' => $totalActualCases - $totalExpectedCases,
            'totalExpectedPallets' => $totalExpectedPallets,
            'totalActualPallets' => $totalActualPallets,
            'totalPalletVariance' => $totalActualPallets - $totalExpectedPallets,
            'depotBreakdown' => $depotBreakdown,
        ];
    }

    /**
     * Transfer booking from current bay to another bay
     */
    public function transferBay(Request $request, Booking $booking)
    {
        $this->ensureDepotAccess();
        $allowedDepotIds = $this->getAllowedDepotIds();

        // Check if user has access to this booking's depot
        if (! in_array($booking->slot->depot_id, $allowedDepotIds)) {
            abort(403, 'You do not have access to this booking.');
        }

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'new_bay_id' => 'required|exists:tipping_bays,id',
                'transfer_reason' => 'required|string|max:255',
            ]);

            $newBay = \App\Models\TippingBay::findOrFail($validated['new_bay_id']);

            // Ensure the new bay is in an allowed depot
            if (! in_array($newBay->depot_id, $allowedDepotIds)) {
                return back()->withErrors(['new_bay_id' => 'You do not have access to this bay.']);
            }

            if ($booking->transferToBay($newBay, $validated['transfer_reason'])) {
                return redirect()
                    ->route('app.bookings.show', $booking)
                    ->with('success', "Booking transferred to {$newBay->name} successfully.");
            } else {
                return back()->withErrors(['transfer' => 'Unable to transfer booking. Check bay availability and booking status.']);
            }
        }

        // GET request - show transfer form
        $movement = $booking->getOrCreateMovement();
        $availableBays = \App\Models\TippingBay::with('depot')
            ->whereIn('depot_id', $allowedDepotIds)
            ->where('is_active', true)
            ->where('id', '!=', $movement->tipping_bay_id) // Exclude current bay
            ->orderBy('name')
            ->get();

        return view('warehouse.bookings.transfer-bay', compact('booking', 'availableBays'));
    }

    /**
     * Quick action: Move empty trailer to waiting area
     */
    public function moveToWaitingArea(Request $request, Booking $booking)
    {
        $this->ensureDepotAccess();
        $allowedDepotIds = $this->getAllowedDepotIds();
        
        if (!in_array($booking->slot->depot_id, $allowedDepotIds)) {
            abort(403, 'You do not have access to this booking.');
        }

        $movement = $booking->getOrCreateMovement();
        
        // Can only move if trailer is empty or completed
        if (!in_array($movement->current_status, ['empty', 'at_bay'])) {
            return back()->withErrors(['action' => 'Trailer must be empty to move to waiting area.']);
        }

        // Find available drop location or use the original one
        $dropLocation = $movement->tippingLocation;
        if (!$dropLocation) {
            $dropLocation = \App\Models\TippingLocation::whereIn('depot_id', $allowedDepotIds)
                ->where('is_active', true)
                ->first();
        }

        if (!$dropLocation) {
            return back()->withErrors(['action' => 'No waiting area available.']);
        }

        \DB::transaction(function () use ($booking, $movement, $dropLocation) {
            // Free up the bay if occupied
            if ($movement->tippingBay) {
                $movement->tippingBay->markAvailable($booking);
            }

            // Move to waiting area
            $movement->update([
                'current_status' => 'in_parking',
                'tipping_bay_id' => null,
                'tipping_location_id' => $dropLocation->id,
                'custom_fields' => array_merge(
                    $movement->custom_fields ?? [],
                    ['trailer_status' => 'empty_available']
                ),
                'operation_notes' => ($movement->operation_notes ?? '') . "\n[Quick Move] " . now()->format('M j H:i') . " - Moved to waiting area for collection"
            ]);

            \App\Models\BookingHistory::recordAction(
                $booking,
                'modified',
                "Empty trailer moved to waiting area: {$dropLocation->name}",
                null,
                null,
                ['location_id' => $dropLocation->id, 'action_type' => 'quick_move_to_waiting']
            );
        });

        return back()->with('success', 'Trailer moved to waiting area successfully - bay is now available for next vehicle.');
    }

    /**
     * Quick action: Clear bay immediately after tipping
     */
    public function clearBay(Request $request, Booking $booking)
    {
        $this->ensureDepotAccess();
        $allowedDepotIds = $this->getAllowedDepotIds();
        
        if (!in_array($booking->slot->depot_id, $allowedDepotIds)) {
            abort(403, 'You do not have access to this booking.');
        }

        $movement = $booking->getOrCreateMovement();
        
        // Can only clear if tipping is complete
        if ($movement->current_status !== 'empty') {
            return back()->withErrors(['action' => 'Tipping must be completed before clearing bay.']);
        }

        $bayName = $movement->tippingBay?->name ?? 'Unknown Bay';

        \DB::transaction(function () use ($booking, $movement, $bayName) {
            // Mark bay as available
            if ($movement->tippingBay) {
                $movement->tippingBay->markAvailable($booking);
            }

            // Update movement to indicate bay cleared
            $movement->update([
                'tipping_bay_id' => null,
                'operation_notes' => ($movement->operation_notes ?? '') . "\n[Bay Cleared] " . now()->format('M j H:i') . " - Bay cleared for next vehicle"
            ]);

            \App\Models\BookingHistory::recordAction(
                $booking,
                'modified',
                "Bay cleared - {$bayName} now available",
                null,
                null,
                ['action_type' => 'quick_clear_bay', 'bay_name' => $bayName]
            );
        });

        return back()->with('success', "Bay {$bayName} cleared successfully - ready for next vehicle.");
    }

    /**
     * Fix historical bookings with collected trailers but no departed_at
     */
    public function fixHistoricalDepartures(Request $request)
    {
        $this->ensureDepotAccess();
        
        if ($request->isMethod('post')) {
            $fixed = 0;
            
            // Find bookings that have trailer_collected status but no departed_at
            $bookingsToFix = Booking::whereNotNull('arrived_at')
                ->whereNull('departed_at')
                ->whereHas('movements', function ($q) {
                    $q->whereIn('current_status', ['trailer_collected', 'departed']);
                })
                ->with('movements')
                ->get();

            foreach ($bookingsToFix as $booking) {
                $movement = $booking->movements()->first();
                
                // Use trailer_collected_at, actual_departure, or movement updated_at as departure time
                $departureTime = $movement->trailer_collected_at 
                    ?? $movement->actual_departure 
                    ?? $movement->updated_at;

                if ($departureTime) {
                    $booking->update([
                        'departed_at' => $departureTime
                    ]);
                    
                    \App\Models\BookingHistory::recordAction(
                        $booking,
                        'modified',
                        'Fixed historical departure time from collection record',
                        null,
                        null,
                        ['departure_time' => $departureTime, 'action_type' => 'historical_fix']
                    );
                    
                    $fixed++;
                }
            }
            
            return back()->with('success', "Fixed {$fixed} historical booking records with missing departure times.");
        }
        
        // GET request - show bookings that need fixing
        $bookingsNeedingFix = Booking::whereNotNull('arrived_at')
            ->whereNull('departed_at')
            ->whereHas('movements', function ($q) {
                $q->whereIn('current_status', ['trailer_collected', 'departed']);
            })
            ->with(['movements', 'slot.depot', 'customer'])
            ->orderBy('arrived_at', 'desc')
            ->get();

        return view('warehouse.bookings.fix-historical-departures', compact('bookingsNeedingFix'));
    }

    /**
     * Start tipping process for booking
     */
    public function startTipping(Request $request, Booking $booking)
    {
        $this->ensureDepotAccess();
        $allowedDepotIds = $this->getAllowedDepotIds();

        // Check if user has access to this booking's depot
        if (! in_array($booking->slot->depot_id, $allowedDepotIds)) {
            abort(403, 'You do not have access to this booking.');
        }

        if ($booking->startTipping(auth()->id(), 'Started via booking view')) {
            return back()->with('success', 'Tipping started successfully.');
        } else {
            return back()->withErrors(['tipping' => 'Unable to start tipping. Check booking status and bay assignment.']);
        }
    }

    /**
     * Complete tipping process for booking
     */
    public function completeTipping(Request $request, Booking $booking)
    {
        $this->ensureDepotAccess();
        $allowedDepotIds = $this->getAllowedDepotIds();

        // Check if user has access to this booking's depot
        if (! in_array($booking->slot->depot_id, $allowedDepotIds)) {
            abort(403, 'You do not have access to this booking.');
        }

        $validated = $request->validate([
            'immediate_depart' => 'sometimes|boolean',
            'tipping_notes' => 'nullable|string|max:500',
            'actual_pallets' => 'nullable|array',
            'actual_pallets.*' => 'nullable|integer|min:0|max:999',
        ]);

        $immediateDepart = $validated['immediate_depart'] ?? false;
        $notes = $validated['tipping_notes'] ?? 'Completed via booking view';
        $actualPallets = $validated['actual_pallets'] ?? [];

        // Update actual pallet counts before completing tipping
        if (!empty($actualPallets)) {
            foreach ($actualPallets as $poId => $actualCount) {
                $po = $booking->poNumbers()->with('lines')->find($poId);
                if ($po && $actualCount !== null) {
                    // Distribute the actual pallets across the PO lines proportionally
                    if ($po->lines->count() > 0) {
                        $totalExpected = $po->lines->sum('expected_pallets');
                        if ($totalExpected > 0) {
                            $remainingActual = $actualCount;
                            $processedLines = 0;
                            
                            foreach ($po->lines as $line) {
                                $processedLines++;
                                if ($processedLines === $po->lines->count()) {
                                    // Last line gets remaining pallets to avoid rounding errors
                                    $lineActual = $remainingActual;
                                } else {
                                    $expectedRatio = $line->expected_pallets / $totalExpected;
                                    $lineActual = round($actualCount * $expectedRatio);
                                    $remainingActual -= $lineActual;
                                }
                                $line->update(['actual_pallets' => max(0, $lineActual)]);
                            }
                        } else {
                            // No expected pallets, distribute evenly
                            $palletPerLine = floor($actualCount / $po->lines->count());
                            $remainder = $actualCount % $po->lines->count();
                            
                            foreach ($po->lines as $index => $line) {
                                $lineActual = $palletPerLine + ($index < $remainder ? 1 : 0);
                                $line->update(['actual_pallets' => $lineActual]);
                            }
                        }
                    }
                }
            }
            
            $notes .= "\nActual pallets recorded: " . json_encode($actualPallets);
        }

        if ($booking->completeTipping($notes, null, $immediateDepart)) {
            $message = 'Tipping completed successfully.';
            if (!empty($actualPallets)) {
                $message .= ' Actual pallet counts have been recorded.';
            }
            if ($immediateDepart) {
                $message .= ' Vehicle marked as departed at tipping completion time.';
            }

            return back()->with('success', $message);
        } else {
            return back()->withErrors(['tipping' => 'Unable to complete tipping. Check booking status.']);
        }
    }

    public function unbook(Request $request, Booking $booking)
    {
        // Check access
        $this->ensureDepotAccess();
        $allowedDepotIds = $this->getAllowedDepotIds();
        if (! in_array($booking->slot->depot_id, $allowedDepotIds)) {
            abort(403, 'You do not have access to this booking.');
        }

        // Only allow unbooking if arrived
        if (! $booking->arrived_at) {
            return back()->withErrors(['error' => 'This booking has not been marked as arrived yet.']);
        }

        // Get the movement to check current status
        $movement = $booking->movements()->first();
        
        // Don't allow unbooking if tipping has started
        if ($movement && in_array($movement->current_status, ['unloading', 'empty', 'departed'])) {
            return back()->withErrors(['error' => 'Cannot unbook: Tipping process has already started or completed.']);
        }

        // Clear arrival data from booking
        $booking->update([
            'arrived_at' => null,
            'status' => 'scheduled',
        ]);

        // Reset movement if it exists
        if ($movement) {
            // Clear movement data but keep the basic record
            $movement->update([
                'current_status' => 'scheduled',
                'actual_arrival' => null,
                'additional_data' => null,
                'custom_fields' => null,
                'tipping_location_id' => null,
                'tipping_bay_id' => null,
                'in_parking_at' => null,
                'moved_to_bay_at' => null,
            ]);

            // Free up any occupied location or bay
            if ($movement->tippingLocation) {
                $movement->tippingLocation->markAvailable($booking);
            }
            if ($movement->tippingBay) {
                $movement->tippingBay->markAvailable($booking);
            }
        }

        // Record history
        \App\Models\BookingHistory::recordAction(
            $booking,
            'modified',
            'Vehicle arrival cancelled - booking reset to scheduled status',
            null,
            null,
            ['action_type' => 'unbooked', 'user' => auth()->user()->name]
        );

        return back()->with('success', 'Vehicle arrival has been cancelled. Booking reset to scheduled status.');
    }

    public function trailerOperationsDashboard(Request $request)
    {
        $this->ensureDepotAccess();
        $allowedDepotIds = $this->getAllowedDepotIds();

        // Get all movements currently on site with comprehensive data (including factory bookings)
        $movementsOnSite = Movement::with([
            'booking.slot.depot', 
            'booking.customer', 
            'booking.poNumbers',
            'factoryBooking.depot',
            'factoryBooking.customer',
            'factoryBooking.poNumbers',
            'tippingBay', 
            'tippingLocation'
        ])
            ->where(function($query) use ($allowedDepotIds) {
                // Regular bookings
                $query->whereNotNull('booking_id')
                      ->whereHas('booking.slot', fn($q) => $q->whereIn('depot_id', $allowedDepotIds))
                      // Factory bookings
                      ->orWhere(function($subQuery) use ($allowedDepotIds) {
                          $subQuery->whereNotNull('factory_booking_id')
                                   ->whereHas('factoryBooking', fn($q) => $q->whereIn('depot_id', $allowedDepotIds));
                      });
            })
            ->whereIn('current_status', ['arrived', 'in_parking', 'in_parking', 'in_parking', 'at_bay', 'unloading', 'empty', 'trailer_collected'])
            ->where(function($query) {
                // For regular departures
                $query->whereNull('actual_departure')
                      // For unit departures (where unit left but trailer may still be on site)
                      ->orWhere(function($subQuery) {
                          $subQuery->whereNotNull('unit_departed_at')
                                   ->whereNull('collection_unit_departed_at');
                      });
            })
            ->get()
            ->map(function ($movement) {
                // Handle both regular bookings and factory bookings
                $booking = $movement->booking;
                $factoryBooking = $movement->factoryBooking;
                $isFactory = $factoryBooking !== null;
                $activeBooking = $isFactory ? $factoryBooking : $booking;
                
                // Calculate comprehensive timing data
                $arrival = $movement->actual_arrival ?? $activeBooking?->arrived_at;
                
                // Determine if trailer is loaded or empty
                $isLoaded = !in_array($movement->current_status, ['empty', 'departed']);
                // Only truly detached if status is 'in_parking' - otherwise still attached
                $isAttached = $movement->current_status !== 'in_parking';
                
                // Calculate durations (ensure positive values)
                $timeOnSite = $arrival ? $arrival->diffInMinutes(now()) : 0;
                $tippingDuration = null;
                if ($movement->unloading_started_at && $movement->unloading_completed_at) {
                    $tippingDuration = $movement->unloading_started_at->diffInMinutes($movement->unloading_completed_at);
                }
                
                // Calculate time in current status
                $timeInCurrentStatus = null;
                $statusStartTime = match($movement->current_status) {
                    'at_bay' => $movement->moved_to_bay_at,
                    'unloading' => $movement->unloading_started_at,
                    'in_parking' => $movement->moved_to_location_at,
                    'in_parking' => $movement->in_parking_at,
                    'empty' => $movement->unloading_completed_at,
                    default => $arrival,
                };
                
                if ($statusStartTime) {
                    $timeInCurrentStatus = $statusStartTime->diffInMinutes(now());
                }
                
                // Determine workflow priority (1 = highest)
                $priority = match($movement->current_status) {
                    'unloading' => 1, // Currently tipping - highest priority
                    'at_bay' => 2,    // Ready to start tipping
                    'empty' => 3,     // Need to clear bay/location
                    'in_parking' => 4, // Waiting to move to bay
                    'in_parking' => 5, // Detached, waiting
                    'arrived' => 6,   // Just arrived
                    default => 7,
                };
                
                // Add calculated fields to movement
                $movement->calculated_data = [
                    'is_loaded' => $isLoaded,
                    'is_attached' => $isAttached,
                    'time_on_site_minutes' => $timeOnSite,
                    'time_in_current_status_minutes' => $timeInCurrentStatus,
                    'tipping_duration_minutes' => $tippingDuration,
                    'workflow_priority' => $priority,
                    'arrival_time' => $arrival,
                    'status_start_time' => $statusStartTime,
                ];
                
                return $movement;
            })
            ->sortBy([
                ['calculated_data.workflow_priority', 'asc'],
                ['calculated_data.time_in_current_status_minutes', 'desc']
            ]);

        return view('warehouse.bookings.trailer-operations-dashboard', compact('movementsOnSite'));
    }

    public function operationsControl(Request $request)
    {
        $this->ensureDepotAccess();
        $allowedDepotIds = $this->getAllowedDepotIds();
        
        // Get user's default depot for action restrictions
        $user = auth()->user();
        $defaultDepotId = $user->depot_id ?? $allowedDepotIds[0] ?? null;
        
        // Allow viewing all depots but note which is default for actions
        $selectedDepotId = $request->get('depot_id');
        
        // Show all allowed depots for viewing, but track default for actions
        if ($selectedDepotId && in_array($selectedDepotId, $allowedDepotIds)) {
            $currentDepotId = $selectedDepotId;
        } elseif ($selectedDepotId === "") {
            // Explicitly selected "All Depots"
            $currentDepotId = null;
        } else {
            $currentDepotId = null; // Show all depots
        }
        
        // Filter movements by selected depot or show all allowed depots
        $depotIds = $currentDepotId ? [$currentDepotId] : $allowedDepotIds;

        // Get all active movements with streamlined data for operations
        $activeMovements = Movement::with([
            'booking.slot.depot', 
            'booking.customer', 
            'booking.poNumbers',
            'tippingBay', 
            'tippingLocation'
        ])
            ->whereNotNull('booking_id')
            ->whereHas('booking.slot', fn($q) => $q->whereIn('depot_id', $depotIds))
            ->whereIn('current_status', ['arrived', 'in_parking', 'in_parking', 'at_bay', 'unloading', 'empty', 'in_parking', 'trailer_collected'])
            ->where(function($query) {
                $query->whereNull('actual_departure')
                      ->whereNull('collection_unit_departed_at');
            })
            ->orderByRaw("FIELD(current_status, 'unloading', 'at_bay', 'empty', 'in_parking', 'in_parking', 'trailer_collected', 'arrived', 'in_parking')")
            ->orderBy('created_at', 'asc')
            ->get();

        // Calculate summary statistics
        $stats = [
            'on_site' => $activeMovements->count(),
            'in_drop_zone' => $activeMovements->whereIn('current_status', ['in_parking', 'in_parking'])->count(),
            'at_bay' => $activeMovements->where('current_status', 'at_bay')->count(),
            'tipping' => $activeMovements->where('current_status', 'unloading')->count(),
            'empty' => $activeMovements->where('current_status', 'empty')->count(),
            'awaiting_collection' => $activeMovements->where('current_status', 'in_parking')->count(),
            'being_collected' => $activeMovements->where('current_status', 'trailer_collected')->count(),
        ];
        
        // Get all allowed depots for depot selector
        $allDepots = \App\Models\Depot::whereIn('id', $allowedDepotIds)->get();

        return view('warehouse.bookings.operations-control', compact(
            'activeMovements', 
            'stats', 
            'allDepots', 
            'currentDepotId',
            'defaultDepotId',
            'allowedDepotIds'
        ));
    }

    /**
     * Show arrival form for a booking
     */
    public function arrivalForm(Booking $booking)
    {
        $this->ensureDepotAccess();
        $allowedDepotIds = $this->getAllowedDepotIds();
        $bookingDepotId = $booking->slot->depot_id;

        // Ensure user has access to this booking's depot
        if (!in_array($bookingDepotId, $allowedDepotIds)) {
            abort(403, 'Access denied to this booking\'s depot');
        }

        // Get tipping locations and bays for the booking's specific depot only
        $tippingLocations = \App\Models\TippingLocation::with('depot')
            ->where('depot_id', $bookingDepotId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $tippingBays = \App\Models\TippingBay::with('depot')
            ->where('depot_id', $bookingDepotId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $trailerTypes = \App\Models\TrailerType::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('warehouse.bookings.arrival-form', compact('booking', 'tippingLocations', 'tippingBays', 'trailerTypes'));
    }

    /**
     * Process arrival for a booking
     */
    public function arrival(Request $request, Booking $booking)
    {
        $this->ensureDepotAccess();
        $allowedDepotIds = $this->getAllowedDepotIds();
        $bookingDepotId = $booking->slot->depot_id;

        // Ensure user has access to this booking's depot
        if (!in_array($bookingDepotId, $allowedDepotIds)) {
            abort(403, 'Access denied to this booking\'s depot');
        }

        $validated = $request->validate([
            'vehicle_registration' => 'required|string|max:50',
            'container_number' => 'nullable|string|max:50',
            'carrier_id' => 'nullable|exists:carriers,id',
            'carrier_name' => 'required|string|max:100',
            'trailer_type_id' => 'required|exists:trailer_types,id',
            'tipping_location_id' => 'nullable|exists:tipping_locations,id',
            'tipping_bay_id' => 'nullable|exists:tipping_bays,id',
            'tipping_type' => 'required|in:live_tip,drop',
        ]);

        // Normalize input data
        if (!empty($validated['vehicle_registration'])) {
            $validated['vehicle_registration'] = $this->normalizeVehicleRegistration($validated['vehicle_registration']);
        }
        if (!empty($validated['container_number'])) {
            $validated['container_number'] = $this->normalizeContainerNumber($validated['container_number']);
        }
        if (!empty($validated['carrier_name'])) {
            $validated['carrier_name'] = $this->normalizeCarrierName($validated['carrier_name']);
        }

        // Update booking basic info
        $arrivalTime = now();
        $basicBookingData = [
            'arrived_at' => $arrivalTime,
            'status' => 'in_progress',
            'vehicle_registration' => $validated['vehicle_registration'],
            'container_number' => $validated['container_number'] ?? null,
            'carrier_id' => $validated['carrier_id'] ?? null,
            'trailer_type_id' => $validated['trailer_type_id'],
            'tipping_type' => $validated['tipping_type'],
        ];

        // Update booking with arrival data
        $booking->update($basicBookingData);

        // Create or update movement record
        $movement = $booking->getOrCreateMovement();
        $movement->update([
            'actual_arrival' => $arrivalTime,
            'current_status' => 'arrived',
            'carrier_company' => $validated['carrier_name'],
        ]);

        // Record arrival status using configurable arrival time rules
        \App\Models\BookingHistory::recordArrival($booking, $arrivalTime, 'Vehicle arrived on site');

        // Handle direct assignment to location or bay if specified
        if (!empty($validated['tipping_location_id'])) {
            $tippingLocation = \App\Models\TippingLocation::find($validated['tipping_location_id']);
            
            if ($tippingLocation && $tippingLocation->isAvailable() && $tippingLocation->depot_id === $bookingDepotId) {
                $booking->dropTrailer($tippingLocation, 'Vehicle arrived and directly assigned to drop location');
                return redirect()->route('app.bookings.index')->with('success', 'Vehicle arrived and moved to drop location: '.$tippingLocation->name);
            }
        }

        return redirect()->route('app.bookings.index')->with('success', 'Vehicle arrived on site - marked as in progress.');
    }

    /**
     * Get the tipping status for a factory booking based on its movements
     */
    private function getFactoryBookingTippingStatus($factoryBooking)
    {
        $movement = $factoryBooking->movements()->first();
        
        if (!$movement) {
            return $factoryBooking->completed_at ? 'completed' : 'processing';
        }
        
        // Map movement status to tipping status
        return match ($movement->current_status) {
            'scheduled', 'en_route' => 'scheduled',
            'arrived' => 'arrived',
            'in_parking' => 'waiting',
            'at_bay' => 'at_bay',
            'unloading' => 'tipping_in_progress',
            'empty' => 'tipping_completed',
            'ready_to_depart', 'departed' => 'departed',
            default => $factoryBooking->completed_at ? 'completed' : 'processing'
        };
    }
}
