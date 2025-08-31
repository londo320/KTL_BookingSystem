<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        <title><?php echo e(config('app.name', 'Laravel')); ?></title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-900">
        <div class="min-h-screen">
            <!-- Dynamic Navigation for All Users -->
            <?php if(auth()->guard()->check()): ?>
                <?php if(auth()->user()->hasRole('customer')): ?>
                    <?php echo $__env->make('layouts.customer-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php else: ?>
                    <?php echo $__env->make('layouts.dynamic-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
            <?php endif; ?>

            <!-- Page Heading -->
            <?php if (! empty(trim($__env->yieldContent('header')))): ?>
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <?php echo $__env->yieldContent('header'); ?>
                    </div>
                </header>
            <?php endif; ?>

            <!-- Page Content -->
            <main>
                <?php echo $__env->yieldContent('content'); ?>
            </main>

            <!-- Flash Messages -->
            <?php if(session('success')): ?>
                <div id="flash-message" class="fixed top-20 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50">
                    <div class="flex items-center">
                        <span>✅ <?php echo e(session('success')); ?></span>
                        <button onclick="document.getElementById('flash-message').remove()" class="ml-4 text-green-500 hover:text-green-700">×</button>
                    </div>
                </div>
                <script>
                    setTimeout(() => {
                        const msg = document.getElementById('flash-message');
                        if (msg) msg.remove();
                    }, 5000);
                </script>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div id="flash-error" class="fixed top-20 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-50">
                    <div class="flex items-center">
                        <span>❌ <?php echo e(session('error')); ?></span>
                        <button onclick="document.getElementById('flash-error').remove()" class="ml-4 text-red-500 hover:text-red-700">×</button>
                    </div>
                </div>
                <script>
                    setTimeout(() => {
                        const msg = document.getElementById('flash-error');
                        if (msg) msg.remove();
                    }, 5000);
                </script>
            <?php endif; ?>
        </div>

        <!-- Additional Scripts -->
        <?php echo $__env->yieldPushContent('scripts'); ?>
    </body>
</html><?php /**PATH /Users/londo/Herd/test/resources/views/layouts/app-unified.blade.php ENDPATH**/ ?>