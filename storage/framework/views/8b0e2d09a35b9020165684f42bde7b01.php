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
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-800 leading-tight">
                📋 Depot Bookings Management
            </h2>
            <div class="flex gap-2">
                <a href="<?php echo e(route('depot.bookings.fix-historical-departures')); ?>"
                   class="px-3 py-1 bg-orange-600 text-white rounded hover:bg-orange-700 text-sm">
                  🔧 Fix Historical Data
                </a>
            </div>
        </div>
     <?php $__env->endSlot(); ?>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <?php if(session('success')): ?>
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>
        <!-- Filters -->
        <div class="bg-white p-4 rounded-lg shadow mb-6">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date From</label>
                    <input type="date" name="from" value="<?php echo e(request('from')); ?>" 
                           class="w-full border-gray-300 rounded">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date To</label>
                    <input type="date" name="to" value="<?php echo e(request('to')); ?>" 
                           class="w-full border-gray-300 rounded">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Arrival Status</label>
                    <select name="arrival" class="w-full border-gray-300 rounded">
                        <option value="">All</option>
                        <option value="not_arrived" <?php if(request('arrival') == 'not_arrived'): echo 'selected'; endif; ?>>Not Arrived</option>
                        <option value="arrived" <?php if(request('arrival') == 'arrived'): echo 'selected'; endif; ?>>Arrived</option>
                        <option value="onsite" <?php if(request('arrival') == 'onsite'): echo 'selected'; endif; ?>>On Site</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Customer</label>
                    <select name="customer_id" class="w-full border-gray-300 rounded">
                        <option value="">All Customers</option>
                        <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($customer->id); ?>" <?php if(request('customer_id') == $customer->id): echo 'selected'; endif; ?>>
                                <?php echo e($customer->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="flex space-x-2">
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Filter
                    </button>
                    <a href="<?php echo e(route('depot.bookings.index')); ?>" 
                       class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                        Clear
                    </a>
                </div>
            </form>
        </div>
        <!-- Bookings Table -->
        <div class="bg-white shadow overflow-hidden sm:rounded-lg">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Booking Ref
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Depot & Time
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Customer
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Vehicle/Container
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Expected/Actual
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
                        <?php $__empty_1 = true; $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-mono text-sm font-semibold text-blue-600">
                                        <?php echo e($booking->booking_reference); ?>

                                    </div>
                                    <?php if($booking->reference): ?>
                                        <div class="text-xs text-gray-600">Collection: <?php echo e($booking->reference); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo e($booking->slot->depot->name); ?>

                                    </div>
                                    <?php if($booking->estimated_arrival): ?>
                                        <div class="text-xs text-blue-600 font-semibold">
                                            💬 Updated ETA: <?php echo e(\Carbon\Carbon::parse($booking->estimated_arrival)->format('d-M H:i')); ?>

                                        </div>
                                    <?php endif; ?>
                                    <div class="text-xs text-gray-500">
                                        <?php echo e($booking->slot->start_at->format('d-M H:i')); ?> → 
                                        <?php echo e($booking->slot->end_at->format('H:i')); ?>

                                    </div>
                                    <?php if($booking->gate_number || $booking->bay_number): ?>
                                        <div class="text-xs text-blue-600">
                                            <?php if($booking->gate_number): ?>Gate <?php echo e($booking->gate_number); ?> <?php endif; ?>
                                            <?php if($booking->bay_number): ?>Bay <?php echo e($booking->bay_number); ?><?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo e($booking->customer->name ?? 'N/A'); ?>

                                    </div>
                                    <?php if($booking->carrier_company): ?>
                                        <div class="text-xs text-gray-500"><?php echo e($booking->carrier_company); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if($booking->vehicle_registration): ?>
                                        <div class="text-sm">🚛 <?php echo e($booking->vehicle_registration); ?></div>
                                    <?php endif; ?>
                                    <?php if($booking->container_number): ?>
                                        <div class="text-sm">📦 <?php echo e($booking->container_number); ?></div>
                                    <?php endif; ?>
                                    <?php if($booking->driver_name): ?>
                                        <div class="text-xs text-gray-600">👤 <?php echo e($booking->driver_name); ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs">
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <strong>Expected:</strong><br>
                                            <?php echo e($booking->total_expected_cases ?? 0); ?> cases<br>
                                            <?php echo e($booking->total_expected_pallets ?? 0); ?> pallets
                                        </div>
                                        <div>
                                            <strong>Actual:</strong><br>
                                            <span class="<?php echo e($booking->total_actual_cases ? 'text-green-600 font-semibold' : 'text-gray-400'); ?>">
                                                <?php echo e($booking->total_actual_cases ?? '-'); ?> cases
                                            </span><br>
                                            <span class="<?php echo e($booking->total_actual_pallets ? 'text-green-600 font-semibold' : 'text-gray-400'); ?>">
                                                <?php echo e($booking->total_actual_pallets ?? '-'); ?> pallets
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php if($booking->arrived_at): ?>
                                        <div class="text-xs">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                ✅ Arrived <?php echo e($booking->arrived_at->format('H:i')); ?>

                                            </span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if($booking->departed_at): ?>
                                        <div class="text-xs mt-1">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                🕒 Departed <?php echo e($booking->departed_at->format('H:i')); ?>

                                            </span>
                                        </div>
                                    <?php elseif($booking->arrived_at): ?>
                                        <div class="text-xs mt-1">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                🏢 On Site
                                            </span>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-xs">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                ⏳ Pending
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex flex-col space-y-1">
                                        <?php if(!$booking->arrived_at): ?>
                                            <button onclick="openArrivalModal(<?php echo e($booking->id); ?>, '<?php echo e($booking->booking_reference); ?>', '<?php echo e(addslashes($booking->customer->name ?? 'N/A')); ?>', '<?php echo e($booking->slot->depot->name); ?>', '<?php echo e($booking->slot->start_at->format('d-M-Y H:i')); ?>', '<?php echo e($booking->vehicle_registration ?? ''); ?>', '<?php echo e($booking->container_number ?? ''); ?>', '<?php echo e($booking->carrier_company ?? ''); ?>', '<?php echo e($booking->gate_number ?? ''); ?>', '<?php echo e($booking->total_expected_cases ?? 0); ?>', '<?php echo e($booking->total_expected_pallets ?? 0); ?>', '<?php echo e(addslashes($booking->special_instructions ?? '')); ?>')" 
                                                    class="text-green-600 hover:text-green-900 text-xs bg-green-100 px-2 py-1 rounded cursor-pointer">
                                                🚛 Process Arrival
                                            </button>
                                        <?php elseif(!$booking->departed_at): ?>
                                            <form method="POST" action="<?php echo e(route('depot.bookings.departure', $booking)); ?>" class="inline">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?>
                                                <button type="submit" 
                                                        class="text-blue-600 hover:text-blue-900 text-xs bg-blue-100 px-2 py-1 rounded">
                                                    Mark Departed
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <a href="<?php echo e(route('depot.bookings.edit', $booking)); ?>" 
                                           class="text-indigo-600 hover:text-indigo-900 text-xs bg-indigo-100 px-2 py-1 rounded text-center">
                                            Edit Details
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    No bookings found for your assigned depots.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($bookings->hasPages()): ?>
                <div class="bg-white px-4 py-3 border-t border-gray-200">
                    <?php echo e($bookings->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
    <!-- Arrival Modal -->
    <div id="arrivalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <!-- Modal Header -->
                <div class="flex justify-between items-center pb-4 border-b">
                    <h3 class="text-lg font-semibold text-gray-900">🚛 Vehicle Arrival Processing</h3>
                    <button onclick="closeArrivalModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <!-- Booking Summary -->
                <div id="bookingSummary" class="mt-4 p-4 bg-indigo-50 rounded-lg">
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
                                   class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <p class="text-xs text-gray-500 mt-1">Required for arrival processing</p>
                        </div>
                        <!-- Container Number -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Container/Trailer Number</label>
                            <input type="text" id="containerNumber" name="container_number"
                                   placeholder="e.g., CONT123456"
                                   class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <!-- Gate Number -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Gate Number</label>
                            <input type="text" id="gateNumber" name="gate_number"
                                   class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <!-- Bay Number -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bay Number</label>
                            <input type="text" id="bayNumber" name="bay_number"
                                   class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <!-- Actual Cases -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Actual Cases</label>
                            <input type="number" id="actualCases" name="actual_cases" min="0"
                                   class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <p id="expectedCases" class="text-xs text-gray-500 mt-1">Expected: 0</p>
                        </div>
                        <!-- Actual Pallets -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Actual Pallets</label>
                            <input type="number" id="actualPallets" name="actual_pallets" min="0"
                                   class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                            <p id="expectedPallets" class="text-xs text-gray-500 mt-1">Expected: 0</p>
                        </div>
                    </div>
                    <!-- Special Instructions -->
                    <div id="specialInstructions" class="mt-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200 hidden">
                        <h4 class="font-medium text-yellow-800 mb-2">⚠️ Special Instructions:</h4>
                        <p id="specialInstructionsText" class="text-yellow-700"></p>
                    </div>
                    <!-- Arrival Time Display -->
                    <div class="mt-4 p-4 bg-indigo-50 rounded-lg border border-indigo-200">
                        <h4 class="font-medium text-indigo-800 mb-2">📅 Arrival Time:</h4>
                        <p class="text-indigo-700 font-semibold" id="arrivalTime">Will be recorded as: [Current Time]</p>
                    </div>
                    <!-- Form Actions -->
                    <div class="mt-6 flex justify-end space-x-4">
                        <button type="button" onclick="closeArrivalModal()" 
                                class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-semibold">
                            🚛 Mark Vehicle Arrived
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        let currentBookingId = null;
        function openArrivalModal(bookingId, bookingRef, customer, depot, scheduledTime, vehicleReg, containerNum, carrierCompany, gateNum, expectedCases, expectedPallets, specialInstructions) {
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
            document.getElementById('arrivalForm').action = `/depot-admin/bookings/${bookingId}/arrival`;
            // Populate form fields
            document.getElementById('vehicleRegistration').value = vehicleReg;
            document.getElementById('containerNumber').value = containerNum;
            document.getElementById('carrierCompany').value = carrierCompany;
            document.getElementById('gateNumber').value = gateNum;
            // Update expected quantities display
            document.getElementById('expectedCases').textContent = `Expected: ${expectedCases}`;
            document.getElementById('expectedPallets').textContent = `Expected: ${expectedPallets}`;
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
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/depot-admin/bookings/index.blade.php ENDPATH**/ ?>