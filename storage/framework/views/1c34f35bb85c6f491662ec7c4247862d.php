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
            🚛 Live Arrivals Management
        </h2>
     <?php $__env->endSlot(); ?>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <div class="mb-6 bg-blue-50 border-l-4 border-blue-400 p-4">
            <div class="flex">
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        <strong>Live Arrivals View</strong> - Auto-refreshes every 30 seconds. Shows all expected arrivals for today and tomorrow.
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
                                Scheduled Time
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Estimated Arrival
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php $__empty_1 = true; $__currentLoopData = $arrivals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-50 <?php echo e($booking->arrived_at ? 'bg-green-50' : 
                                (Carbon\Carbon::parse($booking->slot->start_at)->isPast() ? 'bg-red-50' : 'bg-white')); ?>">
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
                                    <?php if($booking->carrier_company): ?>
                                        <div class="text-xs text-gray-600">🚛 <?php echo e($booking->carrier_company); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        📍 <?php echo e($booking->slot->depot->name); ?>

                                    </div>
                                    <div class="text-sm text-gray-600">
                                        <?php echo e($booking->slot->start_at->format('d-M H:i')); ?>

                                    </div>
                                    <?php if($booking->tipping_bay_id): ?>
                                        <div class="text-xs text-blue-600">
                                            <?php if($booking->tippingBay): ?>🏗️ <?php echo e($booking->tippingBay->name); ?><?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if($booking->estimated_arrival): ?>
                                        <div class="text-sm text-purple-600">
                                            📅 <?php echo e(Carbon\Carbon::parse($booking->estimated_arrival)->format('d-M H:i')); ?>

                                        </div>
                                        <div class="text-xs text-gray-500">
                                            <?php echo e(Carbon\Carbon::parse($booking->estimated_arrival)->diffForHumans()); ?>

                                        </div>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-sm">Not provided</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if($booking->arrived_at): ?>
                                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                            ✅ Arrived <?php echo e($booking->arrived_at->format('H:i')); ?>

                                        </span>
                                        <?php if(!$booking->departed_at): ?>
                                            <div class="text-xs text-blue-600 mt-1">🏢 On site</div>
                                        <?php endif; ?>
                                    <?php elseif(Carbon\Carbon::parse($booking->slot->start_at)->isPast()): ?>
                                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                            ⚠️ Overdue
                                        </span>
                                        <div class="text-xs text-red-600 mt-1">
                                            <?php echo e(Carbon\Carbon::parse($booking->slot->start_at)->diffForHumans()); ?>

                                        </div>
                                    <?php else: ?>
                                        <span class="inline-flex px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            ⏳ Expected
                                        </span>
                                        <div class="text-xs text-gray-600 mt-1">
                                            <?php echo e(Carbon\Carbon::parse($booking->slot->start_at)->diffForHumans()); ?>

                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <?php if(!$booking->arrived_at): ?>
                                        <button onclick="openArrivalModal(<?php echo e($booking->id); ?>, '<?php echo e($booking->booking_reference); ?>', '<?php echo e(addslashes($booking->customer->name ?? 'N/A')); ?>', '<?php echo e($booking->slot->depot->name); ?>', '<?php echo e($booking->slot->start_at->format('d-M-Y H:i')); ?>', '<?php echo e($booking->vehicle_registration ?? ''); ?>', '<?php echo e($booking->container_number ?? ''); ?>', '<?php echo e($booking->carrier_company ?? ''); ?>', '<?php echo e($booking->expected_cases ?? 0); ?>', '<?php echo e($booking->expected_pallets ?? 0); ?>', '<?php echo e(addslashes($booking->special_instructions ?? '')); ?>')" 
                                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm cursor-pointer">
                                            🚛 Process Arrival
                                        </button>
                                    <?php elseif(!$booking->departed_at): ?>
                                        <form method="POST" action="<?php echo e(route('site.bookings.departure', $booking)); ?>" class="inline">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PATCH'); ?>
                                            <button type="submit" 
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                                                Mark Departed
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-gray-500 text-sm">Completed</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    No expected arrivals found for today or tomorrow.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if($arrivals->hasPages()): ?>
                <div class="bg-white px-4 py-3 border-t border-gray-200">
                    <?php echo e($arrivals->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Arrival Modal - Updated <?php echo e(date('Y-m-d-H-i-s')); ?> -->
    <div id="arrivalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Modal Header -->
                <div class="flex justify-between items-center pb-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">🚛 Vehicle Arrival Processing - MODAL VERSION</h3>
                    <button onclick="closeArrivalModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Booking Summary -->
                <div id="bookingSummary" class="mt-4 p-4 bg-green-50 rounded-lg">
                    <!-- Will be populated by JavaScript -->
                </div>

                <!-- Arrival Form -->
                <form id="arrivalForm" method="POST" class="mt-6">
                    <?php echo csrf_field(); ?>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <!-- Required Vehicle Registration -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Vehicle Registration <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="vehicleRegistration" name="vehicle_registration" required
                                   placeholder="e.g., AB12 CDE"
                                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            <p class="text-xs text-gray-500 mt-1">Required for arrival processing</p>
                        </div>

                        <!-- Container Number -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Container/Trailer Number</label>
                            <input type="text" id="containerNumber" name="container_number"
                                   placeholder="e.g., CONT123456 or TR123456"
                                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            <p class="text-xs text-gray-500 mt-1">Can be updated if different from booking</p>
                        </div>

                        <!-- Carrier Company -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Carrier Company</label>
                            <input type="text" id="carrierCompany" name="carrier_company"
                                   placeholder="e.g., ABC Transport Ltd"
                                   class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        </div>


                        <!-- Tipping Location Assignment -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">🚛 Tipping Drop Location</label>
                            <select id="tippingLocation" name="tipping_location_id" class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                                <option value="">– Assign Drop Location –</option>
                                <?php if(isset($tippingLocations)): ?>
                                    <?php $__currentLoopData = $tippingLocations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($location->id); ?>">
                                            <?php echo e($location->name); ?> (<?php echo e($location->getAvailableCapacity()); ?>/<?php echo e($location->capacity); ?> available)
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Optional: Assign vehicle to parking area</p>
                        </div>

                        <!-- Tipping Bay Assignment -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">🏗️ Tipping Bay (Direct Assignment)</label>
                            <select id="tippingBay" name="tipping_bay_id" class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                                <option value="">– Skip to bay directly –</option>
                                <?php if(isset($tippingBays)): ?>
                                    <?php $__currentLoopData = $tippingBays; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($bay->id); ?>" <?php if($bay->is_occupied): echo 'disabled'; endif; ?>>
                                            <?php echo e($bay->name); ?> (<?php echo e($bay->depot->name); ?>) 
                                            <?php if($bay->is_occupied): ?>
                                                - Occupied
                                            <?php else: ?>
                                                - Available
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Optional: Assign vehicle directly to tipping bay</p>
                        </div>

                    </div>

                    <!-- Special Instructions -->
                    <div id="specialInstructions" class="mt-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200 hidden">
                        <h4 class="font-medium text-yellow-800 mb-2">⚠️ Special Instructions:</h4>
                        <p id="specialInstructionsText" class="text-yellow-700"></p>
                    </div>

                    <!-- Arrival Time Display -->
                    <div class="mt-4 p-4 bg-green-50 rounded-lg border border-green-200">
                        <h4 class="font-medium text-green-800 mb-2">📅 Arrival Time:</h4>
                        <p class="text-green-700 font-semibold" id="arrivalTime">Will be recorded as: [Current Time]</p>
                    </div>

                    <!-- Form Actions -->
                    <div class="mt-6 flex justify-end space-x-4">
                        <button type="button" onclick="closeArrivalModal()" 
                                class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
                            🚛 Mark Vehicle Arrived
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Updated: <?php echo e(date('Y-m-d H:i:s')); ?> - Removed old fields, added tipping logic
        let currentBookingId = null;

        function openArrivalModal(bookingId, bookingRef, customer, depot, scheduledTime, vehicleReg, containerNum, carrierCompany, expectedCases, expectedPallets, specialInstructions) {
            currentBookingId = bookingId;
            
            // Update booking summary
            document.getElementById('bookingSummary').innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <strong>Booking:</strong> ${bookingRef}<br>
                        <strong>Customer:</strong> ${customer}
                    </div>
                    <div>
                        <strong>Depot:</strong> ${depot}<br>
                        <strong>Scheduled:</strong> ${scheduledTime}
                    </div>
                    <div>
                        <strong>Expected:</strong> ${expectedCases} cases, ${expectedPallets} pallets
                    </div>
                </div>
            `;

            // Update form action
            document.getElementById('arrivalForm').action = `/site-admin/bookings/${bookingId}/arrival`;

            // Populate form fields
            document.getElementById('vehicleRegistration').value = vehicleReg;
            document.getElementById('containerNumber').value = containerNum;
            document.getElementById('carrierCompany').value = carrierCompany;

            // Show special instructions if any
            if (specialInstructions && specialInstructions.trim() !== '') {
                document.getElementById('specialInstructionsText').textContent = specialInstructions;
                document.getElementById('specialInstructions').classList.remove('hidden');
            } else {
                document.getElementById('specialInstructions').classList.add('hidden');
            }

            // Update arrival time display
            updateArrivalTime();

            // Show modal
            document.getElementById('arrivalModal').classList.remove('hidden');
            
            // Focus on vehicle registration field
            setTimeout(() => {
                document.getElementById('vehicleRegistration').focus();
            }, 100);
        }

        function closeArrivalModal() {
            document.getElementById('arrivalModal').classList.add('hidden');
            currentBookingId = null;
        }

        function updateArrivalTime() {
            const now = new Date();
            const timeString = now.toLocaleString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            });
            document.getElementById('arrivalTime').textContent = `Will be recorded as: ${timeString}`;
        }

        // Close modal when clicking outside
        document.getElementById('arrivalModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeArrivalModal();
            }
        });

        // Update time display every second
        setInterval(() => {
            if (!document.getElementById('arrivalModal').classList.contains('hidden')) {
                updateArrivalTime();
            }
        }, 1000);
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal4beee80a27ac16bdb2a9a95f2c509448)): ?>
<?php $attributes = $__attributesOriginal4beee80a27ac16bdb2a9a95f2c509448; ?>
<?php unset($__attributesOriginal4beee80a27ac16bdb2a9a95f2c509448); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal4beee80a27ac16bdb2a9a95f2c509448)): ?>
<?php $component = $__componentOriginal4beee80a27ac16bdb2a9a95f2c509448; ?>
<?php unset($__componentOriginal4beee80a27ac16bdb2a9a95f2c509448); ?>
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/site-admin/arrivals.blade.php ENDPATH**/ ?>