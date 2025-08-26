<?php

namespace App\Modules\Outbound\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Outbound\Models\OutboundLoad;
use App\Modules\Outbound\Models\OutboundOrder;
use App\Modules\Outbound\Models\LoadCollection;
use App\Modules\Outbound\Models\CustomerAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OutboundDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Date filter - default to today
        $date = $request->input('date', today());
        $dateFilter = Carbon::parse($date);

        // Get key performance indicators
        $kpis = $this->getKPIs($dateFilter);

        // Get active loads summary
        $activeLoads = $this->getActiveLoads();

        // Get today's collections
        $todaysCollections = $this->getTodaysCollections($dateFilter);

        // Get urgent/priority orders
        $urgentOrders = $this->getUrgentOrders();

        // Get recent activity
        $recentActivity = $this->getRecentActivity();

        // Get performance metrics
        $performanceMetrics = $this->getPerformanceMetrics($dateFilter);

        return view('outbound::admin.dashboard.index', compact(
            'kpis',
            'activeLoads',
            'todaysCollections',
            'urgentOrders',
            'recentActivity',
            'performanceMetrics',
            'dateFilter'
        ));
    }

    protected function getKPIs($date)
    {
        $startOfWeek = $date->copy()->startOfWeek();
        $endOfWeek = $date->copy()->endOfWeek();

        return [
            // Today's numbers
            'todays_loads' => OutboundLoad::whereDate('created_at', $date)->count(),
            'todays_orders' => OutboundOrder::whereDate('created_at', $date)->count(),
            'todays_collections' => LoadCollection::whereDate('planned_collection_time', $date)->count(),
            'todays_deliveries' => OutboundOrder::whereDate('estimated_delivery_time', $date)->count(),

            // Active numbers
            'active_loads' => OutboundLoad::active()->count(),
            'pending_collections' => LoadCollection::byStatus('pending')->count(),
            'in_transit_orders' => OutboundOrder::byStatus('in_transit')->count(),
            'out_for_delivery' => OutboundOrder::byStatus('out_for_delivery')->count(),

            // This week's numbers
            'week_loads_created' => OutboundLoad::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count(),
            'week_orders_delivered' => OutboundOrder::byStatus('delivered')
                ->whereBetween('actual_delivery_time', [$startOfWeek, $endOfWeek])
                ->count(),

            // Overdue alerts
            'overdue_collections' => LoadCollection::overdue()->count(),
            'overdue_orders' => OutboundOrder::overdue()->count(),
        ];
    }

    protected function getActiveLoads()
    {
        return OutboundLoad::with(['orders.customer', 'plannedVehicle', 'assignedDriver'])
            ->active()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($load) {
                return [
                    'load' => $load,
                    'status_class' => $this->getStatusClass($load->status),
                    'progress_percentage' => $this->calculateLoadProgress($load),
                    'customer_count' => $load->orders->unique('customer_id')->count(),
                    'next_action' => $this->getNextAction($load),
                ];
            });
    }

    protected function getTodaysCollections($date)
    {
        return LoadCollection::with(['outboundLoad', 'depot'])
            ->whereDate('planned_collection_time', $date)
            ->orderBy('planned_collection_time')
            ->get()
            ->map(function ($collection) {
                return [
                    'collection' => $collection,
                    'is_overdue' => $collection->isOverdue(),
                    'status_class' => $this->getStatusClass($collection->status),
                    'time_until_collection' => $this->getTimeUntilCollection($collection),
                ];
            });
    }

    protected function getUrgentOrders()
    {
        return OutboundOrder::with(['customer', 'customerAddress', 'outboundLoad'])
            ->where(function ($query) {
                $query->where('delivery_priority', 'urgent')
                      ->orWhere('must_deliver_by', '<=', now()->addDay())
                      ->orWhere(function ($q) {
                          $q->where('delivery_priority', 'priority')
                            ->where('must_deliver_by', '<=', now()->addDays(2));
                      });
            })
            ->whereIn('status', ['pending', 'ready_for_collection', 'collected', 'in_transit', 'out_for_delivery'])
            ->orderBy('must_deliver_by')
            ->orderBy('delivery_priority')
            ->limit(15)
            ->get()
            ->map(function ($order) {
                return [
                    'order' => $order,
                    'priority_class' => $this->getPriorityClass($order->delivery_priority),
                    'is_overdue' => $order->isOverdue(),
                    'time_remaining' => $order->must_deliver_by ? $order->must_deliver_by->diffForHumans() : null,
                ];
            });
    }

    protected function getRecentActivity()
    {
        // This would typically come from an activity log or audit trail
        // For now, return recent load and order updates
        $recentLoads = OutboundLoad::with('createdBy')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        $recentOrders = OutboundOrder::with(['customer', 'outboundLoad'])
            ->where('status', '!=', 'pending')
            ->orderBy('updated_at', 'desc')
            ->limit(5)
            ->get();

        $activities = collect();

        foreach ($recentLoads as $load) {
            $activities->push([
                'type' => 'load',
                'action' => 'Load ' . ($load->wasRecentlyCreated ? 'created' : 'updated'),
                'description' => "Load {$load->load_reference} - {$load->status}",
                'timestamp' => $load->updated_at,
                'user' => $load->createdBy->name ?? 'System',
            ]);
        }

        foreach ($recentOrders as $order) {
            $activities->push([
                'type' => 'order',
                'action' => 'Order ' . $order->status,
                'description' => "Order {$order->order_reference} for {$order->customer->name}",
                'timestamp' => $order->updated_at,
                'user' => 'System',
            ]);
        }

        return $activities->sortByDesc('timestamp')->take(10);
    }

    protected function getPerformanceMetrics($date)
    {
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();

        $totalOrders = OutboundOrder::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        $deliveredOrders = OutboundOrder::byStatus('delivered')
            ->whereBetween('actual_delivery_time', [$startOfMonth, $endOfMonth])
            ->count();

        $onTimeDeliveries = OutboundOrder::byStatus('delivered')
            ->whereBetween('actual_delivery_time', [$startOfMonth, $endOfMonth])
            ->whereRaw('actual_delivery_time <= estimated_delivery_time')
            ->count();

        return [
            'delivery_rate' => $totalOrders > 0 ? round(($deliveredOrders / $totalOrders) * 100, 1) : 0,
            'on_time_rate' => $deliveredOrders > 0 ? round(($onTimeDeliveries / $deliveredOrders) * 100, 1) : 0,
            'total_orders_month' => $totalOrders,
            'delivered_orders_month' => $deliveredOrders,
            'on_time_deliveries' => $onTimeDeliveries,
            'average_delivery_time' => $this->getAverageDeliveryTime($startOfMonth, $endOfMonth),
        ];
    }

    protected function calculateLoadProgress(OutboundLoad $load): int
    {
        $statusProgress = [
            'planning' => 10,
            'ready_for_collection' => 25,
            'collecting' => 50,
            'in_transit' => 75,
            'delivering' => 90,
            'completed' => 100,
            'cancelled' => 0,
        ];

        return $statusProgress[$load->status] ?? 0;
    }

    protected function getNextAction(OutboundLoad $load): string
    {
        switch ($load->status) {
            case 'planning':
                return 'Assign vehicle and driver';
            case 'ready_for_collection':
                return 'Start collection route';
            case 'collecting':
                return 'Complete collections';
            case 'in_transit':
                return 'Start deliveries';
            case 'delivering':
                return 'Complete deliveries';
            default:
                return 'No action required';
        }
    }

    protected function getTimeUntilCollection(LoadCollection $collection): string
    {
        if ($collection->planned_collection_time->isPast()) {
            return 'Overdue by ' . $collection->planned_collection_time->diffForHumans();
        }

        return 'Due ' . $collection->planned_collection_time->diffForHumans();
    }

    protected function getStatusClass(string $status): string
    {
        $statusClasses = [
            'planning' => 'text-gray-600',
            'pending' => 'text-gray-600',
            'ready' => 'text-blue-600',
            'ready_for_collection' => 'text-blue-600',
            'collecting' => 'text-yellow-600',
            'collected' => 'text-indigo-600',
            'in_transit' => 'text-orange-600',
            'delivering' => 'text-purple-600',
            'out_for_delivery' => 'text-purple-600',
            'delivered' => 'text-green-600',
            'completed' => 'text-green-600',
            'failed' => 'text-red-600',
            'cancelled' => 'text-red-600',
        ];

        return $statusClasses[$status] ?? 'text-gray-600';
    }

    protected function getPriorityClass(string $priority): string
    {
        $priorityClasses = [
            'standard' => 'text-gray-600',
            'priority' => 'text-yellow-600',
            'urgent' => 'text-red-600',
        ];

        return $priorityClasses[$priority] ?? 'text-gray-600';
    }

    protected function getAverageDeliveryTime($startDate, $endDate): float
    {
        $avgMinutes = OutboundOrder::byStatus('delivered')
            ->whereBetween('actual_delivery_time', [$startDate, $endDate])
            ->selectRaw('AVG(TIMESTAMPDIFF(MINUTE, estimated_delivery_time, actual_delivery_time)) as avg_minutes')
            ->value('avg_minutes');

        return round($avgMinutes ?: 0, 1);
    }

    // AJAX endpoints for dashboard updates
    public function getLoadStatusCounts()
    {
        $statusCounts = OutboundLoad::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return response()->json($statusCounts);
    }

    public function getOrderStatusCounts()
    {
        $statusCounts = OutboundOrder::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return response()->json($statusCounts);
    }

    public function getUpcomingCollections()
    {
        $collections = LoadCollection::with(['outboundLoad', 'depot'])
            ->where('planned_collection_time', '>=', now())
            ->where('planned_collection_time', '<=', now()->addHours(4))
            ->orderBy('planned_collection_time')
            ->get();

        return response()->json($collections);
    }
}