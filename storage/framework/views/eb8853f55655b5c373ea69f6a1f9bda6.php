<?php $__env->startSection('title', 'Load ' . $load->load_reference); ?>

<?php $__env->startSection('content'); ?>
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="<?php echo e(route('outbound.loads.index')); ?>" 
                       class="text-gray-600 hover:text-gray-900">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900"><?php echo e($load->load_reference); ?></h1>
                        <?php if($load->load_name): ?>
                            <p class="text-gray-600 mt-1"><?php echo e($load->load_name); ?></p>
                        <?php endif; ?>
                    </div>
                    <span class="px-3 py-1 text-sm font-medium rounded-full 
                        <?php switch($load->status):
                            case ('planning'): ?>
                                bg-gray-100 text-gray-800
                                <?php break; ?>
                            <?php case ('ready_for_collection'): ?>
                                bg-blue-100 text-blue-800
                                <?php break; ?>
                            <?php case ('collecting'): ?>
                                bg-yellow-100 text-yellow-800
                                <?php break; ?>
                            <?php case ('in_transit'): ?>
                                bg-orange-100 text-orange-800
                                <?php break; ?>
                            <?php case ('delivering'): ?>
                                bg-purple-100 text-purple-800
                                <?php break; ?>
                            <?php case ('completed'): ?>
                                bg-green-100 text-green-800
                                <?php break; ?>
                            <?php case ('cancelled'): ?>
                                bg-red-100 text-red-800
                                <?php break; ?>
                        <?php endswitch; ?>">
                        <?php echo e(ucfirst(str_replace('_', ' ', $load->status))); ?>

                    </span>
                </div>
                <div class="flex space-x-3">
                    <a href="<?php echo e(route('outbound.loads.timing-analysis', $load)); ?>" 
                       class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                        Timing Analysis
                    </a>
                    <a href="<?php echo e(route('outbound.loads.edit', $load)); ?>" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                        Edit Load
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Load Summary -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Load Summary</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-600"><?php echo e($load->total_orders); ?></div>
                                <div class="text-sm text-gray-500">Orders</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-600"><?php echo e($load->total_customers); ?></div>
                                <div class="text-sm text-gray-500">Customers</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-orange-600"><?php echo e($load->total_collection_points); ?></div>
                                <div class="text-sm text-gray-500">Collection Points</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-purple-600"><?php echo e($load->total_pallets); ?></div>
                                <div class="text-sm text-gray-500">Pallets</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Orders -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-900">Orders (<?php echo e($load->orders->count()); ?>)</h2>
                        <?php if($load->status === 'planning'): ?>
                            <a href="<?php echo e(route('outbound.loads.add-order', $load)); ?>" 
                               class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                                Add Order
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="overflow-x-auto">
                        <?php if($load->orders->count() > 0): ?>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Delivery Address</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php $__currentLoopData = $load->orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900"><?php echo e($order->order_reference); ?></div>
                                                <?php if($order->po_number): ?>
                                                    <div class="text-xs text-gray-500">PO: <?php echo e($order->po_number); ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900"><?php echo e($order->customer->name); ?></div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900">
                                                    <?php echo e($order->customerAddress->address_line_1); ?>

                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    <?php echo e($order->customerAddress->city); ?>, <?php echo e($order->customerAddress->postcode); ?>

                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    <?php echo e($order->expected_pallets); ?>p / <?php echo e($order->expected_cases); ?>c / <?php echo e($order->expected_units); ?>u
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                                    <?php switch($order->status):
                                                        case ('pending'): ?>
                                                            bg-gray-100 text-gray-800
                                                            <?php break; ?>
                                                        <?php case ('ready_for_collection'): ?>
                                                            bg-blue-100 text-blue-800
                                                            <?php break; ?>
                                                        <?php case ('collected'): ?>
                                                            bg-indigo-100 text-indigo-800
                                                            <?php break; ?>
                                                        <?php case ('in_transit'): ?>
                                                            bg-orange-100 text-orange-800
                                                            <?php break; ?>
                                                        <?php case ('out_for_delivery'): ?>
                                                            bg-purple-100 text-purple-800
                                                            <?php break; ?>
                                                        <?php case ('delivered'): ?>
                                                            bg-green-100 text-green-800
                                                            <?php break; ?>
                                                        <?php case ('failed'): ?>
                                                            bg-red-100 text-red-800
                                                            <?php break; ?>
                                                    <?php endswitch; ?>">
                                                    <?php echo e(ucfirst(str_replace('_', ' ', $order->status))); ?>

                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                                    <?php switch($order->delivery_priority):
                                                        case ('standard'): ?>
                                                            bg-gray-100 text-gray-800
                                                            <?php break; ?>
                                                        <?php case ('priority'): ?>
                                                            bg-yellow-100 text-yellow-800
                                                            <?php break; ?>
                                                        <?php case ('urgent'): ?>
                                                            bg-red-100 text-red-800
                                                            <?php break; ?>
                                                    <?php endswitch; ?>">
                                                    <?php echo e(ucfirst($order->delivery_priority)); ?>

                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <a href="<?php echo e(route('outbound.orders.show', $order)); ?>" 
                                                   class="text-blue-600 hover:text-blue-900">View</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="p-8 text-center text-gray-500">
                                <div class="mb-4">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <p>No orders added to this load yet</p>
                                <?php if($load->status === 'planning'): ?>
                                    <p class="text-sm mt-2">
                                        <a href="<?php echo e(route('outbound.loads.add-order', $load)); ?>" 
                                           class="text-blue-600 hover:text-blue-800">Add your first order</a>
                                    </p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Collections -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h2 class="text-lg font-semibold text-gray-900">Collections (<?php echo e($load->collections->count()); ?>)</h2>
                        <?php if($load->status === 'planning'): ?>
                            <a href="<?php echo e(route('outbound.loads.add-collection', $load)); ?>" 
                               class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                                Add Collection
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="overflow-x-auto">
                        <?php if($load->collections->count() > 0): ?>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Depot</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Planned Time</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sequence</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php $__currentLoopData = $load->collections->sortBy('collection_sequence'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $collection): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900"><?php echo e($collection->depot->name); ?></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900"><?php echo e($collection->planned_collection_time->format('M j, Y H:i')); ?></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900"><?php echo e($collection->collection_sequence ?? 'Not set'); ?></div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    <?php echo e($collection->depot_pallets); ?>p / <?php echo e($collection->depot_cases); ?>c / <?php echo e($collection->depot_units); ?>u
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                                    <?php switch($collection->status):
                                                        case ('pending'): ?>
                                                            bg-gray-100 text-gray-800
                                                            <?php break; ?>
                                                        <?php case ('ready'): ?>
                                                            bg-blue-100 text-blue-800
                                                            <?php break; ?>
                                                        <?php case ('collecting'): ?>
                                                            bg-yellow-100 text-yellow-800
                                                            <?php break; ?>
                                                        <?php case ('collected'): ?>
                                                            bg-green-100 text-green-800
                                                            <?php break; ?>
                                                        <?php case ('failed'): ?>
                                                            bg-red-100 text-red-800
                                                            <?php break; ?>
                                                    <?php endswitch; ?>">
                                                    <?php echo e(ucfirst($collection->status)); ?>

                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <a href="<?php echo e(route('outbound.collections.show', $collection)); ?>" 
                                                   class="text-blue-600 hover:text-blue-900">View</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="p-8 text-center text-gray-500">
                                <div class="mb-4">
                                    <svg class="w-12 h-12 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <p>No collection points added yet</p>
                                <?php if($load->status === 'planning'): ?>
                                    <p class="text-sm mt-2">
                                        <a href="<?php echo e(route('outbound.loads.add-collection', $load)); ?>" 
                                           class="text-green-600 hover:text-green-800">Add collection point</a>
                                    </p>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Load Details -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Load Details</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created</dt>
                            <dd class="text-sm text-gray-900"><?php echo e($load->created_at->format('M j, Y H:i')); ?></dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Source</dt>
                            <dd class="text-sm text-gray-900"><?php echo e(ucfirst(str_replace('_', ' ', $load->created_from))); ?></dd>
                        </div>
                        <?php if($load->plannedVehicle): ?>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Vehicle</dt>
                                <dd class="text-sm text-gray-900"><?php echo e($load->plannedVehicle->registration); ?></dd>
                            </div>
                        <?php endif; ?>
                        <?php if($load->assignedDriver): ?>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Driver</dt>
                                <dd class="text-sm text-gray-900"><?php echo e($load->assignedDriver->name); ?></dd>
                            </div>
                        <?php endif; ?>
                        <?php if($load->optimized_distance_km): ?>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Route Distance</dt>
                                <dd class="text-sm text-gray-900"><?php echo e($load->optimized_distance_km); ?> km</dd>
                            </div>
                        <?php endif; ?>
                        <?php if($load->estimated_duration_minutes): ?>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Estimated Duration</dt>
                                <dd class="text-sm text-gray-900"><?php echo e(floor($load->estimated_duration_minutes / 60)); ?>h <?php echo e($load->estimated_duration_minutes % 60); ?>m</dd>
                            </div>
                        <?php endif; ?>
                        <?php if($load->notes): ?>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Notes</dt>
                                <dd class="text-sm text-gray-900"><?php echo e($load->notes); ?></dd>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Quick Actions</h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <?php switch($load->status):
                            case ('planning'): ?>
                                <button class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded">
                                    Mark Ready for Collection
                                </button>
                                <?php break; ?>
                            <?php case ('ready_for_collection'): ?>
                                <button class="w-full bg-yellow-600 hover:bg-yellow-700 text-white py-2 px-4 rounded">
                                    Start Collection
                                </button>
                                <?php break; ?>
                            <?php case ('collecting'): ?>
                                <button class="w-full bg-orange-600 hover:bg-orange-700 text-white py-2 px-4 rounded">
                                    Complete Collections
                                </button>
                                <?php break; ?>
                            <?php case ('in_transit'): ?>
                                <button class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded">
                                    Start Deliveries
                                </button>
                                <?php break; ?>
                            <?php case ('delivering'): ?>
                                <button class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded">
                                    Complete Load
                                </button>
                                <?php break; ?>
                        <?php endswitch; ?>
                        
                        <?php if($load->status !== 'completed'): ?>
                            <button class="w-full bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded">
                                Print Load Sheet
                            </button>
                            <button class="w-full bg-indigo-600 hover:bg-indigo-700 text-white py-2 px-4 rounded">
                                Export to GPS
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/outbound/admin/loads/show.blade.php ENDPATH**/ ?>