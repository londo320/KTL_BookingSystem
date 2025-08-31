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
      <h2 class="font-semibold text-xl">Pallet Type: <?php echo e($palletType->name); ?></h2>
      <div class="flex gap-2">
        <a href="<?php echo e(route('app.settings.pallet-types.edit', $palletType)); ?>"
           class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
          Edit
        </a>
        <a href="<?php echo e(route('app.settings.pallet-types')); ?>"
           class="px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm">
          Back to List
        </a>
      </div>
    </div>
   <?php $__env->endSlot(); ?>
  <div class="py-6 max-w-2xl mx-auto">
    <div class="bg-white p-6 rounded-lg shadow">
      <div class="grid grid-cols-1 gap-6">
        
        <div>
          <label class="block text-sm font-medium text-gray-700">Name</label>
          <div class="mt-1 p-3 bg-gray-50 border border-gray-300 rounded-lg">
            <?php echo e($palletType->name); ?>

          </div>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700">Code</label>
          <div class="mt-1 p-3 bg-gray-50 border border-gray-300 rounded-lg">
            <span class="font-mono"><?php echo e($palletType->code); ?></span>
          </div>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700">Description</label>
          <div class="mt-1 p-3 bg-gray-50 border border-gray-300 rounded-lg min-h-[100px]">
            <?php echo e($palletType->description ?: 'No description provided'); ?>

          </div>
        </div>
        
        <div>
          <label class="block text-sm font-medium text-gray-700">Status</label>
          <div class="mt-1">
            <?php if($palletType->is_active): ?>
              <span class="px-3 py-1 bg-green-100 text-green-800 text-sm rounded-full">Active</span>
            <?php else: ?>
              <span class="px-3 py-1 bg-red-100 text-red-800 text-sm rounded-full">Inactive</span>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
    
    <?php
      $expectedCount = $palletType->poLinesExpected()->count();
      $actualCount = $palletType->poLinesActual()->count();
      $totalUsage = $expectedCount + $actualCount;
    ?>
    <?php if($totalUsage > 0): ?>
      <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="font-medium text-blue-800 mb-2">📊 Usage Statistics</h3>
        <div class="text-blue-700 text-sm space-y-1">
          <p>This pallet type is used in <strong><?php echo e($totalUsage); ?></strong> PO lines total:</p>
          <ul class="list-disc list-inside ml-4">
            <li><?php echo e($expectedCount); ?> as expected pallet type</li>
            <li><?php echo e($actualCount); ?> as actual pallet type</li>
          </ul>
        </div>
      </div>
    <?php else: ?>
      <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
        <h3 class="font-medium text-gray-600 mb-2">📊 Usage Statistics</h3>
        <p class="text-gray-500 text-sm">This pallet type is not currently in use.</p>
      </div>
    <?php endif; ?>
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
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/pallet-types/show.blade.php ENDPATH**/ ?>