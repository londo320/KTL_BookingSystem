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
      <div>
        <h2 class="font-semibold text-xl text-gray-800">Time Slots Management</h2>
        <p class="text-sm text-gray-600 mt-1">Manage available booking time slots across depots</p>
      </div>
      <div class="text-sm">
        <?php if(!$currentDepotId): ?>
          <span class="text-gray-600">Viewing: <span class="font-medium text-purple-600">All Depots</span></span>
          <span class="ml-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Actions Restricted</span>
        <?php else: ?>
          <?php $currentDepot = $allDepots->firstWhere('id', $currentDepotId); ?>
          <span class="text-gray-600">Viewing: <span class="font-medium text-blue-600"><?php echo e($currentDepot?->name ?? 'Unknown Depot'); ?></span></span>
          <?php if($currentDepotId == $defaultDepotId): ?>
            <span class="ml-2 text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Actions Enabled</span>
          <?php else: ?>
            <span class="ml-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">View Only</span>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>
   <?php $__env->endSlot(); ?>
<div class="py-6 max-w-7xl mx-auto">
  
  <?php if(session('success')): ?>
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
      <?php echo e(session('success')); ?>

    </div>
  <?php endif; ?>

  
  <form method="GET" action="<?php echo e(route('admin.slots.index')); ?>" class="flex flex-wrap gap-4 items-end mb-4">
    <div>
      <label for="depot" class="block text-sm font-medium">View</label>
      <select name="depot_id" id="depot" class="border rounded px-2 py-1">
        <option value="" <?php echo e(!$currentDepotId ? 'selected' : ''); ?>>All Depots (View Only)</option>
        <?php $__currentLoopData = $allDepots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($depot->id); ?>" <?php echo e($currentDepotId == $depot->id ? 'selected' : ''); ?>>
            <?php echo e($depot->name); ?> <?php echo e($depot->id == $defaultDepotId ? '(Default - Actions Enabled)' : '(View Only)'); ?>

          </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>

    <div>
      <label for="date" class="block text-sm font-medium">Date</label>
      <input type="date" name="date" id="date" class="border rounded px-2 py-1" value="<?php echo e(request('date')); ?>">
    </div>

    <div>
      <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Filter</button>
      <a href="<?php echo e(route('admin.slots.index')); ?>" class="text-sm text-gray-600 ml-2 hover:underline">Clear Filters</a>
    </div>

    <div class="ml-auto">
      <?php if(request()->has('show_past')): ?>
        <a href="<?php echo e(route('admin.slots.index', request()->except('show_past'))); ?>" class="text-sm text-blue-600 hover:underline">Hide Past Slots</a>
      <?php else: ?>
        <a href="<?php echo e(route('admin.slots.index', array_merge(request()->all(), ['show_past' => true]))); ?>" class="text-sm text-blue-600 hover:underline">Show Past Slots</a>
      <?php endif; ?>
    </div>
  </form>

  
  <div class="overflow-x-auto bg-white shadow rounded">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 text-left">Depot</th>
          <th class="px-4 py-2 text-left">Start</th>
          <th class="px-4 py-2 text-left">End</th>
          <th class="px-4 py-2 text-left">Capacity</th>
          <th class="px-4 py-2 text-left">Usage</th>
          <th class="px-4 py-2 text-left">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $slots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
          <tr class="border-t hover:bg-gray-50">
            <td class="px-4 py-2"><?php echo e($slot->depot->name); ?></td>
<td class="px-4 py-2"><?php echo e(\Carbon\Carbon::parse($slot->start_at)->format('d-M H:i')); ?></td>
<td class="px-4 py-2"><?php echo e(\Carbon\Carbon::parse($slot->end_at)->format('d-M H:i')); ?></td>
            <td class="px-4 py-2"><?php echo e($slot->capacity); ?></td>
            <td class="px-4 py-2"><?php echo e($slot->bookings_count); ?> / <?php echo e($slot->capacity); ?></td>
            <td class="px-4 py-2 space-x-2">
              <?php $canTakeAction = $slot->depot_id == $defaultDepotId; ?>
              <?php if($canTakeAction): ?>
                <a href="<?php echo e(route('admin.slots.edit', $slot)); ?>" class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-xs">Edit</a>
                <form action="<?php echo e(route('admin.slots.destroy', $slot)); ?>" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                  <?php echo csrf_field(); ?>
                  <?php echo method_field('DELETE'); ?>
                  <button type="submit" class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs">Delete</button>
                </form>
              <?php else: ?>
                <span class="px-2 py-1 bg-gray-300 text-gray-500 rounded text-xs cursor-not-allowed" 
                      title="Actions only available for your default depot">Edit</span>
                <span class="px-2 py-1 bg-gray-300 text-gray-500 rounded text-xs cursor-not-allowed" 
                      title="Actions only available for your default depot">Delete</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
          <tr>
            <td colspan="6" class="text-center py-4 text-gray-500">No slots found.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
    <div class="p-4">
      <?php echo e($slots->appends(request()->query())->links()); ?>

    </div>
  </div>
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
<?php /**PATH /Users/londo/Herd/test/resources/views/admin/slots/index.blade.php ENDPATH**/ ?>