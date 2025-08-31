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
<?php $__env->startSection('content'); ?>
<div class="p-6 space-y-4">
  <a href="<?php echo e(route('app.slot-templates.create')); ?>"
     class="bg-blue-600 text-white px-4 py-2 rounded">New Template</a>
  <?php if(session('success')): ?>
    <div class="bg-green-100 text-green-800 p-2 rounded"><?php echo e(session('success')); ?></div>
  <?php endif; ?>
  <table class="min-w-full border">
    <thead>
      <tr class="bg-gray-200">
        <th class="p-2">Depot</th>
        <th class="p-2">Weekday</th>
        <th class="p-2">Start</th>
        <th class="p-2">Type</th>
        <th class="p-2">Length</th>
        <th class="p-2">Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tpl): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <tr class="border-t">
        <td class="p-2"><?php echo e($tpl->depot->name); ?></td>
        <td class="p-2"><?php echo e(\Illuminate\Support\Str::ucfirst(
            \Carbon\Carbon::create()->startOfWeek()->addDays($tpl->weekday)->format('l')
          )); ?></td>
        <td class="p-2"><?php echo e(\Carbon\Carbon::parse($tpl->start_time)->format('H:i')); ?></td>
        <td class="p-2"><?php echo e($tpl->bookingType->name); ?></td>
        <td class="p-2"><?php echo e($tpl->default_length); ?>m</td>
        <td class="p-2 space-x-2">
          <a href="<?php echo e(route('app.slot-templates.edit',$tpl)); ?>"
             class="text-blue-600">Edit</a>
          <form method="POST" action="<?php echo e(route('app.slot-templates.destroy',$tpl)); ?>"
                class="inline"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
            <button class="text-red-600"
                    onclick="return confirm('Really delete?')">Del</button>
          </form>
        </td>
      </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
  </table>
</div>
<?php $__env->stopSection(); ?>
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
<?php /**PATH /Users/londo/Herd/test/resources/views/admin/slot-templates/oldindex.blade.php ENDPATH**/ ?>