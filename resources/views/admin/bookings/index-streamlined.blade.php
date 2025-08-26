<x-app-layout>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="font-semibold text-xl">📋 Site Operations Control</h2>
        <p class="text-sm text-gray-600 mt-1">Live booking management and vehicle operations</p>
      </div>
      <div class="flex gap-2">
        @php
          $routePrefix = 'app.';
        @endphp
        <a href="{{ route($routePrefix . 'queue-management') }}"
           class="px-3 py-1 bg-orange-600 text-white rounded hover:bg-orange-700 text-sm">
          ⚡ Priority Queue
        </a>
        <a href="{{ route($routePrefix . 'bookings.create') }}"
           class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
          + New Booking
        </a>
      </div>
    </div>
  </x-slot>
  <div class="py-6 max-w-full mx-auto px-4">
    @if(session('success'))
      <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
        {{ session('success') }}
      </div>
    @endif
    {{-- Comprehensive Live Filters --}}
    <div class="mb-6 bg-white rounded-lg shadow-sm border">
      {{-- Primary Filters Row --}}
      <div class="p-4 border-b border-gray-200">
        <div class="flex flex-wrap items-center gap-4">
          {{-- Quick Search --}}
          <div class="flex-1 min-w-80">
            <input type="text" 
                   id="liveSearch"
                   placeholder="🔍 Search booking ref, customer, vehicle, container..."
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm"
                   value="{{ request('search') }}">
          </div>
          {{-- Live Status Filters --}}
          <div class="flex gap-2 items-center">
            <span class="text-sm font-medium text-gray-700">Status:</span>
            <button onclick="setStatusFilter('outstanding')" 
                    class="filter-btn px-3 py-1 rounded text-sm transition-colors {{ request('status', 'outstanding') == 'outstanding' ? 'bg-orange-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
              ⏳ Outstanding
            </button>
            <button onclick="setStatusFilter('completed')" 
                    class="filter-btn px-3 py-1 rounded text-sm transition-colors {{ request('status') == 'completed' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
              ✅ Completed
            </button>
            <button onclick="setStatusFilter('all')" 
                    class="filter-btn px-3 py-1 rounded text-sm transition-colors {{ request('status') == 'all' ? 'bg-gray-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
              📋 All
            </button>
          </div>
          {{-- Auto Refresh Toggle --}}
          <div class="flex items-center gap-2">
            <input type="checkbox" id="autoRefresh" checked class="rounded">
            <label for="autoRefresh" class="text-sm text-gray-700">Auto-refresh</label>
            <span id="refreshCountdown" class="text-xs text-gray-500">(60s)</span>
          </div>
        </div>
      </div>
      {{-- Secondary Filters Row --}}
      <div class="p-4">
        <div class="flex flex-wrap items-center gap-4">
          {{-- Quick Date Filters --}}
          <div class="flex gap-2 items-center">
            <span class="text-sm font-medium text-gray-700">Quick Date:</span>
            <button onclick="setDateFilter('today')" 
                    class="filter-btn px-3 py-1 rounded text-sm transition-colors {{ request('filter', 'today') == 'today' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
              📅 Today
            </button>
            <button onclick="setDateFilter('tomorrow')" 
                    class="filter-btn px-3 py-1 rounded text-sm transition-colors {{ request('filter') == 'tomorrow' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
              🗓️ Tomorrow
            </button>
            <button onclick="setDateFilter('this_week')" 
                    class="filter-btn px-3 py-1 rounded text-sm transition-colors {{ request('filter') == 'this_week' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
              📊 This Week
            </button>
            <button onclick="setDateFilter('next_week')" 
                    class="filter-btn px-3 py-1 rounded text-sm transition-colors {{ request('filter') == 'next_week' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
              📈 Next Week
            </button>
          </div>
          {{-- Depot Filter --}}
          <div class="flex gap-2 items-center">
            <label class="text-sm font-medium text-gray-700">Depot:</label>
            <select id="depotFilter" onchange="setDepotFilter(this.value)" class="border border-gray-300 rounded px-2 py-1 text-sm">
              <option value="">All Depots</option>
              @foreach($depots as $depot)
                <option value="{{ $depot->id }}" {{ request('depot_id') == $depot->id ? 'selected' : '' }}>
                  {{ $depot->name }}
                </option>
              @endforeach
            </select>
          </div>
          {{-- Customer Filter --}}
          <div class="flex gap-2 items-center">
            <label class="text-sm font-medium text-gray-700">Customer:</label>
            <select id="customerFilter" onchange="setCustomerFilter(this.value)" class="border border-gray-300 rounded px-2 py-1 text-sm">
              <option value="">All Customers</option>
              @foreach($customers as $customer)
                <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                  {{ $customer->name }}
                </option>
              @endforeach
            </select>
          </div>
          {{-- Arrival Status Filter --}}
          <div class="flex gap-2 items-center">
            <label class="text-sm font-medium text-gray-700">Arrival:</label>
            <select id="arrivalFilter" onchange="setArrivalFilter(this.value)" class="border border-gray-300 rounded px-2 py-1 text-sm">
              <option value="">All</option>
              <option value="not_arrived" {{ request('arrival') == 'not_arrived' ? 'selected' : '' }}>📋 Not Arrived</option>
              <option value="late_runners" {{ request('arrival') == 'late_runners' ? 'selected' : '' }}>⏰ Late Runners</option>
              <option value="arrived" {{ request('arrival') == 'arrived' ? 'selected' : '' }}>✅ Arrived</option>
              <option value="on_time" {{ request('arrival') == 'on_time' ? 'selected' : '' }}>🎯 On Time</option>
              <option value="arrived_late" {{ request('arrival') == 'arrived_late' ? 'selected' : '' }}>🔶 Arrived Late</option>
              <option value="onsite" {{ request('arrival') == 'onsite' ? 'selected' : '' }}>🚛 On Site</option>
            </select>
          </div>
          {{-- Week Number Filter --}}
          <div class="flex gap-2 items-center">
            <label class="text-sm font-medium text-gray-700">Week:</label>
            <select id="weekFilter" onchange="setWeekFilter(this.value)" class="border border-gray-300 rounded px-2 py-1 text-sm">
              <option value="">Current Period</option>
              @for($week = 1; $week <= 52; $week++)
                @php
                  $weekStart = \Carbon\Carbon::now()->setISODate(\Carbon\Carbon::now()->year, $week)->startOfWeek();
                  $weekEnd = $weekStart->clone()->endOfWeek();
                  $isCurrentWeek = $week === \Carbon\Carbon::now()->weekOfYear;
                @endphp
                <option value="{{ $week }}" {{ request('week_number') == $week ? 'selected' : '' }}>
                  Week {{ $week }} ({{ $weekStart->format('M d') }} - {{ $weekEnd->format('M d') }}){{ $isCurrentWeek ? ' - Current' : '' }}
                </option>
              @endfor
            </select>
          </div>
          {{-- Custom Date Range --}}
          <div class="flex gap-2 items-center">
            <label class="text-sm font-medium text-gray-700">From:</label>
            <input type="date" id="fromDate" onchange="setDateRange()" value="{{ request('from') }}" class="border border-gray-300 rounded px-2 py-1 text-sm">
            <label class="text-sm font-medium text-gray-700">To:</label>
            <input type="date" id="toDate" onchange="setDateRange()" value="{{ request('to') }}" class="border border-gray-300 rounded px-2 py-1 text-sm">
          </div>
          {{-- Clear Filters --}}
          @if(request()->hasAny(['search', 'status', 'filter', 'depot_id', 'customer_id', 'arrival', 'week_number', 'from', 'to']))
            <button onclick="clearAllFilters()" class="px-3 py-1 bg-red-500 text-white rounded text-sm hover:bg-red-600 transition-colors">
              🔄 Clear All
            </button>
          @endif
        </div>
      </div>
    </div>
    {{-- Operational Quick Actions --}}
    <div class="mb-4 flex flex-wrap gap-2 justify-between items-center">
      <div class="flex gap-2">
        <button onclick="openTrailerCollectionModal()" 
                class="px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm font-medium">
          🚛 Trailer Collection
        </button>
        <a href="{{ route($routePrefix . 'trailer-location-report') }}" 
           class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium">
          📍 Site Map
        </a>
      </div>
      {{-- Live Stats --}}
      <div class="flex gap-4 text-sm">
        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded">
          <span id="onSiteCount">{{ $bookings->where('arrived_at', '!=', null)->where('departed_at', null)->count() }}</span> On Site
        </span>
        <span class="px-2 py-1 bg-orange-100 text-orange-800 rounded">
          <span id="awaitingCount">{{ $bookings->where('arrived_at', null)->count() }}</span> Expected
        </span>
        <span class="px-2 py-1 bg-green-100 text-green-800 rounded">
          <span id="completedCount">{{ $bookings->where('departed_at', '!=', null)->count() }}</span> Completed
        </span>
      </div>
    </div>
    {{-- Streamlined Bookings Table --}}
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
          @include('admin.bookings.partials.streamlined-rows', ['bookings' => $bookings])
        </tbody>
      </table>
    </div>
    {{-- Pagination --}}
    <div class="mt-4">{{ $bookings->links() }}</div>
  </div>
  {{-- Modals remain the same but simplified --}}
  @include('admin.bookings.partials.arrival-modal')
  @include('admin.bookings.partials.departure-modal')
  @include('admin.bookings.partials.collection-modal')
  <script>
    // Live filtering and updates
    let currentFilters = {
      search: '{{ request("search") }}',
      status: '{{ request("status", "outstanding") }}',
      filter: '{{ request("filter", "today") }}',
      depot_id: '{{ request("depot_id") }}',
      customer_id: '{{ request("customer_id") }}',
      arrival: '{{ request("arrival") }}',
      week_number: '{{ request("week_number") }}',
      from: '{{ request("from") }}',
      to: '{{ request("to") }}'
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
      const url = `{{ route($routePrefix . 'bookings.streamlined') }}?${params.toString()}`;
      console.log('Fetching URL:', url);
      fetch(`{{ route($routePrefix . 'bookings.streamlined') }}?${params.toString()}`, {
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
      // Auto-assign to available drop zone if only one available
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
      window.open('{{ route("admin.empty-unit-collection") }}', '_blank', 'width=1200,height=800,scrollbars=yes,resizable=yes');
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
</x-app-layout>