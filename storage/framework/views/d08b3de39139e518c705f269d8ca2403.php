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

   <?php $__env->slot('header', null, []); ?> 
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl">Pallet Types Management</h2>
      <div class="flex space-x-2">
        <button onclick="openAddModal()" 
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
          ➕ Add Pallet Type
        </button>
        <a href="<?php echo e(route('admin.settings.dashboard')); ?>"
           class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
          Back to Settings
        </a>
      </div>
    </div>
   <?php $__env->endSlot(); ?>

  <div class="py-6 max-w-7xl mx-auto">
    
    
    <?php if(session('success')): ?>
      <div class="mb-6 p-4 bg-green-100 border border-green-300 rounded-lg">
        <p class="text-green-800"><?php echo e(session('success')); ?></p>
      </div>
    <?php endif; ?>
    
    <?php if($errors->any()): ?>
      <div class="mb-6 p-4 bg-red-100 border border-red-300 rounded-lg">
        <ul class="text-red-800">
          <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li><?php echo e($error); ?></li>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
      </div>
    <?php endif; ?>

    
    <div class="bg-white rounded-lg shadow">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">📦 Pallet Types</h3>
        <p class="text-sm text-gray-600 mt-1">Manage pallet types used in bookings and tipping operations</p>
      </div>
      
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Code</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <?php $__empty_1 = true; $__currentLoopData = $palletTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $palletType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
              <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="font-medium text-gray-900"><?php echo e($palletType->name); ?></div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span class="font-mono bg-gray-100 px-2 py-1 rounded text-sm"><?php echo e($palletType->code); ?></span>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm text-gray-600"><?php echo e($palletType->description ?: 'No description'); ?></div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <?php if($palletType->is_active): ?>
                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Active</span>
                  <?php else: ?>
                    <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded">Inactive</span>
                  <?php endif; ?>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex space-x-2">
                    <button onclick="openEditModal(<?php echo e($palletType->id); ?>, '<?php echo e(addslashes($palletType->name)); ?>', '<?php echo e($palletType->code); ?>', '<?php echo e(addslashes($palletType->description)); ?>', <?php echo e($palletType->is_active ? 'true' : 'false'); ?>)"
                            class="text-blue-600 hover:text-blue-900">Edit</button>
                    <button onclick="confirmDelete(<?php echo e($palletType->id); ?>, '<?php echo e(addslashes($palletType->name)); ?>')"
                            class="text-red-600 hover:text-red-900">Delete</button>
                  </div>
                </td>
              </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
              <tr>
                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                  No pallet types found. Click "Add Pallet Type" to create your first pallet type.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  
  <div id="addModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
      <h3 class="text-lg font-semibold mb-4">Add New Pallet Type</h3>
      <form action="<?php echo e(route('admin.settings.pallet-types.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
            <input type="text" name="name" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="Euro Pallet">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Code *</label>
            <input type="text" name="code" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="EUR">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                      placeholder="Optional description..."></textarea>
          </div>
          <div>
            <label class="flex items-center">
              <input type="checkbox" name="is_active" value="1" checked
                     class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
              <span class="ml-2 text-sm text-gray-700">Active</span>
            </label>
          </div>
        </div>
        <div class="flex justify-end space-x-3 mt-6">
          <button type="button" onclick="closeAddModal()"
                  class="px-4 py-2 text-gray-600 border border-gray-300 rounded hover:bg-gray-50">
            Cancel
          </button>
          <button type="submit"
                  class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Create Pallet Type
          </button>
        </div>
      </form>
    </div>
  </div>

  
  <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
      <h3 class="text-lg font-semibold mb-4">Edit Pallet Type</h3>
      <form id="editForm" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <div class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
            <input type="text" id="editName" name="name" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Code *</label>
            <input type="text" id="editCode" name="code" required
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
            <textarea id="editDescription" name="description" rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
          </div>
          <div>
            <label class="flex items-center">
              <input type="checkbox" id="editActive" name="is_active" value="1"
                     class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
              <span class="ml-2 text-sm text-gray-700">Active</span>
            </label>
          </div>
        </div>
        <div class="flex justify-end space-x-3 mt-6">
          <button type="button" onclick="closeEditModal()"
                  class="px-4 py-2 text-gray-600 border border-gray-300 rounded hover:bg-gray-50">
            Cancel
          </button>
          <button type="submit"
                  class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Update Pallet Type
          </button>
        </div>
      </form>
    </div>
  </div>

  
  <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
      <h3 class="text-lg font-semibold mb-4 text-red-800">Delete Pallet Type</h3>
      <p class="text-gray-700 mb-6">
        Are you sure you want to delete "<span id="deleteName" class="font-semibold"></span>"? 
        This action cannot be undone.
      </p>
      <form id="deleteForm" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('DELETE'); ?>
        <div class="flex justify-end space-x-3">
          <button type="button" onclick="closeDeleteModal()"
                  class="px-4 py-2 text-gray-600 border border-gray-300 rounded hover:bg-gray-50">
            Cancel
          </button>
          <button type="submit"
                  class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
            Delete Pallet Type
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Add Modal Functions
    function openAddModal() {
      document.getElementById('addModal').classList.remove('hidden');
      document.getElementById('addModal').classList.add('flex');
    }

    function closeAddModal() {
      document.getElementById('addModal').classList.add('hidden');
      document.getElementById('addModal').classList.remove('flex');
    }

    // Edit Modal Functions
    function openEditModal(id, name, code, description, isActive) {
      document.getElementById('editForm').action = `/admin/settings/pallet-types/${id}`;
      document.getElementById('editName').value = name;
      document.getElementById('editCode').value = code;
      document.getElementById('editDescription').value = description;
      document.getElementById('editActive').checked = isActive;
      
      document.getElementById('editModal').classList.remove('hidden');
      document.getElementById('editModal').classList.add('flex');
    }

    function closeEditModal() {
      document.getElementById('editModal').classList.add('hidden');
      document.getElementById('editModal').classList.remove('flex');
    }

    // Delete Modal Functions
    function confirmDelete(id, name) {
      document.getElementById('deleteForm').action = `/admin/settings/pallet-types/${id}`;
      document.getElementById('deleteName').textContent = name;
      
      document.getElementById('deleteModal').classList.remove('hidden');
      document.getElementById('deleteModal').classList.add('flex');
    }

    function closeDeleteModal() {
      document.getElementById('deleteModal').classList.add('hidden');
      document.getElementById('deleteModal').classList.remove('flex');
    }

    // Close modals when clicking outside
    document.addEventListener('click', function(e) {
      if (e.target.id === 'addModal') closeAddModal();
      if (e.target.id === 'editModal') closeEditModal();
      if (e.target.id === 'deleteModal') closeDeleteModal();
    });
  </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/admin/settings/pallet-types.blade.php ENDPATH**/ ?>