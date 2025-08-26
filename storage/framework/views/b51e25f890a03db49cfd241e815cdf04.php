  
  <?php echo $__env->make('layouts.admin-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>



<?php $__env->startSection('content'); ?>
<div class="py-6 max-w-4xl mx-auto">
  <h2 class="text-xl font-semibold mb-4">Depots</h2>

  <div class="mb-4">
    <a href="<?php echo e(route('app.depots.create')); ?>" class="bg-blue-600 text-white px-4 py-2 rounded">+ New Depot</a>
  </div>

  <?php if(session('success')): ?>
    <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
      <?php echo e(session('success')); ?>

    </div>
  <?php endif; ?>

  <table class="w-full table-auto border">
    <thead>
      <tr class="bg-gray-100">
        <th class="p-2 border">Name</th>
        <th class="p-2 border">Location</th>
        <th class="p-2 border">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
          <td class="p-2 border"><?php echo e($depot->name); ?></td>
          <td class="p-2 border"><?php echo e($depot->location ?? '-'); ?></td>
          <td class="p-2 border space-x-2">
            <a href="<?php echo e(route('app.depots.edit', $depot)); ?>" class="text-blue-600">Edit</a>
            <form action="<?php echo e(route('app.depots.destroy', $depot)); ?>" method="POST" class="inline">
              <?php echo csrf_field(); ?>
              <?php echo method_field('DELETE'); ?>
              <button type="submit" class="text-red-600" onclick="return confirm('Are you sure?')">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
  </table>

  <div class="mt-4">
    <?php echo e($depots->links()); ?>

  </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/depots/index.blade.php ENDPATH**/ ?>