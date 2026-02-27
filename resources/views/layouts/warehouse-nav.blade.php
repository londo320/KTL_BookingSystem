<nav class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('app.dashboard') }}">
                        <img src="{{ asset('images/ktl_logo.svg') }}"
                             alt="KLT Logo"
                             class="h-8 w-auto"
                             onerror="this.style.display='none'; document.getElementById('fallback-logo').style.display='block';">
                        <div id="fallback-logo" class="hidden w-8 h-8 bg-blue-600 rounded flex items-center justify-center">
                            <span class="text-white font-bold text-xs">KL</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-4 sm:-my-px sm:ml-10 sm:flex sm:items-center">
                    <!-- Dashboard -->
                    <a href="{{ route('app.dashboard') }}"
                       class="inline-flex items-center gap-2 px-1 pt-1 border-b-2 text-sm font-medium {{ request()->routeIs('app.dashboard') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        <span class="inline-block align-middle" style="line-height: 1; vertical-align: middle;">📊</span>
                        <span>Dashboard</span>
                    </a>

                    <!-- Bookings -->
                    @if(auth()->user()->hasFunction('bookings.view') || auth()->user()->hasFunction('bookings.view-streamlined') || auth()->user()->hasFunction('slots.view'))
                        <div class="relative">
                            <button onclick="toggleBookingsMenu()"
                                    class="inline-flex items-center gap-2 px-1 pt-1 border-b-2 border-transparent text-sm font-medium {{ request()->routeIs('app.bookings*') || request()->routeIs('app.slots.*') ? 'text-gray-900' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                <span class="inline-block align-middle" style="line-height: 1; vertical-align: middle;">📋</span>
                                <span>Bookings</span>
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="bookings-menu" class="hidden absolute top-full left-0 mt-1 w-56 bg-white rounded-md shadow-lg z-50 border border-gray-200">
                                @if(auth()->user()->hasFunction('bookings.view'))
                                    <a href="{{ route('app.bookings.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('app.bookings.index') ? 'bg-indigo-50 text-indigo-600 font-medium' : '' }}">
                                        📊 Bookings (Full View)
                                        <div class="text-xs text-gray-500 mt-0.5">Complete booking management</div>
                                    </a>
                                @endif
                                @if(auth()->user()->hasFunction('bookings.view-streamlined'))
                                    <a href="{{ route('app.bookings.streamlined') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ request()->routeIs('app.bookings.streamlined') ? 'bg-indigo-50 text-indigo-600 font-medium' : '' }}">
                                        ⚡ Bookings (Live View)
                                        <div class="text-xs text-gray-500 mt-0.5">Streamlined with live updates</div>
                                    </a>
                                @endif
                                @if(auth()->user()->hasFunction('slots.view'))
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <a href="{{ route('app.slots.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📅 Slots
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Operations -->
                    @if(auth()->user()->hasFunction('tipping-workflow.show') || auth()->user()->hasFunction('operations-control.view') || auth()->user()->hasFunction('queue-management.view') || auth()->user()->hasFunction('tipping-bays.view') || auth()->user()->hasFunction('depot-map.view'))
                        <div class="relative">
                            <button onclick="toggleOperationsMenu()"
                                    class="inline-flex items-center gap-2 px-1 pt-1 border-b-2 border-transparent text-sm font-medium {{ request()->routeIs('app.tipping-*') || request()->routeIs('app.operations-*') || request()->routeIs('app.queue-*') || request()->routeIs('app.depot-map.*') ? 'text-gray-900' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                <span class="inline-block align-middle" style="line-height: 1; vertical-align: middle;">🚛</span>
                                <span>Operations</span>
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="operations-menu" class="hidden absolute top-full left-0 mt-1 w-56 bg-white rounded-md shadow-lg z-50 border border-gray-200">
                                @if(auth()->user()->hasFunction('depot-map.view'))
                                    <a href="{{ route('app.depot-map.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        🗺️ Depot Map
                                    </a>
                                @endif
                                @if(auth()->user()->hasFunction('tipping-workflow.show'))
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase">Tipping</div>
                                    <a href="{{ route('app.tipping-workflow.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📊 Tipping Dashboard
                                    </a>
                                @endif
                                @if(auth()->user()->hasFunction('tipping-locations.view'))
                                    <a href="{{ route('app.tipping-locations.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📍 Drop Locations
                                    </a>
                                @endif
                                @if(auth()->user()->hasFunction('tipping-bays.view'))
                                    <a href="{{ route('app.tipping-bays.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        🚛 Tipping Bays
                                    </a>
                                @endif
                                @if(auth()->user()->hasFunction('queue-management.view'))
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase">Site Control</div>
                                    <a href="{{ route('app.queue-management') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        ⚡ Queue Management
                                    </a>
                                @endif
                                @if(auth()->user()->hasFunction('operations-control.view'))
                                    <a href="{{ route('app.operations-control') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        🎯 Operations Control
                                    </a>
                                @endif
                                @if(auth()->user()->hasFunction('trailer-operations-dashboard.view'))
                                    <a href="{{ route('app.trailer-operations-dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        🚛 Trailer Operations
                                    </a>
                                @endif
                                @if(auth()->user()->hasFunction('empty-unit-collection.view'))
                                    <a href="{{ route('app.empty-unit-collection') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📦 Empty Unit Collection
                                    </a>
                                @endif
                                @if(auth()->user()->hasFunction('factory-bookings.view'))
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <a href="{{ route('app.factory-bookings.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        🏭 Factory Inbound
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Management -->
                    @if(auth()->user()->hasFunction('customers.view') || auth()->user()->hasFunction('carriers.view') || auth()->user()->hasFunction('users.view') || auth()->user()->hasFunction('custom-roles.view') || auth()->user()->hasFunction('customer-behavior.view'))
                        <div class="relative">
                            <button onclick="toggleManagementMenu()"
                                    class="inline-flex items-center gap-2 px-1 pt-1 border-b-2 border-transparent text-sm font-medium {{ request()->routeIs('app.customers.*') || request()->routeIs('app.carriers.*') || request()->routeIs('app.users.*') || request()->routeIs('app.custom-roles.*') || request()->routeIs('app.customer-behavior.*') ? 'text-gray-900' : 'text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                <span class="inline-block align-middle" style="line-height: 1; vertical-align: middle;">👥</span>
                                <span>Management</span>
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="management-menu" class="hidden absolute top-full left-0 mt-1 w-56 bg-white rounded-md shadow-lg z-50 border border-gray-200">
                                @if(auth()->user()->hasFunction('customers.view'))
                                    <a href="{{ route('app.customers.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        🏭 Customers
                                    </a>
                                @endif
                                @if(auth()->user()->hasFunction('customer-behavior.view'))
                                    <a href="{{ route('app.customer-behavior.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📈 Customer Analysis
                                    </a>
                                @endif
                                @if(auth()->user()->hasFunction('carriers.view'))
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <a href="{{ route('app.carriers.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        🚚 Carriers
                                    </a>
                                @endif
                                @if(auth()->user()->hasFunction('users.view'))
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <a href="{{ route('app.users.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        👤 Users
                                    </a>
                                @endif
                                @if(auth()->user()->hasFunction('custom-roles.view'))
                                    <a href="{{ route('app.custom-roles.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        🏷️ Custom Roles
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Configuration -->
                    @if(auth()->user()->hasFunction('depots.view') || auth()->user()->hasFunction('products.view') || auth()->user()->hasFunction('slot-templates.view') || auth()->user()->hasFunction('booking-types.view') || auth()->user()->hasFunction('pallet-types.view') || auth()->user()->hasFunction('trailer-types.view') || auth()->user()->hasFunction('settings.manage'))
                        <div class="relative">
                            <button onclick="toggleConfigMenu()"
                                    class="inline-flex items-center gap-2 px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                <span class="inline-block align-middle" style="line-height: 1; vertical-align: middle;">⚙️</span>
                                <span>Configuration</span>
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="config-menu" class="hidden absolute top-full left-0 mt-1 w-56 bg-white rounded-md shadow-lg z-50 border border-gray-200">
                                @if(auth()->user()->hasFunction('depots.view'))
                                    <a href="{{ route('app.depots.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        🏢 Depots
                                    </a>
                                @endif
                                @if(auth()->user()->hasFunction('products.view'))
                                    <a href="{{ route('app.products.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📦 Products
                                    </a>
                                @endif
                                @if(auth()->user()->hasFunction('slot-templates.view'))
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase">Booking Setup</div>
                                    <a href="{{ route('app.slot-templates.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📋 Slot Templates
                                    </a>
                                @endif
                                @if(auth()->user()->hasFunction('booking-types.view'))
                                    <a href="{{ route('app.booking-types.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📝 Booking Types
                                    </a>
                                @endif
                                @if(auth()->user()->hasFunction('pallet-types.view'))
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase">Equipment Types</div>
                                    <a href="{{ route('app.settings.pallet-types') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📦 Pallet Types
                                    </a>
                                @endif
                                @if(auth()->user()->hasFunction('trailer-types.view'))
                                    <a href="{{ route('app.trailer-types.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        🚛 Trailer Types
                                    </a>
                                @endif
                                @if(auth()->user()->hasFunction('settings.manage'))
                                    <div class="border-t border-gray-100 my-1"></div>
                                    <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase">System</div>
                                    <a href="{{ route('app.settings.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        🛠️ System Settings
                                    </a>
                                    <a href="{{ route('app.settings.factory-tipping-targets') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        🏭 Factory Tipping Targets
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Reports -->
                    @if(auth()->user()->hasFunction('trailer-location-report.view') || auth()->user()->hasFunction('bookings.export.pdf') || auth()->user()->hasFunction('slot-usage.view'))
                        <div class="relative">
                            <button onclick="toggleReportsMenu()"
                                    class="inline-flex items-center gap-2 px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                <span class="inline-block align-middle" style="line-height: 1; vertical-align: middle;">📊</span>
                                <span>Reports</span>
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="reports-menu" class="hidden absolute top-full left-0 mt-1 w-56 bg-white rounded-md shadow-lg z-50 border border-gray-200">
                                @if(auth()->user()->hasFunction('trailer-location-report.view'))
                                    <a href="{{ route('app.trailer-location-report') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📍 Trailer Location Report
                                    </a>
                                @endif
                                @if(auth()->user()->hasFunction('bookings.export.pdf'))
                                    <a href="{{ route('app.bookings.index', ['export' => 'pdf']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📄 Booking Report
                                    </a>
                                @endif
                                @if(auth()->user()->hasFunction('slot-usage.view'))
                                    <a href="{{ route('app.slot-usage.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📈 Slot Usage Analytics
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Outbound (if enabled) -->
                    @if(\App\Models\Setting::get('outbound_module_enabled', false))
                        <div class="relative">
                            <button onclick="toggleOutboundMenu()"
                                    class="inline-flex items-center gap-2 px-1 pt-1 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                <span class="inline-block align-middle" style="line-height: 1; vertical-align: middle;">📦</span>
                                <span>Outbound</span>
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="outbound-menu" class="hidden absolute top-full left-0 mt-1 w-56 bg-white rounded-md shadow-lg z-50 border border-gray-200">
                                <div class="px-4 py-2 text-xs font-semibold text-indigo-400 uppercase border-b border-gray-100">
                                    🧪 Beta - Testing Phase
                                </div>
                                <a href="{{ route('outbound.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    📊 Outbound Dashboard
                                    <div class="text-xs text-gray-500 mt-0.5">Overview of delivery operations</div>
                                </a>
                                <a href="{{ route('outbound.loads.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    📦 Load Management
                                    <div class="text-xs text-gray-500 mt-0.5">Create and manage delivery loads</div>
                                </a>
                                <a href="{{ route('outbound.arrivals.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    🚛 Driver Arrivals
                                    <div class="text-xs text-gray-500 mt-0.5">Register loads when drivers arrive</div>
                                </a>
                                <a href="{{ route('outbound.imports.dashboard') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    📁 WMS File Imports
                                    <div class="text-xs text-gray-500 mt-0.5">Upload and process WMS files</div>
                                </a>
                                <a href="{{ route('outbound.addresses.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    📍 Customer Addresses
                                    <div class="text-xs text-gray-500 mt-0.5">Manage delivery addresses</div>
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <div class="px-4 py-2">
                                    <div class="text-xs text-gray-500">⚠️ Testing module - Safe to explore</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Right Side Navigation -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <!-- User Dropdown -->
                <div class="relative">
                    <button onclick="toggleUserMenu()"
                            class="flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
                        {{ Auth::user()->name }}
                        @if(Auth::user()->depot_id)
                            <span class="ml-1 text-xs text-gray-400">
                                ({{ Auth::user()->defaultDepot->name }})
                            </span>
                        @endif
                        <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div id="user-menu" class="hidden absolute right-0 top-full mt-1 w-48 bg-white rounded-md shadow-lg z-50 border border-gray-200">
                        <a href="{{ route('profile.edit') }}"
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            👤 Edit Profile
                        </a>

                        {{-- User Switching (Authorized Users Only) --}}
                        @if(auth()->check() && ((!app()->isProduction()) || (auth()->user()->email === 'paul.carr@knowleslogistics.com')) && (auth()->user()->switch_user_enabled ?? false))
                            <div class="border-t border-gray-100"></div>
                            @if(session('original_admin_id'))
                                <form method="POST" action="{{ route('switch-back') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-orange-600 hover:bg-orange-50 font-semibold">
                                        🔄 Switch Back to Admin
                                    </button>
                                </form>
                            @else
                                <div class="px-4 py-2">
                                    <select onchange="switchUser(this.value)" class="w-full text-sm border border-gray-300 rounded px-2 py-1 bg-white">
                                        <option value="">🔄 Switch User</option>
                                        @foreach(\App\Models\User::with('roles')->get() as $user)
                                            <option value="{{ $user->id }}">
                                                {{ $user->name }} ({{ $user->roles->pluck('name')->join(', ') ?: 'No Role' }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        @endif

                        <div class="border-t border-gray-100">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    🚪 Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile menu button -->
            <div class="-mr-2 flex items-center sm:hidden">
                <button onclick="toggleMobileMenu()"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Navigation Menu -->
    <div id="mobile-menu" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <a href="{{ route('app.dashboard') }}"
               class="block pl-3 pr-4 py-2 text-base font-medium {{ request()->routeIs('app.dashboard') ? 'text-indigo-700 border-l-4 border-indigo-500 bg-indigo-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50' }}">
                📊 Dashboard
            </a>

            @if(auth()->user()->hasFunction('bookings.view'))
                <a href="{{ route('app.bookings.index') }}"
                   class="block pl-3 pr-4 py-2 text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50">
                    📋 Bookings
                </a>
            @endif

            @if(auth()->user()->hasFunction('slots.view'))
                <a href="{{ route('app.slots.index') }}"
                   class="block pl-3 pr-4 py-2 text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50">
                    📅 Slots
                </a>
            @endif
        </div>
    </div>
</nav>

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

function toggleBookingsMenu() {
    const menu = document.getElementById('bookings-menu');
    menu.classList.toggle('hidden');
    closeOtherMenus('bookings-menu');
}

function toggleOperationsMenu() {
    const menu = document.getElementById('operations-menu');
    menu.classList.toggle('hidden');
    closeOtherMenus('operations-menu');
}

function toggleManagementMenu() {
    const menu = document.getElementById('management-menu');
    menu.classList.toggle('hidden');
    closeOtherMenus('management-menu');
}

function toggleReportsMenu() {
    const menu = document.getElementById('reports-menu');
    menu.classList.toggle('hidden');
    closeOtherMenus('reports-menu');
}

function toggleConfigMenu() {
    const menu = document.getElementById('config-menu');
    menu.classList.toggle('hidden');
    closeOtherMenus('config-menu');
}

function toggleUserMenu() {
    const menu = document.getElementById('user-menu');
    menu.classList.toggle('hidden');
    closeOtherMenus('user-menu');
}

function toggleOutboundMenu() {
    const menu = document.getElementById('outbound-menu');
    menu.classList.toggle('hidden');
    closeOtherMenus('outbound-menu');
}

function toggleMobileMenu() {
    const menu = document.getElementById('mobile-menu');
    menu.classList.toggle('hidden');
}

function closeOtherMenus(exceptMenuId) {
    const menuIds = ['bookings-menu', 'operations-menu', 'management-menu', 'reports-menu', 'config-menu', 'user-menu', 'outbound-menu'];
    menuIds.forEach(menuId => {
        if (menuId !== exceptMenuId) {
            const menu = document.getElementById(menuId);
            if (menu) menu.classList.add('hidden');
        }
    });
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.relative') && !event.target.closest('button')) {
        closeOtherMenus(null);
    }
});
</script>
