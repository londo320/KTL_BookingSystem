  
  <?php echo $__env->make('layouts.admin-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<?php $__env->startSection('content'); ?>
<div class="p-6 bg-white rounded-lg shadow">
  <h1 class="text-2xl font-semibold mb-4">Products</h1>
  <a href="<?php echo e(route('admin.products.create')); ?>"
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
          <a href="<?php echo e(route('admin.products.edit', $product)); ?>"
             class="mr-2 text-blue-600 hover:underline">Edit</a>
          <form action="<?php echo e(route('admin.products.destroy', $product)); ?>"
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/products/index.blade.php ENDPATH**/ ?>