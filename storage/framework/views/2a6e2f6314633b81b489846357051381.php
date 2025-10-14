<?php $__env->startSection('content'); ?>
<div class="p-6 bg-white rounded-lg shadow">
    <h1 class="text-2xl font-semibold mb-4">Add New Rule</h1>
    <form action="<?php echo e(route('app.customer-depot-products.store')); ?>" method="POST">
        <?php echo $__env->make('admin.customer_depot_products.form', ['rule' => null], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/customer_depot_products/create.blade.php ENDPATH**/ ?>