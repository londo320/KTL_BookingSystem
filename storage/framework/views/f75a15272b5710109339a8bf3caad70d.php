<?php $__env->startSection('content'); ?>
<div class="py-6 max-w-6xl mx-auto">
  <div class="flex items-center justify-between mb-4">
    <h2 class="text-2xl font-semibold">Customers Management</h2>
    <a href="<?php echo e(route('app.customers.create')); ?>"
       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
      + New Customer
    </a>
  </div>

  <?php if(session('success')): ?>
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
      <?php echo e(session('success')); ?>

    </div>
  <?php endif; ?>

  <table class="min-w-full bg-white shadow rounded overflow-hidden">
    <thead class="bg-gray-100">
      <tr>
        <th class="px-4 py-2 text-left">Name</th>
        <th class="px-4 py-2 text-left">Email</th>
        <th class="px-4 py-2 text-left">Assigned Users</th>
        <th class="px-4 py-2 text-left">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr class="border-t hover:bg-gray-50">
          <td class="px-4 py-2"><?php echo e($customer->name); ?></td>
          <td class="px-4 py-2"><?php echo e($customer->email); ?></td>
          <td class="px-4 py-2">
            <?php $__empty_1 = true; $__currentLoopData = $customer->users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <span class="inline-block bg-gray-200 rounded-full px-2 py-1 text-xs mr-1">
                <?php echo e($user->name); ?>

              </span>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <span class="text-gray-500 text-xs">—</span>
            <?php endif; ?>
          </td>
          <td class="px-4 py-2 space-x-2">
            <a href="<?php echo e(route('app.customers.edit', $customer)); ?>"
               class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-xs">
               Edit
            </a>
            <a href="<?php echo e(route('app.customer-behavior.settings', $customer)); ?>"
               class="px-2 py-1 bg-purple-500 text-white rounded hover:bg-purple-600 text-xs">
               🔧 Limits
            </a>
            <form action="<?php echo e(route('app.customers.destroy', $customer)); ?>"
                  method="POST"
                  class="inline-block"
                  onsubmit="return confirm('Delete this customer?');">
              <?php echo csrf_field(); ?>
              <?php echo method_field('DELETE'); ?>
              <button type="submit"
                      class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs">
                Delete
              </button>
            </form>
          </td>
        </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
  </table>

  <div class="p-4">
    <?php echo e($customers->links()); ?>

  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/customers/index.blade.php ENDPATH**/ ?>