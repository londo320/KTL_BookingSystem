<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\FactoryBooking;
use App\Models\Depot;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomerDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:customer|customer-admin']);
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Get customer's assigned companies
        $userCustomerIds = $user->customers()->pluck('customers.id')->toArray();
        
        if (empty($userCustomerIds)) {
            // If no customers assigned, show empty dashboard
            $userCustomerIds = [0]; // No customer will have ID 0
        }

        // Apply depot filter if provided, or restore from session
        $depotFilter = $request->input('depot_id');
        $allowedDepotIds = null; // Customers can see all depots for their bookings

        // Session-based filtering
        if (! $depotFilter && $request->session()->has('customer_dashboard_depot_filter')) {
            $depotFilter = $request->session()->get('customer_dashboard_depot_filter');
        }

        if ($depotFilter) {
            $allowedDepotIds = [$depotFilter];
            $request->session()->put('customer_dashboard_depot_filter', $depotFilter);
        } elseif ($request->has('depot_id') && $request->input('depot_id') === '') {
            $request->session()->forget('customer_dashboard_depot_filter');
        }

        // Today's stats
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        $todaysBookingsQuery = Booking::whereIn('customer_id', $userCustomerIds)
            ->whereHas('slot', function ($q) use ($allowedDepotIds, $today, $tomorrow) {
                $q->whereBetween('start_at', [$today, $tomorrow]);
                if ($allowedDepotIds) {
                    $q->whereIn('depot_id', $allowedDepotIds);
                }
            });

        $todaysBookings = $todaysBookingsQuery->with(['slot.depot', 'customer', 'bookingType'])->get();

        // Current arrivals on site - Regular bookings
        $currentlyOnSiteQuery = Booking::whereIn('customer_id', $userCustomerIds)
            ->whereNotNull('arrived_at')
            ->whereNull('departed_at')
            ->whereDoesntHave('movements', function ($q) {
                $q->whereIn('current_status', ['departed', 'trailer_collected']);
            });

        if ($allowedDepotIds) {
            $currentlyOnSiteQuery->whereHas('slot', function ($q) use ($allowedDepotIds) {
                $q->whereIn('depot_id', $allowedDepotIds);
            });
        }

        $regularOnSite = $currentlyOnSiteQuery->count();

        // Current arrivals on site - Factory bookings
        $factoryOnSiteQuery = FactoryBooking::whereIn('customer_id', $userCustomerIds)
            ->whereNotNull('arrived_at')
            ->whereNull('completed_at');

        if ($allowedDepotIds) {
            $factoryOnSiteQuery->whereIn('depot_id', $allowedDepotIds);
        }

        $factoryOnSite = $factoryOnSiteQuery->count();

        $currentlyOnSite = $regularOnSite + $factoryOnSite;

        // Today's arrivals
        $todaysArrivalsQuery = Booking::whereIn('customer_id', $userCustomerIds)
            ->whereDate('arrived_at', $today);

        if ($allowedDepotIds) {
            $todaysArrivalsQuery->whereHas('slot', function ($q) use ($allowedDepotIds) {
                $q->whereIn('depot_id', $allowedDepotIds);
            });
        }

        $todaysArrivals = $todaysArrivalsQuery->count();

        // Late runners (customer's bookings that were scheduled before now but haven't arrived)
        $lateRunnersQuery = Booking::whereIn('customer_id', $userCustomerIds)
            ->whereHas('slot', function ($q) use ($allowedDepotIds) {
                $q->where('start_at', '<', now());
                if ($allowedDepotIds) {
                    $q->whereIn('depot_id', $allowedDepotIds);
                }
            })
            ->whereNull('arrived_at');

        $lateRunners = $lateRunnersQuery->count();

        $stats = [
            'total_bookings' => $todaysBookings->count(),
            'arrived' => $todaysArrivals,
            'departed' => $todaysBookings->whereNotNull('departed_at')->count(),
            'outstanding' => $todaysBookings->whereNull('arrived_at')->count(),
            'on_site' => $currentlyOnSite,
            'late_runners' => $lateRunners,
        ];

        // Upcoming bookings (next 3 hours)
        $upcomingBookingsQuery = Booking::whereIn('customer_id', $userCustomerIds)
            ->whereHas('slot', function ($q) use ($allowedDepotIds) {
                $q->whereBetween('start_at', [now(), now()->addHours(3)]);
                if ($allowedDepotIds) {
                    $q->whereIn('depot_id', $allowedDepotIds);
                }
            })
            ->whereNull('arrived_at');

        $upcomingBookings = $upcomingBookingsQuery->with(['slot.depot', 'customer', 'bookingType'])
            ->join('slots', 'bookings.slot_id', '=', 'slots.id')
            ->orderBy('slots.start_at', 'asc')
            ->select('bookings.*')
            ->take(10)
            ->get();

        // Current arrivals - Regular bookings
        $currentArrivalsQuery = Booking::whereIn('customer_id', $userCustomerIds)
            ->whereNotNull('arrived_at')
            ->whereNull('departed_at')
            ->whereDoesntHave('movements', function ($q) {
                $q->whereIn('current_status', ['departed', 'trailer_collected']);
            });

        if ($allowedDepotIds) {
            $currentArrivalsQuery->whereHas('slot', function ($q) use ($allowedDepotIds) {
                $q->whereIn('depot_id', $allowedDepotIds);
            });
        }

        $regularArrivals = $currentArrivalsQuery->with(['slot.depot', 'customer', 'bookingType'])
            ->orderBy('arrived_at', 'asc')
            ->get();

        // Current arrivals - Factory bookings
        $factoryArrivalsQuery = FactoryBooking::whereIn('customer_id', $userCustomerIds)
            ->whereNotNull('arrived_at')
            ->whereNull('completed_at');

        if ($allowedDepotIds) {
            $factoryArrivalsQuery->whereIn('depot_id', $allowedDepotIds);
        }

        $factoryArrivals = $factoryArrivalsQuery->with(['depot', 'customer'])
            ->orderBy('arrived_at', 'asc')
            ->get()
            ->map(function ($factoryBooking) {
                // Transform factory booking to look like regular booking for consistency
                return (object) [
                    'id' => $factoryBooking->id,
                    'booking_reference' => $factoryBooking->reference,
                    'customer' => $factoryBooking->customer,
                    'arrived_at' => $factoryBooking->arrived_at,
                    'vehicle_registration' => $factoryBooking->vehicle_registration,
                    'trailer_registration' => $factoryBooking->trailer_registration,
                    'slot' => (object) [
                        'depot' => $factoryBooking->depot,
                    ],
                    'gate_number' => null,
                    'bay_number' => null,
                    'container_number' => null,
                    'type' => 'factory', // Mark as factory booking
                ];
            });

        // Combine regular and factory arrivals
        $currentArrivals = $regularArrivals->concat($factoryArrivals)->sortBy('arrived_at');

        // Late runners data
        $lateRunnersDataQuery = Booking::whereIn('customer_id', $userCustomerIds)
            ->whereHas('slot', function ($q) use ($allowedDepotIds) {
                $q->where('start_at', '<', now());
                if ($allowedDepotIds) {
                    $q->whereIn('depot_id', $allowedDepotIds);
                }
            })
            ->whereNull('arrived_at');

        $lateRunnersData = $lateRunnersDataQuery->with(['slot.depot', 'customer', 'bookingType'])
            ->join('slots', 'bookings.slot_id', '=', 'slots.id')
            ->orderBy('slots.start_at', 'asc')
            ->select('bookings.*')
            ->take(10)
            ->get();

        // Get depots for filter (all depots that customer has bookings in)
        $userDepots = Depot::whereHas('slots.bookings', function ($q) use ($userCustomerIds) {
            $q->whereIn('customer_id', $userCustomerIds);
        })->get();

        // Get customer companies
        $userCustomers = $user->customers;

        return view('customer.dashboard', compact(
            'stats',
            'todaysBookings',
            'upcomingBookings',
            'currentArrivals',
            'lateRunnersData',
            'userDepots',
            'userCustomers',
            'depotFilter'
        ));
    }
}
