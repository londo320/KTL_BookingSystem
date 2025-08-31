<?php $__env->startSection('content'); ?>
<div class="p-6 bg-white rounded-lg shadow">
  <h1 class="text-2xl font-semibold mb-4">Add New Product</h1>
  <form action="<?php echo e(route('app.products.store')); ?>" method="POST">
    <?php echo $__env->make('admin.products.form', ['product' => new \App\Models\Product], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/products/create.blade.php ENDPATH**/ ?>