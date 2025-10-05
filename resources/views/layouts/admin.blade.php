<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin – @yield('title', 'Dashboard')</title>
    <!-- TailwindCSS from CDN -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.x/dist/tailwind.min.css" rel="stylesheet">
    <style>
        /* Optional custom styles for admin panel */
        .navbar { box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body class="bg-gray-100 font-sans">

    <!-- Unified Navigation -->
    @include('layouts.warehouse-nav')

    <!-- Main Content Section -->
    <main class="container mx-auto px-4">
        @yield('content')
    </main>

    <!-- TailwindJS & FontAwesome (for icons, e.g. dropdown) -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</body>
</html>
