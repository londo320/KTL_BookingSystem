
<?php if (isset($component)) { $__componentOriginalc9242005886028143da563f7b99f0c87 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc9242005886028143da563f7b99f0c87 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.warehouse-layout','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('warehouse-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<div class="py-6 max-w-7xl mx-auto">
    
    <?php if(session('success')): ?>
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    
    <div class="mb-4">
        <a href="<?php echo e(route('app.users.create')); ?>"
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
              
              <td class="px-4 py-2">
                <?php echo e($user->name); ?>

                <?php if(!($user->is_active ?? true)): ?>
                  <span class="ml-2 text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">❌ Disabled</span>
                <?php endif; ?>
              </td>
              
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
                  <?php if($user->canBeEditedBy(auth()->user())): ?>
                    <a href="<?php echo e(route('app.users.edit', $user)); ?>"
                       class="inline-block text-center px-2 py-1 bg-yellow-500 text-white rounded-full hover:bg-yellow-600 text-xs">
                      Edit
                    </a>
                  <?php else: ?>
                    <span class="inline-block text-center px-2 py-1 bg-gray-300 text-gray-500 rounded-full text-xs cursor-not-allowed">
                        No Access
                    </span>
                  <?php endif; ?>
                  
                  <?php if(auth()->user()->hasRole('admin') || auth()->user()->isProtectedSystemOwner()): ?>
                    <form action="<?php echo e(route('app.users.destroy', $user)); ?>" method="POST">
                      <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                      <button type="submit"
                              class="inline-block text-center px-2 py-1 bg-red-500 text-white rounded-full hover:bg-red-600 text-xs"
                              onclick="return confirm('Are you sure you want to delete this user?')">
                        Delete
                      </button>
                    </form>
                  <?php endif; ?>
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
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc9242005886028143da563f7b99f0c87)): ?>
<?php $attributes = $__attributesOriginalc9242005886028143da563f7b99f0c87; ?>
<?php unset($__attributesOriginalc9242005886028143da563f7b99f0c87); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc9242005886028143da563f7b99f0c87)): ?>
<?php $component = $__componentOriginalc9242005886028143da563f7b99f0c87; ?>
<?php unset($__componentOriginalc9242005886028143da563f7b99f0c87); ?>
<?php endif; ?>
<?php /**PATH /Users/londo/Herd/test/resources/views/warehouse/users/index.blade.php ENDPATH**/ ?>