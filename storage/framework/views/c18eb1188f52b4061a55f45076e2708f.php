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
  <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
   <?php $__env->slot('header', null, []); ?> 
    <div class="flex items-center justify-between">
      <div>
        <h2 class="font-semibold text-xl">📋 Site Operations Control</h2>
        <p class="text-sm text-gray-600 mt-1">Live booking management and vehicle operations</p>
      </div>
      <div class="flex gap-2">
        <?php
          $routePrefix = 'app.';
        ?>
        <a href="<?php echo e(route($routePrefix . 'queue-management')); ?>"
           class="px-3 py-1 bg-orange-600 text-white rounded hover:bg-orange-700 text-sm">
          ⚡ Priority Queue
        </a>
        <a href="<?php echo e(route($routePrefix . 'bookings.create')); ?>"
           class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
          + New Booking
        </a>
      </div>
    </div>
   <?php $__env->endSlot(); ?>
  <div class="py-6 max-w-full mx-auto px-4">
    <?php if(session('success')): ?>
      <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
        <?php echo e(session('success')); ?>

      </div>
    <?php endif; ?>
    
    <div class="mb-6 bg-white rounded-lg shadow-sm border">
      
      <div class="p-4 border-b border-gray-200">
        <div class="flex flex-wrap items-center gap-4">
          
          <div class="flex-1 min-w-80">
            <input type="text" 
                   id="liveSearch"
                   placeholder="🔍 Search booking ref, customer, vehicle, container..."
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm"
                   value="<?php echo e(request('search')); ?>">
          </div>
          
          <div class="flex gap-2 items-center">
            <span class="text-sm font-medium text-gray-700">Status:</span>
            <button onclick="setStatusFilter('outstanding')" 
                    class="filter-btn px-3 py-1 rounded text-sm transition-colors <?php echo e(request('status', 'outstanding') == 'outstanding' ? 'bg-orange-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'); ?>">
              ⏳ Outstanding
            </button>
            <button onclick="setStatusFilter('completed')" 
                    class="filter-btn px-3 py-1 rounded text-sm transition-colors <?php echo e(request('status') == 'completed' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'); ?>">
              ✅ Completed
            </button>
            <button onclick="setStatusFilter('all')" 
                    class="filter-btn px-3 py-1 rounded text-sm transition-colors <?php echo e(request('status') == 'all' ? 'bg-gray-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'); ?>">
              📋 All
            </button>
          </div>
          
          <div class="flex items-center gap-2">
            <input type="checkbox" id="autoRefresh" checked class="rounded">
            <label for="autoRefresh" class="text-sm text-gray-700">Auto-refresh</label>
            <span id="refreshCountdown" class="text-xs text-gray-500">(60s)</span>
          </div>
        </div>
      </div>
      
      <div class="p-4">
        <div class="flex flex-wrap items-center gap-4">
          
          <div class="flex gap-2 items-center">
            <span class="text-sm font-medium text-gray-700">Quick Date:</span>
            <button onclick="setDateFilter('today')" 
                    class="filter-btn px-3 py-1 rounded text-sm transition-colors <?php echo e(request('filter', 'today') == 'today' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'); ?>">
              📅 Today
            </button>
            <button onclick="setDateFilter('tomorrow')" 
                    class="filter-btn px-3 py-1 rounded text-sm transition-colors <?php echo e(request('filter') == 'tomorrow' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'); ?>">
              🗓️ Tomorrow
            </button>
            <button onclick="setDateFilter('this_week')" 
                    class="filter-btn px-3 py-1 rounded text-sm transition-colors <?php echo e(request('filter') == 'this_week' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'); ?>">
              📊 This Week
            </button>
            <button onclick="setDateFilter('next_week')" 
                    class="filter-btn px-3 py-1 rounded text-sm transition-colors <?php echo e(request('filter') == 'next_week' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'); ?>">
              📈 Next Week
            </button>
          </div>
          
          <div class="flex gap-2 items-center">
            <label class="text-sm font-medium text-gray-700">Depot:</label>
            <select id="depotFilter" onchange="setDepotFilter(this.value)" class="border border-gray-300 rounded px-2 py-1 text-sm">
              <option value="">All Depots</option>
              <?php $__currentLoopData = $depots; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $depot): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($depot->id); ?>" <?php echo e(request('depot_id') == $depot->id ? 'selected' : ''); ?>>
                  <?php echo e($depot->name); ?>

                </option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          
          <div class="flex gap-2 items-center">
            <label class="text-sm font-medium text-gray-700">Customer:</label>
            <select id="customerFilter" onchange="setCustomerFilter(this.value)" class="border border-gray-300 rounded px-2 py-1 text-sm">
              <option value="">All Customers</option>
              <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($customer->id); ?>" <?php echo e(request('customer_id') == $customer->id ? 'selected' : ''); ?>>
                  <?php echo e($customer->name); ?>

                </option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
          
          <div class="flex gap-2 items-center">
            <label class="text-sm font-medium text-gray-700">Arrival:</label>
            <select id="arrivalFilter" onchange="setArrivalFilter(this.value)" class="border border-gray-300 rounded px-2 py-1 text-sm">
              <option value="">All</option>
              <option value="not_arrived" <?php echo e(request('arrival') == 'not_arrived' ? 'selected' : ''); ?>>📋 Not Arrived</option>
              <option value="late_runners" <?php echo e(request('arrival') == 'late_runners' ? 'selected' : ''); ?>>⏰ Late Runners</option>
              <option value="arrived" <?php echo e(request('arrival') == 'arrived' ? 'selected' : ''); ?>>✅ Arrived</option>
              <option value="on_time" <?php echo e(request('arrival') == 'on_time' ? 'selected' : ''); ?>>🎯 On Time</option>
              <option value="arrived_late" <?php echo e(request('arrival') == 'arrived_late' ? 'selected' : ''); ?>>🔶 Arrived Late</option>
              <option value="onsite" <?php echo e(request('arrival') == 'onsite' ? 'selected' : ''); ?>>🚛 On Site</option>
            </select>
          </div>
          
          <div class="flex gap-2 items-center">
            <label class="text-sm font-medium text-gray-700">Week:</label>
            <select id="weekFilter" onchange="setWeekFilter(this.value)" class="border border-gray-300 rounded px-2 py-1 text-sm">
              <option value="">Current Period</option>
              <?php for($week = 1; $week <= 52; $week++): ?>
                <?php
                  $weekStart = \Carbon\Carbon::now()->setISODate(\Carbon\Carbon::now()->year, $week)->startOfWeek();
                  $weekEnd = $weekStart->clone()->endOfWeek();
                  $isCurrentWeek = $week === \Carbon\Carbon::now()->weekOfYear;
                ?>
                <option value="<?php echo e($week); ?>" <?php echo e(request('week_number') == $week ? 'selected' : ''); ?>>
                  Week <?php echo e($week); ?> (<?php echo e($weekStart->format('M d')); ?> - <?php echo e($weekEnd->format('M d')); ?>)<?php echo e($isCurrentWeek ? ' - Current' : ''); ?>

                </option>
              <?php endfor; ?>
            </select>
          </div>
          
          <div class="flex gap-2 items-center">
            <label class="text-sm font-medium text-gray-700">From:</label>
            <input type="date" id="fromDate" onchange="setDateRange()" value="<?php echo e(request('from')); ?>" class="border border-gray-300 rounded px-2 py-1 text-sm">
            <label class="text-sm font-medium text-gray-700">To:</label>
            <input type="date" id="toDate" onchange="setDateRange()" value="<?php echo e(request('to')); ?>" class="border border-gray-300 rounded px-2 py-1 text-sm">
          </div>
          
          <?php if(request()->hasAny(['search', 'status', 'filter', 'depot_id', 'customer_id', 'arrival', 'week_number', 'from', 'to'])): ?>
            <button onclick="clearAllFilters()" class="px-3 py-1 bg-red-500 text-white rounded text-sm hover:bg-red-600 transition-colors">
              🔄 Clear All
            </button>
          <?php endif; ?>
        </div>
      </div>
    </div>
    
    <div class="mb-4 flex flex-wrap gap-2 justify-between items-center">
      <div class="flex gap-2">
        <button onclick="openTrailerCollectionModal()" 
                class="px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm font-medium">
          🚛 Trailer Collection
        </button>
        <a href="<?php echo e(route($routePrefix . 'trailer-location-report')); ?>" 
           class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium">
          📍 Site Map
        </a>
      </div>
      
      <div class="flex gap-4 text-sm">
        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded">
          <span id="onSiteCount"><?php echo e($bookings->where('arrived_at', '!=', null)->where('departed_at', null)->count()); ?></span> On Site
        </span>
        <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded">
          <span id="awaitingCount"><?php echo e($bookings->where('arrived_at', null)->count()); ?></span> Expected
        </span>
        <span class="px-2 py-1 bg-green-100 text-green-800 rounded">
          <span id="completedCount"><?php echo e($bookings->where('departed_at', '!=', null)->count()); ?></span> Completed
        </span>
      </div>
    </div>
    
    <div id="bookingsContainer" class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status & Booking</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time & Customer</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Vehicle & Location</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Load Info</th>
            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200" id="bookingsTableBody">
          <?php echo $__env->make('admin.bookings.partials.streamlined-rows', ['bookings' => $bookings], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </tbody>
      </table>
    </div>
    
    <div class="mt-4"><?php echo e($bookings->links()); ?></div>
  </div>
  
  <?php echo $__env->make('admin.bookings.partials.arrival-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php echo $__env->make('admin.bookings.partials.departure-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php echo $__env->make('admin.bookings.partials.collection-modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <script>
    // Live filtering and updates
    let currentFilters = {
      search: '<?php echo e(request("search")); ?>',
      status: '<?php echo e(request("status", "outstanding")); ?>',
      filter: '<?php echo e(request("filter", "today")); ?>',
      depot_id: '<?php echo e(request("depot_id")); ?>',
      customer_id: '<?php echo e(request("customer_id")); ?>',
      arrival: '<?php echo e(request("arrival")); ?>',
      week_number: '<?php echo e(request("week_number")); ?>',
      from: '<?php echo e(request("from")); ?>',
      to: '<?php echo e(request("to")); ?>'
    };
    let searchTimeout;
    let refreshTimer;
    let refreshCountdown = 60; // Increased to 60 seconds to be less aggressive
    let autoRefreshEnabled = true;
    let userIsFiltering = false; // Pause auto-refresh when user is actively filtering
    // Initialize
    document.addEventListener('DOMContentLoaded', function() {
      startAutoRefresh();
      updateRefreshCountdown();
      updateFilterButtons(); // Set correct button states on load
      // Set up all live filter event handlers
      setupLiveFilters();
    });
    // Set up live filter event handlers
    function setupLiveFilters() {
      console.log('Setting up live filters...');
      // Live search
      const searchInput = document.getElementById('liveSearch');
      if (searchInput) {
        searchInput.addEventListener('input', function() {
          console.log('Search input changed to:', this.value);
          pauseAutoRefreshTemporarily();
          const query = this.value.trim();
          currentFilters.search = query;
          clearTimeout(searchTimeout);
          searchTimeout = setTimeout(() => {
            updateBookings();
          }, 500);
        });
        console.log('Search input event handler attached');
      }
      // Depot filter
      const depotFilter = document.getElementById('depotFilter');
      if (depotFilter) {
        depotFilter.addEventListener('change', function() {
          console.log('Depot filter changed:', this.value);
          setDepotFilter(this.value);
        });
        console.log('Depot filter event handler attached');
      }
      // Customer filter
      const customerFilter = document.getElementById('customerFilter');
      if (customerFilter) {
        customerFilter.addEventListener('change', function() {
          console.log('Customer filter changed:', this.value);
          setCustomerFilter(this.value);
        });
        console.log('Customer filter event handler attached');
      }
      // Arrival filter
      const arrivalFilter = document.getElementById('arrivalFilter');
      if (arrivalFilter) {
        arrivalFilter.addEventListener('change', function() {
          console.log('Arrival filter changed:', this.value);
          setArrivalFilter(this.value);
        });
        console.log('Arrival filter event handler attached');
      }
      // Week filter
      const weekFilter = document.getElementById('weekFilter');
      if (weekFilter) {
        weekFilter.addEventListener('change', function() {
          console.log('Week filter changed:', this.value);
          setWeekFilter(this.value);
        });
        console.log('Week filter event handler attached');
      }
      // Date range filters
      const fromDate = document.getElementById('fromDate');
      if (fromDate) {
        fromDate.addEventListener('change', function() {
          console.log('From date changed:', this.value);
          setDateRange();
        });
        console.log('From date event handler attached');
      }
      const toDate = document.getElementById('toDate');
      if (toDate) {
        toDate.addEventListener('change', function() {
          console.log('To date changed:', this.value);
          setDateRange();
        });
        console.log('To date event handler attached');
      }
      // Auto refresh toggle
      const autoRefresh = document.getElementById('autoRefresh');
      if (autoRefresh) {
        autoRefresh.addEventListener('change', function() {
          console.log('Auto refresh toggled:', this.checked);
          autoRefreshEnabled = this.checked;
          if (autoRefreshEnabled) {
            refreshCountdown = 30;
          }
        });
        console.log('Auto refresh event handler attached');
      }
      console.log('All live filter event handlers setup complete');
    }
    // Filter functions
    function setStatusFilter(status) {
      pauseAutoRefreshTemporarily();
      currentFilters.status = status;
      updateFilterButtons();
      updateBookings();
    }
    function setDateFilter(filter) {
      pauseAutoRefreshTemporarily();
      currentFilters.filter = filter;
      // Clear conflicting filters
      currentFilters.week_number = '';
      currentFilters.from = '';
      currentFilters.to = '';
      document.getElementById('weekFilter').value = '';
      document.getElementById('fromDate').value = '';
      document.getElementById('toDate').value = '';
      updateFilterButtons();
      updateBookings();
    }
    function setDepotFilter(depot_id) {
      console.log('Depot filter changed to:', depot_id);
      pauseAutoRefreshTemporarily();
      currentFilters.depot_id = depot_id;
      updateBookings();
    }
    function setCustomerFilter(customer_id) {
      console.log('Customer filter changed to:', customer_id);
      pauseAutoRefreshTemporarily();
      currentFilters.customer_id = customer_id;
      updateBookings();
    }
    function setArrivalFilter(arrival) {
      console.log('Arrival filter changed to:', arrival);
      pauseAutoRefreshTemporarily();
      currentFilters.arrival = arrival;
      updateBookings();
    }
    function setWeekFilter(week_number) {
      pauseAutoRefreshTemporarily();
      currentFilters.week_number = week_number;
      // Clear conflicting filters
      currentFilters.filter = '';
      currentFilters.from = '';
      currentFilters.to = '';
      document.getElementById('fromDate').value = '';
      document.getElementById('toDate').value = '';
      updateFilterButtons();
      updateBookings();
    }
    function setDateRange() {
      pauseAutoRefreshTemporarily();
      const fromDate = document.getElementById('fromDate').value;
      const toDate = document.getElementById('toDate').value;
      currentFilters.from = fromDate;
      currentFilters.to = toDate;
      if (fromDate || toDate) {
        // Clear conflicting filters
        currentFilters.filter = '';
        currentFilters.week_number = '';
        document.getElementById('weekFilter').value = '';
        updateFilterButtons();
      }
      updateBookings();
    }
    // Pause auto-refresh temporarily when user is actively filtering
    function pauseAutoRefreshTemporarily() {
      userIsFiltering = true;
      refreshCountdown = 60; // Reset countdown when user filters
      // Resume auto-refresh after 10 seconds of inactivity
      setTimeout(() => {
        userIsFiltering = false;
        console.log('Auto-refresh resumed after user inactivity');
      }, 10000);
    }
    function clearAllFilters() {
      currentFilters = {
        search: '',
        status: 'outstanding',
        filter: 'today',
        depot_id: '',
        customer_id: '',
        arrival: '',
        week_number: '',
        from: '',
        to: ''
      };
      // Reset form elements
      document.getElementById('liveSearch').value = '';
      document.getElementById('depotFilter').value = '';
      document.getElementById('customerFilter').value = '';
      document.getElementById('arrivalFilter').value = '';
      document.getElementById('weekFilter').value = '';
      document.getElementById('fromDate').value = '';
      document.getElementById('toDate').value = '';
      updateFilterButtons();
      updateBookings();
    }
    function updateFilterButtons() {
      // Update active states for all filter buttons
      document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('bg-orange-600', 'bg-blue-600', 'bg-green-600', 'bg-gray-600', 'text-white');
        btn.classList.add('bg-gray-100', 'text-gray-700');
      });
      // Set active button styles for status filters
      const statusButtons = document.querySelectorAll(`[onclick*="setStatusFilter('${currentFilters.status}')"]`);
      statusButtons.forEach(btn => {
        btn.classList.remove('bg-gray-100', 'text-gray-700');
        if (currentFilters.status === 'outstanding') {
          btn.classList.add('bg-orange-600', 'text-white');
        } else if (currentFilters.status === 'completed') {
          btn.classList.add('bg-green-600', 'text-white');
        } else if (currentFilters.status === 'all') {
          btn.classList.add('bg-gray-600', 'text-white');
        }
      });
      // Set active button styles for date filters
      const dateButtons = document.querySelectorAll(`[onclick*="setDateFilter('${currentFilters.filter}')"]`);
      dateButtons.forEach(btn => {
        btn.classList.remove('bg-gray-100', 'text-gray-700');
        btn.classList.add('bg-blue-600', 'text-white');
      });
    }
    // Update bookings via AJAX
    function updateBookings() {
      console.log('updateBookings called with filters:', currentFilters);
      // Show loading state
      const tableContainer = document.getElementById('bookingsContainer');
      tableContainer.classList.add('loading');
      // Clean up filters - remove empty values
      const cleanFilters = {};
      for (const [key, value] of Object.entries(currentFilters)) {
        if (value && value.trim && value.trim() !== '') {
          cleanFilters[key] = value;
        } else if (value && !value.trim && value !== '') {
          cleanFilters[key] = value;
        }
      }
      console.log('Clean filters being sent:', cleanFilters);
      const params = new URLSearchParams(cleanFilters);
      const url = `<?php echo e(route($routePrefix . 'bookings.streamlined')); ?>?${params.toString()}`;
      console.log('Fetching URL:', url);
      fetch(`<?php echo e(route($routePrefix . 'bookings.streamlined')); ?>?${params.toString()}`, {
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'text/html'
        }
      })
      .then(response => {
        if (!response.ok) {
          throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.text();
      })
      .then(html => {
        // Update table body smoothly to reduce flashing
        const tableBody = document.getElementById('bookingsTableBody');
        if (tableBody) {
          // Fade out briefly, update content, then fade back in
          tableBody.style.opacity = '0.7';
          setTimeout(() => {
            tableBody.innerHTML = html;
            tableBody.style.opacity = '1';
            // Only add highlight animation for manual filter changes, not auto-refresh
            if (userIsFiltering) {
              tableBody.classList.add('booking-row-updated');
              setTimeout(() => {
                tableBody.classList.remove('booking-row-updated');
              }, 1000);
            }
          }, 100);
        }
        // Update stats
        updateLiveStats();
        // Remove loading state
        tableContainer.classList.remove('loading');
        // Show subtle feedback
        showFilterFeedback();
      })
      .catch(error => {
        console.error('Failed to update bookings:', error);
        showNotification('❌ Failed to update bookings', 'error');
        // Remove loading state
        tableContainer.classList.remove('loading');
      });
    }
    // Show subtle feedback when filters are applied
    function showFilterFeedback() {
      const searchInput = document.getElementById('liveSearch');
      const originalBorder = searchInput.style.borderColor;
      searchInput.style.borderColor = '#10b981'; // green
      setTimeout(() => {
        searchInput.style.borderColor = originalBorder;
      }, 300);
    }
    // Auto refresh
    function startAutoRefresh() {
      if (refreshTimer) clearInterval(refreshTimer);
      refreshTimer = setInterval(() => {
        updateRefreshCountdown();
        if (autoRefreshEnabled && !userIsFiltering && refreshCountdown <= 0) {
          console.log('Auto-refresh triggered');
          // Show subtle auto-refresh indicator
          showNotification('🔄 Auto-refreshed', 'info');
          updateBookings();
          refreshCountdown = 60; // Reset to 60 seconds
        }
      }, 1000);
    }
    function updateRefreshCountdown() {
      if (autoRefreshEnabled) {
        refreshCountdown--;
        document.getElementById('refreshCountdown').textContent = `(${refreshCountdown}s)`;
        if (refreshCountdown <= 0) {
          refreshCountdown = 30;
        }
      } else {
        document.getElementById('refreshCountdown').textContent = '(paused)';
      }
    }
    // Auto refresh toggle handler is now in setupLiveFilters()
    // Update live stats
    function updateLiveStats() {
      // Count bookings in current view
      const rows = document.querySelectorAll('#bookingsTableBody tr');
      let onSite = 0, awaiting = 0, completed = 0;
      rows.forEach(row => {
        if (row.classList.contains('on-site')) onSite++;
        else if (row.classList.contains('awaiting')) awaiting++;
        else if (row.classList.contains('completed')) completed++;
      });
      document.getElementById('onSiteCount').textContent = onSite;
      document.getElementById('awaitingCount').textContent = awaiting;
      document.getElementById('completedCount').textContent = completed;
    }
    // Streamlined modal functions
    function openArrivalModal(bookingId, bookingRef, customer, depot, scheduledTime, vehicleReg, containerNum, carrierCompany, expectedCases, expectedPallets, specialInstructions) {
      // Similar to existing but streamlined for quick processing
      const modal = document.getElementById('arrivalModal');
      // Pre-populate critical fields
      document.getElementById('vehicleRegistration').value = vehicleReg;
      document.getElementById('containerNumber').value = containerNum;
      document.getElementById('carrierCompany').value = carrierCompany;
      // Auto-assign to available parking area if only one available
      const dropZones = document.querySelectorAll('#tippingLocation option:not([value=""])');
      if (dropZones.length === 1) {
        document.getElementById('tippingLocation').value = dropZones[0].value;
      }
      modal.classList.remove('hidden');
      document.getElementById('vehicleRegistration').focus();
    }
    function processArrival(bookingId) {
      // Quick arrival processing
      const form = document.getElementById('arrivalForm');
      const formData = new FormData(form);
      fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          closeArrivalModal();
          updateBookings();
          showNotification('✅ Vehicle arrived successfully', 'success');
        } else {
          showNotification('❌ ' + (data.message || 'Error processing arrival'), 'error');
        }
      })
      .catch(error => {
        console.error('Arrival processing failed:', error);
        showNotification('❌ Failed to process arrival', 'error');
      });
    }
    // Quick notification system
    function showNotification(message, type = 'info') {
      const notification = document.createElement('div');
      notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
        type === 'success' ? 'bg-green-500 text-white' :
        type === 'error' ? 'bg-red-500 text-white' :
        'bg-blue-500 text-white'
      }`;
      notification.textContent = message;
      document.body.appendChild(notification);
      setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
      }, 3000);
    }
    // Keyboard shortcuts for operators
    document.addEventListener('keydown', function(e) {
      // Ctrl+F - Focus search
      if (e.ctrlKey && e.key === 'f') {
        e.preventDefault();
        document.getElementById('liveSearch').focus();
      }
      // Ctrl+R - Refresh
      if (e.ctrlKey && e.key === 'r') {
        e.preventDefault();
        updateBookings();
        showNotification('🔄 Refreshed', 'info');
      }
      // Escape - Close modals
      if (e.key === 'Escape') {
        document.querySelectorAll('[id$="Modal"]').forEach(modal => {
          if (!modal.classList.contains('hidden')) {
            modal.classList.add('hidden');
          }
        });
      }
    });
    // Trailer Collection Modal Functions
    function openTrailerCollectionModal() {
      // Open the empty unit collection page in a new window/tab
      window.open('<?php echo e(route("admin.empty-unit-collection")); ?>', '_blank', 'width=1200,height=800,scrollbars=yes,resizable=yes');
    }
  </script>
  <style>
    /* Smooth transitions for live updates */
    .filter-btn {
      transition: all 0.2s ease;
    }
    .filter-btn:hover {
      transform: translateY(-1px);
    }
    /* Highlight new/updated rows */
    @keyframes highlight {
      0% { background-color: #fef3c7; }
      100% { background-color: transparent; }
    }
    .booking-row-updated {
      animation: highlight 2s ease-out;
    }
    /* Loading states */
    .loading {
      opacity: 0.6;
      pointer-events: none;
    }
  </style>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc9242005886028143da563f7b99f0c87)): ?>
<?php $attributes = $__attributesOriginalc9242005886028143da563f7b99f0c87; ?>
<?php unset($__attributesOriginalc9242005886028143da563f7b99f0c87); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc9242005886028143da563f7b99f0c87)): ?>
<?php $component = $__componentOriginalc9242005886028143da563f7b99f0c87; ?>
<?php unset($__componentOriginalc9242005886028143da563f7b99f0c87); ?>
<?php endif; ?><?php /**PATH /Users/londo/Herd/test/resources/views/warehouse/bookings/index-streamlined.blade.php ENDPATH**/ ?>