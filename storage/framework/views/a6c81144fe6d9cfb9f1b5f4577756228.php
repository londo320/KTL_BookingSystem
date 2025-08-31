<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">🚚 Trailer Collection</h2>
                <p class="text-sm text-gray-600 mt-1">Trailers ready for collection - Full, Empty & Rejected</p>
            </div>
            <div class="flex items-center space-x-3">
                <?php if(count($allDepots) > 1): ?>
                    <div class="flex items-center space-x-2">
                        <label for="depot-select" class="text-sm font-medium text-gray-700">Depot:</label>
                        <select id="depot-select" onchange="location.href='?depot_id='+this.value" 
                                class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Depots</option>
                            <?php $__currentLoopData = $allDepots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($depot->id); ?>" <?php echo e($currentDepotId == $depot->id ? 'selected' : ''); ?>>
                                    <?php echo e($depot->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                <?php endif; ?>
                <button onclick="location.reload()" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    🔄 Refresh
                </button>
            </div>
        </div>
     <?php $__env->endSlot(); ?>
    <div class="py-6">
        <?php if(session('success')): ?>
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded max-w-6xl mx-auto">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>
        <?php if($errors->any()): ?>
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded max-w-6xl mx-auto">
                <ul class="list-disc list-inside text-sm">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>
        <div class="max-w-6xl mx-auto">
            <!-- Summary Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-yellow-500">
                    <h3 class="text-lg font-semibold text-gray-800">📦 Regular Bookings</h3>
                    <p class="text-3xl font-bold text-yellow-600"><?php echo e($trailers->count()); ?></p>
                    <p class="text-sm text-gray-600">Awaiting Collection</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                    <h3 class="text-lg font-semibold text-gray-800">🏭 Factory Bookings</h3>
                    <p class="text-3xl font-bold text-purple-600"><?php echo e($factoryTrailers->count()); ?></p>
                    <p class="text-sm text-gray-600">Awaiting Collection</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                    <h3 class="text-lg font-semibold text-gray-800">📋 Total Trailers</h3>
                    <p class="text-3xl font-bold text-green-600"><?php echo e($trailers->count() + $factoryTrailers->count()); ?></p>
                    <p class="text-sm text-gray-600">Ready for Collection</p>
                </div>
            </div>
            <!-- Regular Bookings -->
            <?php if($trailers->count() > 0): ?>
                <div class="bg-white rounded-lg shadow overflow-hidden mb-6">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-800">📦 Regular Bookings - Collection List</h3>
                        <p class="text-sm text-gray-600 mt-1"><?php echo e($trailers->count()); ?> trailers awaiting collection</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking Info</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php $__currentLoopData = $trailers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trailer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $movement = $trailer->movements->first();
                                        $status = $movement->current_status ?? 'unknown';
                                        $statusColor = match($status) {
                                            'empty' => 'bg-blue-100 text-blue-800',
                                            'trailer_dropped' => 'bg-yellow-100 text-yellow-800',
                                            'awaiting_collection' => 'bg-green-100 text-green-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                        $statusText = match($status) {
                                            'empty' => 'Empty',
                                            'trailer_dropped' => 'Full/Dropped',
                                            'awaiting_collection' => 'Ready for Collection',
                                            default => ucwords(str_replace('_', ' ', $status))
                                        };
                                    ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">#<?php echo e($trailer->id); ?></div>
                                                <div class="text-sm text-gray-500"><?php echo e($trailer->slot->depot->name); ?></div>
                                                <?php if($trailer->trailer_registration): ?>
                                                    <div class="text-xs text-gray-400"><?php echo e($trailer->trailer_registration); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?php echo e($trailer->customer->name); ?></div>
                                            <?php if($trailer->poNumbers->count() > 0): ?>
                                                <div class="text-xs text-gray-500">
                                                    PO: <?php echo e($trailer->poNumbers->pluck('po_number')->implode(', ')); ?>

                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($statusColor); ?>">
                                                <?php echo e($statusText); ?>

                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php if($movement && $movement->tippingLocation): ?>
                                                <div class="font-medium"><?php echo e($movement->tippingLocation->name); ?></div>
                                                <?php if($movement->tippingLocation->code): ?>
                                                    <div class="text-xs text-gray-500"><?php echo e($movement->tippingLocation->code); ?></div>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-gray-400">No location</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button onclick="collectTrailer(<?php echo e($trailer->id); ?>)" 
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                ✅ Collect
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
            <!-- Factory Bookings -->
            <?php if($factoryTrailers->count() > 0): ?>
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-800">🏭 Factory Bookings - Collection List</h3>
                        <p class="text-sm text-gray-600 mt-1"><?php echo e($factoryTrailers->count()); ?> factory trailers awaiting collection</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking Info</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php $__currentLoopData = $factoryTrailers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trailer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
                                        $movement = $trailer->movements->first();
                                        $status = $movement->current_status ?? 'unknown';
                                        $statusColor = match($status) {
                                            'empty' => 'bg-blue-100 text-blue-800',
                                            'trailer_dropped' => 'bg-yellow-100 text-yellow-800',
                                            'awaiting_collection' => 'bg-green-100 text-green-800',
                                            default => 'bg-gray-100 text-gray-800'
                                        };
                                        $statusText = match($status) {
                                            'empty' => 'Empty',
                                            'trailer_dropped' => 'Full/Dropped',
                                            'awaiting_collection' => 'Ready for Collection',
                                            default => ucwords(str_replace('_', ' ', $status))
                                        };
                                    ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900">Factory #<?php echo e($trailer->id); ?></div>
                                                <div class="text-sm text-gray-500"><?php echo e($trailer->depot->name); ?></div>
                                                <?php if($trailer->trailer_registration): ?>
                                                    <div class="text-xs text-gray-400"><?php echo e($trailer->trailer_registration); ?></div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900"><?php echo e($trailer->customer->name); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e($statusColor); ?>">
                                                <?php echo e($statusText); ?>

                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php if($movement && $movement->tippingLocation): ?>
                                                <div class="font-medium"><?php echo e($movement->tippingLocation->name); ?></div>
                                                <?php if($movement->tippingLocation->code): ?>
                                                    <div class="text-xs text-gray-500"><?php echo e($movement->tippingLocation->code); ?></div>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-gray-400">No location</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button onclick="collectFactoryTrailer(<?php echo e($trailer->id); ?>)" 
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                ✅ Collect
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
            <?php if($trailers->count() === 0 && $factoryTrailers->count() === 0): ?>
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <div class="text-6xl mb-4">🚚</div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">No Trailers Awaiting Collection</h3>
                    <p class="text-gray-600">All trailers have been collected or are still in the tipping workflow.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Collection Modal -->
    <div id="collectionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">🚚 Record Trailer Collection</h3>
                <form id="collectionForm" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Collection Unit Reg <span class="text-red-500">*</span></label>
                            <input type="text" name="collection_unit_registration" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="e.g., ABC123">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Driver Name</label>
                            <input type="text" name="collection_driver_name"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Driver's name">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Driver Phone</label>
                            <input type="text" name="collection_driver_phone"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Phone number">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <textarea name="notes" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Collection notes..."></textarea>
                        </div>
                    </div>
                    <div class="flex items-center justify-between mt-6">
                        <button type="button" onclick="closeCollectionModal()"
                                class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                            Record Collection
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function collectTrailer(bookingId) {
            document.getElementById('collectionForm').action = `/admin/trailer-collection/${bookingId}/collect`;
            document.getElementById('collectionModal').classList.remove('hidden');
        }
        function collectFactoryTrailer(bookingId) {
            document.getElementById('collectionForm').action = `/admin/factory-trailer-collection/${bookingId}/collect`;
            document.getElementById('collectionModal').classList.remove('hidden');
        }
        function closeCollectionModal() {
            document.getElementById('collectionModal').classList.add('hidden');
            document.getElementById('collectionForm').reset();
        }
        // Close modal when clicking outside
        document.getElementById('collectionModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCollectionModal();
            }
        });
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/trailer-collection/index.blade.php ENDPATH**/ ?>