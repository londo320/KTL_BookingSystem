
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
<div class="py-6 max-w-4xl mx-auto">
  <h2 class="text-2xl font-semibold mb-4">New Customer</h2>

  <?php if($errors->any()): ?>
    <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
      <ul class="list-disc list-inside">
        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <li><?php echo e($error); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </ul>
    </div>
  <?php endif; ?>

  <form action="<?php echo e(route('app.customers.store')); ?>" method="POST" class="space-y-4 bg-white shadow rounded p-6">
    <?php echo csrf_field(); ?>

    <div>
      <label for="name" class="block text-sm font-medium">Name</label>
      <input type="text"
             name="name"
             id="name"
             value="<?php echo e(old('name')); ?>"
             class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
      <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    
    <div>
      <label for="emails" class="block text-sm font-medium">Contact Emails (comma-separated)</label>
      <input type="text"
             name="emails"
             id="emails"
             value="<?php echo e(old('emails')); ?>"
             placeholder="e.g. alice@example.com, bob@example.com"
             class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"/>
      <?php $__errorArgs = ['emails'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    
    <div>
      <label for="user_ids" class="block text-sm font-medium">Assign Users (Optional)</label>
      <select name="user_ids[]"
              id="user_ids"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"
              multiple
              size="6">
        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($user->id); ?>"
            <?php echo e(in_array($user->id, old('user_ids', [])) ? 'selected' : ''); ?>>
            <?php echo e($user->name); ?> (<?php echo e($user->email); ?>)
          </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
      <p class="text-sm text-gray-600 mt-1">Hold Ctrl/Cmd to select multiple users. Leave empty if no users need assignment.</p>
      <?php $__errorArgs = ['user_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <p class="text-red-600 text-sm mt-1"><?php echo e($message); ?></p>
      <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <div class="flex justify-end">
      <button type="submit"
              class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        Create Customer
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
<?php /**PATH /Users/londo/Herd/test/resources/views/warehouse/customers/create.blade.php ENDPATH**/ ?>