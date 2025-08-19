<?php $__env->startSection('content'); ?>
<div class="p-6 bg-white rounded shadow">
  <h2 class="text-xl mb-4">Case Ranges for <?php echo e($depot->name); ?></h2>
  <a href="<?php echo e(route('admin.depots.case-ranges.create', $depot)); ?>" class="btn">Add Range</a>
  <table class="mt-4 w-full">
    <thead><tr><th>Min</t h><th>Max</th><th>Duration (min)</th><th>Actions</th></tr></thead>
    <tbody>
      <?php $__currentLoopData = $ranges; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $r): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <tr>
        <td><?php echo e($r->min_cases ?? '0'); ?></td>
        <td><?php echo e($r->max_cases ?? '∞'); ?></td>
        <td><?php echo e($r->duration_minutes); ?></td>
        <td>
          <a href="<?php echo e(route('admin.depots.case-ranges.edit', [$depot,$r])); ?>" class="link">Edit</a>
          <form action="<?php echo e(route('admin.depots.case-ranges.destroy', [$depot,$r])); ?>" method="POST" class="inline">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button onclick="return confirm('Delete?')" class="link text-red-600">Delete</button>
          </form>
        </td>
      </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
  </table>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/depot_case_ranges/index.blade.php ENDPATH**/ ?>