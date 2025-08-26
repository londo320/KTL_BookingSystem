<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingHistory;
use App\Models\Customer;
use App\Models\CustomerBehaviorSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CustomerBehaviorController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'function-access']);
    }

    public function settings(Customer $customer)
    {
        $customer->load(['behaviorSettings']);

        $currentSettings = CustomerBehaviorSetting::getCustomerSettings($customer->id);
        $availableSettings = CustomerBehaviorSetting::getAvailableSettings();

        return view('warehouse.customer-behavior.settings', compact(
            'customer',
            'currentSettings',
            'availableSettings'
        ));
    }

    public function updateSettings(Request $request, Customer $customer)
    {
        $availableSettings = CustomerBehaviorSetting::getAvailableSettings();

        // Validate each setting
        $rules = [];
        foreach ($availableSettings as $key => $config) {
            switch ($config['type']) {
                case 'integer':
                    $rules[$key] = [
                        'required',
                        'integer',
                        'min:'.($config['min'] ?? 0),
                        'max:'.($config['max'] ?? 999),
                    ];
                    break;
                case 'boolean':
                    $rules[$key] = 'boolean';
                    break;
                case 'float':
                    $rules[$key] = ['required', 'numeric', 'min:0'];
                    break;
                default:
                    $rules[$key] = ['required', 'string', 'max:255'];
            }
        }

        $validated = $request->validate($rules);

        DB::transaction(function () use ($customer, $validated, $availableSettings) {
            foreach ($validated as $key => $value) {
                $config = $availableSettings[$key];

                // Only save if different from default
                if ($value != $config['default']) {
                    CustomerBehaviorSetting::setCustomerSetting(
                        $customer->id,
                        $key,
                        $value,
                        $config['type'],
                        $config['description']
                    );
                } else {
                    // Remove setting if it matches default
                    CustomerBehaviorSetting::where('customer_id', $customer->id)
                        ->where('setting_key', $key)
                        ->delete();
                }
            }
        });

        return redirect()
            ->route('admin.customer-behavior.settings', $customer)
            ->with('success', 'Customer behavior settings updated successfully.');
    }

    public function resetSettings(Customer $customer)
    {
        CustomerBehaviorSetting::where('customer_id', $customer->id)->delete();

        return redirect()
            ->route('admin.customer-behavior.settings', $customer)
            ->with('success', 'Customer behavior settings reset to defaults.');
    }

    public function index(Request $request)
    {
        $days = $request->get('days', 30);
        $sortBy = $request->get('sort', 'rebook_count');
        $direction = $request->get('direction', 'desc');

        // Get customer behavior analytics
        $customers = $this->getCustomerBehaviorStats($days, $sortBy, $direction);

        // Overall stats
        $overallStats = $this->getOverallStats($days);

        return view('warehouse.customer-behavior.index', compact(
            'customers',
            'overallStats',
            'days',
            'sortBy',
            'direction'
        ));
    }

    public function show(Customer $customer, Request $request)
    {
        $days = $request->get('days', 90);
        $filter = $request->get('filter', 'all'); // all, bad, good, late

        // Load customer users relationship
        $customer->load('users');

        // Detailed customer analysis with late arrivals
        $customerStats = $this->getDetailedCustomerStats($customer->id, $days);

        // Recent booking history - only show customer behavior relevant actions
        $historyQuery = BookingHistory::where('customer_id', $customer->id)
            ->recentActivity($days)
            ->whereIn('action', ['created', 'rebooked', 'cancelled', 'completed', 'late_arrival', 'early_arrival', 'on_time_arrival'])
            ->with(['booking.slot.depot', 'originalSlot.depot', 'newSlot.depot', 'user']);

        // Apply behavior filter
        switch ($filter) {
            case 'bad':
                $historyQuery->where(function ($query) {
                    $query->whereIn('action', ['rebooked', 'cancelled', 'late_arrival', 'early_arrival'])
                          ->orWhere('is_last_minute', 1);
                });
                break;
            case 'good':
                $historyQuery->whereIn('action', ['created', 'completed', 'on_time_arrival'])
                    ->where('is_last_minute', 0);
                break;
            case 'late':
                $historyQuery->where('action', 'late_arrival');
                break;
            case 'early':
                $historyQuery->where('action', 'early_arrival');
                break;
            case 'on_time':
                $historyQuery->where('action', 'on_time_arrival');
                break;
            // 'all' shows everything - no additional filter
        }

        $recentHistory = $historyQuery->orderBy('created_at', 'desc')->paginate(20);

        // Behavioral patterns over time
        $patterns = $this->getCustomerPatterns($customer->id, $days);

        return view('warehouse.customer-behavior.show', compact(
            'customer',
            'customerStats',
            'recentHistory',
            'patterns',
            'days',
            'filter'
        ));
    }

    public function flagged(Request $request)
    {
        $days = $request->get('days', 30);

        // Get customers flagged for excessive behavior
        $flaggedCustomers = $this->getFlaggedCustomers($days);

        return view('warehouse.customer-behavior.flagged', compact(
            'flaggedCustomers',
            'days'
        ));
    }

    public function export(Request $request)
    {
        $days = $request->get('days', 30);
        $customers = $this->getCustomerBehaviorStats($days);

        $csv = "Customer Name,User Emails,Total Bookings,Rebooks,Cancellations,Last Minute Actions,Avg Hours Notice,Risk Score\n";

        foreach ($customers as $customer) {
            $csv .= sprintf(
                "%s,\"%s\",%d,%d,%d,%d,%.1f,%s\n",
                $customer->name,
                $customer->user_emails ?? 'No users assigned',
                $customer->total_bookings,
                $customer->rebook_count,
                $customer->cancellation_count,
                $customer->last_minute_count,
                $customer->avg_hours_notice,
                $customer->risk_level
            );
        }

        $filename = 'customer_behavior_analysis_'.now()->format('Y-m-d').'.csv';

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', "attachment; filename=\"$filename\"");
    }

    private function getCustomerBehaviorStats(int $days, string $sortBy = 'rebook_count', string $direction = 'desc')
    {
        // Check if booking_history table exists
        $hasBookingHistory = \Schema::hasTable('booking_history');

        if (! $hasBookingHistory) {
            // Return dummy data when booking_history table doesn't exist
            return collect([]);
        }

        $query = Customer::select([
            'customers.id',
            'customers.name',
            DB::raw('GROUP_CONCAT(DISTINCT users.email) as user_emails'),
            DB::raw('COUNT(DISTINCT bookings.id) as total_bookings'),
            DB::raw('COALESCE(SUM(CASE WHEN bh.action = "rebooked" THEN 1 ELSE 0 END), 0) as rebook_count'),
            DB::raw('COALESCE(SUM(CASE WHEN bh.action = "cancelled" THEN 1 ELSE 0 END), 0) as cancellation_count'),
            DB::raw('COALESCE(SUM(CASE WHEN bh.action = "late_arrival" THEN 1 ELSE 0 END), 0) as late_arrivals'),
            DB::raw('COALESCE(SUM(CASE WHEN bh.action = "early_arrival" THEN 1 ELSE 0 END), 0) as early_arrivals'),
            DB::raw('COALESCE(SUM(CASE WHEN bh.is_last_minute = 1 AND bh.action IN ("rebooked", "cancelled") THEN 1 ELSE 0 END), 0) as last_minute_count'),
            DB::raw('AVG(CASE WHEN bh.action IN ("rebooked", "cancelled") THEN bh.hours_before_slot END) as avg_hours_notice'),
            DB::raw('(
                (COALESCE(SUM(CASE WHEN bh.action = "rebooked" THEN 1 ELSE 0 END), 0) * 2) + 
                (COALESCE(SUM(CASE WHEN bh.action = "cancelled" THEN 1 ELSE 0 END), 0) * 1) + 
                (COALESCE(SUM(CASE WHEN bh.action = "late_arrival" THEN 1 ELSE 0 END), 0) * 2) + 
                (COALESCE(SUM(CASE WHEN bh.action = "early_arrival" THEN 1 ELSE 0 END), 0) * 1) + 
                (COALESCE(SUM(CASE WHEN bh.is_last_minute = 1 AND bh.action IN ("rebooked", "cancelled") THEN 1 ELSE 0 END), 0) * 3)
            ) as risk_score'),
        ])
            ->leftJoin('customer_user', 'customers.id', '=', 'customer_user.customer_id')
            ->leftJoin('users', 'customer_user.user_id', '=', 'users.id')
            ->leftJoin('bookings', 'customers.id', '=', 'bookings.customer_id')
            ->leftJoin('booking_history as bh', function ($join) use ($days) {
                $join->on('customers.id', '=', 'bh.customer_id')
                    ->where('bh.created_at', '>=', Carbon::now()->subDays($days));
            })
            ->groupBy('customers.id', 'customers.name')
            ->having(DB::raw('COUNT(DISTINCT bookings.id)'), '>', 0);

        // Apply sorting
        $validSorts = ['name', 'total_bookings', 'rebook_count', 'cancellation_count', 'last_minute_count', 'risk_score'];
        if (in_array($sortBy, $validSorts)) {
            $query->orderBy($sortBy, $direction);
        }

        return $query->get()->map(function ($customer) {
            $customer->risk_level = $this->calculateRiskLevel($customer->risk_score, $customer->total_bookings);
            $customer->avg_hours_notice = round($customer->avg_hours_notice ?? 0, 1);

            return $customer;
        });
    }

    private function getDetailedCustomerStats(int $customerId, int $days): array
    {
        $stats = BookingHistory::where('customer_id', $customerId)
            ->recentActivity($days)
            ->selectRaw('
                COUNT(*) as total_actions,
                SUM(CASE WHEN action = "created" THEN 1 ELSE 0 END) as bookings_created,
                SUM(CASE WHEN action = "rebooked" THEN 1 ELSE 0 END) as total_rebooks,
                SUM(CASE WHEN action = "cancelled" THEN 1 ELSE 0 END) as total_cancellations,
                SUM(CASE WHEN action = "completed" THEN 1 ELSE 0 END) as completed_bookings,
                SUM(CASE WHEN action = "late_arrival" THEN 1 ELSE 0 END) as late_arrivals,
                SUM(CASE WHEN action = "early_arrival" THEN 1 ELSE 0 END) as early_arrivals,
                SUM(CASE WHEN action = "on_time_arrival" THEN 1 ELSE 0 END) as on_time_arrivals,
                SUM(CASE WHEN action IN ("late_arrival", "early_arrival", "on_time_arrival") THEN 1 ELSE 0 END) as total_arrivals,
                SUM(CASE WHEN is_last_minute = 1 AND action IN ("rebooked", "cancelled") THEN 1 ELSE 0 END) as last_minute_actions,
                AVG(CASE WHEN action IN ("rebooked", "cancelled") THEN hours_before_slot END) as avg_hours_notice,
                MIN(CASE WHEN action IN ("rebooked", "cancelled") THEN hours_before_slot END) as min_hours_notice,
                MAX(CASE WHEN action IN ("rebooked", "cancelled") THEN hours_before_slot END) as max_hours_notice
            ')
            ->first();

        return [
            'total_actions' => $stats->total_actions ?? 0,
            'bookings_created' => $stats->bookings_created ?? 0,
            'total_rebooks' => $stats->total_rebooks ?? 0,
            'total_cancellations' => $stats->total_cancellations ?? 0,
            'completed_bookings' => $stats->completed_bookings ?? 0,
            'late_arrivals' => $stats->late_arrivals ?? 0,
            'early_arrivals' => $stats->early_arrivals ?? 0,
            'on_time_arrivals' => $stats->on_time_arrivals ?? 0,
            'total_arrivals' => $stats->total_arrivals ?? 0,
            'last_minute_actions' => $stats->last_minute_actions ?? 0,
            'avg_hours_notice' => round($stats->avg_hours_notice ?? 0, 1),
            'min_hours_notice' => round($stats->min_hours_notice ?? 0, 1),
            'max_hours_notice' => round($stats->max_hours_notice ?? 0, 1),
            'completion_rate' => $stats->bookings_created > 0 ?
                round(($stats->completed_bookings / $stats->bookings_created) * 100, 1) : 0,
            'late_arrival_rate' => $stats->total_arrivals > 0 ?
                round(($stats->late_arrivals / $stats->total_arrivals) * 100, 1) : 0,
            'early_arrival_rate' => $stats->total_arrivals > 0 ?
                round(($stats->early_arrivals / $stats->total_arrivals) * 100, 1) : 0,
            'on_time_arrival_rate' => $stats->total_arrivals > 0 ?
                round(($stats->on_time_arrivals / $stats->total_arrivals) * 100, 1) : 0,
        ];
    }

    private function getCustomerPatterns(int $customerId, int $days): array
    {
        // Weekly pattern analysis
        $weeklyPatterns = BookingHistory::where('customer_id', $customerId)
            ->recentActivity($days)
            ->selectRaw('
                WEEK(created_at) as week_number,
                COUNT(*) as actions,
                SUM(CASE WHEN action IN ("rebooked", "cancelled") THEN 1 ELSE 0 END) as changes,
                SUM(CASE WHEN is_last_minute = 1 AND action IN ("rebooked", "cancelled") THEN 1 ELSE 0 END) as last_minute
            ')
            ->groupBy('week_number')
            ->orderBy('week_number', 'desc')
            ->get();

        // Day of week patterns
        $dayPatterns = BookingHistory::where('customer_id', $customerId)
            ->recentActivity($days)
            ->selectRaw('
                DAYOFWEEK(created_at) as day_of_week,
                COUNT(*) as actions,
                SUM(CASE WHEN action IN ("rebooked", "cancelled") THEN 1 ELSE 0 END) as changes
            ')
            ->groupBy('day_of_week')
            ->orderBy('day_of_week')
            ->get();

        return [
            'weekly' => $weeklyPatterns,
            'daily' => $dayPatterns,
        ];
    }

    private function getFlaggedCustomers(int $days)
    {
        // Check if booking_history table exists
        $hasBookingHistory = \Schema::hasTable('booking_history');

        if (! $hasBookingHistory) {
            return collect([]);
        }

        return Customer::select([
            'customers.id',
            'customers.name',
            DB::raw('SUM(CASE WHEN bh.action = "rebooked" THEN 1 ELSE 0 END) as rebook_count'),
            DB::raw('SUM(CASE WHEN bh.action = "cancelled" THEN 1 ELSE 0 END) as cancellation_count'),
            DB::raw('SUM(CASE WHEN bh.is_last_minute = 1 AND bh.action IN ("rebooked", "cancelled") THEN 1 ELSE 0 END) as last_minute_count'),
            DB::raw('GROUP_CONCAT(DISTINCT CASE WHEN bh.is_last_minute = 1 AND bh.action IN ("rebooked", "cancelled") THEN DATE(bh.created_at) END) as last_minute_dates'),
        ])
            ->join('booking_history as bh', 'customers.id', '=', 'bh.customer_id')
            ->where('bh.created_at', '>=', Carbon::now()->subDays($days))
            ->groupBy('customers.id', 'customers.name')
            ->havingRaw('SUM(CASE WHEN bh.is_last_minute = 1 AND bh.action IN ("rebooked", "cancelled") THEN 1 ELSE 0 END) >= 3') // 3+ last minute actions
            ->orHavingRaw('SUM(CASE WHEN bh.action = "rebooked" THEN 1 ELSE 0 END) >= 5') // 5+ rebooks
            ->orderByDesc('last_minute_count')
            ->get();
    }

    private function getOverallStats(int $days): array
    {
        $stats = BookingHistory::recentActivity($days)
            ->selectRaw('
                COUNT(DISTINCT customer_id) as total_customers,
                COUNT(*) as total_actions,
                SUM(CASE WHEN action = "rebooked" THEN 1 ELSE 0 END) as total_rebooks,
                SUM(CASE WHEN action = "cancelled" THEN 1 ELSE 0 END) as total_cancellations,
                SUM(CASE WHEN is_last_minute = 1 AND action IN ("rebooked", "cancelled") THEN 1 ELSE 0 END) as total_last_minute,
                AVG(CASE WHEN action IN ("rebooked", "cancelled") THEN hours_before_slot END) as avg_notice_hours
            ')
            ->first();

        return [
            'total_customers' => $stats->total_customers ?? 0,
            'total_rebooks' => $stats->total_rebooks ?? 0,
            'total_cancellations' => $stats->total_cancellations ?? 0,
            'total_last_minute' => $stats->total_last_minute ?? 0,
            'avg_notice_hours' => round($stats->avg_notice_hours ?? 0, 1),
            'rebook_rate' => $stats->total_actions > 0 ?
                round(($stats->total_rebooks / $stats->total_actions) * 100, 1) : 0,
            'last_minute_rate' => $stats->total_actions > 0 ?
                round(($stats->total_last_minute / $stats->total_actions) * 100, 1) : 0,
        ];
    }

    private function calculateRiskLevel(int $riskScore, int $totalBookings): string
    {
        if ($totalBookings === 0) {
            return 'No Data';
        }

        $riskRatio = $riskScore / $totalBookings;

        if ($riskRatio >= 3) {
            return 'High Risk';
        }
        if ($riskRatio >= 1.5) {
            return 'Medium Risk';
        }
        if ($riskRatio >= 0.5) {
            return 'Low Risk';
        }

        return 'Normal';
    }
}
