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
   <?php $__env->slot('header', null, []); ?> 
    <h2 class="text-xl font-semibold">➕ Add New Product</h2>
   <?php $__env->endSlot(); ?>

  <div class="max-w-xl mx-auto py-6">
    <?php if($errors->any()): ?>
      <div class="bg-red-100 text-red-700 p-3 mb-4 rounded">
        <ul class="list-disc list-inside">
          <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($error); ?></li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      </div>
    <?php endif; ?>

    <form action="<?php echo e(route('app.products.store')); ?>" method="POST" class="space-y-4">
      <?php echo csrf_field(); ?>

      <div>
        <label for="sku" class="block text-sm font-medium">SKU</label>
        <input type="text" name="sku" id="sku" value="<?php echo e(old('sku')); ?>" required
               class="w-full border rounded px-3 py-2">
      </div>

      <div>
        <label for="description" class="block text-sm font-medium">Description</label>
        <input type="text" name="description" id="description" value="<?php echo e(old('description')); ?>" required
               class="w-full border rounded px-3 py-2">
      </div>

      <div class="flex justify-end">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
          💾 Create Product
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
<?php /**PATH /Users/londo/Herd/test/resources/views/warehouse/depots/products/create.blade.php ENDPATH**/ ?>