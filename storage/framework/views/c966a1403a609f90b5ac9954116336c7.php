<nav class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="<?php echo e(route('app.dashboard')); ?>">
                        <img src="<?php echo e(asset('images/ktl_logo.svg')); ?>" 
                             alt="KLT Logo" 
                             class="h-8 w-auto"
                             onerror="this.style.display='none'; document.getElementById('fallback-logo').style.display='block';">
                        <div id="fallback-logo" class="hidden w-8 h-8 bg-blue-600 rounded flex items-center justify-center">
                            <span class="text-white font-bold text-xs">KL</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <!-- Dashboard -->
                    <a href="<?php echo e(route('app.dashboard')); ?>" 
                       class="inline-flex items-center px-1 pt-1 text-sm font-medium <?php echo e(request()->routeIs('app.dashboard') ? 'border-b-2 border-indigo-500 text-gray-900' : 'text-gray-500 hover:text-gray-700'); ?>">
                        📊 Dashboard
                    </a>

                    <!-- Bookings -->
                    <?php if(auth()->user()->hasFunction('bookings.view')): ?>
                        <a href="<?php echo e(route('app.bookings.index')); ?>" 
                           class="inline-flex items-center px-1 pt-1 text-sm font-medium <?php echo e(request()->routeIs('app.bookings*') ? 'border-b-2 border-indigo-500 text-gray-900' : 'text-gray-500 hover:text-gray-700'); ?>">
                            📋 Bookings
                        </a>
                    <?php endif; ?>

                    <!-- Operations -->
                    <?php if(auth()->user()->hasFunction('tipping-workflow.show') || auth()->user()->hasFunction('operations-control.view') || auth()->user()->hasFunction('queue-management.view')): ?>
                        <div class="relative">
                            <button onclick="toggleOperationsMenu()" 
                                    class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 hover:text-gray-700">
                                🚛 Operations
                                <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="operations-menu" class="hidden absolute top-full left-0 mt-1 w-56 bg-white rounded-md shadow-lg z-50 border border-gray-200">
                                <?php if(auth()->user()->hasFunction('tipping-workflow.show')): ?>
                                    <a href="<?php echo e(route('app.tipping-workflow')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        🚛 Tipping Workflow
                                    </a>
                                <?php endif; ?>
                                <?php if(auth()->user()->hasFunction('operations-control.view')): ?>
                                    <a href="<?php echo e(route('app.operations-control')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        ⚙️ Operations Control
                                    </a>
                                <?php endif; ?>
                                <?php if(auth()->user()->hasFunction('queue-management.view')): ?>
                                    <a href="<?php echo e(route('app.queue-management')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📋 Queue Management
                                    </a>
                                <?php endif; ?>
                                <?php if(auth()->user()->hasFunction('factory-bookings.view')): ?>
                                    <a href="<?php echo e(route('app.factory-bookings.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        🏭 Factory Inbound
                                    </a>
                                <?php endif; ?>
                                <?php if(auth()->user()->hasFunction('depot-map.view')): ?>
                                    <a href="<?php echo e(route('app.depot-map.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        🗺️ Depot Map
                                    </a>
                                <?php endif; ?>
                                <a href="<?php echo e(route('app.trailer-operations-dashboard')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    🚛 Trailer Operations
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Management -->
                    <?php if(auth()->user()->hasFunction('customers.view') || auth()->user()->hasFunction('carriers.view') || auth()->user()->hasFunction('depots.view') || auth()->user()->hasFunction('users.view')): ?>
                        <div class="relative">
                            <button onclick="toggleManagementMenu()" 
                                    class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 hover:text-gray-700">
                                👥 Management
                                <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="management-menu" class="hidden absolute top-full left-0 mt-1 w-56 bg-white rounded-md shadow-lg z-50 border border-gray-200">
                                <?php if(auth()->user()->hasFunction('customers.view')): ?>
                                    <a href="<?php echo e(route('app.customers.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        🏭 Customers
                                    </a>
                                <?php endif; ?>
                                <?php if(auth()->user()->hasFunction('carriers.view')): ?>
                                    <a href="<?php echo e(route('app.carriers.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        🚚 Carriers
                                    </a>
                                <?php endif; ?>
                                <?php if(auth()->user()->hasFunction('users.view')): ?>
                                    <a href="<?php echo e(route('app.users.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        👥 Users
                                    </a>
                                <?php endif; ?>
                                <?php if(auth()->user()->hasFunction('custom-roles.view')): ?>
                                    <a href="<?php echo e(route('app.custom-roles.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        🏷️ Custom Roles
                                    </a>
                                <?php endif; ?>
                                <?php if(auth()->user()->hasFunction('customer-behavior.view')): ?>
                                    <a href="<?php echo e(route('app.customer-behavior.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📊 Customer Behavior
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Configuration -->
                    <?php if(auth()->user()->hasFunction('slots.view') || auth()->user()->hasFunction('tipping-bays.view') || auth()->user()->hasFunction('depots.view') || auth()->user()->hasFunction('booking-types.view')): ?>
                        <div class="relative">
                            <button onclick="toggleConfigMenu()" 
                                    class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 hover:text-gray-700">
                                ⚙️ Configuration
                                <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="config-menu" class="hidden absolute top-full left-0 mt-1 w-56 bg-white rounded-md shadow-lg z-50 border border-gray-200">
                                <?php if(auth()->user()->hasFunction('depots.view')): ?>
                                    <a href="<?php echo e(route('app.depots.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        🏢 Depots
                                    </a>
                                <?php endif; ?>
                                <?php if(auth()->user()->hasFunction('slots.view')): ?>
                                    <a href="<?php echo e(route('app.slots.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📅 Slots
                                    </a>
                                <?php endif; ?>
                                <?php if(auth()->user()->hasFunction('slot-templates.view')): ?>
                                    <a href="<?php echo e(route('app.slot-templates.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📋 Slot Templates
                                    </a>
                                <?php endif; ?>
                                <?php if(auth()->user()->hasFunction('tipping-bays.view')): ?>
                                    <a href="<?php echo e(route('app.tipping-bays.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        🏗️ Tipping Bays
                                    </a>
                                <?php endif; ?>
                                <?php if(auth()->user()->hasFunction('tipping-locations.view')): ?>
                                    <a href="<?php echo e(route('app.tipping-locations.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📍 Tipping Locations
                                    </a>
                                <?php endif; ?>
                                <?php if(auth()->user()->hasFunction('booking-types.view')): ?>
                                    <a href="<?php echo e(route('app.booking-types.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📝 Booking Types
                                    </a>
                                <?php endif; ?>
                                <?php if(auth()->user()->hasFunction('pallet-types.view')): ?>
                                    <a href="<?php echo e(route('app.settings.pallet-types')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📦 Pallet Types
                                    </a>
                                <?php endif; ?>
                                <?php if(auth()->user()->hasFunction('trailer-types.view')): ?>
                                    <a href="<?php echo e(route('app.trailer-types.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        🚛 Trailer Types
                                    </a>
                                <?php endif; ?>
                                <?php if(auth()->user()->hasFunction('settings.manage')): ?>
                                    <div class="border-t border-gray-200 my-2"></div>
                                    <a href="<?php echo e(route('app.settings.factory-tipping-targets')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        🏭 Factory Tipping Targets
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Reports -->
                    <?php if(auth()->user()->hasFunction('trailer-location-report.view') || auth()->user()->hasFunction('bookings.export.pdf') || auth()->user()->hasFunction('customer-behavior.view')): ?>
                        <div class="relative">
                            <button onclick="toggleReportsMenu()" 
                                    class="inline-flex items-center px-1 pt-1 text-sm font-medium text-gray-500 hover:text-gray-700">
                                📊 Reports
                                <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="reports-menu" class="hidden absolute top-full left-0 mt-1 w-56 bg-white rounded-md shadow-lg z-50 border border-gray-200">
                                <?php if(auth()->user()->hasFunction('trailer-location-report.view')): ?>
                                    <a href="<?php echo e(route('app.trailer-report')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📍 Trailer Location Report
                                    </a>
                                <?php endif; ?>
                                <?php if(auth()->user()->hasFunction('bookings.export.pdf')): ?>
                                    <a href="<?php echo e(route('app.bookings.index', ['export' => 'pdf'])); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📄 Booking Report
                                    </a>
                                <?php endif; ?>
                                <?php if(auth()->user()->hasFunction('customer-behavior.view')): ?>
                                    <a href="<?php echo e(route('app.customer-behavior.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📈 Customer Behavior Analysis
                                    </a>
                                <?php endif; ?>
                                <?php if(auth()->user()->hasFunction('slot-usage.view')): ?>
                                    <a href="<?php echo e(route('app.slot-usage.index')); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        📈 Slot Usage Analytics
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Side Navigation -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <!-- User Dropdown -->
                <div class="relative">
                    <button onclick="toggleUserMenu()" 
                            class="flex items-center px-3 py-2 text-sm font-medium text-gray-500 hover:text-gray-700">
                        <?php echo e(Auth::user()->name); ?>

                        <?php if(Auth::user()->depot_id): ?>
                            <span class="ml-1 text-xs text-gray-400">
                                (<?php echo e(Auth::user()->defaultDepot->name); ?>)
                            </span>
                        <?php endif; ?>
                        <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div id="user-menu" class="hidden absolute right-0 top-full mt-1 w-48 bg-white rounded-md shadow-lg z-50 border border-gray-200">
                        <a href="<?php echo e(route('profile.edit')); ?>" 
                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                            👤 Edit Profile
                        </a>
                        
                        <div class="border-t border-gray-100">
                            <form method="POST" action="<?php echo e(route('logout')); ?>">
                                <?php echo csrf_field(); ?>
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
            <a href="<?php echo e(route('app.dashboard')); ?>" 
               class="block pl-3 pr-4 py-2 text-base font-medium <?php echo e(request()->routeIs('app.dashboard') ? 'text-indigo-700 border-r-4 border-indigo-500 bg-indigo-50' : 'text-gray-600 hover:text-gray-800 hover:bg-gray-50'); ?>">
                📊 Dashboard
            </a>
            
            <?php if(auth()->user()->hasFunction('bookings.view')): ?>
                <a href="<?php echo e(route('app.bookings.index')); ?>" 
                   class="block pl-3 pr-4 py-2 text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50">
                    📋 Bookings
                </a>
            <?php endif; ?>
            
            <?php if(auth()->user()->hasFunction('factory.view')): ?>
                <a href="<?php echo e(route('app.factory-bookings.index')); ?>" 
                   class="block pl-3 pr-4 py-2 text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50">
                    🏭 Factory Inbound
                </a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<script>
function toggleOperationsMenu() {
    const menu = document.getElementById('operations-menu');
    menu.classList.toggle('hidden');
}

function toggleManagementMenu() {
    const menu = document.getElementById('management-menu');
    menu.classList.toggle('hidden');
}

function toggleReportsMenu() {
    const menu = document.getElementById('reports-menu');
    menu.classList.toggle('hidden');
}

function toggleConfigMenu() {
    const menu = document.getElementById('config-menu');
    menu.classList.toggle('hidden');
}

function toggleUserMenu() {
    const menu = document.getElementById('user-menu');
    menu.classList.toggle('hidden');
}

function toggleMobileMenu() {
    const menu = document.getElementById('mobile-menu');
    menu.classList.toggle('hidden');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    const operationsMenu = document.getElementById('operations-menu');
    const managementMenu = document.getElementById('management-menu');
    const reportsMenu = document.getElementById('reports-menu');
    const configMenu = document.getElementById('config-menu');
    const userMenu = document.getElementById('user-menu');
    
    if (!event.target.closest('.relative')) {
        operationsMenu?.classList.add('hidden');
        managementMenu?.classList.add('hidden');
        reportsMenu?.classList.add('hidden');
        configMenu?.classList.add('hidden');
        userMenu?.classList.add('hidden');
    }
});
</script><?php /**PATH /Users/londo/Herd/test/resources/views/layouts/warehouse-nav.blade.php ENDPATH**/ ?>