<nav class="bg-gray-800 text-white">
    <div class="max-w-7xl mx-auto px-4 py-3 flex justify-between items-center">
        <div class="flex space-x-4">
            <a href="<?php echo e(route('customer.dashboard')); ?>"
               class="hover:bg-gray-700 px-3 py-2 rounded <?php echo e(request()->routeIs('customer.dashboard') ? 'bg-gray-700' : ''); ?>">
                Dashboard
            </a>

            <a href="<?php echo e(route('customer.bookings.index')); ?>"
               class="hover:bg-gray-700 px-3 py-2 rounded <?php echo e(request()->routeIs('customer.bookings.index') ? 'bg-gray-700' : ''); ?>">
                My Bookings
            </a>

            <a href="<?php echo e(route('customer.bookings.create')); ?>"
               class="hover:bg-gray-700 px-3 py-2 rounded <?php echo e(request()->routeIs('customer.bookings.create') ? 'bg-gray-700' : ''); ?>">
                Book a Slot
            </a>
        </div>

        <div class="flex space-x-2">
            
            <?php if(!app()->isProduction() && session('original_admin_id')): ?>
                <form method="POST" action="<?php echo e(route('switch-back')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="bg-orange-600 hover:bg-orange-700 px-3 py-2 rounded text-sm font-semibold">
                        🔄 Switch Back to Admin
                    </button>
                </form>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="bg-red-600 hover:bg-red-700 px-3 py-2 rounded text-sm">
                    Logout
                </button>
            </form>
        </div>
    </div>
</nav>
<?php /**PATH /Users/londo/Herd/test/resources/views/layouts/customer-nav.blade.php ENDPATH**/ ?>