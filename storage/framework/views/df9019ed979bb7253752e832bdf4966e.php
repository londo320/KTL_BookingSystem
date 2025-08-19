<?php $__env->startSection('content'); ?>
<div class="py-6 max-w-7xl mx-auto">
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold">Slot Release Rules</h1>
    <a href="<?php echo e(route('admin.slotReleaseRules.create')); ?>" 
       class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
      Create New Rule
    </a>
  </div>

  <?php if(session('success')): ?>
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
      <?php echo e(session('success')); ?>

    </div>
  <?php endif; ?>

  <?php $__currentLoopData = $rules->groupBy('depot.name'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depotName => $rulesForDepot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="mb-8">
      <h2 class="text-xl font-bold mb-2"><?php echo e($depotName); ?></h2>
      <table class="w-full bg-white shadow rounded overflow-hidden text-sm mb-4">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-3 py-2 text-left">Customers</th>
            <th class="px-3 py-2 text-left">Day</th>
            <th class="px-3 py-2 text-left">Time</th>
            <th class="px-3 py-2 text-left">Cutoff</th>
            <th class="px-3 py-2 text-left">Priority</th>
            <th class="px-3 py-2 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $rulesForDepot->sortBy('release_day'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="border-t hover:bg-gray-50">
              <td class="px-3 py-2 align-top">
                <?php if($rule->customers->count()): ?>
                  <div class="flex flex-wrap gap-1">
                    <?php $__currentLoopData = $rule->customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cust): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                      <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full"><?php echo e($cust->name); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                  </div>
                <?php else: ?>
                  <span class="text-gray-500">Any</span>
                <?php endif; ?>
              </td>
              <td class="px-3 py-2"><?php echo e(['Mon','Tue','Wed','Thu','Fri','Sat','Sun'][$rule->release_day - 1]); ?></td>
              <td class="px-3 py-2"><?php echo e(\Carbon\Carbon::createFromFormat('H:i:s', $rule->release_time)->format('H:i')); ?></td>
              <td class="px-3 py-2"><?php echo e($rule->lock_cutoff_days); ?>d @ <?php echo e(\Carbon\Carbon::createFromFormat('H:i:s', $rule->lock_cutoff_time)->format('H:i')); ?></td>
              <td class="px-3 py-2"><?php echo e($rule->priority); ?></td>
              <td class="px-3 py-2 space-x-2">
                <a href="<?php echo e(route('admin.slotReleaseRules.edit', $rule)); ?>"
                   class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-xs">Edit</a>
                <form action="<?php echo e(route('admin.slotReleaseRules.destroy', $rule)); ?>" method="POST" class="inline" onsubmit="return confirm('Delete this rule?');">
                  <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                  <button class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs">Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>
    </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

  <div class="mt-4">
    <?php echo e($rules->links()); ?>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/slotReleaseRules/index.blade.php ENDPATH**/ ?>