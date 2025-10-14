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
    <h2 class="text-xl font-semibold">🛠 Admin Settings Panel</h2>
   <?php $__env->endSlot(); ?>
  <div class="max-w-4xl mx-auto py-6 space-y-6">
    <?php if(session('success')): ?>
      <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded">
        <?php echo e(session('success')); ?>

      </div>
    <?php endif; ?>
    
    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-semibold text-gray-800 mb-4">🚛 Tipping Workflow Settings</h3>
      <form method="POST" action="<?php echo e(route('app.settings.tipping-workflow')); ?>">
        <?php echo csrf_field(); ?>
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
          <div>
            <h4 class="font-medium text-gray-800">Enable Tipping Workflow</h4>
            <p class="text-sm text-gray-600 mt-1">
              When enabled, enforces the structured tipping workflow process. 
              When disabled, allows manual bay assignments without workflow enforcement.
            </p>
          </div>
          <div class="flex items-center">
            <input type="hidden" name="tipping_workflow_enabled" value="0">
            <input 
              type="checkbox" 
              name="tipping_workflow_enabled" 
              value="1"
              <?php echo e($tippingWorkflowEnabled ? 'checked' : ''); ?>

              onchange="this.form.submit()"
              class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
            >
            <span class="ml-2 text-sm font-medium text-gray-700">
              <?php echo e($tippingWorkflowEnabled ? 'Enabled' : 'Disabled'); ?>

            </span>
          </div>
        </div>
      </form>
    </div>

    
    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-semibold text-gray-800 mb-4">🔧 Module Management</h3>
      <p class="text-sm text-gray-600 mb-4">
        Enable or disable system modules. Disabled modules will be completely hidden from navigation and inaccessible.
      </p>
      
      <div class="space-y-4">
        
        <form method="POST" action="<?php echo e(route('app.settings.inbound-module')); ?>">
          <?php echo csrf_field(); ?>
          <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
            <div>
              <h4 class="font-medium text-gray-800">Inbound Operations</h4>
              <p class="text-sm text-gray-600 mt-1">
                Container bookings, factory bookings, tipping workflow, and all existing inbound processes.
              </p>
            </div>
            <div class="flex items-center">
              <input type="hidden" name="inbound_module_enabled" value="0">
              <input 
                type="checkbox" 
                name="inbound_module_enabled" 
                value="1"
                <?php echo e($inboundModuleEnabled ? 'checked' : ''); ?>

                onchange="this.form.submit()"
                class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500"
              >
              <span class="ml-2 text-sm font-medium text-gray-700">
                <?php echo e($inboundModuleEnabled ? 'Enabled' : 'Disabled'); ?>

              </span>
            </div>
          </div>
        </form>

        
        <form method="POST" action="<?php echo e(route('app.settings.outbound-module')); ?>">
          <?php echo csrf_field(); ?>
          <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
            <div>
              <h4 class="font-medium text-gray-800">Outbound Operations</h4>
              <p class="text-sm text-gray-600 mt-1">
                Load management, WMS file imports, driver arrivals, customer address management, and delivery scheduling.
              </p>
            </div>
            <div class="flex items-center">
              <input type="hidden" name="outbound_module_enabled" value="0">
              <input 
                type="checkbox" 
                name="outbound_module_enabled" 
                value="1"
                <?php echo e($outboundModuleEnabled ? 'checked' : ''); ?>

                onchange="this.form.submit()"
                class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
              >
              <span class="ml-2 text-sm font-medium text-gray-700">
                <?php echo e($outboundModuleEnabled ? 'Enabled' : 'Disabled'); ?>

              </span>
            </div>
          </div>
        </form>
      </div>
    </div>

    
    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-semibold text-gray-800 mb-4">👥 User Approval Settings</h3>
      <p class="text-sm text-gray-600 mb-4">
        Configure email notifications for new user registration approvals.
      </p>
      
      <form method="POST" action="<?php echo e(route('app.settings.admin-approval-emails')); ?>">
        <?php echo csrf_field(); ?>
        <div class="space-y-4">
          <div>
            <label for="admin_approval_emails" class="block text-sm font-medium text-gray-700 mb-2">
              Admin Approval Email Addresses
            </label>
            <textarea 
              name="admin_approval_emails" 
              id="admin_approval_emails"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              placeholder="admin@knowleslogistics.com, manager@knowleslogistics.com"
            ><?php echo e($adminApprovalEmails); ?></textarea>
            <p class="mt-1 text-sm text-gray-500">
              Enter email addresses separated by commas. These admins will receive notifications when new users register and need approval.
            </p>
          </div>
          <div class="flex justify-end">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
              Save Email Settings
            </button>
          </div>
        </div>
      </form>
    </div>

    
    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-semibold text-gray-800 mb-4">🗺️ Depot Map Management</h3>
      <p class="text-sm text-gray-600 mb-4">
        Configure and manage interactive depot maps showing real-time bay status and positions.
      </p>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="<?php echo e(route('app.depot-map.index')); ?>" 
           class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
          <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
            🗺️
          </div>
          <div class="ml-4">
            <h4 class="font-medium text-gray-900">View Depot Map</h4>
            <p class="text-sm text-gray-500">See live bay status and operations</p>
          </div>
        </a>
        <?php if($depots->count() > 0): ?>
        <a href="<?php echo e(route('app.depot-map.manage-positions', $depots->first())); ?>" 
           class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
        <?php else: ?>
        <div class="flex items-center p-4 border border-gray-200 rounded-lg bg-gray-100 opacity-50">
        <?php endif; ?>
          <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
            🎯
          </div>
          <div class="ml-4">
            <h4 class="font-medium text-gray-900">Position Bays</h4>
            <p class="text-sm text-gray-500"><?php echo e($depots->count() > 0 ? 'Drag and drop bay positions on map' : 'No depots configured'); ?></p>
          </div>
        <?php if($depots->count() > 0): ?>
        </a>
        <?php else: ?>
        </div>
        <?php endif; ?>
        <a href="<?php echo e(route('app.depots.index')); ?>" 
           class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
          <div class="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
            🏭
          </div>
          <div class="ml-4">
            <h4 class="font-medium text-gray-900">Manage Depots</h4>
            <p class="text-sm text-gray-500">Upload map files and depot settings</p>
          </div>
        </a>
        <a href="<?php echo e(route('app.tipping-bays.index')); ?>" 
           class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
          <div class="flex-shrink-0 w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
            🚛
          </div>
          <div class="ml-4">
            <h4 class="font-medium text-gray-900">Manage Bays</h4>
            <p class="text-sm text-gray-500">Configure tipping bay settings</p>
          </div>
        </a>
      </div>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
      <a href="<?php echo e(route('app.depots.index')); ?>" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        📦 Manage Depots
      </a>
      <a href="<?php echo e(route('app.booking-types.index')); ?>" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        🧱 Manage Booking Types
      </a>
      <a href="<?php echo e(route('app.slot-templates.index')); ?>" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        🕒 Slot Duration Rules (Handball etc.)
      </a>
      <a href="<?php echo e(route('app.slot-capacity.index')); ?>" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        ⚙️ Slot Generation Rules
      </a>
      <a href="<?php echo e(route('app.slots.generate.form')); ?>" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        🧮 Generate Slots
      </a>
      <a href="<?php echo e(route('app.test-email.index')); ?>" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        📧 Test Password Reset Emails
      </a>
      <a href="<?php echo e(route('app.slot-usage.index')); ?>" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        📊 Slot Usage Viewer
      </a>
      <a href="<?php echo e(route('app.products.index')); ?>" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        📦 Products
      </a>
      <a href="<?php echo e(route('app.users.index')); ?>" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        👥 Users Settings
      </a>
      <a href="<?php echo e(route('app.customers.index')); ?>" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        👥 Customer Settings
      </a>
      <a href="<?php echo e(route('app.slotReleaseRules.index')); ?>" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        ⚙️ Slot Rules Config
      </a>
      <a href="<?php echo e(route('app.settings.pallet-types')); ?>" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        📦 Pallet Types
      </a>
      <a href="<?php echo e(route('app.trailer-types.index')); ?>" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        🚛 Trailer Types
      </a>
      <a href="<?php echo e(route('app.carriers.index')); ?>" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        🚚 Carrier Management
      </a>
      <a href="<?php echo e(route('app.tipping-locations.index')); ?>" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        📍 Tipping Locations
      </a>
      <a href="<?php echo e(route('app.tipping-bays.index')); ?>" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        🏗️ Tipping Bays
      </a>
      <a href="<?php echo e(route('app.depot-map.index')); ?>" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        🗺️ Depot Map View
      </a>
      <?php if($depots->count() > 0): ?>
      <a href="<?php echo e(route('app.depot-map.manage-positions', $depots->first())); ?>" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        🎯 Position Bays on Map
      </a>
      <?php else: ?>
      <div class="block p-4 bg-gray-100 shadow rounded opacity-50">
        🎯 Position Bays on Map (No depots)
      </div>
      <?php endif; ?>
      <a href="<?php echo e(route('app.arrival-time-settings.index')); ?>" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        🕐 Arrival Time Rules
      </a>
<?php if($depots->count()): ?>
  <div class="col-span-2 mt-6">
    <h3 class="text-lg font-semibold mb-2">🔁 Customer Depot Product Rules</h3>
    <p class="text-sm text-gray-600 mb-2">
      <a href="<?php echo e(route('app.customer-depot-products.index')); ?>" class="text-blue-600 hover:underline">
        Manage Customer-Depot-Product relationships
      </a>
    </p>
  </div>
<?php endif; ?>
    </div>
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
<?php /**PATH /Users/londo/Herd/test/resources/views/admin/settings/dashboard.blade.php ENDPATH**/ ?>