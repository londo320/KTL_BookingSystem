<?php $__env->startSection('content'); ?>
<div class="p-6 bg-white rounded-lg shadow">
  <div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-semibold">Products</h1>
    <a href="<?php echo e(route('app.products.create')); ?>"
       class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
      + New Product
    </a>
  </div>

  
  <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
    <form method="GET" action="<?php echo e(route('app.products.index')); ?>" class="flex gap-3 items-end">
      <div class="flex-1">
        <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">Filter by Customer</label>
        <select name="customer_id" id="customer_id" class="block w-full border-gray-300 rounded text-sm py-2" onchange="this.form.submit()">
          <option value="">All Customers</option>
          <?php $__currentLoopData = \App\Models\Customer::orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($customer->id); ?>" <?php if(request('customer_id') == $customer->id): echo 'selected'; endif; ?>>
              <?php echo e($customer->name); ?>

            </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
      <div>
        <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
          Filter
        </button>
      </div>
      <?php if(request('customer_id')): ?>
        <div>
          <a href="<?php echo e(route('app.products.index')); ?>" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
            Clear Filter
          </a>
        </div>
      <?php endif; ?>
    </form>
  </div>

  <?php if(session('success')): ?>
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
      <?php echo e(session('success')); ?>

    </div>
  <?php endif; ?>

  <table class="w-full table-auto border-collapse">
    <thead>
      <tr class="bg-gray-100">
        <th class="px-4 py-2 text-left">Customer</th>
        <th class="px-4 py-2 text-left">SKU</th>
        <th class="px-4 py-2 text-left">Description</th>
        <th class="px-4 py-2 text-left">Type</th>
        <th class="px-4 py-2 text-center">Cases/Pallet</th>
        <th class="px-4 py-2 text-right">Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <tr class="border-t hover:bg-gray-50">
        <td class="px-4 py-2">
          <span class="font-medium text-gray-900"><?php echo e($product->customer->name ?? 'N/A'); ?></span>
        </td>
        <td class="px-4 py-2">
          <span class="font-mono text-sm"><?php echo e($product->sku); ?></span>
        </td>
        <td class="px-4 py-2 text-gray-700"><?php echo e($product->description); ?></td>
        <td class="px-4 py-2">
          <span class="px-2 py-1 text-xs rounded <?php echo e($product->product_type === 'finished_product' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800'); ?>">
            <?php echo e(ucfirst(str_replace('_', ' ', $product->product_type))); ?>

          </span>
        </td>
        <td class="px-4 py-2 text-center"><?php echo e($product->cases_per_pallet ?? '-'); ?></td>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/products/index.blade.php ENDPATH**/ ?>