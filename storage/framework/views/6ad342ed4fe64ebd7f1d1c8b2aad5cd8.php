<nav class="bg-green-800 text-white">
    <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
        <div class="flex space-x-4">
            <a href="<?php echo e(route('site.dashboard')); ?>"
               class="hover:bg-green-700 px-3 py-2 rounded <?php echo e(request()->routeIs('site.dashboard') ? 'bg-green-700' : ''); ?>">
                🚪 Gate Dashboard
            </a>

            <a href="<?php echo e(route('app.bookings.index')); ?>"
               class="hover:bg-green-700 px-3 py-2 rounded <?php echo e(request()->routeIs('app.bookings.*') ? 'bg-green-700' : ''); ?>">
                📋 Bookings
            </a>

            <a href="<?php echo e(route('site.arrivals.index')); ?>"
               class="hover:bg-green-700 px-3 py-2 rounded <?php echo e(request()->routeIs('site.arrivals.*') ? 'bg-green-700' : ''); ?>">
                🚛 Live Arrivals
            </a>

            <a href="<?php echo e(route('site.departures.index')); ?>"
               class="hover:bg-green-700 px-3 py-2 rounded <?php echo e(request()->routeIs('site.departures.*') ? 'bg-green-700' : ''); ?>">
                🕒 Departures
            </a>

            <a href="<?php echo e(route('site.search')); ?>"
               class="hover:bg-green-700 px-3 py-2 rounded <?php echo e(request()->routeIs('site.search') ? 'bg-green-700' : ''); ?>">
                🔍 Quick Search
            </a>
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

            <span class="text-sm">
                👤 <?php echo e(auth()->user()->name); ?> 
                <span class="text-green-300">(Gate Operator)</span>
            </span>
            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="bg-red-600 hover:bg-red-700 px-3 py-2 rounded text-sm">
                    Logout
                </button>
            </form>
        </div>
    </div>
</nav><?php /**PATH /Users/londo/Herd/test/resources/views/layouts/site-admin-nav.blade.php ENDPATH**/ ?>