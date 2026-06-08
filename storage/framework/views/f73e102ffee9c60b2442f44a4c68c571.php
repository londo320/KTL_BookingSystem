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
    <div class="flex items-center justify-between">
      <div>
        <h2 class="font-semibold text-xl text-gray-800">Time Slots Management</h2>
        <p class="text-sm text-gray-600 mt-1">Manage available booking time slots across depots</p>
      </div>
      <div class="text-sm">
        <?php if(!$currentDepotId): ?>
          <span class="text-gray-600">Viewing: <span class="font-medium text-purple-600">All Depots</span></span>
          <span class="ml-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Actions Restricted</span>
        <?php else: ?>
          <?php $currentDepot = $allDepots->firstWhere('id', $currentDepotId); ?>
          <span class="text-gray-600">Viewing: <span class="font-medium text-blue-600"><?php echo e($currentDepot?->name ?? 'Unknown Depot'); ?></span></span>
          <?php if($currentDepotId == $defaultDepotId): ?>
            <span class="ml-2 text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Actions Enabled</span>
          <?php else: ?>
            <span class="ml-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">View Only</span>
          <?php endif; ?>
        <?php endif; ?>
      </div>
    </div>
   <?php $__env->endSlot(); ?>
<div class="py-6 max-w-7xl mx-auto">
  
  <?php if(session('success')): ?>
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
      <?php echo e(session('success')); ?>

    </div>
  <?php endif; ?>
  
  <?php if($currentDepotId == $defaultDepotId): ?>
    <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
      <h3 class="font-semibold text-gray-800 mb-3">Bulk Delete Options</h3>
      <div class="flex flex-wrap gap-3">
        
        <form method="POST" action="<?php echo e(route('app.slots.bulk-delete-by-date')); ?>" class="flex items-end gap-2" onsubmit="return confirm('Delete all empty slots on this date? Slots with bookings will be preserved.');">
          <?php echo csrf_field(); ?>
          <?php echo method_field('DELETE'); ?>
          <div>
            <label for="delete_date" class="block text-xs font-medium text-gray-700">Delete by Date</label>
            <input type="date" name="date" id="delete_date" class="border rounded px-2 py-1 text-sm" required>
          </div>
          <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 text-sm">
            🗑️ Delete Empty Slots
          </button>
        </form>

        <span class="text-gray-300 self-end pb-1">|</span>

        
        <div class="flex items-end gap-2">
          <button type="button" onclick="deleteSelected()" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 text-sm">
            🗑️ Delete Selected (<span id="selected-count">0</span>)
          </button>
          <button type="button" onclick="toggleSelectAll()" class="bg-gray-600 text-white px-3 py-1 rounded hover:bg-gray-700 text-sm">
            Select All Empty
          </button>
        </div>
      </div>
    </div>
  <?php endif; ?>

  
  <form method="GET" action="<?php echo e(route('app.slots.index')); ?>" class="flex flex-wrap gap-4 items-end mb-4">
    <div>
      <label for="depot" class="block text-sm font-medium">View</label>
      <select name="depot_id" id="depot" class="border rounded px-2 py-1">
        <option value="" <?php echo e(!$currentDepotId ? 'selected' : ''); ?>>All Depots (View Only)</option>
        <?php $__currentLoopData = $allDepots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($depot->id); ?>" <?php echo e($currentDepotId == $depot->id ? 'selected' : ''); ?>>
            <?php echo e($depot->name); ?> <?php echo e($depot->id == $defaultDepotId ? '(Default - Actions Enabled)' : '(View Only)'); ?>

          </option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
    <div>
      <label for="date" class="block text-sm font-medium">Date</label>
      <input type="date" name="date" id="date" class="border rounded px-2 py-1" value="<?php echo e(request('date')); ?>">
    </div>
    <div>
      <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Filter</button>
      <a href="<?php echo e(route('app.slots.index')); ?>" class="text-sm text-gray-600 ml-2 hover:underline">Clear Filters</a>
    </div>
    <div class="ml-auto flex gap-2">
      <?php if(request()->has('show_past')): ?>
        <a href="<?php echo e(route('app.slots.index', request()->except('show_past'))); ?>" class="text-sm text-blue-600 hover:underline">Hide Past Slots</a>
      <?php else: ?>
        <a href="<?php echo e(route('app.slots.index', array_merge(request()->all(), ['show_past' => true]))); ?>" class="text-sm text-blue-600 hover:underline">Show Past Slots</a>
      <?php endif; ?>
      <span class="text-gray-300">|</span>
      <?php if($groupedView): ?>
        <a href="<?php echo e(route('app.slots.index', request()->except('grouped'))); ?>" class="text-sm text-blue-600 hover:underline">📋 Detailed View</a>
      <?php else: ?>
        <a href="<?php echo e(route('app.slots.index', array_merge(request()->all(), ['grouped' => true]))); ?>" class="text-sm text-blue-600 hover:underline">📊 Grouped View</a>
      <?php endif; ?>
    </div>
  </form>

  <?php if($groupedView && $groupedSlots): ?>
    
    <div class="overflow-x-auto bg-white shadow rounded">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-2 text-left">Depot</th>
            <th class="px-4 py-2 text-left">Date & Time</th>
            <th class="px-4 py-2 text-left">Capacity</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $groupedSlots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="border-t hover:bg-gray-50">
              <td class="px-4 py-2"><?php echo e($group['depot_name']); ?></td>
              <td class="px-4 py-2">
                <div class="font-medium"><?php echo e(\Carbon\Carbon::parse($group['start_at'])->format('D d-M-Y')); ?></div>
                <div class="text-xs text-gray-600"><?php echo e($group['time']); ?></div>
              </td>
              <td class="px-4 py-2">
                <?php
                  $available = $group['total_capacity'] - $group['total_used'];
                  $percentage = $group['total_capacity'] > 0 ? ($group['total_used'] / $group['total_capacity']) * 100 : 0;
                ?>
                <div class="flex items-center gap-2">
                  <span class="font-medium <?php echo e($available > 0 ? 'text-green-600' : 'text-red-600'); ?>">
                    <?php echo e($group['total_used']); ?> / <?php echo e($group['total_capacity']); ?>

                  </span>
                  <div class="flex-1 h-2 bg-gray-200 rounded overflow-hidden max-w-[100px]">
                    <div class="h-full <?php echo e($percentage >= 100 ? 'bg-red-500' : ($percentage >= 75 ? 'bg-yellow-500' : 'bg-green-500')); ?>"
                         style="width: <?php echo e(min($percentage, 100)); ?>%"></div>
                  </div>
                </div>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="3" class="text-center py-4 text-gray-500">No slots found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  <?php else: ?>
    
    <div class="overflow-x-auto bg-white shadow rounded">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100">
          <tr>
            <?php if($currentDepotId == $defaultDepotId): ?>
              <th class="px-2 py-2 w-12">
                <input type="checkbox" id="select-all-header" onclick="toggleSelectAll()" class="h-4 w-4">
              </th>
            <?php endif; ?>
            <th class="px-4 py-2 text-left">Depot</th>
            <th class="px-4 py-2 text-left">Bay</th>
            <th class="px-4 py-2 text-left">Time</th>
            <th class="px-4 py-2 text-left">Capacity</th>
            <th class="px-4 py-2 text-left">Usage</th>
            <th class="px-4 py-2 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php $__empty_1 = true; $__currentLoopData = $slots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $slot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="border-t hover:bg-gray-50">
              <?php if($currentDepotId == $defaultDepotId): ?>
                <td class="px-2 py-2">
                  <?php if($slot->occupying_bookings_count == 0): ?>
                    <input type="checkbox" class="slot-checkbox h-4 w-4" value="<?php echo e($slot->id); ?>" onchange="updateSelectedCount()">
                  <?php else: ?>
                    <span class="text-gray-300" title="Has bookings">🔒</span>
                  <?php endif; ?>
                </td>
              <?php endif; ?>
              <td class="px-4 py-2"><?php echo e($slot->depot->name); ?></td>
              <td class="px-4 py-2">
                <?php if($slot->tippingBay): ?>
                  <span class="font-medium"><?php echo e($slot->tippingBay->name); ?></span>
                  <?php if($slot->tippingBay->code): ?>
                    <span class="text-xs text-gray-500">(<?php echo e($slot->tippingBay->code); ?>)</span>
                  <?php endif; ?>
                <?php else: ?>
                  <span class="text-gray-400">No Bay</span>
                <?php endif; ?>
              </td>
              <td class="px-4 py-2"><?php echo e(\Carbon\Carbon::parse($slot->start_at)->format('D d-M H:i')); ?></td>
              <td class="px-4 py-2"><?php echo e($slot->capacity); ?></td>
              <td class="px-4 py-2">
                <?php
                  $available = $slot->capacity - $slot->occupying_bookings_count;
                ?>
                <span class="<?php echo e($available > 0 ? 'text-green-600' : 'text-red-600'); ?> font-medium">
                  <?php echo e($slot->occupying_bookings_count); ?> / <?php echo e($slot->capacity); ?>

                </span>
              </td>
              <td class="px-4 py-2 space-x-2">
                <?php $canTakeAction = $slot->depot_id == $defaultDepotId; ?>
                <?php if($canTakeAction): ?>
                  <a href="<?php echo e(route('app.slots.edit', $slot)); ?>" class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-xs">Edit</a>
                  <form action="<?php echo e(route('app.slots.destroy', $slot)); ?>" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs">Delete</button>
                  </form>
                <?php else: ?>
                  <span class="px-2 py-1 bg-gray-300 text-gray-500 rounded text-xs cursor-not-allowed"
                        title="Actions only available for your default depot">Edit</span>
                  <span class="px-2 py-1 bg-gray-300 text-gray-500 rounded text-xs cursor-not-allowed"
                        title="Actions only available for your default depot">Delete</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
              <td colspan="<?php echo e($currentDepotId == $defaultDepotId ? 7 : 6); ?>" class="text-center py-4 text-gray-500">No slots found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>

  
  <?php if($currentDepotId == $defaultDepotId): ?>
    <script>
      function updateSelectedCount() {
        const count = document.querySelectorAll('.slot-checkbox:checked').length;
        document.getElementById('selected-count').textContent = count;
      }

      function toggleSelectAll() {
        const checkboxes = document.querySelectorAll('.slot-checkbox');
        const selectAllHeader = document.getElementById('select-all-header');
        const allChecked = selectAllHeader?.checked || false;

        checkboxes.forEach(cb => {
          cb.checked = !allChecked;
        });

        if (selectAllHeader) {
          selectAllHeader.checked = !allChecked;
        }

        updateSelectedCount();
      }

      function deleteSelected() {
        const selected = Array.from(document.querySelectorAll('.slot-checkbox:checked')).map(cb => cb.value);

        if (selected.length === 0) {
          alert('Please select slots to delete');
          return;
        }

        if (!confirm(`Delete ${selected.length} selected slot(s)? Only empty slots will be deleted.`)) {
          return;
        }

        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo e(route("app.slots.bulk-delete-selected")); ?>';

        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '<?php echo e(csrf_token()); ?>';
        form.appendChild(csrfToken);

        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);

        selected.forEach(id => {
          const input = document.createElement('input');
          input.type = 'hidden';
          input.name = 'slot_ids[]';
          input.value = id;
          form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
      }
    </script>
  <?php endif; ?>
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
<?php /**PATH /Users/londo/Herd/test/resources/views/admin/slots/index.blade.php ENDPATH**/ ?>