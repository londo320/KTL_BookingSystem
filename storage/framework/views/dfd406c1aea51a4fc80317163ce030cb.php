  


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
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc9242005886028143da563f7b99f0c87)): ?>
<?php $attributes = $__attributesOriginalc9242005886028143da563f7b99f0c87; ?>
<?php unset($__attributesOriginalc9242005886028143da563f7b99f0c87); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc9242005886028143da563f7b99f0c87)): ?>
<?php $component = $__componentOriginalc9242005886028143da563f7b99f0c87; ?>
<?php unset($__componentOriginalc9242005886028143da563f7b99f0c87); ?>
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/warehouse/depots/index.blade.php ENDPATH**/ ?>