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
   <?php $__env->slot('header', null, []); ?> 
    <h2 class="text-xl font-semibold">🛠 Manage Products for Depot: <?php echo e($depot->name); ?></h2>
   <?php $__env->endSlot(); ?>

  <div class="max-w-5xl mx-auto py-6 space-y-6">

    <?php if(session('success')): ?>
      <div class="bg-green-100 p-3 rounded text-green-800"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <form method="POST" action="<?php echo e(route('app.depots.products.update', $depot)); ?>">
      <?php echo csrf_field(); ?>

      <table class="min-w-full text-sm border">
        <thead>
          <tr class="bg-gray-100">
            <th class="p-2 text-left">SKU</th>
            <th class="p-2 text-left">Description</th>
            <th class="p-2 text-center">Min Cases</th>
            <th class="p-2 text-center">Max Cases</th>
            <th class="p-2 text-center">Duration Override (mins)</th>
            <th class="p-2 text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr class="border-t">
              <td class="p-2"><?php echo e($product->sku); ?></td>
              <td class="p-2"><?php echo e($product->description); ?></td>

              <td class="p-2 text-center">
                <input type="number" name="products[<?php echo e($product->id); ?>][min_cases]" class="border p-1 w-20 text-center"
                       value="<?php echo e(old("products.{$product->id}.min_cases", $assigned[$product->id]->pivot->min_cases ?? '')); ?>">
              </td>

              <td class="p-2 text-center">
                <input type="number" name="products[<?php echo e($product->id); ?>][max_cases]" class="border p-1 w-20 text-center"
                       value="<?php echo e(old("products.{$product->id}.max_cases", $assigned[$product->id]->pivot->max_cases ?? '')); ?>">
              </td>

              <td class="p-2 text-center">
                <input type="number" name="products[<?php echo e($product->id); ?>][duration_override_minutes]" class="border p-1 w-24 text-center"
                       value="<?php echo e(old("products.{$product->id}.duration_override_minutes", $assigned[$product->id]->pivot->duration_override_minutes ?? '')); ?>">
              </td>

              <td class="p-2 text-center">
                <?php if(isset($assigned[$product->id])): ?>
                  <form method="POST" action="<?php echo e(route('app.depots.products.destroy', [$depot, $product])); ?>"
                        onsubmit="return confirm('Remove this product from depot?');" class="inline-block">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button class="text-red-600 hover:underline text-xs" type="submit">Remove</button>
                  </form>
                <?php else: ?>
                  <span class="text-gray-400 text-xs">Not assigned</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
      </table>

      <div class="mt-4">
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">💾 Save Changes</button>
      </div>
    </form>
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
<?php /**PATH /Users/londo/Herd/test/resources/views/admin/depots/products/index.blade.php ENDPATH**/ ?>