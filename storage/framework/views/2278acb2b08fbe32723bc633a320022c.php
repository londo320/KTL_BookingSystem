<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – <?php echo $__env->yieldContent('title', 'Dashboard'); ?></title>
    <!-- TailwindCSS from CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.x/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Optional custom styles for admin panel */
        .navbar { box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body class="bg-gray-100 font-sans">

    <!-- Admin Navigation -->
    <?php echo $__env->make('layouts.admin-nav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Main Content Section -->
    <main class="container mx-auto px-4">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- TailwindJS & FontAwesome (for icons, e.g. dropdown) -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
<?php /**PATH /Users/londo/Herd/test/resources/views/layouts/admin.blade.php ENDPATH**/ ?>