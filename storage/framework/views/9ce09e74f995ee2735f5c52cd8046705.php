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
    <h2 class="text-xl font-semibold">✏️ Edit Booking Type</h2>
   <?php $__env->endSlot(); ?>
  <div class="py-6 max-w-4xl mx-auto space-y-6">
    <form method="POST" action="<?php echo e(route('app.booking-types.update', $bookingType)); ?>">
      <?php echo csrf_field(); ?>
      <?php echo method_field('PUT'); ?>

      
      <div class="bg-white p-6 rounded shadow">
        <h3 class="font-semibold mb-4 text-lg">Basic Information</h3>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium mb-1">Name</label>
            <input type="text" name="name" class="w-full border rounded p-2"
                   value="<?php echo e(old('name', $bookingType->name)); ?>" required>
            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
              <p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>
          <div>
            <label class="block text-sm font-medium mb-1">Default Duration (minutes)</label>
            <input type="number" name="duration_minutes" class="w-full border rounded p-2"
                   value="<?php echo e(old('duration_minutes', $bookingType->duration_minutes ?? 60)); ?>" required min="1">
            <p class="text-xs text-gray-500 mt-1">Used when no depot or customer-specific duration is set</p>
            <?php $__errorArgs = ['duration_minutes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
              <p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
          </div>
        </div>
      </div>

      
      <div class="bg-white p-6 rounded shadow">
        <h3 class="font-semibold mb-4 text-lg">Depot-Specific Durations</h3>
        <p class="text-sm text-gray-600 mb-4">Override the default duration for specific depots</p>
        <div class="space-y-3">
          <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="flex items-center gap-4">
              <label class="w-1/3 text-sm"><?php echo e($depot->name); ?></label>
              <input type="number"
                     name="depot_durations[<?php echo e($depot->id); ?>]"
                     class="border rounded p-2 w-32"
                     value="<?php echo e(old('depot_durations.'.$depot->id, $depotDurations[$depot->id] ?? '')); ?>"
                     placeholder="Default"
                     min="1">
              <span class="text-xs text-gray-500">minutes (leave empty for default)</span>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>

      
      <div class="bg-white p-6 rounded shadow">
        <h3 class="font-semibold mb-4 text-lg">Customer-Specific Durations</h3>
        <p class="text-sm text-gray-600 mb-4">Override durations for specific customers (optionally per depot)</p>
        <div class="space-y-4">
          <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="border-l-4 border-blue-200 pl-4 py-2">
              <div class="font-medium text-sm mb-2"><?php echo e($customer->name); ?></div>

              
              <div class="flex items-center gap-4 mb-2">
                <label class="w-1/3 text-sm text-gray-600">All Depots</label>
                <input type="number"
                       name="customer_durations[<?php echo e($customer->id); ?>][all]"
                       class="border rounded p-2 w-32"
                       value="<?php echo e(old('customer_durations.'.$customer->id.'.all', $customerDurations[$customer->id.'_all']['duration'] ?? '')); ?>"
                       placeholder="Default"
                       min="1">
                <span class="text-xs text-gray-500">minutes</span>
              </div>

              
              <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex items-center gap-4 ml-4">
                  <label class="w-1/3 text-sm text-gray-500"><?php echo e($depot->name); ?></label>
                  <input type="number"
                         name="customer_durations[<?php echo e($customer->id); ?>][<?php echo e($depot->id); ?>]"
                         class="border rounded p-2 w-32"
                         value="<?php echo e(old('customer_durations.'.$customer->id.'.'.$depot->id, $customerDurations[$customer->id.'_'.$depot->id]['duration'] ?? '')); ?>"
                         placeholder="Default"
                         min="1">
                  <span class="text-xs text-gray-500">minutes</span>
                </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>

      <div class="flex justify-end gap-4">
        <a href="<?php echo e(route('app.booking-types.index')); ?>"
           class="text-sm text-gray-600 hover:underline">Cancel</a>
        <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          Update Booking Type
        </button>
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
<?php /**PATH /Users/londo/Herd/test/resources/views/admin/booking_types/edit.blade.php ENDPATH**/ ?>