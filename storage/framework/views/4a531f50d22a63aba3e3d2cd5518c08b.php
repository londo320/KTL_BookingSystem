<nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 mb-6">
  <div class="flex justify-between items-center px-6 py-3">
    <ul class="flex space-x-4">
    <li>
      <a href="<?php echo e(route('app.dashboard')); ?>"
         class="px-3 py-1 rounded <?php echo e(request()->routeIs('app.dashboard') ? 'bg-blue-500 text-white' : 'text-gray-700 dark:text-gray-300'); ?>">
        Dashboard
      </a>
    </li>
    <?php if (\Illuminate\Support\Facades\Blade::check('canFunction', 'slots.view')): ?>
    <li>
      <a href="<?php echo e(route('app.slots.index')); ?>"
         class="px-3 py-1 rounded <?php echo e(request()->routeIs('app.slots.*') ? 'bg-blue-500 text-white' : 'text-gray-700 dark:text-gray-300'); ?>">
        Slots
      </a>
    </li>
    <?php endif; ?>
    <?php if (\Illuminate\Support\Facades\Blade::check('hasAnyFunction', ['bookings.view', 'bookings.view-streamlined'])): ?>
    <li class="relative">
      <?php
        $routePrefix = 'app.';
      ?>
      <button onclick="toggleBookingsDropdown()" 
              class="px-3 py-1 rounded flex items-center <?php echo e(request()->routeIs('app.bookings.*') && !request()->routeIs('app.customer-behavior.*') ? 'bg-blue-500 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100'); ?>">
        📋 Bookings 
        <svg id="bookings-arrow" class="ml-1 w-4 h-4 transform transition-transform" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
      </button>
      <div id="bookings-dropdown" class="absolute left-0 mt-2 w-56 bg-white border border-gray-200 rounded-md shadow-lg z-20 hidden">
        <?php if (\Illuminate\Support\Facades\Blade::check('canFunction', 'bookings.view')): ?>
        <a href="<?php echo e(route($routePrefix . 'bookings.index')); ?>" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo e(request()->routeIs('app.bookings.index') || (request()->routeIs('app.bookings.*') && !request()->routeIs('app.bookings.streamlined')) ? 'bg-blue-50 text-blue-600' : ''); ?>">
          📊 Bookings (Full View)
          <div class="text-xs text-gray-500 mt-1">Complete booking management with all features</div>
        </a>
        <?php endif; ?>
        <?php if (\Illuminate\Support\Facades\Blade::check('canFunction', 'bookings.view-streamlined')): ?>
        <a href="<?php echo e(route($routePrefix . 'bookings.streamlined')); ?>" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo e(request()->routeIs('app.bookings.streamlined') ? 'bg-blue-50 text-blue-600' : ''); ?>">
          ⚡ Bookings (Live View)
          <div class="text-xs text-gray-500 mt-1">Streamlined interface with live updates</div>
        </a>
        <a href="<?php echo e(route($routePrefix . 'bookings.streamlined')); ?>" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo e(request()->routeIs('app.bookings.streamlined') ? 'bg-blue-50 text-blue-600' : ''); ?>">
          ⚡ Bookings (Test View)
          <div class="text-xs text-gray-500 mt-1">Live Arrivals another Test View</div>
        </a>
        <?php endif; ?>
      </div>
    </li>
    <?php endif; ?>
    <li>
      <a href="<?php echo e(route('app.customer-behavior.index')); ?>"
         class="px-3 py-1 rounded <?php echo e(request()->routeIs('app.customer-behavior.*') ? 'bg-purple-500 text-white' : 'text-gray-700 dark:text-gray-300'); ?>">
        Customer Analysis
      </a>
    </li>
    <li>
      <?php
        $depotMapPrefix = 'app.';
      ?>
      <a href="<?php echo e(route($depotMapPrefix . 'depot-map.index')); ?>"
         class="px-3 py-1 rounded <?php echo e(request()->routeIs('app.depot-map.*') ? 'bg-green-500 text-white' : 'text-gray-700 dark:text-gray-300'); ?>">
        🗺️ Depot Map
      </a>
    </li>
    <li class="relative">
      <button onclick="toggleTippingDropdown()" 
              class="px-3 py-1 rounded flex items-center <?php echo e(request()->routeIs('app.tipping-*') ? 'bg-orange-500 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100'); ?>">
        🚛 Tipping 
        <svg id="tipping-arrow" class="ml-1 w-4 h-4 transform transition-transform" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
      </button>
      <div id="tipping-dropdown" class="absolute left-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg z-20 hidden">
        <a href="<?php echo e(route('app.tipping-workflow.dashboard')); ?>" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo e(request()->routeIs('app.tipping-workflow.*') ? 'bg-orange-50 text-orange-600' : ''); ?>">
          📊 Dashboard
        </a>
        <a href="<?php echo e(route('app.tipping-locations.index')); ?>" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo e(request()->routeIs('app.tipping-locations.*') ? 'bg-orange-50 text-orange-600' : ''); ?>">
          📍 Drop Locations
        </a>
        <a href="<?php echo e(route('app.tipping-bays.index')); ?>" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo e(request()->routeIs('app.tipping-bays.*') ? 'bg-orange-50 text-orange-600' : ''); ?>">
          🚛 Tipping Bays
        </a>
        <?php
          $mapRoutePrefix = 'app.';
        ?>
        <a href="<?php echo e(route($mapRoutePrefix . 'depot-map.index')); ?>" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo e(request()->routeIs('app.depot-map.*') ? 'bg-orange-50 text-orange-600' : ''); ?>">
          🗺️ Depot Map
        </a>
        <div class="border-t border-gray-100 my-1"></div>
        <?php
          $tippingRoutePrefix = 'app.';
        ?>
        <a href="<?php echo e(route($tippingRoutePrefix . 'queue-management')); ?>" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo e(request()->routeIs('app.queue-management') ? 'bg-orange-50 text-orange-600' : ''); ?>">
          ⚡ Queue Management
        </a>
        <a href="<?php echo e(route($tippingRoutePrefix . 'operations-control')); ?>" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo e(request()->routeIs('app.operations-control') ? 'bg-orange-50 text-orange-600' : ''); ?>">
          🎯 Site Operations Control
        </a>
        <a href="<?php echo e(route($tippingRoutePrefix . 'trailer-operations-dashboard')); ?>" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo e(request()->routeIs('app.trailer-operations-dashboard') ? 'bg-orange-50 text-orange-600' : ''); ?>">
          🚛 Trailer Operations Dashboard
        </a>
        <a href="<?php echo e(route($tippingRoutePrefix . 'trailer-location-report')); ?>" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo e(request()->routeIs('app.trailer-location-report') ? 'bg-orange-50 text-orange-600' : ''); ?>">
          📍 Trailer Location Report
        </a>
        <a href="<?php echo e(route($tippingRoutePrefix . 'empty-unit-collection')); ?>" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo e(request()->routeIs('app.empty-unit-collection*') ? 'bg-orange-50 text-orange-600' : ''); ?>">
          🚛 Empty Unit Collection
        </a>
        <div class="border-t border-gray-100 my-1"></div>
        <a href="<?php echo e(route('app.tipping-guide')); ?>" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo e(request()->routeIs('app.tipping-guide') ? 'bg-orange-50 text-orange-600' : ''); ?>">
          📋 User Guide
        </a>
      </div>
    </li>
    <?php if(\App\Models\Setting::get('outbound_module_enabled', false)): ?>
    <li class="relative">
      <button onclick="toggleOutboundDropdown()" 
              class="px-3 py-1 rounded flex items-center <?php echo e(request()->routeIs('outbound.*') ? 'bg-indigo-500 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100'); ?>">
        🚛 Outbound 
        <svg id="outbound-arrow" class="ml-1 w-4 h-4 transform transition-transform" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
      </button>
      <div id="outbound-dropdown" class="absolute left-0 mt-2 w-56 bg-white border border-gray-200 rounded-md shadow-lg z-20 hidden">
        <div class="px-4 py-2 text-xs font-semibold text-indigo-400 uppercase border-b border-gray-100">
          🧪 Beta - Testing Phase
        </div>
        <a href="<?php echo e(route('outbound.dashboard')); ?>" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo e(request()->routeIs('outbound.dashboard') ? 'bg-indigo-50 text-indigo-600' : ''); ?>">
          📊 Outbound Dashboard
          <div class="text-xs text-gray-500 mt-1">Overview of delivery operations</div>
        </a>
        <a href="<?php echo e(route('outbound.loads.index')); ?>" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo e(request()->routeIs('outbound.loads.*') ? 'bg-indigo-50 text-indigo-600' : ''); ?>">
          📦 Load Management
          <div class="text-xs text-gray-500 mt-1">Create and manage delivery loads</div>
        </a>
        <a href="<?php echo e(route('outbound.arrivals.dashboard')); ?>" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo e(request()->routeIs('outbound.arrivals.*') ? 'bg-indigo-50 text-indigo-600' : ''); ?>">
          🚛 Driver Arrivals
          <div class="text-xs text-gray-500 mt-1">Register loads when drivers arrive</div>
        </a>
        <a href="<?php echo e(route('outbound.imports.dashboard')); ?>" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo e(request()->routeIs('outbound.imports.*') ? 'bg-indigo-50 text-indigo-600' : ''); ?>">
          📁 WMS File Imports
          <div class="text-xs text-gray-500 mt-1">Upload and process WMS files</div>
        </a>
        <a href="<?php echo e(route('outbound.addresses.index')); ?>" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo e(request()->routeIs('outbound.addresses.*') ? 'bg-indigo-50 text-indigo-600' : ''); ?>">
          📍 Customer Addresses
          <div class="text-xs text-gray-500 mt-1">Manage delivery addresses</div>
        </a>
        <div class="border-t border-gray-100 my-1"></div>
        <div class="px-4 py-2">
          <div class="text-xs text-gray-500">
            ⚠️ Testing module - Safe to explore
          </div>
        </div>
      </div>
    </li>
    <?php endif; ?>
    <li class="relative">
      <button onclick="toggleSettingsDropdown()" 
              class="px-3 py-1 rounded flex items-center <?php echo e(request()->routeIs('app.settings.*') ? 'bg-blue-500 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100'); ?>">
        ⚙️ Settings 
        <svg id="settings-arrow" class="ml-1 w-4 h-4 transform transition-transform" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
      </button>
      <div id="settings-dropdown" class="absolute left-0 mt-2 w-56 bg-white border border-gray-200 rounded-md shadow-lg z-20 hidden">
        
        <?php if(auth()->user()->hasRole('depot-admin|site-admin')): ?>
          <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase border-b border-gray-100">Site Configuration</div>
          <a href="<?php echo e(route('app.depots.index')); ?>" 
             class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo e(request()->routeIs('app.depots.*') ? 'bg-blue-50 text-blue-600' : ''); ?>">
            🏭 Depots
          </a>
          <a href="<?php echo e(route('app.products.index')); ?>" 
             class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo e(request()->routeIs('app.products.*') ? 'bg-blue-50 text-blue-600' : ''); ?>">
            📦 Products
          </a>
          <a href="<?php echo e(route('app.customers.index')); ?>" 
             class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo e(request()->routeIs('app.customers.*') ? 'bg-blue-50 text-blue-600' : ''); ?>">
            👥 Customers
          </a>
          <a href="<?php echo e(route('app.slot-templates.index')); ?>" 
             class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo e(request()->routeIs('app.slot-templates.*') ? 'bg-blue-50 text-blue-600' : ''); ?>">
            📅 Slot Templates
          </a>
          <a href="<?php echo e(route('app.booking-types.index')); ?>" 
             class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo e(request()->routeIs('app.booking-types.*') ? 'bg-blue-50 text-blue-600' : ''); ?>">
            📝 Booking Types
          </a>
        <?php endif; ?>

        
        <?php if(auth()->user()->hasRole('admin')): ?>
          <?php if(auth()->user()->hasRole('depot-admin|site-admin')): ?>
            <div class="border-t border-gray-100 my-1"></div>
          <?php endif; ?>
          <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase border-b border-gray-100">Admin Settings</div>
          <a href="<?php echo e(route('app.settings.dashboard')); ?>" 
             class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
            🛠️ System Settings
          </a>
          <a href="<?php echo e(route('app.users.index')); ?>" 
             class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo e(request()->routeIs('app.users.*') ? 'bg-blue-50 text-blue-600' : ''); ?>">
            👤 User Management
          </a>
          <a href="<?php echo e(route('app.custom-roles.index')); ?>" 
             class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo e(request()->routeIs('app.custom-roles.*') ? 'bg-blue-50 text-blue-600' : ''); ?>">
            🏷️ Custom Roles
          </a>
        <?php endif; ?>

        
        <div class="border-t border-gray-100 my-1"></div>
        <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase border-b border-gray-100">Operations</div>
        <a href="<?php echo e(route('app.settings.factory-tipping-targets')); ?>" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?php echo e(request()->routeIs('app.settings.factory-tipping-targets*') ? 'bg-blue-50 text-blue-600' : ''); ?>">
          🏭 Factory Tipping Targets
        </a>
      </div>
    </li>
    </ul>

    
    <?php if(!app()->isProduction() && session('original_admin_id')): ?>
      <div class="flex items-center space-x-2">
        <span class="text-sm text-orange-600 font-medium">🔄 Testing as: <?php echo e(auth()->user()->name); ?></span>
        <form action="<?php echo e(route('switch-back')); ?>" method="POST" class="inline">
          <?php echo csrf_field(); ?>
          <button type="submit" class="px-2 py-1 bg-orange-500 text-white rounded text-xs hover:bg-orange-600">
            Switch Back
          </button>
        </form>
      </div>
    <?php elseif(!app()->isProduction()): ?>
      <div class="relative">
        <select onchange="switchUser(this.value)" class="text-xs border border-gray-300 rounded px-2 py-1 bg-white">
          <option value="">🔄 Switch User (Testing)</option>
          <?php $__currentLoopData = \App\Models\User::with('roles')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <option value="<?php echo e($user->id); ?>">
              <?php echo e($user->name); ?> (<?php echo e($user->roles->pluck('name')->join(', ') ?: 'No Role'); ?>)
            </option>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
      </div>
    <?php endif; ?>
  </div>

  <script>
  function switchUser(userId) {
    if (userId) {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = `/admin/switch-user/${userId}`;
      
      const token = document.createElement('input');
      token.type = 'hidden';
      token.name = '_token';
      token.value = '<?php echo e(csrf_token()); ?>';
      
      form.appendChild(token);
      document.body.appendChild(form);
      form.submit();
    }
  }

  function toggleBookingsDropdown() {
    const dropdown = document.getElementById('bookings-dropdown');
    const arrow = document.getElementById('bookings-arrow');
    
    // Close other dropdowns if open
    document.getElementById('tipping-dropdown').classList.add('hidden');
    document.getElementById('tipping-arrow').classList.remove('rotate-180');
    document.getElementById('settings-dropdown').classList.add('hidden');
    document.getElementById('settings-arrow').classList.remove('rotate-180');
    const outboundDropdown = document.getElementById('outbound-dropdown');
    if (outboundDropdown) {
      outboundDropdown.classList.add('hidden');
      document.getElementById('outbound-arrow').classList.remove('rotate-180');
    }
    
    if (dropdown.classList.contains('hidden')) {
      dropdown.classList.remove('hidden');
      arrow.classList.add('rotate-180');
    } else {
      dropdown.classList.add('hidden');
      arrow.classList.remove('rotate-180');
    }
  }

  function toggleTippingDropdown() {
    const dropdown = document.getElementById('tipping-dropdown');
    const arrow = document.getElementById('tipping-arrow');
    
    // Close other dropdowns if open
    document.getElementById('bookings-dropdown').classList.add('hidden');
    document.getElementById('bookings-arrow').classList.remove('rotate-180');
    document.getElementById('settings-dropdown').classList.add('hidden');
    document.getElementById('settings-arrow').classList.remove('rotate-180');
    const outboundDropdown = document.getElementById('outbound-dropdown');
    if (outboundDropdown) {
      outboundDropdown.classList.add('hidden');
      document.getElementById('outbound-arrow').classList.remove('rotate-180');
    }
    
    if (dropdown.classList.contains('hidden')) {
      dropdown.classList.remove('hidden');
      arrow.classList.add('rotate-180');
    } else {
      dropdown.classList.add('hidden');
      arrow.classList.remove('rotate-180');
    }
  }

  function toggleSettingsDropdown() {
    const dropdown = document.getElementById('settings-dropdown');
    const arrow = document.getElementById('settings-arrow');
    
    // Close other dropdowns if open
    document.getElementById('bookings-dropdown').classList.add('hidden');
    document.getElementById('bookings-arrow').classList.remove('rotate-180');
    document.getElementById('tipping-dropdown').classList.add('hidden');
    document.getElementById('tipping-arrow').classList.remove('rotate-180');
    const outboundDropdown = document.getElementById('outbound-dropdown');
    if (outboundDropdown) {
      outboundDropdown.classList.add('hidden');
      document.getElementById('outbound-arrow').classList.remove('rotate-180');
    }
    
    if (dropdown.classList.contains('hidden')) {
      dropdown.classList.remove('hidden');
      arrow.classList.add('rotate-180');
    } else {
      dropdown.classList.add('hidden');
      arrow.classList.remove('rotate-180');
    }
  }

  function toggleOutboundDropdown() {
    const dropdown = document.getElementById('outbound-dropdown');
    const arrow = document.getElementById('outbound-arrow');
    
    // Close other dropdowns if open
    document.getElementById('bookings-dropdown').classList.add('hidden');
    document.getElementById('bookings-arrow').classList.remove('rotate-180');
    document.getElementById('tipping-dropdown').classList.add('hidden');
    document.getElementById('tipping-arrow').classList.remove('rotate-180');
    document.getElementById('settings-dropdown').classList.add('hidden');
    document.getElementById('settings-arrow').classList.remove('rotate-180');
    
    if (dropdown.classList.contains('hidden')) {
      dropdown.classList.remove('hidden');
      arrow.classList.add('rotate-180');
    } else {
      dropdown.classList.add('hidden');
      arrow.classList.remove('rotate-180');
    }
  }

  // Close dropdowns when clicking outside
  document.addEventListener('click', function(event) {
    const bookingsDropdown = document.getElementById('bookings-dropdown');
    const tippingDropdown = document.getElementById('tipping-dropdown');
    const settingsDropdown = document.getElementById('settings-dropdown');
    const outboundDropdown = document.getElementById('outbound-dropdown');
    const bookingsButton = event.target.closest('button[onclick="toggleBookingsDropdown()"]');
    const tippingButton = event.target.closest('button[onclick="toggleTippingDropdown()"]');
    const settingsButton = event.target.closest('button[onclick="toggleSettingsDropdown()"]');
    const outboundButton = event.target.closest('button[onclick="toggleOutboundDropdown()"]');
    
    // Close bookings dropdown if clicking outside
    if (!bookingsButton && !bookingsDropdown.contains(event.target)) {
      bookingsDropdown.classList.add('hidden');
      document.getElementById('bookings-arrow').classList.remove('rotate-180');
    }
    
    // Close tipping dropdown if clicking outside
    if (!tippingButton && !tippingDropdown.contains(event.target)) {
      tippingDropdown.classList.add('hidden');
      document.getElementById('tipping-arrow').classList.remove('rotate-180');
    }
    
    // Close settings dropdown if clicking outside
    if (!settingsButton && !settingsDropdown.contains(event.target)) {
      settingsDropdown.classList.add('hidden');
      document.getElementById('settings-arrow').classList.remove('rotate-180');
    }
    
    // Close outbound dropdown if clicking outside
    if (outboundDropdown && !outboundButton && !outboundDropdown.contains(event.target)) {
      outboundDropdown.classList.add('hidden');
      document.getElementById('outbound-arrow').classList.remove('rotate-180');
    }
  });
  </script>
</nav>
<?php /**PATH /Users/londo/Herd/test/resources/views/layouts/admin-nav.blade.php ENDPATH**/ ?>