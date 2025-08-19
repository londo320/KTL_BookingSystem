<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <?php echo $__env->make('layouts.admin-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="max-w-4xl mx-auto py-6">
        <h2 class="text-xl font-bold mb-4">Depot Products for <?php echo e($depot->name); ?></h2>

        <form method="POST" action="<?php echo e(route('admin.depots.products.store', $depot)); ?>" class="mb-6 bg-white p-4 rounded shadow space-y-4">
            <?php echo csrf_field(); ?>
            <div>
                <label class="block font-medium">Product (SKU)</label>
                <select name="product_id" class="border p-2 w-full">
                    <?php $__currentLoopData = $allProducts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($product->id); ?>"><?php echo e($product->sku); ?> — <?php echo e($product->description); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block">Min Cases</label>
                    <input type="number" name="min_cases" class="w-full border p-2" min="0">
                </div>
                <div>
                    <label class="block">Max Cases</label>
                    <input type="number" name="max_cases" class="w-full border p-2" min="0">
                </div>
                <div>
                    <label class="block">Override Duration (minutes)</label>
                    <input type="number" name="duration_override_minutes" class="w-full border p-2" min="0" step="15">
                </div>
            </div>

            <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                ➕ Add Product to Depot
            </button>
        </form>

        <table class="w-full text-sm bg-white shadow rounded">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="p-2">SKU</th>
                    <th class="p-2">Description</th>
                    <th class="p-2">Min</th>
                    <th class="p-2">Max</th>
                    <th class="p-2">Override</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $depot->products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr class="border-t">
                        <td class="p-2"><?php echo e($product->sku); ?></td>
                        <td class="p-2"><?php echo e($product->description); ?></td>
                        <td class="p-2"><?php echo e($product->pivot->min_cases); ?></td>
                        <td class="p-2"><?php echo e($product->pivot->max_cases); ?></td>
                        <td class="p-2"><?php echo e($product->pivot->duration_override_minutes ?? '—'); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>
    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH /Users/londo/Herd/test/resources/views/admin/depots/products.blade.php ENDPATH**/ ?>