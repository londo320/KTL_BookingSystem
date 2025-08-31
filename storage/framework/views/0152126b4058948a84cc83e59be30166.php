<?php $__env->startSection('content'); ?>
<div class="py-6 max-w-4xl mx-auto">
  <?php if(session('success')): ?>
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
      <?php echo e(session('success')); ?>

    </div>
  <?php endif; ?>

  <h1 class="text-2xl font-semibold mb-6">
    <?php echo e(isset($rule->id) ? 'Edit' : 'Create'); ?> Slot Release Rule
  </h1>

  <form method="POST" action="<?php echo e(isset($rule->id) ? route('app.slotReleaseRules.update', $rule) : route('app.slotReleaseRules.store')); ?>">
    <?php echo csrf_field(); ?>
    <?php if(isset($rule->id)): ?>
      <?php echo method_field('PUT'); ?>
    <?php endif; ?>

    <!-- Depot Selection -->
    <div class="mb-4">
      <label for="depot_id" class="block text-sm font-medium">
        Depot
        <span class="text-gray-500 text-xs italic">Select the warehouse location this rule applies to.</span>
      </label>
      <select name="depot_id" id="depot_id" class="mt-1 block w-full border-gray-300 rounded">
        <option value="">– Choose depot –</option>
        <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($id); ?>" <?php if(old('depot_id', $rule->depot_id) == $id): echo 'selected'; endif; ?>><?php echo e($name); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
      <?php $__errorArgs = ['depot_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <!-- Customers (many-to-many) -->
    <div class="mb-4">
      <label class="block text-sm font-medium">
        Customers (optional)
        <span class="text-gray-500 text-xs italic">Select which customers this rule applies to; leave none to apply to all.</span>
      </label>
      <?php
        $selected = old('customer_ids', isset($rule->id) ? $rule->customers->pluck('id')->toArray() : []);
      ?>
      <div class="mt-2 grid grid-cols-2 gap-4">
        <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <label class="inline-flex items-center">
            <input type="checkbox" name="customer_ids[]" value="<?php echo e($id); ?>" class="form-checkbox" <?php if(in_array($id, $selected)): echo 'checked'; endif; ?>>
            <span class="ml-2 text-sm"><?php echo e($name); ?></span>
          </label>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </div>
      <?php $__errorArgs = ['customer_ids'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <!-- Release Day -->
    <div class="mb-4">
      <label for="release_day" class="block text-sm font-medium">
        Release Day
        <span class="text-gray-500 text-xs italic">Choose the weekday when slots become available for booking.</span>
      </label>
      <?php
        $days = [1=>'Mon',2=>'Tue',3=>'Wed',4=>'Thu',5=>'Fri',6=>'Sat',7=>'Sun'];
      ?>
      <select name="release_day" id="release_day" class="mt-1 block w-full border-gray-300 rounded">
        <option value="">– Select day –</option>
        <?php $__currentLoopData = $days; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $num => $abbr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($num); ?>" <?php if(old('release_day', $rule->release_day) == $num): echo 'selected'; endif; ?>><?php echo e($abbr); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
      <?php $__errorArgs = ['release_day'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <!-- Release Time -->
    <div class="mb-4">
      <label for="release_time" class="block text-sm font-medium">
        Release Time
        <span class="text-gray-500 text-xs italic">Time of day (HH:MM) when slots go live on the release day.</span>
      </label>
      <input type="time" name="release_time" id="release_time" value="<?php echo e(old('release_time', substr($rule->release_time, 0, 5))); ?>" class="mt-1 block w-32 border-gray-300 rounded">
      <?php $__errorArgs = ['release_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <!-- Lock Cutoff Days & Time -->
    <div class="grid grid-cols-2 gap-4 mb-4">
      <div>
        <label for="lock_cutoff_days" class="block text-sm font-medium">
          Cutoff Days
          <span class="text-gray-500 text-xs italic">How many days before the slot date changes are locked.</span>
        </label>
        <input type="number" name="lock_cutoff_days" id="lock_cutoff_days" value="<?php echo e(old('lock_cutoff_days', $rule->lock_cutoff_days)); ?>" min="0" class="mt-1 block w-24 border-gray-300 rounded">
        <?php $__errorArgs = ['lock_cutoff_days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <div>
        <label for="lock_cutoff_time" class="block text-sm font-medium">
          Cutoff Time
          <span class="text-gray-500 text-xs italic">Time on the cutoff days when edits are no longer allowed.</span>
        </label>
        <input type="time" name="lock_cutoff_time" id="lock_cutoff_time" value="<?php echo e(old('lock_cutoff_time', substr($rule->lock_cutoff_time, 0, 5))); ?>" class="mt-1 block w-32 border-gray-300 rounded">
        <?php $__errorArgs = ['lock_cutoff_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
    </div>

    <!-- Priority -->
    <div class="mb-4">
      <label for="priority" class="block text-sm font-medium">
        Priority
        <span class="text-gray-500 text-xs italic">Higher number gives this rule precedence when multiple apply.</span>
      </label>
      <input type="number" name="priority" id="priority" value="<?php echo e(old('priority', $rule->priority)); ?>" min="0" class="mt-1 block w-24 border-gray-300 rounded">
      <?php $__errorArgs = ['priority'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><p class="text-red-600 text-sm"><?php echo e($message); ?></p><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <!-- Submit -->
    <div class="mt-6">
      <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        <?php echo e(isset($rule->id) ? 'Update Rule' : 'Create Rule'); ?>

      </button>
    </div>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/slotReleaseRules/form.blade.php ENDPATH**/ ?>