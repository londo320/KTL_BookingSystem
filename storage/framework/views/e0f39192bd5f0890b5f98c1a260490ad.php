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
  <?php echo $__env->make('layouts.admin-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

   <?php $__env->slot('header', null, []); ?> 
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl">Pallet Types Management</h2>
      <div class="flex gap-2">
        <a href="<?php echo e(route('admin.pallet-types.create')); ?>"
           class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
          + Add Pallet Type
        </a>
      </div>
    </div>
   <?php $__env->endSlot(); ?>

  <div class="py-6 max-w-7xl mx-auto">
    <?php if(session('success')): ?>
      <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
        <?php echo e(session('success')); ?>

      </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
      <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
        <?php echo e(session('error')); ?>

      </div>
    <?php endif; ?>

    
    <form method="GET" class="mb-4 flex flex-wrap gap-4 items-end bg-gray-50 p-4 rounded">
      <div>
        <label class="block text-sm font-medium">Search</label>
        <input type="text" name="search" value="<?php echo e(request('search')); ?>" 
               placeholder="Name, code, or description..."
               class="border rounded px-2 py-1 text-sm w-64">
      </div>
      <div>
        <label class="block text-sm font-medium">Status</label>
        <select name="status" class="border rounded px-2 py-1 text-sm">
          <option value="">All</option>
          <option value="active" <?php if(request('status') === 'active'): echo 'selected'; endif; ?>>Active</option>
          <option value="inactive" <?php if(request('status') === 'inactive'): echo 'selected'; endif; ?>>Inactive</option>
        </select>
      </div>
      <div class="flex space-x-2">
        <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Filter</button>
        <a href="<?php echo e(route('admin.pallet-types.index')); ?>" class="px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm">Clear</a>
      </div>
    </form>

    
    <div class="bg-white shadow rounded overflow-hidden">
      <table class="min-w-full">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-2 text-left">Name</th>
            <th class="px-4 py-2 text-left">Code</th>
            <th class="px-4 py-2 text-left">Description</th>
            <th class="px-4 py-2 text-left">Status</th>
            <th class="px-4 py-2 text-left">Usage</th>
            <th class="px-4 py-2 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $palletTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $palletType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="border-t hover:bg-gray-50 <?php echo e(!$palletType->is_active ? 'opacity-60' : ''); ?>">
              <td class="px-4 py-2">
                <div class="font-medium"><?php echo e($palletType->name); ?></div>
              </td>
              <td class="px-4 py-2">
                <span class="font-mono bg-gray-100 px-2 py-1 rounded text-sm"><?php echo e($palletType->code); ?></span>
              </td>
              <td class="px-4 py-2">
                <div class="text-sm text-gray-600"><?php echo e($palletType->description ?: 'No description'); ?></div>
              </td>
              <td class="px-4 py-2">
                <?php if($palletType->is_active): ?>
                  <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Active</span>
                <?php else: ?>
                  <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">Inactive</span>
                <?php endif; ?>
              </td>
              <td class="px-4 py-2">
                <?php
                  $expectedCount = $palletType->poLinesExpected()->count();
                  $actualCount = $palletType->poLinesActual()->count();
                  $totalUsage = $expectedCount + $actualCount;
                ?>
                <?php if($totalUsage > 0): ?>
                  <div class="text-sm">
                    <div><?php echo e($totalUsage); ?> PO lines</div>
                    <div class="text-xs text-gray-500">
                      <?php echo e($expectedCount); ?> expected, <?php echo e($actualCount); ?> actual
                    </div>
                  </div>
                <?php else: ?>
                  <span class="text-gray-400 text-sm">Not used</span>
                <?php endif; ?>
              </td>
              <td class="px-4 py-2">
                <div class="flex items-center space-x-2">
                  <a href="<?php echo e(route('admin.pallet-types.show', $palletType)); ?>"
                     class="text-blue-600 hover:text-blue-800 text-sm">View</a>
                  <a href="<?php echo e(route('admin.pallet-types.edit', $palletType)); ?>"
                     class="text-green-600 hover:text-green-800 text-sm">Edit</a>
                  
                  <form method="POST" action="<?php echo e(route('admin.pallet-types.toggle-active', $palletType)); ?>" class="inline">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <button type="submit" 
                            class="text-yellow-600 hover:text-yellow-800 text-sm">
                      <?php echo e($palletType->is_active ? 'Deactivate' : 'Activate'); ?>

                    </button>
                  </form>

                  <?php if($totalUsage === 0): ?>
                    <form method="POST" action="<?php echo e(route('admin.pallet-types.destroy', $palletType)); ?>" 
                          class="inline" onsubmit="return confirm('Are you sure you want to delete this pallet type?')">
                      <?php echo csrf_field(); ?>
                      <?php echo method_field('DELETE'); ?>
                      <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                    </form>
                  <?php endif; ?>
                </div>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                No pallet types found.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    
    <?php if($palletTypes->hasPages()): ?>
      <div class="mt-4">
        <?php echo e($palletTypes->appends(request()->query())->links()); ?>

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
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/pallet-types/index.blade.php ENDPATH**/ ?>