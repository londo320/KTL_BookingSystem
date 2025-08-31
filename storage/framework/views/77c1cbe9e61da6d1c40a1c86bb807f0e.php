<?php $__env->startSection('title', 'Register Driver Arrival'); ?>

<?php $__env->startSection('content'); ?>
<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-4">
                <a href="<?php echo e(route('outbound.arrivals.dashboard')); ?>" 
                   class="text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Register Driver Arrival</h1>
                    <p class="text-gray-600 mt-1">Enter driver and load details from paperwork</p>
                </div>
            </div>
        </div>

        <form action="<?php echo e(route('outbound.arrivals.store')); ?>" method="POST" class="space-y-8">
            <?php echo csrf_field(); ?>

            <!-- Load Information -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Load Information</h2>
                    <p class="text-sm text-gray-600">From driver's paperwork or consignment note</p>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="load_reference" class="block text-sm font-medium text-gray-700 mb-2">
                                Load Reference <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="load_reference" id="load_reference" 
                                   value="<?php echo e(old('load_reference')); ?>"
                                   class="form-input w-full rounded-md <?php $__errorArgs = ['load_reference'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   placeholder="LD250823001" required>
                            <?php $__errorArgs = ['load_reference'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <p class="text-sm text-gray-500 mt-1">
                                Load number from driver's paperwork (must match WMS files)
                            </p>
                        </div>

                        <div>
                            <label for="driver_paperwork_ref" class="block text-sm font-medium text-gray-700 mb-2">
                                Paperwork Reference
                            </label>
                            <input type="text" name="driver_paperwork_ref" id="driver_paperwork_ref" 
                                   value="<?php echo e(old('driver_paperwork_ref')); ?>"
                                   class="form-input w-full rounded-md <?php $__errorArgs = ['driver_paperwork_ref'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   placeholder="Consignment note number">
                            <?php $__errorArgs = ['driver_paperwork_ref'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div>
                        <label for="expected_orders" class="block text-sm font-medium text-gray-700 mb-2">
                            Expected Number of Orders
                        </label>
                        <input type="number" name="expected_orders" id="expected_orders" 
                               value="<?php echo e(old('expected_orders')); ?>"
                               class="form-input w-full rounded-md <?php $__errorArgs = ['expected_orders'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               placeholder="5" min="0" max="1000">
                        <?php $__errorArgs = ['expected_orders'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <p class="text-sm text-gray-500 mt-1">
                            Helps track matching progress (leave blank if unknown)
                        </p>
                    </div>
                </div>
            </div>

            <!-- Vehicle Information -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Vehicle Information</h2>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="vehicle_registration" class="block text-sm font-medium text-gray-700 mb-2">
                                Vehicle Registration <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="vehicle_registration" id="vehicle_registration" 
                                   value="<?php echo e(old('vehicle_registration')); ?>"
                                   class="form-input w-full rounded-md <?php $__errorArgs = ['vehicle_registration'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   placeholder="AB12 CDE" required>
                            <?php $__errorArgs = ['vehicle_registration'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label for="trailer_registration" class="block text-sm font-medium text-gray-700 mb-2">
                                Trailer Registration
                            </label>
                            <input type="text" name="trailer_registration" id="trailer_registration" 
                                   value="<?php echo e(old('trailer_registration')); ?>"
                                   class="form-input w-full rounded-md <?php $__errorArgs = ['trailer_registration'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   placeholder="TR12 345">
                            <?php $__errorArgs = ['trailer_registration'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div>
                        <label for="carrier_company" class="block text-sm font-medium text-gray-700 mb-2">
                            Carrier Company <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="carrier_company" id="carrier_company" 
                               value="<?php echo e(old('carrier_company')); ?>"
                               class="form-input w-full rounded-md <?php $__errorArgs = ['carrier_company'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               placeholder="ABC Transport Ltd" required>
                        <?php $__errorArgs = ['carrier_company'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>

            <!-- Driver Information -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Driver Information</h2>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="driver_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Driver Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="driver_name" id="driver_name" 
                                   value="<?php echo e(old('driver_name')); ?>"
                                   class="form-input w-full rounded-md <?php $__errorArgs = ['driver_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   placeholder="John Smith" required>
                            <?php $__errorArgs = ['driver_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label for="driver_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Driver Phone
                            </label>
                            <input type="tel" name="driver_phone" id="driver_phone" 
                                   value="<?php echo e(old('driver_phone')); ?>"
                                   class="form-input w-full rounded-md <?php $__errorArgs = ['driver_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   placeholder="07123 456789">
                            <?php $__errorArgs = ['driver_phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label for="driver_license" class="block text-sm font-medium text-gray-700 mb-2">
                                Driver License
                            </label>
                            <input type="text" name="driver_license" id="driver_license" 
                                   value="<?php echo e(old('driver_license')); ?>"
                                   class="form-input w-full rounded-md <?php $__errorArgs = ['driver_license'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   placeholder="SMIT123456AB7CD">
                            <?php $__errorArgs = ['driver_license'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Arrival Details -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Arrival Details</h2>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="arrival_depot_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Arrival Depot <span class="text-red-500">*</span>
                            </label>
                            <select name="arrival_depot_id" id="arrival_depot_id" 
                                    class="form-select w-full rounded-md <?php $__errorArgs = ['arrival_depot_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="">Select Depot</option>
                                <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($depot->id); ?>" <?php echo e(old('arrival_depot_id') == $depot->id ? 'selected' : ''); ?>>
                                        <?php echo e($depot->name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['arrival_depot_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Arrival Time
                            </label>
                            <div class="text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-md px-3 py-2">
                                <?php echo e(now()->format('H:i - M j, Y')); ?> (Now)
                            </div>
                            <p class="text-sm text-gray-500 mt-1">
                                Arrival time will be recorded as current time
                            </p>
                        </div>
                    </div>

                    <div>
                        <label for="arrival_notes" class="block text-sm font-medium text-gray-700 mb-2">
                            Arrival Notes
                        </label>
                        <textarea name="arrival_notes" id="arrival_notes" rows="3"
                                  class="form-textarea w-full rounded-md <?php $__errorArgs = ['arrival_notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                  placeholder="Any special notes about the arrival or load"><?php echo e(old('arrival_notes')); ?></textarea>
                        <?php $__errorArgs = ['arrival_notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>

            <!-- Information Panel -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <div class="flex">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-blue-800">What happens next?</h3>
                        <div class="mt-2 text-sm text-blue-700">
                            <ol class="list-decimal list-inside space-y-1">
                                <li>Driver arrival will be registered in the system</li>
                                <li>System will attempt to find matching WMS orders by load reference</li>
                                <li>If orders are found, they will be automatically linked to this arrival</li>
                                <li>Driver can proceed to collection once orders are matched and verified</li>
                                <li>Upload WMS files through <strong>📁 WMS File Imports</strong> if orders not yet in system</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-4">
                <a href="<?php echo e(route('outbound.arrivals.dashboard')); ?>" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-md font-medium">
                    Cancel
                </a>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium">
                    Register Arrival
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Auto-uppercase registration fields
document.addEventListener('DOMContentLoaded', function() {
    const regFields = ['vehicle_registration', 'trailer_registration'];
    regFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('input', function() {
                this.value = this.value.toUpperCase();
            });
        }
    });

    // Format load reference
    const loadRefField = document.getElementById('load_reference');
    if (loadRefField) {
        loadRefField.addEventListener('input', function() {
            this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
        });
    }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/outbound/admin/physical-loads/create.blade.php ENDPATH**/ ?>