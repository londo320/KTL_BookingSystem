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
<?php /**PATH /Users/londo/Herd/test/resources/views/warehouse/slot-templates/oldindex.blade.php ENDPATH**/ ?>