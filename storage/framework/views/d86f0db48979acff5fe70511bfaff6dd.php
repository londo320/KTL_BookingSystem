
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
<div class="p-6 bg-white rounded shadow">
  <h2 class="text-xl mb-4">Generate Slots</h2>
  <!-- Server time/info -->
<div class="mb-4 text-sm text-gray-600">
  <p>Server Time (<?php echo e(config('app.timezone')); ?>): <?php echo e(\Carbon\Carbon::now()->toDateTimeString()); ?></p>
</div>
  <form action="<?php echo e(route('app.slots.generate')); ?>" method="POST" class="space-y-4">
    <?php echo csrf_field(); ?>

    
    <div>
      <label for="depot_id" class="block text-sm font-medium">Select Depot</label>
      <select name="depot_id" id="depot_id" required class="mt-1 block w-1/3 border rounded p-2">
        <option value="">-- Choose a Depot --</option>
        <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($depot->id); ?>" <?php echo e(old('depot_id') == $depot->id ? 'selected' : ''); ?>>
            <?php echo e($depot->name); ?>

          </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
      <?php $__errorArgs = ['depot_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <p class="text-red-600 text-sm"><?php echo e($message); ?></p>
      <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium">Start Date</label>
        <input type="date" id="start_date" class="mt-1 block w-1/3 border rounded p-2" readonly />
      </div>
      <div>
        <label class="block text-sm font-medium">End Date</label>
        <input type="date" id="end_date" class="mt-1 block w-1/3 border rounded p-2" readonly />
      </div>
    </div>

    <div>
      <label for="days" class="block text-sm font-medium">Generate Ahead (days)</label>
      <input type="number" name="days" id="days_input" placeholder="Leave blank for default" class="mt-1 block w-1/3 border rounded p-2" />
      <?php $__errorArgs = ['days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <p class="text-red-600 text-sm"><?php echo e($message); ?></p>
      <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    </div>

    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded">Run Generator</button>
  </form>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const daysInput = document.getElementById('days_input');
    const startDateField = document.getElementById('start_date');
    const endDateField = document.getElementById('end_date');
    const today = new Date();

    // Initialize dates
    const formatDate = d => d.toISOString().split('T')[0];
    startDateField.value = formatDate(today);
    updateEndDate();

    daysInput.addEventListener('input', updateEndDate);

    function updateEndDate() {
      const days = parseInt(daysInput.value, 10);
      const offset = isNaN(days) ? <?php echo e(config('slots.default_generate_days', 14)); ?> : days;
      const end = new Date(today);
      end.setDate(end.getDate() + offset);
      endDateField.value = formatDate(end);
    }
  });
</script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc9242005886028143da563f7b99f0c87)): ?>
<?php $attributes = $__attributesOriginalc9242005886028143da563f7b99f0c87; ?>
<?php unset($__attributesOriginalc9242005886028143da563f7b99f0c87); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc9242005886028143da563f7b99f0c87)): ?>
<?php $component = $__componentOriginalc9242005886028143da563f7b99f0c87; ?>
<?php unset($__componentOriginalc9242005886028143da563f7b99f0c87); ?>
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/warehouse/slots/generate.blade.php ENDPATH**/ ?>