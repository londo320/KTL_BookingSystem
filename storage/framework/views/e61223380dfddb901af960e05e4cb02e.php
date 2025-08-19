<?php $__env->startSection('content'); ?>
<div class="p-6 bg-white rounded-lg shadow">
    <h1 class="text-2xl font-semibold mb-4">Customer-Depot-Product Rules</h1>

    <a href="<?php echo e(route('admin.customer-depot-products.create')); ?>"
       class="inline-block mb-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        + New Rule
    </a>

    <?php if(session('success')): ?>
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
            <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <table class="w-full table-auto border-collapse">
        <thead>
            <tr class="bg-gray-100">
                <th class="px-4 py-2 text-left">Customer</th>
                <th class="px-4 py-2 text-left">Depot</th>
                <th class="px-4 py-2 text-left">Product</th>
                <th class="px-4 py-2 text-center">Min Cases</th>
                <th class="px-4 py-2 text-center">Max Cases</th>
                <th class="px-4 py-2 text-center">Override (min)</th>
                <th class="px-4 py-2 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="border-t">
                <td class="px-4 py-2"><?php echo e($rule->customer->name); ?></td>
                <td class="px-4 py-2"><?php echo e($rule->depot->name); ?></td>
                <td class="px-4 py-2"><?php echo e($rule->product->sku); ?></td>
                <td class="px-4 py-2 text-center"><?php echo e($rule->min_cases ?? '–'); ?></td>
                <td class="px-4 py-2 text-center"><?php echo e($rule->max_cases ?? '–'); ?></td>
                <td class="px-4 py-2 text-center"><?php echo e($rule->override_duration_minutes ?? '–'); ?></td>
                <td class="px-4 py-2 text-right">
                    <a href="<?php echo e(route('admin.customer-depot-products.edit', $rule)); ?>"
                       class="mr-2 text-blue-600 hover:underline">Edit</a>
                    <form action="<?php echo e(route('admin.customer-depot-products.destroy', $rule)); ?>"
                          method="POST" class="inline">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit"
                                class="text-red-600 hover:underline"
                                onclick="return confirm('Delete this rule?')">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/customer_depot_products/index.blade.php ENDPATH**/ ?>