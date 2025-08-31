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
    <h2 class="text-xl font-semibold">Slot Generation Settings</h2>
   <?php $__env->endSlot(); ?>
  <div class="max-w-5xl mx-auto py-6">
    <?php if(session('success')): ?>
      <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
        <?php echo e(session('success')); ?>

      </div>
    <?php endif; ?>
    <form method="POST" action="<?php echo e(route('app.slot-settings.store')); ?>">
      <?php echo csrf_field(); ?>
      <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="mb-6 border-b pb-4">
          <h3 class="text-lg font-semibold mb-2"><?php echo e($depot->name); ?></h3>
          <?php
            $settings = $depot->slotGenerationSetting;
          ?>
          <div class="grid grid-cols-3 gap-4">
            <div>
              <label>Start Time</label>
              <input type="time" name="settings[<?php echo e($depot->id); ?>][start_time]"
                     value="<?php echo e(old("settings.{$depot->id}.start_time", $settings?->start_time?->format('H:i') ?? '06:00')); ?>"
                     class="w-full border rounded p-2">
            </div>
            <div>
              <label>End Time</label>
              <input type="time" name="settings[<?php echo e($depot->id); ?>][end_time]"
                     value="<?php echo e(old("settings.{$depot->id}.end_time", $settings?->end_time?->format('H:i') ?? '18:00')); ?>"
                     class="w-full border rounded p-2">
            </div>
            <div>
              <label>Interval (mins)</label>
              <input type="number" name="settings[<?php echo e($depot->id); ?>][interval_minutes]"
                     value="<?php echo e(old("settings.{$depot->id}.interval_minutes", $settings?->interval_minutes ?? 60)); ?>"
                     class="w-full border rounded p-2" min="15" max="180">
            </div>
            <div>
              <label>Slots per Block</label>
              <input type="number" name="settings[<?php echo e($depot->id); ?>][slots_per_block]"
                     value="<?php echo e(old("settings.{$depot->id}.slots_per_block", $settings?->slots_per_block ?? 1)); ?>"
                     class="w-full border rounded p-2" min="1" max="10">
            </div>
            <div>
              <label>Default Capacity</label>
              <input type="number" name="settings[<?php echo e($depot->id); ?>][default_capacity]"
                     value="<?php echo e(old("settings.{$depot->id}.default_capacity", $settings?->default_capacity ?? 1)); ?>"
                     class="w-full border rounded p-2" min="1" max="10">
            </div>
            <div>
              <label>Days Active</label>
              <div class="flex flex-wrap gap-2 mt-1">
                <?php $__currentLoopData = ['mon','tue','wed','thu','fri','sat','sun']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <label class="flex items-center gap-1">
                    <input type="checkbox"
                           name="settings[<?php echo e($depot->id); ?>][days_active][]"
                           value="<?php echo e($day); ?>"
                           <?php if(in_array($day, $settings?->days_active ?? [])): echo 'checked'; endif; ?>>
                    <?php echo e(ucfirst($day)); ?>

                  </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
              </div>
            </div>
          </div>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        Save Settings
      </button>
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
<?php /**PATH /Users/londo/Herd/test/resources/views/admin/slot-settings/index.blade.php ENDPATH**/ ?>