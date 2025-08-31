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
    <h2 class="text-xl font-semibold">Generate Slots</h2>
   <?php $__env->endSlot(); ?>
  <div class="max-w-xl mx-auto py-6">
    <?php if(session('success')): ?>
      <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
        <?php echo e(session('success')); ?>

      </div>
    <?php endif; ?>
    <?php if($errors->any()): ?>
      <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
        <ul class="list-disc pl-5">
          <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($error); ?></li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      </div>
    <?php endif; ?>
    <form method="POST" action="<?php echo e(route('app.generate-slots.store')); ?>">
      <?php echo csrf_field(); ?>
      <div class="mb-4">
        <label class="block mb-1">Depot</label>
        <select name="depot_id" class="w-full border rounded p-2">
          <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($depot->id); ?>"><?php echo e($depot->name); ?></option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <?php
        $tomorrow = \Carbon\Carbon::tomorrow(config('app.timezone'))->format('Y-m-d');
        $selectedDate = old('date', request('date', $tomorrow));
      ?>
      <div class="mb-4">
        <label class="block mb-1">Date</label>
        <input 
          type="date" 
          name="date" 
          value="<?php echo e($selectedDate); ?>" 
          min="<?php echo e($tomorrow); ?>"
          class="w-full border rounded p-2"
        >
      </div>
      <button class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        Generate Slots
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
<?php /**PATH /Users/londo/Herd/test/resources/views/admin/generate-slots/index.blade.php ENDPATH**/ ?>