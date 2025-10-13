<?php $__env->startSection('content'); ?>
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Edit Product</h1>
            <a href="<?php echo e(route('app.products.index')); ?>" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                ← Back to Products
            </a>
        </div>

        <div class="bg-white shadow-md rounded-lg p-6">
            <form action="<?php echo e(route('app.products.update', $product)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <?php echo $__env->make('admin.products.form', ['product' => $product], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

                <div class="mt-6 flex justify-end gap-3">
                    <a href="<?php echo e(route('app.products.index')); ?>" class="px-6 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Update Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/products/edit.blade.php ENDPATH**/ ?>