<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\FactoryBooking;
use App\Models\Movement;
use App\Models\Depot;
use App\Models\Customer;
use App\Models\BookingType;
use App\Models\Carrier;
use App\Models\TrailerType;
use App\Models\TippingBay;
use App\Models\TippingLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class WarehouseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Check if user has warehouse access (warehouse role or admin)
        $this->middleware(function ($request, $next) {
            if (!auth()->user()->hasWarehouseAccess()) {
                abort(403, 'Access denied. Warehouse access required.');
            }
            return $next($request);
        });
    }

    /**
     * Main warehouse dashboard - matches depot-admin dashboard logic
     */
    public function dashboard(Request $request)
    {
        $user = auth()->user();

        // Get depots based on user role and assignments (same as depot-admin)
        $userAssignedDepotIds = $user->depots()->pluck('depots.id')->toArray();

        // If no depots assigned, they see nothing
        if (empty($userAssignedDepotIds)) {
            $userAssignedDepotIds = [0]; // No depot will have ID 0
        }

        // Apply depot filter if provided, or restore from session
        $allowedDepotIds = $userAssignedDepotIds;
        $depotFilter = $request->input('depot_id');

        // Session-based filtering (like depot-admin)
        if (! $depotFilter && $request->session()->has('dashboard_depot_filter')) {
            $depotFilter = $request->session()->get('dashboard_depot_filter');
        }

        if ($depotFilter) {
            if (in_array($depotFilter, $userAssignedDepotIds)) {
                $allowedDepotIds = [$depotFilter];
                $request->session()->put('dashboard_depot_filter', $depotFilter);
            } else {
                $request->session()->forget('dashboard_depot_filter');
            }
        } elseif ($request->has('depot_id') && $request->input('depot_id') === '') {
            $request->session()->forget('dashboard_depot_filter');
        }

        // Customer filtering (same logic as depot-admin)
        $allowedCustomerIds = null;
        if ($user->hasRole('admin')) {
            $assignedCustomerIds = $user->customers()->pluck('customers.id')->toArray();
            if (! empty($assignedCustomerIds)) {
                $allowedCustomerIds = $assignedCustomerIds;
            }
        } elseif ($user->hasRole('customer')) {
            $allowedCustomerIds = $user->customers()->pluck('customers.id')->toArray();
            if (empty($allowedCustomerIds)) {
                $allowedCustomerIds = [0];
            }
        } else {
            // Warehouse/depot-admin/site-admin logic
            $assignedCustomerIds = $user->customers()->pluck('customers.id')->toArray();
            if (! empty($assignedCustomerIds)) {
                $allowedCustomerIds = $assignedCustomerIds;
            }
        }

        // Today's stats (depot-admin logic)
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        $todaysBookingsQuery = Booking::whereHas('slot', function ($q) use ($allowedDepotIds, $today, $tomorrow) {
            $q->whereIn('depot_id', $allowedDepotIds)
                ->whereBetween('start_at', [$today, $tomorrow]);
        })
        ->whereNull('cancelled_at');

        if ($allowedCustomerIds !== null) {
            $todaysBookingsQuery->whereIn('customer_id', $allowedCustomerIds)->whereNotNull('customer_id');
        }

        $todaysBookings = $todaysBookingsQuery->with(['slot.depot', 'customer', 'bookingType'])->get();

        // Current arrivals on site - Regular bookings (simplified logic)
        $currentlyOnSiteQuery = Booking::whereHas('slot', function ($q) use ($allowedDepotIds) {
            $q->whereIn('depot_id', $allowedDepotIds);
        })
            ->whereNotNull('arrived_at')
            ->whereNull('cancelled_at')
            ->whereDoesntHave('movements', function ($q) {
                $q->whereNotNull('trailer_collected_at'); // Simple: if trailer collected, not on site
            });

        if ($allowedCustomerIds !== null) {
            $currentlyOnSiteQuery->whereIn('customer_id', $allowedCustomerIds)->whereNotNull('customer_id');
        }

        $regularOnSite = $currentlyOnSiteQuery->count();

        // Current arrivals on site - Factory bookings
        $factoryOnSiteQuery = FactoryBooking::whereIn('depot_id', $allowedDepotIds)
            ->whereNotNull('arrived_at')
            ->whereNull('completed_at');

        if ($allowedCustomerIds !== null) {
            $factoryOnSiteQuery->whereIn('customer_id', $allowedCustomerIds)->whereNotNull('customer_id');
        }

        $factoryOnSite = $factoryOnSiteQuery->count();
        $currentlyOnSite = $regularOnSite + $factoryOnSite;

        // Dropped trailers count (units departed, trailers still on site) - simplified
        $droppedTrailersQuery = Booking::whereHas('slot', function ($q) use ($allowedDepotIds) {
            $q->whereIn('depot_id', $allowedDepotIds);
        })
            ->whereNotNull('arrived_at')
            ->whereNotNull('departed_at')        // Unit has departed
            ->whereNull('cancelled_at')
            ->whereDoesntHave('movements', function ($q) {
                $q->whereNotNull('trailer_collected_at'); // But trailer not collected yet
            });

        if ($allowedCustomerIds !== null) {
            $droppedTrailersQuery->whereIn('customer_id', $allowedCustomerIds)->whereNotNull('customer_id');
        }

        $droppedTrailers = $droppedTrailersQuery->count();

        // Today's arrivals
        $todaysArrivalsQuery = Booking::whereHas('slot', function ($q) use ($allowedDepotIds) {
            $q->whereIn('depot_id', $allowedDepotIds);
        })
        ->whereDate('arrived_at', $today)
        ->whereNull('cancelled_at');

        if ($allowedCustomerIds !== null) {
            $todaysArrivalsQuery->whereIn('customer_id', $allowedCustomerIds)->whereNotNull('customer_id');
        }

        $todaysArrivals = $todaysArrivalsQuery->count();

        // Late runners (bookings that were scheduled before now but haven't arrived)
        $lateRunnersQuery = Booking::whereHas('slot', function ($q) use ($allowedDepotIds) {
            $q->whereIn('depot_id', $allowedDepotIds)
                ->where('start_at', '<', now());
        })
        ->whereNull('arrived_at')
        ->whereNull('cancelled_at');

        if ($allowedCustomerIds !== null) {
            $lateRunnersQuery->whereIn('customer_id', $allowedCustomerIds)->whereNotNull('customer_id');
        }

        $lateRunners = $lateRunnersQuery->count();
        
        // Get tipping late runners with sophisticated rules (excluding dropped trailers)
        $tippingLateRunnersQuery = Booking::whereHas('slot', function ($q) use ($allowedDepotIds) {
            $q->whereIn('depot_id', $allowedDepotIds);
        })
        ->whereNotNull('arrived_at')
        ->whereNull('cancelled_at')
        ->whereHas('movements', function ($q) {
            $q->where('current_status', 'empty')
              ->whereNotNull('unloading_completed_at')
              ->whereNotIn('current_status', ['in_parking']); // Exclude dropped trailers (always ontime)
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
        ->whereNull('cancelled_at')
        ->whereHas('movements', function ($q) {
            $q->whereIn('current_status', ['in_parking', 'empty'])
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

        // Upcoming bookings (next 3 hours)
        $upcomingBookingsQuery = Booking::whereHas('slot', function ($q) use ($allowedDepotIds) {
            $q->whereIn('depot_id', $allowedDepotIds)
                ->whereBetween('start_at', [now(), now()->addHours(3)]);
        })
        ->whereNull('arrived_at')
        ->whereNull('cancelled_at');

        if ($allowedCustomerIds !== null) {
            $upcomingBookingsQuery->whereIn('customer_id', $allowedCustomerIds)->whereNotNull('customer_id');
        }

        $upcomingBookings = $upcomingBookingsQuery->with(['slot.depot', 'customer', 'bookingType'])
            ->join('slots', 'bookings.slot_id', '=', 'slots.id')
            ->orderBy('slots.start_at', 'asc')
            ->select('bookings.*')
            ->take(10)
            ->get();

        // Current arrivals - Regular bookings (simplified logic)
        $currentArrivalsQuery = Booking::whereHas('slot', function ($q) use ($allowedDepotIds) {
            $q->whereIn('depot_id', $allowedDepotIds);
        })
            ->whereNotNull('arrived_at')
            ->whereNull('cancelled_at')
            ->whereDoesntHave('movements', function ($q) {
                $q->whereNotNull('trailer_collected_at'); // Simple: if trailer collected, not on site
            });

        if ($allowedCustomerIds !== null) {
            $currentArrivalsQuery->whereIn('customer_id', $allowedCustomerIds)->whereNotNull('customer_id');
        }

        $regularArrivals = $currentArrivalsQuery->with(['slot.depot', 'customer', 'bookingType'])
            ->orderBy('arrived_at', 'asc')
            ->get();

        // Current arrivals - Factory bookings
        $factoryArrivalsQuery = FactoryBooking::whereIn('depot_id', $allowedDepotIds)
            ->whereNotNull('arrived_at')
            ->whereNull('completed_at');

        if ($allowedCustomerIds !== null) {
            $factoryArrivalsQuery->whereIn('customer_id', $allowedCustomerIds)->whereNotNull('customer_id');
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
                    'original_factory_booking' => $factoryBooking, // Keep reference to original
                ];
            });

        // Combine regular and factory arrivals
        $currentArrivals = $regularArrivals->concat($factoryArrivals)->sortBy('arrived_at');

        // Late runners data
        $lateRunnersDataQuery = Booking::whereHas('slot', function ($q) use ($allowedDepotIds) {
            $q->whereIn('depot_id', $allowedDepotIds)
                ->where('start_at', '<', now());
        })
        ->whereNull('arrived_at')
        ->whereNull('cancelled_at');

        if ($allowedCustomerIds !== null) {
            $lateRunnersDataQuery->whereIn('customer_id', $allowedCustomerIds)->whereNotNull('customer_id');
        }

        $lateRunnersData = $lateRunnersDataQuery->with(['slot.depot', 'customer', 'bookingType'])
            ->join('slots', 'bookings.slot_id', '=', 'slots.id')
            ->orderBy('slots.start_at', 'asc')
            ->select('bookings.*')
            ->take(10)
            ->get();

        // Get depots for display
        $userDepots = $user->depots()->get();
        if ($userDepots->isEmpty()) {
            $userDepots = collect();
        }

        return view('app.dashboard', compact(
            'stats',
            'todaysBookings',
            'upcomingBookings',
            'currentArrivals',
            'lateRunnersData',
            'userDepots',
            'depotFilter'
        ))->with('allDepots', $userDepots);
    }

    /**
     * Unified bookings view (regular + factory)
     */
    public function bookings(Request $request)
    {
        // Check if user can view bookings
        if (!auth()->user()->hasFunction('bookings.view')) {
            abort(403, 'You do not have permission to view bookings.');
        }
        
        $allowedDepotIds = $this->getAllowedDepotIds();
        
        // This will use the existing admin booking controller logic
        // but with function-based permission checks
        return app(\App\Http\Controllers\Admin\BookingController::class)->index($request);
    }

    /**
     * Factory inbound management
     */
    public function factoryBookings(Request $request)
    {
        // Check if user can view factory bookings
        if (!auth()->user()->hasFunction('factory-bookings.view')) {
            abort(403, 'You do not have permission to view factory bookings.');
        }
        
        return app(\App\Http\Controllers\Admin\FactoryBookingController::class)->index($request);
    }

    /**
     * Trailer location report
     */
    public function trailerReport(Request $request)
    {
        if (!auth()->user()->hasFunction('warehouse.trailer-report')) {
            abort(403, 'You do not have permission to view trailer reports.');
        }
        
        return app(\App\Http\Controllers\Admin\BookingController::class)->trailerLocationReport($request);
    }

    /**
     * Tipping workflow
     */
    public function tippingWorkflow(Request $request)
    {
        if (!auth()->user()->hasFunction('warehouse.tipping-workflow')) {
            abort(403, 'You do not have permission to access tipping workflow.');
        }
        
        return app(\App\Http\Controllers\Admin\TippingWorkflowController::class)->dashboard($request);
    }

    /**
     * Get allowed depot IDs for current user
     */
    protected function getAllowedDepotIds(): array
    {
        $user = Auth::user();
        
        // Admin can see all depots
        if ($user->hasRole('admin')) {
            return Depot::pluck('id')->toArray();
        }
        
        // Other roles see their assigned depots
        return $user->depots->pluck('id')->toArray();
    }

    /**
     * Check if current user can perform a function
     */
    protected function canPerform(string $function): bool
    {
        return auth()->user()->hasFunction($function);
    }
}