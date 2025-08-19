<nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 mb-6">
  <div class="flex justify-between items-center px-6 py-3">
    <ul class="flex space-x-4">
    <li>
      <a href="{{ route('admin.dashboard') }}"
         class="px-3 py-1 rounded {{ request()->routeIs('admin.dashboard') ? 'bg-blue-500 text-white' : 'text-gray-700 dark:text-gray-300' }}">
        Dashboard
      </a>
    </li>
    <li>
      <a href="{{ route('admin.slots.index') }}"
         class="px-3 py-1 rounded {{ request()->routeIs('admin.slots.*') ? 'bg-blue-500 text-white' : 'text-gray-700 dark:text-gray-300' }}">
        Slots
      </a>
    </li>
    <li class="relative">
      @php
        $routePrefix = request()->route()->getPrefix() === 'depot-admin' ? 'depot.' : 'admin.';
      @endphp
      <button onclick="toggleBookingsDropdown()" 
              class="px-3 py-1 rounded flex items-center {{ request()->routeIs('admin.bookings.*', 'depot.bookings.*') && !request()->routeIs('admin.customer-behavior.*') ? 'bg-blue-500 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100' }}">
        📋 Bookings 
        <svg id="bookings-arrow" class="ml-1 w-4 h-4 transform transition-transform" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
      </button>
      <div id="bookings-dropdown" class="absolute left-0 mt-2 w-56 bg-white border border-gray-200 rounded-md shadow-lg z-20 hidden">
        <a href="{{ route($routePrefix . 'bookings.index') }}" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('admin.bookings.index', 'depot.bookings.index') || (request()->routeIs('admin.bookings.*', 'depot.bookings.*') && !request()->routeIs('admin.bookings.streamlined', 'depot.bookings.streamlined')) ? 'bg-blue-50 text-blue-600' : '' }}">
          📊 Bookings (Full View)
          <div class="text-xs text-gray-500 mt-1">Complete booking management with all features</div>
        </a>
        <a href="{{ route($routePrefix . 'bookings.streamlined') }}" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('admin.bookings.streamlined', 'depot.bookings.streamlined') ? 'bg-blue-50 text-blue-600' : '' }}">
          ⚡ Bookings (Live View)
          <div class="text-xs text-gray-500 mt-1">Streamlined interface with live updates</div>
        </a>
        <a href="{{ route($routePrefix . 'bookings.streamlined') }}" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('admin.bookings.streamlined', 'depot.bookings.streamlined') ? 'bg-blue-50 text-blue-600' : '' }}">
          ⚡ Bookings (Test View)
          <div class="text-xs text-gray-500 mt-1">Live Arrivals another Test View</div>
        </a>
      </div>
    </li>
    <li>
      <a href="{{ route('admin.customer-behavior.index') }}"
         class="px-3 py-1 rounded {{ request()->routeIs('admin.customer-behavior.*') ? 'bg-purple-500 text-white' : 'text-gray-700 dark:text-gray-300' }}">
        Customer Analysis
      </a>
    </li>
    <li class="relative">
      <button onclick="toggleTippingDropdown()" 
              class="px-3 py-1 rounded flex items-center {{ request()->routeIs('admin.tipping-*') ? 'bg-orange-500 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100' }}">
        🚛 Tipping 
        <svg id="tipping-arrow" class="ml-1 w-4 h-4 transform transition-transform" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
      </button>
      <div id="tipping-dropdown" class="absolute left-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg z-20 hidden">
        <a href="{{ route('admin.tipping-workflow.dashboard') }}" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('admin.tipping-workflow.*') ? 'bg-orange-50 text-orange-600' : '' }}">
          📊 Dashboard
        </a>
        <a href="{{ route('admin.tipping-locations.index') }}" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('admin.tipping-locations.*') ? 'bg-orange-50 text-orange-600' : '' }}">
          📍 Drop Locations
        </a>
        <a href="{{ route('admin.tipping-bays.index') }}" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('admin.tipping-bays.*') ? 'bg-orange-50 text-orange-600' : '' }}">
          🚛 Tipping Bays
        </a>
        <div class="border-t border-gray-100 my-1"></div>
        @php
          $tippingRoutePrefix = request()->route()->getPrefix() === 'depot-admin' ? 'depot.' : 'admin.';
        @endphp
        <a href="{{ route($tippingRoutePrefix . 'queue-management') }}" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('admin.queue-management', 'depot.queue-management') ? 'bg-orange-50 text-orange-600' : '' }}">
          ⚡ Queue Management
        </a>
        <a href="{{ route($tippingRoutePrefix . 'operations-control') }}" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('admin.operations-control', 'depot.operations-control') ? 'bg-orange-50 text-orange-600' : '' }}">
          🎯 Site Operations Control
        </a>
        <a href="{{ route($tippingRoutePrefix . 'trailer-operations-dashboard') }}" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('admin.trailer-operations-dashboard', 'depot.trailer-operations-dashboard') ? 'bg-orange-50 text-orange-600' : '' }}">
          🚛 Trailer Operations Dashboard
        </a>
        <a href="{{ route($tippingRoutePrefix . 'trailer-location-report') }}" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('admin.trailer-location-report', 'depot.trailer-location-report') ? 'bg-orange-50 text-orange-600' : '' }}">
          📍 Trailer Location Report
        </a>
        <a href="{{ route($tippingRoutePrefix . 'empty-unit-collection') }}" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('admin.empty-unit-collection*', 'depot.empty-unit-collection*') ? 'bg-orange-50 text-orange-600' : '' }}">
          🚛 Empty Unit Collection
        </a>
        <div class="border-t border-gray-100 my-1"></div>
        <a href="{{ route('admin.tipping-guide') }}" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('admin.tipping-guide') ? 'bg-orange-50 text-orange-600' : '' }}">
          📋 User Guide
        </a>
      </div>
    </li>
    <li class="relative">
      <button onclick="toggleSettingsDropdown()" 
              class="px-3 py-1 rounded flex items-center {{ request()->routeIs('admin.settings.*') ? 'bg-blue-500 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100' }}">
        ⚙️ Settings 
        <svg id="settings-arrow" class="ml-1 w-4 h-4 transform transition-transform" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
      </button>
      <div id="settings-dropdown" class="absolute left-0 mt-2 w-56 bg-white border border-gray-200 rounded-md shadow-lg z-20 hidden">
        {{-- Site Configuration (for depot-admin and site-admin) --}}
        @if(auth()->user()->hasRole('depot-admin|site-admin'))
          <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase border-b border-gray-100">Site Configuration</div>
          <a href="{{ route('admin.depots.index') }}" 
             class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('admin.depots.*') ? 'bg-blue-50 text-blue-600' : '' }}">
            🏭 Depots
          </a>
          <a href="{{ route('admin.products.index') }}" 
             class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('admin.products.*') ? 'bg-blue-50 text-blue-600' : '' }}">
            📦 Products
          </a>
          <a href="{{ route('admin.customers.index') }}" 
             class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('admin.customers.*') ? 'bg-blue-50 text-blue-600' : '' }}">
            👥 Customers
          </a>
          <a href="{{ route('admin.slot-templates.index') }}" 
             class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('admin.slot-templates.*') ? 'bg-blue-50 text-blue-600' : '' }}">
            📅 Slot Templates
          </a>
          <a href="{{ route('admin.booking-types.index') }}" 
             class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('admin.booking-types.*') ? 'bg-blue-50 text-blue-600' : '' }}">
            📝 Booking Types
          </a>
        @endif

        {{-- Admin Settings (for admin only) --}}
        @if(auth()->user()->hasRole('admin'))
          @if(auth()->user()->hasRole('depot-admin|site-admin'))
            <div class="border-t border-gray-100 my-1"></div>
          @endif
          <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase border-b border-gray-100">Admin Settings</div>
          <a href="{{ route('admin.settings.dashboard') }}" 
             class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
            🛠️ System Settings
          </a>
          <a href="{{ route('admin.users.index') }}" 
             class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('admin.users.*') ? 'bg-blue-50 text-blue-600' : '' }}">
            👤 User Management
          </a>
        @endif

        {{-- Operational Settings (for all) --}}
        <div class="border-t border-gray-100 my-1"></div>
        <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase border-b border-gray-100">Operations</div>
        <a href="{{ route('admin.settings.index') }}" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('admin.settings.index') ? 'bg-blue-50 text-blue-600' : '' }}">
          ⚙️ General Settings
        </a>
      </div>
    </li>
    </ul>

    {{-- User Switching (Testing Only) --}}
    @if(!app()->isProduction() && session('original_admin_id'))
      <div class="flex items-center space-x-2">
        <span class="text-sm text-orange-600 font-medium">🔄 Testing as: {{ auth()->user()->name }}</span>
        <form action="{{ route('switch-back') }}" method="POST" class="inline">
          @csrf
          <button type="submit" class="px-2 py-1 bg-orange-500 text-white rounded text-xs hover:bg-orange-600">
            Switch Back
          </button>
        </form>
      </div>
    @elseif(!app()->isProduction())
      <div class="relative">
        <select onchange="switchUser(this.value)" class="text-xs border border-gray-300 rounded px-2 py-1 bg-white">
          <option value="">🔄 Switch User (Testing)</option>
          @foreach(\App\Models\User::with('roles')->get() as $user)
            <option value="{{ $user->id }}">
              {{ $user->name }} ({{ $user->roles->pluck('name')->join(', ') ?: 'No Role' }})
            </option>
          @endforeach
        </select>
      </div>
    @endif
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
      token.value = '{{ csrf_token() }}';
      
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
    const bookingsButton = event.target.closest('button[onclick="toggleBookingsDropdown()"]');
    const tippingButton = event.target.closest('button[onclick="toggleTippingDropdown()"]');
    const settingsButton = event.target.closest('button[onclick="toggleSettingsDropdown()"]');
    
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
  });
  </script>
</nav>
