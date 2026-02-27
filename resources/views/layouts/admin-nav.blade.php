<nav class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700 mb-6">
  <div class="flex justify-between items-center px-6 py-3">
    <ul class="flex space-x-1 items-center">
    <li>
      <a href="{{ route('app.dashboard') }}"
         class="px-4 py-2 rounded inline-flex items-center gap-2 font-medium text-sm {{ request()->routeIs('app.dashboard') ? 'bg-blue-500 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100' }}">
        <span class="inline-block align-middle" style="line-height: 1; vertical-align: middle;">📊</span>
        <span>Dashboard</span>
      </a>
    </li>
    @canFunction('slots.view')
    <li>
      <a href="{{ route('app.slots.index') }}"
         class="px-4 py-2 rounded inline-flex items-center gap-2 font-medium text-sm {{ request()->routeIs('app.slots.*') ? 'bg-blue-500 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100' }}">
        <span class="inline-block align-middle" style="line-height: 1; vertical-align: middle;">📅</span>
        <span>Slots</span>
      </a>
    </li>
    @endcanFunction
    @hasAnyFunction(['bookings.view', 'bookings.view-streamlined'])
    <li class="relative">
      @php
        $routePrefix = 'app.';
      @endphp
      <button onclick="toggleBookingsDropdown()"
              class="px-4 py-2 rounded inline-flex items-center gap-2 font-medium text-sm {{ request()->routeIs('app.bookings.*') && !request()->routeIs('app.customer-behavior.*') ? 'bg-blue-500 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100' }}">
        <span class="inline-block align-middle" style="line-height: 1; vertical-align: middle;">📋</span>
        <span>Bookings</span>
        <svg id="bookings-arrow" class="w-4 h-4 transform transition-transform" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
      </button>
      <div id="bookings-dropdown" class="absolute left-0 mt-2 w-56 bg-white border border-gray-200 rounded-md shadow-lg z-20 hidden">
        @canFunction('bookings.view')
        <a href="{{ route($routePrefix . 'bookings.index') }}" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('app.bookings.index') || (request()->routeIs('app.bookings.*') && !request()->routeIs('app.bookings.streamlined')) ? 'bg-blue-50 text-blue-600' : '' }}">
          📊 Bookings (Full View)
          <div class="text-xs text-gray-500 mt-1">Complete booking management with all features</div>
        </a>
        @endcanFunction
        @canFunction('bookings.view-streamlined')
        <a href="{{ route($routePrefix . 'bookings.streamlined') }}"
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('app.bookings.streamlined') ? 'bg-blue-50 text-blue-600' : '' }}">
          ⚡ Bookings (Live View)
          <div class="text-xs text-gray-500 mt-1">Streamlined interface with live updates</div>
        </a>
        @endcanFunction
      </div>
    </li>
    @endhasAnyFunction
    <li>
      <a href="{{ route('app.customer-behavior.index') }}"
         class="px-4 py-2 rounded inline-flex items-center gap-2 font-medium text-sm {{ request()->routeIs('app.customer-behavior.*') ? 'bg-purple-500 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100' }}">
        <span class="inline-block align-middle" style="line-height: 1; vertical-align: middle;">📈</span>
        <span>Customer Analysis</span>
      </a>
    </li>
    <li>
      <a href="{{ route('app.depot-map.index') }}"
         class="px-4 py-2 rounded inline-flex items-center gap-2 font-medium text-sm {{ request()->routeIs('app.depot-map.*') ? 'bg-green-500 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100' }}">
        <span class="inline-block align-middle" style="line-height: 1; vertical-align: middle;">🗺️</span>
        <span>Depot Map</span>
      </a>
    </li>
    <li class="relative">
      <button onclick="toggleTippingDropdown()"
              class="px-4 py-2 rounded inline-flex items-center gap-2 font-medium text-sm {{ request()->routeIs('app.tipping-*') ? 'bg-orange-500 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100' }}">
        <span class="inline-block align-middle" style="line-height: 1; vertical-align: middle;">🚛</span>
        <span>Tipping</span>
        <svg id="tipping-arrow" class="w-4 h-4 transform transition-transform" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
      </button>
      <div id="tipping-dropdown" class="absolute left-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg z-20 hidden">
        <a href="{{ route('app.tipping-workflow.dashboard') }}" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('app.tipping-workflow.*') ? 'bg-orange-50 text-orange-600' : '' }}">
          📊 Dashboard
        </a>
        <a href="{{ route('app.tipping-locations.index') }}"
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('app.tipping-locations.*') ? 'bg-orange-50 text-orange-600' : '' }}">
          📍 Drop Locations
        </a>
        <a href="{{ route('app.tipping-bays.index') }}"
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('app.tipping-bays.*') ? 'bg-orange-50 text-orange-600' : '' }}">
          🚛 Tipping Bays
        </a>
        <div class="border-t border-gray-100 my-1"></div>
        <a href="{{ route('app.queue-management') }}" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('app.queue-management') ? 'bg-orange-50 text-orange-600' : '' }}">
          ⚡ Queue Management
        </a>
        <a href="{{ route('app.operations-control') }}"
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('app.operations-control') ? 'bg-orange-50 text-orange-600' : '' }}">
          🎯 Site Operations Control
        </a>
        <a href="{{ route('app.trailer-operations-dashboard') }}"
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('app.trailer-operations-dashboard') ? 'bg-orange-50 text-orange-600' : '' }}">
          🚛 Trailer Operations Dashboard
        </a>
        <a href="{{ route('app.trailer-location-report') }}"
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('app.trailer-location-report') ? 'bg-orange-50 text-orange-600' : '' }}">
          📍 Trailer Location Report
        </a>
        <a href="{{ route('app.empty-unit-collection') }}"
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('app.empty-unit-collection*') ? 'bg-orange-50 text-orange-600' : '' }}">
          🚛 Empty Unit Collection
        </a>
        <div class="border-t border-gray-100 my-1"></div>
        <a href="{{ route('app.tipping-guide') }}" 
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('app.tipping-guide') ? 'bg-orange-50 text-orange-600' : '' }}">
          📋 User Guide
        </a>
      </div>
    </li>
    @if(\App\Models\Setting::get('outbound_module_enabled', false))
    <li class="relative">
      <button onclick="toggleOutboundDropdown()"
              class="px-4 py-2 rounded inline-flex items-center gap-2 font-medium text-sm {{ request()->routeIs('outbound.*') ? 'bg-indigo-500 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100' }}">
        <span class="inline-block align-middle" style="line-height: 1; vertical-align: middle;">📦</span>
        <span>Outbound</span>
        <svg id="outbound-arrow" class="w-4 h-4 transform transition-transform" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
      </button>
      <div id="outbound-dropdown" class="absolute left-0 mt-2 w-56 bg-white border border-gray-200 rounded-md shadow-lg z-20 hidden">
        <div class="px-4 py-2 text-xs font-semibold text-indigo-400 uppercase border-b border-gray-100">
          🧪 Beta - Testing Phase
        </div>
        <a href="{{ route('outbound.dashboard') }}"
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('outbound.dashboard') ? 'bg-indigo-50 text-indigo-600' : '' }}">
          📊 Outbound Dashboard
          <div class="text-xs text-gray-500 mt-1">Overview of delivery operations</div>
        </a>
        <a href="{{ route('outbound.loads.index') }}"
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('outbound.loads.*') ? 'bg-indigo-50 text-indigo-600' : '' }}">
          📦 Load Management
          <div class="text-xs text-gray-500 mt-1">Create and manage delivery loads</div>
        </a>
        <a href="{{ route('outbound.arrivals.dashboard') }}"
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('outbound.arrivals.*') ? 'bg-indigo-50 text-indigo-600' : '' }}">
          🚛 Driver Arrivals
          <div class="text-xs text-gray-500 mt-1">Register loads when drivers arrive</div>
        </a>
        <a href="{{ route('outbound.imports.dashboard') }}"
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('outbound.imports.*') ? 'bg-indigo-50 text-indigo-600' : '' }}">
          📁 WMS File Imports
          <div class="text-xs text-gray-500 mt-1">Upload and process WMS files</div>
        </a>
        <a href="{{ route('outbound.addresses.index') }}"
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('outbound.addresses.*') ? 'bg-indigo-50 text-indigo-600' : '' }}">
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
    @endif
    <li class="relative">
      <button onclick="toggleSettingsDropdown()"
              class="px-4 py-2 rounded inline-flex items-center gap-2 font-medium text-sm {{ request()->routeIs('app.settings.*') || request()->routeIs('app.depots.*') || request()->routeIs('app.products.*') || request()->routeIs('app.customers.*') || request()->routeIs('app.slot-templates.*') || request()->routeIs('app.booking-types.*') || request()->routeIs('app.bay-capacity-rules.*') || request()->routeIs('app.duration-rules.*') || request()->routeIs('app.users.*') || request()->routeIs('app.custom-roles.*') ? 'bg-blue-500 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100' }}">
        <span class="inline-block align-middle" style="line-height: 1; vertical-align: middle;">⚙️</span>
        <span>Settings</span>
        <svg id="settings-arrow" class="w-4 h-4 transform transition-transform" fill="currentColor" viewBox="0 0 20 20">
          <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
      </button>
      <div id="settings-dropdown" class="absolute left-0 mt-2 w-56 bg-white border border-gray-200 rounded-md shadow-lg z-20 hidden">
        {{-- Site Configuration (for depot-admin and site-admin) --}}
        @if(auth()->user()->hasRole('depot-admin|site-admin'))
          <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase border-b border-gray-100">Site Configuration</div>
          <a href="{{ route('app.depots.index') }}"
             class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('app.depots.*') ? 'bg-blue-50 text-blue-600 font-medium' : '' }}">
            🏭 Depots
          </a>
          <a href="{{ route('app.products.index') }}"
             class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('app.products.*') ? 'bg-blue-50 text-blue-600 font-medium' : '' }}">
            📦 Products
          </a>
          <a href="{{ route('app.customers.index') }}"
             class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('app.customers.*') ? 'bg-blue-50 text-blue-600 font-medium' : '' }}">
            👥 Customers
          </a>
          <a href="{{ route('app.slot-templates.index') }}"
             class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('app.slot-templates.*') ? 'bg-blue-50 text-blue-600 font-medium' : '' }}">
            📅 Slot Templates
          </a>
          <a href="{{ route('app.booking-types.index') }}"
             class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('app.booking-types.*') ? 'bg-blue-50 text-blue-600 font-medium' : '' }}">
            📝 Booking Types
          </a>
          <a href="{{ route('app.bay-capacity-rules.index') }}"
             class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('app.bay-capacity-rules.*') ? 'bg-blue-50 text-blue-600 font-medium' : '' }}">
            🚪 Bay Capacity Rules
          </a>
          <a href="{{ route('app.duration-rules.index') }}"
             class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('app.duration-rules.*') ? 'bg-blue-50 text-blue-600 font-medium' : '' }}">
            ⏱️ Duration Rules
          </a>
        @endif

        {{-- Admin Settings (for admin only) --}}
        @if(auth()->user()->hasRole('admin'))
          @if(auth()->user()->hasRole('depot-admin|site-admin'))
            <div class="border-t border-gray-100 my-1"></div>
          @endif
          <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase border-b border-gray-100">Admin Settings</div>
          <a href="{{ route('app.settings.dashboard') }}"
             class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('app.settings.dashboard') ? 'bg-blue-50 text-blue-600 font-medium' : '' }}">
            🛠️ System Settings
          </a>
          <a href="{{ route('app.users.index') }}"
             class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('app.users.*') ? 'bg-blue-50 text-blue-600 font-medium' : '' }}">
            👤 User Management
          </a>
          <a href="{{ route('app.custom-roles.index') }}"
             class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('app.custom-roles.*') ? 'bg-blue-50 text-blue-600 font-medium' : '' }}">
            🏷️ Custom Roles
          </a>
        @endif

        {{-- Operational Settings (for all) --}}
        <div class="border-t border-gray-100 my-1"></div>
        <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase border-b border-gray-100">Operations</div>
        <a href="{{ route('app.settings.factory-tipping-targets') }}"
           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('app.settings.factory-tipping-targets*') ? 'bg-blue-50 text-blue-600 font-medium' : '' }}">
          🏭 Factory Tipping Targets
        </a>
      </div>
    </li>
    </ul>

    {{-- User Switching (Testing Only) --}}
    @if(!app()->isProduction() && session('original_admin_id'))
      <div class="flex items-center space-x-3">
        <span class="text-sm text-orange-600 font-medium">🔄 Testing as: {{ auth()->user()->name }}</span>
        <form action="{{ route('switch-back') }}" method="POST" class="inline">
          @csrf
          <button type="submit" class="px-3 py-1.5 bg-orange-500 text-white rounded text-sm hover:bg-orange-600 transition">
            Switch Back
          </button>
        </form>
      </div>
    @elseif(auth()->check() && ((!app()->isProduction()) || (auth()->user()->email === 'paul.carr@knowleslogistics.com')) && (auth()->user()->switch_user_enabled ?? false))
      <div class="relative">
        <select onchange="switchUser(this.value)" class="text-sm border border-gray-300 rounded px-3 py-2 bg-white hover:border-gray-400 transition">
          <option value="">🔄 Switch User</option>
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
