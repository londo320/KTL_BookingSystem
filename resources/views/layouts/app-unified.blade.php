<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50 dark:bg-gray-900">
        <div class="min-h-screen">
            <!-- Dynamic Navigation for All Users -->
            @auth
                @if(auth()->user()->hasRole('customer'))
                    @include('layouts.customer-nav')
                @else
                    @include('layouts.dynamic-nav')
                @endif
            @endauth

            <!-- Page Heading -->
            @hasSection('header')
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        @yield('header')
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                @yield('content')
            </main>

            <!-- Flash Messages -->
            @if(session('success'))
                <div id="flash-message" class="fixed top-20 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50">
                    <div class="flex items-center">
                        <span>✅ {{ session('success') }}</span>
                        <button onclick="document.getElementById('flash-message').remove()" class="ml-4 text-green-500 hover:text-green-700">×</button>
                    </div>
                </div>
                <script>
                    setTimeout(() => {
                        const msg = document.getElementById('flash-message');
                        if (msg) msg.remove();
                    }, 5000);
                </script>
            @endif

            @if(session('error'))
                <div id="flash-error" class="fixed top-20 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-50">
                    <div class="flex items-center">
                        <span>❌ {{ session('error') }}</span>
                        <button onclick="document.getElementById('flash-error').remove()" class="ml-4 text-red-500 hover:text-red-700">×</button>
                    </div>
                </div>
                <script>
                    setTimeout(() => {
                        const msg = document.getElementById('flash-error');
                        if (msg) msg.remove();
                    }, 5000);
                </script>
            @endif
        </div>

        <!-- Additional Scripts -->
        @stack('scripts')
    </body>
</html>