<?php if (isset($component)) { $__componentOriginal4beee80a27ac16bdb2a9a95f2c509448 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal4beee80a27ac16bdb2a9a95f2c509448 = $attributes; } ?>
<?php $component = App\View\Components\SiteAdminLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('site-admin-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\SiteAdminLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            🕒 Departure Management
        </h2>
     <?php $__env->endSlot(); ?>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <div class="mb-6 bg-orange-50 border-l-4 border-orange-400 p-4">
            <div class="flex">
                <div class="ml-3">
                    <p class="text-sm text-orange-700">
                        <strong>Departure Management</strong> - Shows all vehicles currently on site that need to be marked as departed.
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Booking Reference
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Customer & Vehicle
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Location & Gate
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Arrival Time
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Duration On Site
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__empty_1 = true; $__currentLoopData = $departures; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-50 bg-blue-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-mono text-sm font-semibold text-blue-600">
                                        <?php echo e($booking->booking_reference); ?>

                                    </div>
                                    <?php if($booking->reference): ?>
                                        <div class="text-xs text-gray-500"><?php echo e($booking->reference); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo e($booking->customer->name ?? 'N/A'); ?>

                                    </div>
                                    <?php if($booking->vehicle_registration): ?>
                                        <div class="text-xs text-gray-600">🚛 <?php echo e($booking->vehicle_registration); ?></div>
                                    <?php endif; ?>
                                    <?php if($booking->container_number): ?>
                                        <div class="text-xs text-gray-600">📦 <?php echo e($booking->container_number); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        📍 <?php echo e($booking->slot->depot->name); ?>

                                    </div>
                                    <?php if($booking->gate_number || $booking->bay_number): ?>
                                        <div class="text-sm text-green-600">
                                            <?php if($booking->gate_number): ?>🚪 Gate <?php echo e($booking->gate_number); ?> <?php endif; ?>
                                            <?php if($booking->bay_number): ?>🏗️ Bay <?php echo e($booking->bay_number); ?><?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($booking->special_instructions): ?>
                                        <div class="text-xs text-orange-600 mt-1">
                                            ⚠️ <?php echo e(Str::limit($booking->special_instructions, 30)); ?>

                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        ✅ <?php echo e($booking->arrived_at->format('d-M H:i')); ?>

                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Slot: <?php echo e($booking->slot->start_at->format('d-M H:i')); ?>

                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-blue-600">
                                        <?php echo e($booking->arrived_at->diffForHumans()); ?>

                                    </div>
                                    <div class="text-xs text-gray-500">
                                        <?php echo e($booking->arrived_at->diffInMinutes(now())); ?> minutes
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <form method="POST" action="<?php echo e(route('site.bookings.departure', $booking)); ?>" class="inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <input type="hidden" name="departure_scenario" value="completed_with_trailer">
                                        <input type="hidden" name="departure_notes" value="Quick departure from departures list">
                                        <button type="submit" 
                                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                                            Mark Departed
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    No vehicles currently on site.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if($departures->hasPages()): ?>
                <div class="bg-white px-4 py-3 border-t border-gray-200">
                    <?php echo e($departures->links()); ?>

                </div>
            <?php endif; ?>
        </div>

        <?php if($departures->count() > 0): ?>
            <div class="mt-6 bg-white shadow rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">📊 On-Site Summary</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-blue-50 rounded">
                        <div class="text-2xl font-bold text-blue-600"><?php echo e($departures->count()); ?></div>
                        <div class="text-sm text-gray-600">Vehicles On Site</div>
                    </div>
                    <div class="text-center p-4 bg-green-50 rounded">
                        <div class="text-2xl font-bold text-green-600">
                            <?php echo e($departures->avg(function($booking) { return $booking->arrived_at->diffInMinutes(now()); }) ? round($departures->avg(function($booking) { return $booking->arrived_at->diffInMinutes(now()); })) : 0); ?>

                        </div>
                        <div class="text-sm text-gray-600">Avg. Minutes On Site</div>
                    </div>
                    <div class="text-center p-4 bg-orange-50 rounded">
                        <div class="text-2xl font-bold text-orange-600">
                            <?php echo e($departures->where('arrived_at', '<', now()->subHours(2))->count()); ?>

                        </div>
                        <div class="text-sm text-gray-600">On Site > 2 Hours</div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4beee80a27ac16bdb2a9a95f2c509448)): ?>
<?php $attributes = $__attributesOriginal4beee80a27ac16bdb2a9a95f2c509448; ?>
<?php unset($__attributesOriginal4beee80a27ac16bdb2a9a95f2c509448); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4beee80a27ac16bdb2a9a95f2c509448)): ?>
<?php $component = $__componentOriginal4beee80a27ac16bdb2a9a95f2c509448; ?>
<?php unset($__componentOriginal4beee80a27ac16bdb2a9a95f2c509448); ?>
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/site-admin/departures.blade.php ENDPATH**/ ?>