<?php $__env->startSection('title', 'Outbound Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Outbound Dashboard</h1>
                    <p class="text-gray-600 mt-1">Monitor delivery operations and load management</p>
                </div>
                <div class="flex space-x-3">
                    <input type="date" value="<?php echo e($dateFilter->format('Y-m-d')); ?>" 
                           class="form-input rounded-md" id="date-filter">
                    <a href="<?php echo e(route('outbound.loads.create')); ?>" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">
                        New Load
                    </a>
                </div>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500">Active Loads</h3>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo e($kpis['active_loads']); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500">Pending Collections</h3>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo e($kpis['pending_collections']); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h1.586a1 1 0 01.707.293L10 7H5zM5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500">Out for Delivery</h3>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo e($kpis['out_for_delivery']); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-red-100 rounded-lg">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500">Overdue Items</h3>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo e($kpis['overdue_collections'] + $kpis['overdue_orders']); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Active Loads -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Active Loads</h2>
                </div>
                <div class="p-6">
                    <?php if($activeLoads->count() > 0): ?>
                        <div class="space-y-4">
                            <?php $__currentLoopData = $activeLoads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <h3 class="font-medium text-gray-900"><?php echo e($item['load']->load_reference); ?></h3>
                                            <span class="px-2 py-1 text-xs rounded-full <?php echo e($item['status_class']); ?> bg-gray-100">
                                                <?php echo e(ucfirst(str_replace('_', ' ', $item['load']->status))); ?>

                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">
                                            <?php echo e($item['customer_count']); ?> customers, <?php echo e($item['load']->total_orders); ?> orders
                                        </p>
                                        <div class="mt-2">
                                            <div class="flex items-center">
                                                <div class="w-full bg-gray-200 rounded-full h-2">
                                                    <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo e($item['progress_percentage']); ?>%"></div>
                                                </div>
                                                <span class="ml-2 text-xs text-gray-500"><?php echo e($item['progress_percentage']); ?>%</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <a href="<?php echo e(route('outbound.loads.show', $item['load'])); ?>" 
                                           class="text-blue-600 hover:text-blue-900">View</a>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500 text-center py-8">No active loads</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Today's Collections -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Today's Collections</h2>
                </div>
                <div class="p-6">
                    <?php if($todaysCollections->count() > 0): ?>
                        <div class="space-y-4">
                            <?php $__currentLoopData = $todaysCollections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg <?php echo e($item['is_overdue'] ? 'border-l-4 border-red-500' : ''); ?>">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <h3 class="font-medium text-gray-900"><?php echo e($item['collection']->outboundLoad->load_reference); ?></h3>
                                            <span class="px-2 py-1 text-xs rounded-full <?php echo e($item['status_class']); ?> bg-gray-100">
                                                <?php echo e(ucfirst($item['collection']->status)); ?>

                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">
                                            <?php echo e($item['collection']->depot->name); ?> - <?php echo e($item['time_until_collection']); ?>

                                        </p>
                                        <p class="text-xs text-gray-500">
                                            <?php echo e($item['collection']->planned_collection_time->format('H:i')); ?>

                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500 text-center py-8">No collections today</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Urgent Orders -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Urgent Orders</h2>
                </div>
                <div class="p-6">
                    <?php if($urgentOrders->count() > 0): ?>
                        <div class="space-y-4">
                            <?php $__currentLoopData = $urgentOrders->take(10); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg <?php echo e($item['is_overdue'] ? 'border-l-4 border-red-500' : ''); ?>">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <h3 class="font-medium text-gray-900"><?php echo e($item['order']->order_reference); ?></h3>
                                            <span class="px-2 py-1 text-xs rounded-full <?php echo e($item['priority_class']); ?> bg-gray-100">
                                                <?php echo e(ucfirst($item['order']->delivery_priority)); ?>

                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1"><?php echo e($item['order']->customer->name); ?></p>
                                        <?php if($item['time_remaining']): ?>
                                            <p class="text-xs text-gray-500"><?php echo e($item['time_remaining']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <p class="text-gray-500 text-center py-8">No urgent orders</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Performance Metrics -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Performance Metrics</h2>
                    <p class="text-sm text-gray-500">This month</p>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Delivery Rate</span>
                            <span class="font-semibold text-gray-900"><?php echo e($performanceMetrics['delivery_rate']); ?>%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">On-Time Rate</span>
                            <span class="font-semibold text-gray-900"><?php echo e($performanceMetrics['on_time_rate']); ?>%</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Total Orders</span>
                            <span class="font-semibold text-gray-900"><?php echo e($performanceMetrics['total_orders_month']); ?></span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Delivered Orders</span>
                            <span class="font-semibold text-gray-900"><?php echo e($performanceMetrics['delivered_orders_month']); ?></span>
                        </div>
                        <?php if($performanceMetrics['average_delivery_time'] != 0): ?>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">Avg Delivery Variance</span>
                            <span class="font-semibold text-gray-900">
                                <?php echo e($performanceMetrics['average_delivery_time'] > 0 ? '+' : ''); ?><?php echo e($performanceMetrics['average_delivery_time']); ?>min
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('date-filter').addEventListener('change', function() {
    window.location.href = '<?php echo e(route('outbound.dashboard')); ?>?date=' + this.value;
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/outbound/admin/dashboard/index.blade.php ENDPATH**/ ?>