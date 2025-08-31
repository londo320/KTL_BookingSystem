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
   <?php $__env->slot('header', null, []); ?> 
    <h2 class="text-xl font-semibold">Create Slot</h2>
   <?php $__env->endSlot(); ?>

  <div class="max-w-xl mx-auto py-6">
    <form method="POST" action="<?php echo e(route('app.slots.store')); ?>">
      <?php echo csrf_field(); ?>

      <div class="mb-4">
        <label class="block text-sm">Depot</label>
        <select name="depot_id" class="w-full border rounded p-2">
          <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($depot->id); ?>"><?php echo e($depot->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>

      <div class="mb-4">
        <label class="block text-sm">Booking Type</label>
        <select name="booking_type_id" class="w-full border rounded p-2">
          <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($type->id); ?>"><?php echo e($type->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>

      <div class="mb-4">
        <label class="block text-sm">Start At</label>
        <input type="datetime-local" name="start_at" class="w-full border rounded p-2" required>
      </div>

      <div class="mb-4">
        <label class="block text-sm">End At</label>
        <input type="datetime-local" name="end_at" class="w-full border rounded p-2" required>
      </div>

      <div class="mb-4">
        <label class="inline-flex items-center">
          <input type="checkbox" name="is_blocked" class="mr-2">
          Block this slot
        </label>
      </div>

      <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        Create Slot
      </button>
    </form>
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
<?php /**PATH /Users/londo/Herd/test/resources/views/warehouse/slots/create.blade.php ENDPATH**/ ?>