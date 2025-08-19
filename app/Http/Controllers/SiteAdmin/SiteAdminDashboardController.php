<?php

namespace App\Http\Controllers\SiteAdmin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\TippingBay;
use App\Models\TippingLocation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SiteAdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:site-admin']);
    }

    public function index()
    {
        $user = auth()->user();
        $allowedDepotIds = $user->depots()->pluck('depots.id')->toArray();

        if (empty($allowedDepotIds)) {
            abort(403, 'You are not assigned to any depots.');
        }

        // Today's gate activity
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();

        $todaysBookings = Booking::whereHas('slot', function ($q) use ($allowedDepotIds, $today, $tomorrow) {
            $q->whereIn('depot_id', $allowedDepotIds)
                ->whereBetween('start_at', [$today, $tomorrow]);
        })->with(['slot.depot', 'customer', 'bookingType'])->get();

        // Calculate on_site_now excluding collected trailers
        $onSiteNow = $todaysBookings->filter(function ($booking) {
            // Has arrived but not departed
            if (!$booking->arrived_at || $booking->departed_at) {
                return false;
            }
            // Check movement status - exclude departed/collected
            $movementStatus = $booking->getCurrentMovementStatus();
            return !in_array($movementStatus, ['departed', 'trailer_collected']);
        })->count();

        $stats = [
            'total_expected' => $todaysBookings->count(),
            'arrived_today' => $todaysBookings->whereNotNull('arrived_at')->count(),
            'on_site_now' => $onSiteNow,
            'departed_today' => $todaysBookings->whereNotNull('departed_at')->count(),
            'overdue' => $todaysBookings->filter(function ($booking) {
                return $booking->arrived_at === null &&
                       Carbon::parse($booking->slot->start_at)->isPast();
            })->count(),
        ];

        // Expected arrivals in next hour
        $nextHourArrivals = Booking::whereHas('slot', function ($q) use ($allowedDepotIds) {
            $q->whereIn('depot_id', $allowedDepotIds)
                ->whereBetween('start_at', [now(), now()->addHour()]);
        })
            ->whereNull('arrived_at')
            ->with(['slot.depot', 'customer', 'bookingType'])
            ->orderBy('slot_id')
            ->get();

        // Current vehicles on site - exclude collected trailers
        $onSiteVehicles = Booking::whereHas('slot', function ($q) use ($allowedDepotIds) {
            $q->whereIn('depot_id', $allowedDepotIds);
        })
            ->whereNotNull('arrived_at')
            ->whereNull('departed_at')
            ->whereDoesntHave('movements', function ($q) {
                $q->whereIn('current_status', ['departed', 'trailer_collected']);
            })
            ->with(['slot.depot', 'customer', 'bookingType'])
            ->orderBy('arrived_at')
            ->get();

        // Recent departures (last 2 hours)
        $recentDepartures = Booking::whereHas('slot', function ($q) use ($allowedDepotIds) {
            $q->whereIn('depot_id', $allowedDepotIds);
        })
            ->whereNotNull('departed_at')
            ->where('departed_at', '>=', now()->subHours(2))
            ->with(['slot.depot', 'customer', 'bookingType'])
            ->orderByDesc('departed_at')
            ->take(10)
            ->get();

        // Get user's depots for display
        $userDepots = $user->depots()->get();

        return view('site-admin.dashboard', compact(
            'stats',
            'nextHourArrivals',
            'onSiteVehicles',
            'recentDepartures',
            'userDepots'
        ));
    }

    public function search(Request $request)
    {
        $user = auth()->user();
        $allowedDepotIds = $user->depots()->pluck('depots.id')->toArray();

        $results = collect();

        if ($request->filled('search')) {
            $searchTerm = $request->search;

            $results = Booking::whereHas('slot', function ($q) use ($allowedDepotIds) {
                $q->whereIn('depot_id', $allowedDepotIds);
            })
                ->where(function ($q) use ($searchTerm) {
                    $q->where('booking_reference', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('vehicle_registration', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('container_number', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('reference', 'LIKE', "%{$searchTerm}%");
                })
                ->with(['slot.depot', 'customer', 'bookingType'])
                ->orderByDesc('created_at')
                ->take(20)
                ->get();
        }

        return view('site-admin.search', compact('results'));
    }

    public function arrivals()
    {
        $user = auth()->user();
        $allowedDepotIds = $user->depots()->pluck('depots.id')->toArray();

        // Get all expected arrivals for today and tomorrow
        $arrivals = Booking::whereHas('slot', function ($q) use ($allowedDepotIds) {
            $q->whereIn('depot_id', $allowedDepotIds)
                ->whereBetween('start_at', [Carbon::today(), Carbon::tomorrow()->endOfDay()]);
        })
            ->with(['slot.depot', 'customer', 'bookingType', 'tippingBay'])
            ->orderBy('slot_id')
            ->paginate(50);

        // Get tipping locations and bays for the allowed depots
        $tippingLocations = TippingLocation::whereIn('depot_id', $allowedDepotIds)->get();
        $tippingBays = TippingBay::whereIn('depot_id', $allowedDepotIds)->get();

        return view('site-admin.arrivals', compact('arrivals', 'tippingLocations', 'tippingBays'));
    }

    public function departures()
    {
        $user = auth()->user();
        $allowedDepotIds = $user->depots()->pluck('depots.id')->toArray();

        // Get all vehicles that need to depart (arrived but not departed)
        $departures = Booking::whereHas('slot', function ($q) use ($allowedDepotIds) {
            $q->whereIn('depot_id', $allowedDepotIds);
        })
            ->whereNotNull('arrived_at')
            ->whereNull('departed_at')
            ->with(['slot.depot', 'customer', 'bookingType'])
            ->orderBy('arrived_at')
            ->paginate(50);

        return view('site-admin.departures', compact('departures'));
    }
}
