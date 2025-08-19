<?php $__env->startSection('title', 'Create Arrival Time Setting'); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    ➕ Create Arrival Time Setting
                </h1>
                <p class="mt-2 text-gray-600">Define early/late arrival tolerances for a specific level</p>
            </div>
            <div>
                <a href="<?php echo e(route('admin.arrival-time-settings.index')); ?>" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    ← Back to Settings
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">⚙️ New Arrival Time Setting</h3>
            <p class="text-sm text-gray-600 mt-1">Configure when arrivals are considered early, on-time, or late</p>
        </div>
        
        <form method="POST" action="<?php echo e(route('admin.arrival-time-settings.store')); ?>" class="p-6">
            <?php echo csrf_field(); ?>
            
            <!-- Level Selection -->
            <div class="mb-6">
                <label for="level" class="block text-sm font-medium text-gray-700 mb-2">Setting Level *</label>
                <select name="level" id="level" required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['level'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <option value="">Select setting level...</option>
                    <?php $__currentLoopData = $levels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($key); ?>" <?php echo e(old('level', $level) == $key ? 'selected' : ''); ?>>
                            <?php echo e($key == 'global' ? '🌐' : ($key == 'depot' ? '🏢' : '👤')); ?> <?php echo e($label); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['level'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <p class="mt-1 text-xs text-gray-500">Choose the scope for this setting</p>
            </div>

            <!-- Depot Selection (shown for depot level) -->
            <div class="mb-6" id="depot-section" style="display: none;">
                <label for="depot_id" class="block text-sm font-medium text-gray-700 mb-2">Depot *</label>
                <select name="depot_id" id="depot_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['depot_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <option value="">Select depot...</option>
                    <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($depot->id); ?>" <?php echo e(old('depot_id', $depotId) == $depot->id ? 'selected' : ''); ?>>
                            <?php echo e($depot->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['depot_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Customer Selection (shown for customer level) -->
            <div class="mb-6" id="customer-section" style="display: none;">
                <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-2">Customer *</label>
                <select name="customer_id" id="customer_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['customer_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                    <option value="">Select customer...</option>
                    <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($customer->id); ?>" <?php echo e(old('customer_id', $customerId) == $customer->id ? 'selected' : ''); ?>>
                            <?php echo e($customer->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['customer_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Threshold Settings -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="early_threshold_minutes" class="block text-sm font-medium text-gray-700 mb-2">Early Threshold (minutes) *</label>
                    <div class="relative">
                        <input type="number" name="early_threshold_minutes" id="early_threshold_minutes" 
                               min="0" max="1440" step="1" required
                               value="<?php echo e(old('early_threshold_minutes', 0)); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['early_threshold_minutes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">min</span>
                        </div>
                    </div>
                    <?php $__errorArgs = ['early_threshold_minutes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <p class="mt-1 text-xs text-gray-500">⏪ Arrivals more than this many minutes early will be flagged as "early"</p>
                </div>

                <div>
                    <label for="late_threshold_minutes" class="block text-sm font-medium text-gray-700 mb-2">Late Threshold (minutes) *</label>
                    <div class="relative">
                        <input type="number" name="late_threshold_minutes" id="late_threshold_minutes" 
                               min="0" max="1440" step="1" required
                               value="<?php echo e(old('late_threshold_minutes', 0)); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['late_threshold_minutes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">min</span>
                        </div>
                    </div>
                    <?php $__errorArgs = ['late_threshold_minutes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <p class="mt-1 text-xs text-gray-500">⏰ Arrivals more than this many minutes late will be flagged as "late"</p>
                </div>
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" id="description" rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> border-red-500 <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                          placeholder="Optional description of this setting and when it applies..."><?php echo e(old('description')); ?></textarea>
                <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="mt-1 text-sm text-red-600"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <!-- Preview -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <h4 class="text-sm font-medium text-blue-800 mb-2">📊 Preview Example</h4>
                <div id="preview-content" class="text-sm text-blue-700">
                    <p>For a 10:00 AM booking:</p>
                    <ul class="list-disc list-inside mt-1 space-y-1">
                        <li><span class="font-medium">Early:</span> Before <span id="early-time">9:45 AM</span></li>
                        <li><span class="font-medium">On-time:</span> <span id="ontime-window">9:45 AM - 10:15 AM</span></li>
                        <li><span class="font-medium">Late:</span> After <span id="late-time">10:15 AM</span></li>
                    </ul>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-4">
                <a href="<?php echo e(route('admin.arrival-time-settings.index')); ?>" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium">
                    ➕ Create Setting
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const levelSelect = document.getElementById('level');
    const depotSection = document.getElementById('depot-section');
    const customerSection = document.getElementById('customer-section');
    const earlyInput = document.getElementById('early_threshold_minutes');
    const lateInput = document.getElementById('late_threshold_minutes');

    function updateVisibility() {
        const level = levelSelect.value;
        
        // Show/hide sections based on level
        depotSection.style.display = level === 'depot' ? 'block' : 'none';
        customerSection.style.display = level === 'customer' ? 'block' : 'none';
        
        // Clear values when hiding
        if (level !== 'depot') {
            document.getElementById('depot_id').value = '';
        }
        if (level !== 'customer') {
            document.getElementById('customer_id').value = '';
        }
    }

    function updatePreview() {
        const earlyMin = parseInt(earlyInput.value) || 0;
        const lateMin = parseInt(lateInput.value) || 0;
        
        // Calculate example times for 10:00 AM booking
        const baseTime = new Date();
        baseTime.setHours(10, 0, 0, 0);
        
        const earlyTime = new Date(baseTime.getTime() - (earlyMin * 60000));
        const lateTime = new Date(baseTime.getTime() + (lateMin * 60000));
        
        // Format times
        const formatTime = (date) => date.toLocaleTimeString('en-US', { 
            hour: 'numeric', 
            minute: '2-digit',
            hour12: true 
        });
        
        document.getElementById('early-time').textContent = formatTime(earlyTime);
        document.getElementById('late-time').textContent = formatTime(lateTime);
        document.getElementById('ontime-window').textContent = 
            `${formatTime(earlyTime)} - ${formatTime(lateTime)}`;
    }

    // Event listeners
    levelSelect.addEventListener('change', updateVisibility);
    earlyInput.addEventListener('input', updatePreview);
    lateInput.addEventListener('input', updatePreview);

    // Initial setup
    updateVisibility();
    updatePreview();
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/arrival-time-settings/create.blade.php ENDPATH**/ ?>