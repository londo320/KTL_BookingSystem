<?php $__env->startSection('content'); ?>
<div class="py-6 max-w-7xl mx-auto">
    
    <?php if(session('success')): ?>
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    
    <div class="mb-4">
        <a href="<?php echo e(route('admin.users.create')); ?>"
           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Create New User
        </a>
    </div>

    
    <div class="overflow-x-auto bg-white border border-gray-200">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-2 text-left">Name</th>
            <th class="px-4 py-2 text-left">Email</th>
            <th class="px-4 py-2 text-left align-top">Roles</th>
            <th class="px-4 py-2 text-left">Customer Access</th>
            <th class="px-4 py-2 text-left">Depots</th>
            <th class="px-4 py-2 text-left align-top">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="border-t hover:bg-gray-50">
              
              <td class="px-4 py-2"><?php echo e($user->name); ?></td>
              
              <td class="px-4 py-2"><?php echo e($user->email); ?></td>
              
              <td class="px-4 py-2 align-top">
                <div class="flex flex-wrap gap-1">
                  <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                      <?php echo e($role->name); ?>

                    </span>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
              </td>
              
              <td class="px-4 py-2">
                <div class="text-xs">
                  
                  <?php if($user->customers->count() > 0): ?>
                    <div class="flex flex-wrap gap-1">
                      <?php $__currentLoopData = $user->customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full">
                          <?php echo e($customer->name); ?>

                        </span>
                      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                  <?php elseif($user->hasRole(['admin', 'site-admin', 'depot-admin'])): ?>
                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                      All Customers
                    </span>
                  <?php else: ?>
                    <span class="text-gray-500">No Access</span>
                  <?php endif; ?>
                </div>
              </td>
              
            <td class="px-4 py-2 align-top">
  <div class="flex flex-wrap gap-1">
    <?php $__currentLoopData = $user->depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <span
        class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full"
        style="flex: 0 0 15%; white-space: nowrap;"
      >
        <?php echo e($depot->name); ?>

      </span>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
</td>
              
              <td class="px-4 py-2 align-top">
                <div class="flex flex-col space-y-1">
                  <a href="<?php echo e(route('admin.users.edit', $user)); ?>"
                     class="inline-block text-center px-2 py-1 bg-yellow-500 text-white rounded-full hover:bg-yellow-600 text-xs">
                    Edit
                  </a>
                  <form action="<?php echo e(route('admin.users.destroy', $user)); ?>" method="POST">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button type="submit"
                            class="inline-block text-center px-2 py-1 bg-red-500 text-white rounded-full hover:bg-red-600 text-xs">
                      Delete
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="6" class="text-center py-4">No users found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    
    <div class="mt-4">
      <?php echo e($users->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/users/index.blade.php ENDPATH**/ ?>