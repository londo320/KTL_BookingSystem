<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-6 max-w-6xl">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">🚪 Generate Bay Slots</h1>
        <p class="text-gray-600 mt-1">Configure operational hours per bay and generate hourly slots</p>
    </div>

    <?php if(session('success')): ?>
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            <?php echo e(session('success')); ?>

            <?php if(session('command_output')): ?>
                <details class="mt-2">
                    <summary class="cursor-pointer font-semibold">View Output</summary>
                    <pre class="mt-2 p-2 bg-green-50 rounded text-xs overflow-auto"><?php echo e(session('command_output')); ?></pre>
                </details>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo e(route('app.bay-slot-generation.generate')); ?>" method="POST" id="slot-generation-form" class="space-y-6">
        <?php echo csrf_field(); ?>

        <div class="bg-white shadow rounded-lg p-6 space-y-4">
            <h3 class="text-lg font-semibold text-gray-800">🏭 Depot and Date Range</h3>

            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Depot *</label>
                <select name="depot_id" id="depot-select" required class="block w-full border-gray-300 rounded text-sm py-2">
                    <option value="">Select Depot</option>
                    <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($depot->id); ?>" data-bay-count="<?php echo e($depot->tippingBays->count()); ?>">
                            <?php echo e($depot->name); ?> (<?php echo e($depot->tippingBays->count()); ?> bays)
                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['depot_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Days Ahead *</label>
                <input type="number" name="days" value="<?php echo e(old('days', 14)); ?>" min="1" max="90" required
                       class="block w-full border-gray-300 rounded text-sm py-2">
                <p class="text-xs text-gray-500 mt-1">How many days into the future to generate slots (1-90)</p>
                <?php $__errorArgs = ['days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <p class="text-red-600 text-xs mt-1"><?php echo e($message); ?></p>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        
        <div id="bay-config-section" class="hidden">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-blue-900">⏰ Bay Operational Hours</h3>
                    <button type="button" id="set-all-24hr" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                        Set All to 24/7
                    </button>
                </div>

                <p class="text-sm text-blue-800 mb-4">
                    Configure when each bay is operational. Slots will only be generated during operational hours.
                </p>

                <div id="bays-container" class="space-y-4">
                    
                </div>
            </div>
        </div>

        
        <div id="submit-section" class="hidden flex justify-end gap-3">
            <a href="<?php echo e(route('app.settings.dashboard')); ?>" class="px-6 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                🚀 Generate Slots
            </button>
        </div>
    </form>

    
    <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-3">💡 How It Works</h3>
        <div class="space-y-2 text-sm text-gray-700">
            <p><strong>1. Select Depot:</strong> Choose which depot to generate slots for</p>
            <p><strong>2. Set Days Ahead:</strong> How far into the future to generate (default: 14 days)</p>
            <p><strong>3. Configure Bay Hours:</strong></p>
            <ul class="list-disc list-inside ml-4 space-y-1">
                <li><strong>24/7 Mode:</strong> Generates 24 hourly slots per day (00:00, 01:00... 23:00)</li>
                <li><strong>Custom Hours:</strong> Only generates slots during specified operational hours</li>
                <li><strong>Day Restrictions:</strong> Optionally restrict to specific days of week</li>
            </ul>
            <p><strong>4. Generate:</strong> Slots are created for each bay based on their operational hours</p>
        </div>

        <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded">
            <p class="text-sm text-yellow-800">
                <strong>⚠️ Note:</strong> This will NOT overwrite existing slots. Only new slots for future dates will be created.
            </p>
        </div>

        <div class="mt-4 space-y-2 text-sm text-gray-600">
            <p><strong>Example 1 (24/7 Bay):</strong> Bay A1 set to 24/7 → Generates 24 slots per day (00:00-23:00)</p>
            <p><strong>Example 2 (Day Shift Only):</strong> Bay A2 operates 08:00-17:00 → Generates 9 slots per day (08:00, 09:00... 16:00)</p>
            <p><strong>Example 3 (Weekdays Only):</strong> Bay A3 operates Mon-Fri 06:00-22:00 → Generates 16 slots per weekday, 0 on weekends</p>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const depotSelect = document.getElementById('depot-select');
    const bayConfigSection = document.getElementById('bay-config-section');
    const baysContainer = document.getElementById('bays-container');
    const submitSection = document.getElementById('submit-section');
    const setAll24hrBtn = document.getElementById('set-all-24hr');

    // When depot is selected, load bays
    depotSelect.addEventListener('change', function() {
        const depotId = this.value;

        if (!depotId) {
            bayConfigSection.classList.add('hidden');
            submitSection.classList.add('hidden');
            return;
        }

        // Fetch bays for this depot
        fetch(`<?php echo e(route('app.bay-slot-generation.get-bays')); ?>?depot_id=${depotId}`)
            .then(response => response.json())
            .then(bays => {
                if (bays.length === 0) {
                    baysContainer.innerHTML = '<p class="text-red-600">No active bays found for this depot.</p>';
                    bayConfigSection.classList.remove('hidden');
                    submitSection.classList.add('hidden');
                    return;
                }

                // Render bay configuration cards
                baysContainer.innerHTML = bays.map((bay, index) => `
                    <div class="bg-white border border-gray-300 rounded-lg p-4">
                        <input type="hidden" name="bay_configs[${index}][bay_id]" value="${bay.id}">

                        <div class="flex items-center justify-between mb-3">
                            <h4 class="font-semibold text-gray-900">
                                🚪 ${bay.name} ${bay.code ? `(${bay.code})` : ''}
                            </h4>
                            <label class="flex items-center">
                                <input type="checkbox" name="bay_configs[${index}][is_24_hour]" value="1"
                                       class="bay-24hr-toggle mr-2" data-index="${index}"
                                       ${bay.is_24_hour ? 'checked' : ''}>
                                <span class="text-sm font-medium text-gray-700">24/7 Operation</span>
                            </label>
                        </div>

                        <div class="bay-custom-hours space-y-3" data-index="${index}" ${bay.is_24_hour ? 'style="display:none"' : ''}>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">Start Time</label>
                                    <input type="time" name="bay_configs[${index}][operational_start]"
                                           value="${bay.operational_start ? bay.operational_start.substring(0, 5) : '08:00'}"
                                           class="block w-full border-gray-300 rounded text-sm py-1">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-700 mb-1">End Time</label>
                                    <input type="time" name="bay_configs[${index}][operational_end]"
                                           value="${bay.operational_end ? bay.operational_end.substring(0, 5) : '17:00'}"
                                           class="block w-full border-gray-300 rounded text-sm py-1">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-medium text-gray-700 mb-2">Operational Days (leave unchecked for all days)</label>
                                <div class="grid grid-cols-7 gap-1">
                                    ${['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'].map(day => `
                                        <label class="flex items-center text-xs">
                                            <input type="checkbox" name="bay_configs[${index}][operational_days][]" value="${day}"
                                                   class="mr-1" ${bay.operational_days && bay.operational_days.includes(day) ? 'checked' : ''}>
                                            ${day.substring(0, 3)}
                                        </label>
                                    `).join('')}
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('');

                // Add event listeners for 24hr toggles
                document.querySelectorAll('.bay-24hr-toggle').forEach(toggle => {
                    toggle.addEventListener('change', function() {
                        const index = this.dataset.index;
                        const customHours = document.querySelector(`.bay-custom-hours[data-index="${index}"]`);
                        if (this.checked) {
                            customHours.style.display = 'none';
                        } else {
                            customHours.style.display = 'block';
                        }
                    });
                });

                bayConfigSection.classList.remove('hidden');
                submitSection.classList.remove('hidden');
            })
            .catch(error => {
                console.error('Error loading bays:', error);
                baysContainer.innerHTML = '<p class="text-red-600">Error loading bays. Please try again.</p>';
            });
    });

    // Set all to 24hr button
    setAll24hrBtn.addEventListener('click', function() {
        document.querySelectorAll('.bay-24hr-toggle').forEach(toggle => {
            toggle.checked = true;
            toggle.dispatchEvent(new Event('change'));
        });
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/bay-slot-generation/index.blade.php ENDPATH**/ ?>