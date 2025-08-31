<?php if (isset($component)) { $__componentOriginalc9242005886028143da563f7b99f0c87 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc9242005886028143da563f7b99f0c87 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.warehouse-layout','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('warehouse-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <div class="py-6 max-w-4xl mx-auto space-y-6">
        <?php if(session('success')): ?>
            <div class="bg-green-100 text-green-800 p-4 rounded"><?php echo e(session('success')); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="<?php echo e(route('app.slot-templates.store')); ?>"
              class="bg-white p-6 rounded shadow grid grid-cols-2 gap-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block font-medium">Depot</label>
                <select name="depot_id" class="border p-2 w-full">
                    <option value="">— select depot —</option>
                    <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($d->id); ?>" <?php if(old('depot_id') == $d->id): echo 'selected'; endif; ?>>
                            <?php echo e($d->name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['depot_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block font-medium">Day of Week</label>
                <select name="day_of_week" class="border p-2 w-full">
                    <option value="">— choose day —</option>
                    <?php $__currentLoopData = [
                        1 => 'Monday',
                        2 => 'Tuesday',
                        3 => 'Wednesday',
                        4 => 'Thursday',
                        5 => 'Friday',
                        6 => 'Saturday',
                        0 => 'Sunday'
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($num); ?>" <?php if(old('day_of_week') == $num): echo 'selected'; endif; ?>>
                            <?php echo e($label); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['day_of_week'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <?php
                $times = [];
                for ($hour = 0; $hour < 24; $hour++) {
                    foreach ([0, 30] as $minute) {
                        $times[] = sprintf('%02d:%02d', $hour, $minute);
                    }
                }
            ?>
            <div>
                <label class="block font-medium">Start Time</label>
                <select name="start_time" class="border p-2 w-full">
                    <option value="">— select time —</option>
                    <?php $__currentLoopData = $times; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $time): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($time); ?>" <?php if(old('start_time') === $time): echo 'selected'; endif; ?>>
                            <?php echo e($time); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['start_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block font-medium">End Time</label>
                <select name="end_time" class="border p-2 w-full">
                    <option value="">— select time —</option>
                    <?php $__currentLoopData = $times; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $time): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($time); ?>" <?php if(old('end_time') === $time): echo 'selected'; endif; ?>>
                            <?php echo e($time); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <?php $__errorArgs = ['end_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="col-span-2 text-right">
                <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    ➕ Add Template
                </button>
            </div>
        </form>
        
   <div class="bg-white shadow rounded p-6">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-xl font-bold">Existing Templates</h2>
        <button id="bulkCopyBtn" onclick="openBulkCopyModal()" disabled 
                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 disabled:bg-gray-400 disabled:cursor-not-allowed">
            📋 Copy Selected Templates
        </button>
    </div>
    <?php if($templates->isEmpty()): ?>
        <p class="text-gray-600">No templates yet.</p>
    <?php else: ?>
        <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depotName => $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <h3 class="text-lg font-semibold mt-6 mb-2 border-b pb-1"><?php echo e($depotName); ?></h3>
            <table class="min-w-full text-sm mb-4">
                <thead>
                    <tr class="text-left">
                        <th class="px-3 py-1">
                            <input type="checkbox" id="selectAll-<?php echo e($loop->index); ?>" onchange="toggleGroupSelection(this, <?php echo e($loop->index); ?>)" class="mr-2">
                            Select
                        </th>
                        <th class="px-3 py-1">Day</th>
                        <th class="px-3 py-1">Start</th>
                        <th class="px-3 py-1">End</th>
                        <th class="px-3 py-1">Duration</th>
                        <th class="px-3 py-1">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $group; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tpl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr class="border-t">
                            <td class="px-3 py-1">
                                <?php
                                    $dayNames = ['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
                                    $templateData = [
                                        'id' => $tpl->id,
                                        'depot' => $tpl->depot->name,
                                        'day' => $dayNames[$tpl->day_of_week],
                                        'start' => \Carbon\Carbon::parse($tpl->start_time)->format('H:i'),
                                        'end' => \Carbon\Carbon::parse($tpl->end_time)->format('H:i')
                                    ];
                                ?>
                                <input type="checkbox" class="template-checkbox group-<?php echo e($loop->parent->index); ?>" 
                                       value="<?php echo e($tpl->id); ?>" onchange="updateBulkCopyButton()" 
                                       data-template='<?php echo json_encode($templateData, 15, 512) ?>'>
                            </td>
                            <td class="px-3 py-1">
                                <?php echo e(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'][$tpl->day_of_week]); ?>

                            </td>
                            <td class="px-3 py-1"><?php echo e(\Carbon\Carbon::parse($tpl->start_time)->format('H:i')); ?></td>
                            <td class="px-3 py-1"><?php echo e(\Carbon\Carbon::parse($tpl->end_time)->format('H:i')); ?></td>
                            <td class="px-3 py-1"><?php echo e(abs($tpl->duration_minutes)); ?> min</td>
                            <td class="px-3 py-1 text-sm">
                                <a href="<?php echo e(route('app.slot-templates.edit', $tpl)); ?>" class="text-blue-600 hover:underline">Edit</a>
                                <button onclick="openDuplicateModal(<?php echo e($tpl->id); ?>, '<?php echo e($tpl->depot->name); ?>', '<?php echo e(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'][$tpl->day_of_week]); ?>', '<?php echo e(\Carbon\Carbon::parse($tpl->start_time)->format('H:i')); ?>', '<?php echo e(\Carbon\Carbon::parse($tpl->end_time)->format('H:i')); ?>')" 
                                        class="text-green-600 hover:underline ml-2">Copy</button>
                                <form action="<?php echo e(route('app.slot-templates.destroy', $tpl)); ?>" method="POST" class="inline ml-2"
                                      onsubmit="return confirm('Delete this template?')">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button class="text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
</div>
    </div>
    
    <div id="duplicateModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-bold mb-4">Copy Template to Other Depots</h3>
            <div id="templateInfo" class="mb-4 p-3 bg-gray-100 rounded text-sm"></div>
            <form id="duplicateForm" method="POST">
                <?php echo csrf_field(); ?>
                <div class="mb-4">
                    <label class="block font-medium mb-2">Select Target Depots:</label>
                    <div class="space-y-2 max-h-48 overflow-y-auto">
                        <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="flex items-center">
                                <input type="checkbox" name="depot_ids[]" value="<?php echo e($depot->id); ?>" class="mr-2">
                                <?php echo e($depot->name); ?>

                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeDuplicateModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Copy Template</button>
                </div>
            </form>
        </div>
    </div>
    
    <div id="bulkCopyModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-2xl w-full mx-4">
            <h3 class="text-lg font-bold mb-4">Copy Selected Templates</h3>
            <div id="selectedTemplatesInfo" class="mb-4 p-3 bg-gray-100 rounded text-sm max-h-32 overflow-y-auto"></div>
            <form id="bulkCopyForm" method="POST" action="<?php echo e(route('app.slot-templates.bulk-duplicate')); ?>">
                <?php echo csrf_field(); ?>
                <div id="bulkTemplateIds"></div>
                <!-- Copy Type Selection -->
                <div class="mb-4">
                    <label class="block font-medium mb-2">What do you want to copy?</label>
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="radio" name="copy_type" value="depots" checked class="mr-2" onchange="toggleCopyOptions()">
                            Copy templates to other depots
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="copy_type" value="days" class="mr-2" onchange="toggleCopyOptions()">
                            Copy templates to other days of the week
                        </label>
                        <label class="flex items-center">
                            <input type="radio" name="copy_type" value="both" class="mr-2" onchange="toggleCopyOptions()">
                            Copy to both other depots AND other days
                        </label>
                    </div>
                </div>
                <!-- Depot Selection -->
                <div id="depotSelection" class="mb-4">
                    <label class="block font-medium mb-2">Select Target Depots:</label>
                    <div class="grid grid-cols-2 gap-2 max-h-32 overflow-y-auto">
                        <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="flex items-center">
                                <input type="checkbox" name="depot_ids[]" value="<?php echo e($depot->id); ?>" class="mr-2">
                                <?php echo e($depot->name); ?>

                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <!-- Day Selection -->
                <div id="daySelection" class="mb-4 hidden">
                    <label class="block font-medium mb-2">Select Target Days:</label>
                    <div class="grid grid-cols-2 gap-2">
                        <?php
                            $days = [
                                0 => 'Sunday',
                                1 => 'Monday', 
                                2 => 'Tuesday',
                                3 => 'Wednesday',
                                4 => 'Thursday',
                                5 => 'Friday',
                                6 => 'Saturday'
                            ];
                        ?>
                        <?php $__currentLoopData = $days; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <label class="flex items-center">
                                <input type="checkbox" name="day_of_week[]" value="<?php echo e($value); ?>" class="mr-2">
                                <?php echo e($label); ?>

                            </label>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeBulkCopyModal()" class="px-4 py-2 text-gray-600 hover:text-gray-800">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Copy Templates</button>
                </div>
            </form>
        </div>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc9242005886028143da563f7b99f0c87)): ?>
<?php $attributes = $__attributesOriginalc9242005886028143da563f7b99f0c87; ?>
<?php unset($__attributesOriginalc9242005886028143da563f7b99f0c87); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc9242005886028143da563f7b99f0c87)): ?>
<?php $component = $__componentOriginalc9242005886028143da563f7b99f0c87; ?>
<?php unset($__componentOriginalc9242005886028143da563f7b99f0c87); ?>
<?php endif; ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form[action*="slot-templates"]');
    if (!form) return;
    form.addEventListener('submit', function (e) {
        const startTime = form.querySelector('select[name="start_time"]').value;
        const endTime = form.querySelector('select[name="end_time"]').value;
        if (!startTime || !endTime) return; // Laravel handles empty
        const [startH, startM] = startTime.split(':').map(Number);
        const [endH, endM] = endTime.split(':').map(Number);
        const start = new Date();
        const end = new Date();
        start.setHours(startH, startM, 0);
        end.setHours(endH, endM, 0);
        let duration = (end - start) / 60000; // in minutes
        if (duration <= 0) {
            duration += 1440; // handle overnight (e.g., 23:00–01:00)
        }
        if (duration > 720) {
            e.preventDefault();
            alert("⛔ Duration must not exceed 12 hours.");
            return;
        }
        if (duration % 15 !== 0) {
            e.preventDefault();
            alert("⚠️ Duration must be in 15-minute intervals.");
            return;
        }
    });
});
function openDuplicateModal(templateId, depotName, dayOfWeek, startTime, endTime) {
    const modal = document.getElementById('duplicateModal');
    const templateInfo = document.getElementById('templateInfo');
    const form = document.getElementById('duplicateForm');
    templateInfo.innerHTML = `
        <strong>Copying Template:</strong><br>
        Depot: ${depotName}<br>
        Day: ${dayOfWeek}<br>
        Time: ${startTime} - ${endTime}
    `;
    form.action = `/admin/slot-templates/${templateId}/duplicate`;
    // Uncheck all checkboxes
    const checkboxes = form.querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(cb => cb.checked = false);
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closeDuplicateModal() {
    const modal = document.getElementById('duplicateModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
// Close modal when clicking outside
document.getElementById('duplicateModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDuplicateModal();
    }
});
// Bulk Copy Functions
function updateBulkCopyButton() {
    const selectedCheckboxes = document.querySelectorAll('.template-checkbox:checked');
    const bulkBtn = document.getElementById('bulkCopyBtn');
    if (selectedCheckboxes.length > 0) {
        bulkBtn.disabled = false;
        bulkBtn.textContent = `📋 Copy ${selectedCheckboxes.length} Selected Template(s)`;
    } else {
        bulkBtn.disabled = true;
        bulkBtn.textContent = '📋 Copy Selected Templates';
    }
}
function toggleGroupSelection(selectAllCheckbox, groupIndex) {
    const groupCheckboxes = document.querySelectorAll(`.group-${groupIndex}`);
    groupCheckboxes.forEach(cb => cb.checked = selectAllCheckbox.checked);
    updateBulkCopyButton();
}
function openBulkCopyModal() {
    const selectedCheckboxes = document.querySelectorAll('.template-checkbox:checked');
    if (selectedCheckboxes.length === 0) return;
    const modal = document.getElementById('bulkCopyModal');
    const templateInfo = document.getElementById('selectedTemplatesInfo');
    const templateIds = document.getElementById('bulkTemplateIds');
    let infoHtml = '<strong>Selected Templates:</strong><br>';
    let ids = [];
    selectedCheckboxes.forEach(cb => {
        const template = JSON.parse(cb.dataset.template);
        ids.push(template.id);
        infoHtml += `• ${template.depot} - ${template.day} ${template.start}-${template.end}<br>`;
    });
    templateInfo.innerHTML = infoHtml;
    // Clear previous template IDs and add new ones
    templateIds.innerHTML = '';
    ids.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'template_ids[]';
        input.value = id;
        templateIds.appendChild(input);
    });
    // Clear all selections
    const depotCheckboxes = modal.querySelectorAll('input[name="depot_ids[]"]');
    depotCheckboxes.forEach(cb => cb.checked = false);
    const dayCheckboxes = modal.querySelectorAll('input[name="day_of_week[]"]');
    dayCheckboxes.forEach(cb => cb.checked = false);
    // Reset to depot copy mode
    document.querySelector('input[name="copy_type"][value="depots"]').checked = true;
    toggleCopyOptions();
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}
function closeBulkCopyModal() {
    const modal = document.getElementById('bulkCopyModal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}
// Close bulk modal when clicking outside
document.getElementById('bulkCopyModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeBulkCopyModal();
    }
});
// Toggle copy options based on radio selection
function toggleCopyOptions() {
    const copyType = document.querySelector('input[name="copy_type"]:checked').value;
    const depotSelection = document.getElementById('depotSelection');
    const daySelection = document.getElementById('daySelection');
    if (copyType === 'depots') {
        depotSelection.classList.remove('hidden');
        daySelection.classList.add('hidden');
    } else if (copyType === 'days') {
        depotSelection.classList.add('hidden');
        daySelection.classList.remove('hidden');
    } else if (copyType === 'both') {
        depotSelection.classList.remove('hidden');
        daySelection.classList.remove('hidden');
    }
}
</script>
<?php /**PATH /Users/londo/Herd/test/resources/views/warehouse/slot-templates/index.blade.php ENDPATH**/ ?>