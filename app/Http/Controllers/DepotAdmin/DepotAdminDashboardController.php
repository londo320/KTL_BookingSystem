<?php

namespace App\Http\Controllers\DepotAdmin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Depot;
use App\Models\Slot;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DepotAdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin|depot-admin|site-admin|warehouse']);
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        // Get depots based on user role and assignments
        $userAssignedDepotIds = $user->depots()->pluck('depots.id')->toArray();

        // If no depots assigned, they see nothing
        if (empty($userAssignedDepotIds)) {
            $userAssignedDepotIds = [0]; // No depot will have ID 0
        }

        // Apply depot filter if provided, or restore from session
        $allowedDepotIds = $userAssignedDepotIds;
        $depotFilter = $request->input('depot_id');

        // If no filter in request, try to get from session
        if (! $depotFilter && $request->session()->has('dashboard_depot_filter')) {
            $depotFilter = $request->session()->get('dashboard_depot_filter');
        }

        if ($depotFilter) {
            // Only allow filtering to depots the user has access to
            if (in_array($depotFilter, $userAssignedDepotIds)) {
                $allowedDepotIds = [$depotFilter];
                // Store filter in session
                $request->session()->put('dashboard_depot_filter', $depotFilter);
            } else {
                // Clear invalid filter from session
                $request->session()->forget('dashboard_depot_filter');
            }
        } elseif ($request->has('depot_id') && $request->input('depot_id') === '') {
            // User explicitly cleared the filter
            $request->session()->forget('dashboard_depot_filter');
        }

        // Get customer filtering based on user role and assignments
        $allowedCustomerIds = null;
        if ($user->hasRole('admin')) {
            // Admin: show all customers ONLY if no customers assigned in pivot
            $assignedCustomerIds = $user->customers()->pluck('customers.id')->toArray();
            if (! empty($assignedCustomerIds)) {
                // If specific customers assigned, filter to those only
                $allowedCustomerIds = $assignedCustomerIds;
            }
            // If no customer assignments, admin can see all customers (null = no filter)
        } elseif ($user->hasRole('customer')) {
            // Customer role: only show their assigned customers
            $allowedCustomerIds = $user->customers()->pluck('customers.id')->toArray();
            if (empty($allowedCustomerIds)) {
                $allowedCustomerIds = [0]; // No customer will have ID 0
            }
        } else {
            // Depot-admin/site-admin: check if they have specific customer assignments
            $assignedCustomerIds = $user->customers()->pluck('customers.id')->toArray();
            if (! empty($assignedCustomerIds)) {
                // If specific customers assigned, filter to those only
                $allowedCustomerIds = $assignedCustomerIds;
            }
            // If no customer assignments, they can see all customers (null = no filter)
        }

        // Today's stats
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        // Get today's bookings for most stats
        $todaysBookingsQuery = Booking::whereHas('slot', function ($q) use ($allowedDepotIds, $today, $tomorrow) {
            $q->whereIn('depot_id', $allowedDepotIds)
                ->whereBetween('start_at', [$today, $tomorrow]);
        });

        // Add customer filtering for customer users
        if ($allowedCustomerIds !== null) {
            $todaysBookingsQuery->whereIn('customer_id', $allowedCustomerIds)->whereNotNull('customer_id');
        }

        $todaysBookings = $todaysBookingsQuery->with(['slot.depot', 'customer', 'bookingType'])->get();

        // Get ALL vehicles currently on site (regardless of arrival date)
        // Exclude vehicles with departed movement status or collected trailers
        $currentlyOnSiteQuery = Booking::whereHas('slot', function ($q) use ($allowedDepotIds) {
            $q->whereIn('depot_id', $allowedDepotIds);
        })
            ->whereNotNull('arrived_at')
            ->whereDoesntHave('movements', function ($q) {
                $q->whereNotNull('trailer_collected_at'); // Simple: if trailer collected, not on site
            });

        if ($allowedCustomerIds !== null) {
            $currentlyOnSiteQuery->whereIn('customer_id', $allowedCustomerIds)->whereNotNull('customer_id');
        }

        $currentlyOnSite = $currentlyOnSiteQuery->count();

        // Get today's arrivals (bookings that arrived today regardless of scheduled date)
        $todaysArrivalsQuery = Booking::whereHas('slot', function ($q) use ($allowedDepotIds) {
            $q->whereIn('depot_id', $allowedDepotIds);
        })
            ->whereDate('arrived_at', $today);

        if ($allowedCustomerIds !== null) {
            $todaysArrivalsQuery->whereIn('customer_id', $allowedCustomerIds)->whereNotNull('customer_id');
        }

        $todaysArrivals = $todaysArrivalsQuery->count();

        // Get late runners (bookings that were scheduled before now but haven't arrived)
        $lateRunnersQuery = Booking::whereHas('slot', function ($q) use ($allowedDepotIds) {
            $q->whereIn('depot_id', $allowedDepotIds)
                ->where('start_at', '<', now());
        })
            ->whereNull('arrived_at');

        if ($allowedCustomerIds !== null) {
            $lateRunnersQuery->whereIn('customer_id', $allowedCustomerIds)->whereNotNull('customer_id');
        }

        $lateRunners = $lateRunnersQuery->count();
        
        // Get tipping late runners with sophisticated rules (excluding dropped trailers)
        $tippingLateRunnersQuery = Booking::whereHas('slot', function ($q) use ($allowedDepotIds) {
            $q->whereIn('depot_id', $allowedDepotIds);
        })
        ->whereNotNull('arrived_at')
        ->whereHas('movements', function ($q) {
            $q->where('current_status', 'empty')
              ->whereNotNull('unloading_completed_at')
              ->whereNotIn('current_status', ['trailer_dropped']); // Exclude dropped trailers (always ontime)
        })
        // Complex query: tipping completion > (slot_end + arrival_delay)
        ->whereRaw('
            EXISTS (
                SELECT 1 FROM movements m 
                WHERE m.booking_id = bookings.id 
                AND m.unloading_completed_at > DATE_ADD(
                    slots.end_at, 
                    INTERVAL GREATEST(0, TIMESTAMPDIFF(MINUTE, slots.start_at, bookings.arrived_at)) MINUTE
                )
            )
        ')
        ->join('slots', 'bookings.slot_id', '=', 'slots.id')
        ->select('bookings.*');
        
        if ($allowedCustomerIds !== null) {
            $tippingLateRunnersQuery->whereIn('bookings.customer_id', $allowedCustomerIds)->whereNotNull('bookings.customer_id');
        }
        
        $tippingLateRunners = $tippingLateRunnersQuery->count();
        
        // Get count of dropped trailers (always ontime)
        $droppedTrailersQuery = Booking::whereHas('slot', function ($q) use ($allowedDepotIds) {
            $q->whereIn('depot_id', $allowedDepotIds);
        })
        ->whereHas('movements', function ($q) {
            $q->whereIn('current_status', ['trailer_dropped', 'empty'])
              ->whereNotNull('unloading_completed_at');
        });
        
        if ($allowedCustomerIds !== null) {
            $droppedTrailersQuery->whereIn('customer_id', $allowedCustomerIds)->whereNotNull('customer_id');
        }
        
        $droppedTrailers = $droppedTrailersQuery->count();

        $stats = [
            'total_bookings' => $todaysBookings->count(),
            'arrived' => $todaysArrivals, // Count of vehicles that arrived today
            'departed' => $todaysBookings->whereNotNull('departed_at')->count(),
            'outstanding' => $todaysBookings->whereNull('arrived_at')->count(),
            'on_site' => $currentlyOnSite, // Use the correct count from all bookings
            'late_runners' => $lateRunners, // Count of overdue bookings (arrival)
            'tipping_late' => $tippingLateRunners, // Count of bookings that tipped late
            'dropped_trailers' => $droppedTrailers, // Count of dropped trailers (always ontime)
        ];

        // Upcoming bookings (next 3 hours) - sorted by slot start time (oldest first)
        $upcomingBookingsQuery = Booking::whereHas('slot', function ($q) use ($allowedDepotIds) {
            $q->whereIn('depot_id', $allowedDepotIds)
                ->whereBetween('start_at', [now(), now()->addHours(3)]);
        })
            ->whereNull('arrived_at');

        if ($allowedCustomerIds !== null) {
            $upcomingBookingsQuery->whereIn('customer_id', $allowedCustomerIds)->whereNotNull('customer_id');
        }

        $upcomingBookings = $upcomingBookingsQuery->with(['slot.depot', 'customer', 'bookingType'])
            ->join('slots', 'bookings.slot_id', '=', 'slots.id')
            ->orderBy('slots.start_at', 'asc')
            ->select('bookings.*')
            ->take(10)
            ->get();

        // Current arrivals (arrived but not departed) - sorted by arrival time (oldest first)
        // Exclude vehicles with departed movement status or collected trailers
        $currentArrivalsQuery = Booking::whereHas('slot', function ($q) use ($allowedDepotIds) {
            $q->whereIn('depot_id', $allowedDepotIds);
        })
            ->whereNotNull('arrived_at')
            ->whereDoesntHave('movements', function ($q) {
                $q->whereNotNull('trailer_collected_at'); // Simple: if trailer collected, not on site
            });

        if ($allowedCustomerIds !== null) {
            $currentArrivalsQuery->whereIn('customer_id', $allowedCustomerIds)->whereNotNull('customer_id');
        }

        $currentArrivals = $currentArrivalsQuery->with(['slot.depot', 'customer', 'bookingType'])
            ->orderBy('arrived_at', 'asc')
            ->get();

        // Late runners (overdue bookings that haven't arrived) - sorted by slot start time (oldest first)
        $lateRunnersDataQuery = Booking::whereHas('slot', function ($q) use ($allowedDepotIds) {
            $q->whereIn('depot_id', $allowedDepotIds)
                ->where('start_at', '<', now());
        })
            ->whereNull('arrived_at');

        if ($allowedCustomerIds !== null) {
            $lateRunnersDataQuery->whereIn('customer_id', $allowedCustomerIds)->whereNotNull('customer_id');
        }

        $lateRunnersData = $lateRunnersDataQuery->with(['slot.depot', 'customer', 'bookingType'])
            ->join('slots', 'bookings.slot_id', '=', 'slots.id')
            ->orderBy('slots.start_at', 'asc')
            ->select('bookings.*')
            ->take(10)
            ->get();

        // Get depots for display - show assigned depots for all roles including admin
        $userDepots = $user->depots()->get();
        // If no depots assigned, show empty collection
        if ($userDepots->isEmpty()) {
            $userDepots = collect();
        }

        return view('depot-admin.dashboard', compact(
            'stats',
            'todaysBookings',
            'upcomingBookings',
            'currentArrivals',
            'lateRunnersData',
            'userDepots',
            'depotFilter'
        ));
    }
}
