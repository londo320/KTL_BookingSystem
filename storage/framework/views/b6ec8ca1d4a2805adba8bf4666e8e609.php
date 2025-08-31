  

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
<div class="p-6 bg-white rounded-lg shadow">
  <h1 class="text-2xl font-semibold mb-4">Products</h1>
  <a href="<?php echo e(route('app.products.create')); ?>"
     class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
    + New Product
  </a>

  <?php if(session('success')): ?>
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
      <?php echo e(session('success')); ?>

    </div>
  <?php endif; ?>

  <table class="w-full table-auto border-collapse">
    <thead>
      <tr class="bg-gray-100">
        <th class="px-4 py-2 text-left">SKU</th>
        <th class="px-4 py-2 text-left">Name</th>
        <th class="px-4 py-2 text-right">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <tr class="border-t">
        <td class="px-4 py-2"><?php echo e($product->sku); ?></td>
        <td class="px-4 py-2"><?php echo e($product->name); ?></td>
        <td class="px-4 py-2 text-right">
          <a href="<?php echo e(route('app.products.edit', $product)); ?>"
             class="mr-2 text-blue-600 hover:underline">Edit</a>
          <form action="<?php echo e(route('app.products.destroy', $product)); ?>"
                method="POST" class="inline">
            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button type="submit"
                    class="text-red-600 hover:underline"
                    onclick="return confirm('Delete this product?')">
              Delete
            </button>
          </form>
        </td>
      </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
  </table>

  <div class="mt-4">
    <?php echo e($products->links()); ?>

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
<?php /**PATH /Users/londo/Herd/test/resources/views/warehouse/products/index.blade.php ENDPATH**/ ?>