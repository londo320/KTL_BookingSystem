
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
<div class="py-6 max-w-3xl mx-auto bg-white p-6 rounded shadow">
  
  <?php if(session('success')): ?>
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
      <?php echo e(session('success')); ?>

    </div>
  <?php endif; ?>
  <?php if(session('new_password')): ?>
    <div class="mb-4 p-3 bg-yellow-100 text-yellow-800 rounded">
      <strong>Your new password is:</strong> <?php echo e(session('new_password')); ?>

    </div>
  <?php endif; ?>

  <form action="<?php echo e(route('app.users.store')); ?>" method="POST">
    <?php echo csrf_field(); ?>

    
    <div class="mb-4">
      <label class="block text-sm font-medium">Name</label>
      <input type="text" name="name"
             value="<?php echo e(old('name')); ?>"
             class="mt-1 block w-full border-gray-300 rounded">
      <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    
    <div class="mb-4">
      <label class="block text-sm font-medium">Email</label>
      <input type="email" name="email"
             value="<?php echo e(old('email')); ?>"
             class="mt-1 block w-full border-gray-300 rounded">
      <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    
    <div class="mb-4">
      <label class="block text-sm font-medium">Assign Roles</label>
      <div class="mt-1 space-y-2">
        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <label class="inline-flex items-center">
            <input
              type="checkbox"
              name="role_ids[]"
              value="<?php echo e($role->id); ?>"
              class="border-gray-300 rounded"
              <?php echo e(in_array($role->id, old('role_ids', [])) ? 'checked' : ''); ?>

            >
            <span class="ml-2 text-sm"><?php echo e($role->name); ?></span>
          </label>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
      <?php $__errorArgs = ['role_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    
    <div class="mb-4">
      <label class="block text-sm font-medium">Assign Depots</label>
      <div class="mt-1 space-y-2">
        <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <label class="inline-flex items-center">
            <input
              type="checkbox"
              name="depot_ids[]"
              value="<?php echo e($depot->id); ?>"
              class="border-gray-300 rounded"
              <?php echo e(in_array($depot->id, old('depot_ids', [])) ? 'checked' : ''); ?>

            >
            <span class="ml-2 text-sm"><?php echo e($depot->name); ?></span>
          </label>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
      <?php $__errorArgs = ['depot_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    
    <div class="mb-4">
      <label for="depot_id" class="block text-sm font-medium">Default Depot</label>
      <select name="depot_id" id="depot_id" class="mt-1 block w-full border-gray-300 rounded">
        <option value="">— No Default Depot —</option>
        <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($depot->id); ?>" 
            <?php if($depot->id == old('depot_id')): echo 'selected'; endif; ?>>
            <?php echo e($depot->name); ?>

          </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
      <p class="text-xs text-gray-500 mt-1">
        This depot will be shown by default on operational dashboards. 
        User must also have access to this depot above.
      </p>
      <?php $__errorArgs = ['depot_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    
    <div class="mb-4">
      <label class="block text-sm font-medium mb-2">Customer Assignment</label>
      
      
      <div class="mb-3" id="legacy-customer-field">
        <label class="block text-sm font-medium text-gray-600">Legacy Customer (for customer role)</label>
        <select name="customer_id" class="mt-1 block w-full border-gray-300 rounded">
          <option value="">— No Legacy Customer —</option>
          <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($customer->id); ?>"
              <?php if($customer->id == old('customer_id')): echo 'selected'; endif; ?>>
              <?php echo e($customer->name); ?>

            </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <p class="text-xs text-gray-500 mt-1">Only used for users with customer role</p>
        <?php $__errorArgs = ['customer_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>

      
      <div class="mb-3">
        <label class="block text-sm font-medium">Multiple Customers (for admin/site roles)</label>
        <div class="mt-2 space-y-2 max-h-40 overflow-y-auto border rounded p-2">
          <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <label class="inline-flex items-center w-full">
              <input
                type="checkbox"
                name="customer_ids[]"
                value="<?php echo e($customer->id); ?>"
                class="border-gray-300 rounded"
                <?php echo e(in_array($customer->id, old('customer_ids', [])) ? 'checked' : ''); ?>

              >
              <span class="ml-2 text-sm"><?php echo e($customer->name); ?></span>
            </label>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <p class="text-xs text-gray-500 mt-1">
          Leave empty for admin/site roles to see ALL customers (including future ones).
          Select specific customers to limit access.
        </p>
        <?php $__errorArgs = ['customer_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
    </div>


<div class="mb-4 flex items-center space-x-4">
  
  <div class="flex-1">
    <label class="block text-sm font-medium">Password</label>
    <input type="password" name="password"
           value="<?php echo e(old('password')); ?>"
           class="mt-1 block w-full border-gray-300 rounded"
           placeholder="Enter a password">
    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-sm"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>

  
  <div class="flex items-center text-gray-500">OR</div>

  
  <div class="flex items-center">
    <input type="checkbox" name="generate_password" id="generate_password"
           value="1" class="mr-2" <?php echo e(old('generate_password') ? 'checked' : ''); ?>>
    <label for="generate_password" class="text-sm">Generate random password</label>
    <?php $__errorArgs = ['generate_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-sm"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
  </div>
</div>

    
    <div class="flex justify-end">
      <button type="submit"
              class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        Create User
      </button>
    </div>
  </form>
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
<?php /**PATH /Users/londo/Herd/test/resources/views/warehouse/users/create.blade.php ENDPATH**/ ?>