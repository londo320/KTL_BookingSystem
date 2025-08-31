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
    <div class="py-6 max-w-xl mx-auto">
        <h2 class="text-xl font-bold mb-4">Edit Slot Template</h2>
        <?php
            // Time intervals: 00:00 to 23:45 in 15-minute steps
            $times = [];
            for ($hour = 0; $hour < 24; $hour++) {
                foreach ([0, 30] as $minute) {
                    $times[] = sprintf('%02d:%02d', $hour, $minute);
                }
            }
            $selectedStart = old('start_time', \Carbon\Carbon::parse($slotTemplate->start_time)->format('H:i'));
            $selectedEnd = old('end_time', \Carbon\Carbon::parse($slotTemplate->end_time)->format('H:i'));
        ?>
        <form method="POST" action="<?php echo e(route('app.slot-templates.update', $slotTemplate)); ?>" class="bg-white p-6 rounded shadow space-y-4">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div>
                <label class="block font-medium">Depot</label>
                <select name="depot_id" class="border p-2 w-full">
                    <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($d->id); ?>" <?php if(old('depot_id', $slotTemplate->depot_id) == $d->id): echo 'selected'; endif; ?>>
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
                    <?php $__currentLoopData = [
                        1 => 'Monday',
                        2 => 'Tuesday',
                        3 => 'Wednesday',
                        4 => 'Thursday',
                        5 => 'Friday',
                        6 => 'Saturday',
                        0 => 'Sunday'
                    ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($num); ?>" <?php if(old('day_of_week', $slotTemplate->day_of_week) == $num): echo 'selected'; endif; ?>>
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
            <div>
                <label class="block font-medium">Start Time</label>
                <select name="start_time" class="border p-2 w-full">
                    <?php $__currentLoopData = $times; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $time): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($time); ?>" <?php if($selectedStart === $time): echo 'selected'; endif; ?>>
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
                    <?php $__currentLoopData = $times; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $time): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($time); ?>" <?php if($selectedEnd === $time): echo 'selected'; endif; ?>>
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
            <div class="text-right">
                <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Update</button>
                <a href="<?php echo e(route('app.slot-templates.index')); ?>" class="ml-2 text-sm text-gray-600 hover:underline">Cancel</a>
            </div>
        </form>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
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
</script>
<?php /**PATH /Users/londo/Herd/test/resources/views/admin/slot-templates/edit.blade.php ENDPATH**/ ?>