<nav class="bg-white border-b border-gray-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
        <div class="flex space-x-4">
            <a href="<?php echo e(route('depot.dashboard')); ?>"
               class="text-gray-800 hover:bg-gray-100 px-3 py-2 rounded <?php echo e(request()->routeIs('depot.dashboard') ? 'bg-indigo-100 text-indigo-800' : ''); ?>">
                🏢 Dashboard
            </a>

            <a href="<?php echo e(route('depot.bookings.index')); ?>"
               class="text-gray-800 hover:bg-gray-100 px-3 py-2 rounded <?php echo e(request()->routeIs('depot.bookings.*') ? 'bg-indigo-100 text-indigo-800' : ''); ?>">
                📋 Bookings
            </a>

            <a href="<?php echo e(route('depot.slots.index')); ?>"
               class="text-gray-800 hover:bg-gray-100 px-3 py-2 rounded <?php echo e(request()->routeIs('depot.slots.*') ? 'bg-indigo-100 text-indigo-800' : ''); ?>">
                ⏰ Slots
            </a>

            <a href="<?php echo e(route('depot.arrivals.index')); ?>"
               class="text-gray-800 hover:bg-gray-100 px-3 py-2 rounded <?php echo e(request()->routeIs('depot.arrivals.*') ? 'bg-indigo-100 text-indigo-800' : ''); ?>">
                🚛 Live Arrivals
            </a>

            <?php if(auth()->user()->hasRole('admin')): ?>
            <a href="<?php echo e(route('app.settings.dashboard')); ?>"
               class="text-gray-800 hover:bg-gray-100 px-3 py-2 rounded <?php echo e(request()->routeIs('app.settings.*') ? 'bg-indigo-100 text-indigo-800' : ''); ?>">
                🛠 Admin Settings
            </a>
            <?php endif; ?>
        </div>

        <div class="flex items-center space-x-4">
            
            <?php if(!app()->isProduction() && session('original_admin_id')): ?>
                <form method="POST" action="<?php echo e(route('switch-back')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="bg-orange-600 hover:bg-orange-700 px-2 py-1 rounded text-xs font-semibold">
                        🔄 Switch Back to Admin
                    </button>
                </form>
            <?php endif; ?>

            <span class="text-sm text-gray-800">
                👤 <?php echo e(auth()->user()->name); ?> 
                <span class="text-gray-600">(Depot Admin)</span>
            </span>
            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded text-sm">
                    Logout
                </button>
            </form>
        </div>
    </div>
</nav><?php /**PATH /Users/londo/Herd/test/resources/views/layouts/depot-admin-nav.blade.php ENDPATH**/ ?>