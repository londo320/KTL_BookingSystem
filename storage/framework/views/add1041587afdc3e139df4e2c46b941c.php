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
    <h2 class="text-xl font-semibold">📊 Admin Dashboard</h2>
   <?php $__env->endSlot(); ?>

  <div class="py-6 max-w-7xl mx-auto space-y-8">
    
    <form method="GET" action="<?php echo e(route('admin.dashboard')); ?>" class="flex items-center gap-4 mb-4">
      <div>
        <label for="date" class="text-sm font-medium">Select Date:</label>
        <input type="date" name="date" id="date" class="border rounded px-2 py-1"
               value="<?php echo e($date->format('Y-m-d')); ?>">
      </div>
      <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        Apply Filter
      </button>
      <a href="<?php echo e(route('admin.dashboard')); ?>" class="text-sm text-gray-600 hover:underline">Reset</a>
    </form>

    <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <div class="bg-white shadow rounded p-6">
        <div class="flex justify-between items-center mb-2">
          <div>
            <h3 class="text-lg font-bold"><?php echo e($depot->name); ?></h3>
            <p class="text-sm text-gray-500"><?php echo e($depot->location ?? '—'); ?></p>
          </div>
          <div class="text-sm text-gray-500">
            <?php echo e($depot->summary['date']->format('D, d M Y')); ?>

          </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-2 gap-4 text-sm">
          
          <div class="space-y-1">
            <p><strong>Total Slots:</strong> <?php echo e($depot->summary['total']); ?></p>
            <p><strong>Used:</strong> <?php echo e($depot->summary['used']); ?></p>
            <p><strong>Available:</strong> <?php echo e($depot->summary['available']); ?></p>
          </div>

          
          <div class="space-y-1">
            <p><strong>Arrived:</strong> <?php echo e($depot->summary['arrived']); ?></p>
            <p><strong>In Progress:</strong> <?php echo e($depot->summary['in_progress']); ?></p>
            <p><strong>Finished:</strong> <?php echo e($depot->summary['finished']); ?></p>
            <p><strong>Late:</strong> <span class="text-red-600"><?php echo e($depot->summary['late']); ?></span></p>
          </div>
        </div>

        
        <div class="mt-4">
          <p class="font-semibold mb-1">📦 Bookings by Type</p>
          <ul class="text-sm space-y-1">
            <?php $__empty_1 = true; $__currentLoopData = $bookingTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
             <?php
            $data = $depot->summary['types'][$type->id] ?? ['used' => 0, 'capacity' => 0];
            ?>
        <li><?php echo e($type->name); ?> — <?php echo e($data['used'] ?? 0); ?> / <?php echo e($data['capacity'] ?? 0); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <li>No booking types found.</li>
            <?php endif; ?>

            
<?php
  $unassigned = $depot->summary['types'][null] ?? [];
  $used = $unassigned['used'] ?? 0;
  $capacity = $unassigned['capacity'] ?? 0;
?>

<?php if($used > 0 || $capacity > 0): ?>
  <li class="text-red-600">Unassigned — <?php echo e($used); ?> / <?php echo e($capacity); ?></li>
<?php endif; ?>
          </ul>
        </div>
      </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
<?php /**PATH /Users/londo/Herd/test/resources/views/admin/dashboard.blade.php ENDPATH**/ ?>