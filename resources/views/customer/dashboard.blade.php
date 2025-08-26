<x-app-layout>
    @include('layouts.customer-nav')
    
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            🏢 Customer Dashboard
        </h2>
    </x-slot>
    
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Customer Assignment Info -->
        <div class="mb-6 bg-blue-50 border-l-4 border-blue-400 p-4">
            <div class="flex">
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        <strong>Your Companies:</strong>
                        @if($userCustomers->isEmpty())
                            <span class="inline-block bg-red-100 px-2 py-1 rounded text-xs ml-1 text-red-600">
                                No companies assigned
                            </span>
                        @else
                            @foreach($userCustomers as $customer)
                                <span class="inline-block bg-blue-100 px-2 py-1 rounded text-xs ml-1">
                                    {{ $customer->name }}
                                </span>
                            @endforeach
                        @endif
                    </p>
                </div>
            </div>
        </div>
        
        <!-- Depot Filter -->
        @if($userDepots->count() > 1)
        <div class="mb-6">
            <form method="GET" class="flex items-center space-x-4">
                <label for="depot_filter" class="text-sm font-medium text-gray-700">Filter by Depot:</label>
                <select name="depot_id" id="depot_filter" class="border border-gray-300 rounded-md px-3 py-2 text-sm" onchange="this.form.submit()">
                    <option value="">All Depots</option>
                    @foreach($userDepots as $depot)
                        <option value="{{ $depot->id }}" {{ $depotFilter == $depot->id ? 'selected' : '' }}>
                            {{ $depot->name }}
                        </option>
                    @endforeach
                </select>
                @if($depotFilter)
                    <a href="{{ url()->current() }}" class="text-sm bg-gray-500 text-white px-3 py-2 rounded hover:bg-gray-600">
                        Clear Filter
                    </a>
                @endif
            </form>
        </div>
        @endif
        
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8 max-w-4xl mx-auto">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="text-2xl">📋</div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Today's Bookings
                                </dt>
                                <dd class="text-lg font-medium text-gray-900">
                                    {{ $stats['total_bookings'] ?? 0 }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="text-2xl">✅</div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Arrived
                                </dt>
                                <dd class="text-lg font-medium text-green-600">
                                    {{ $stats['arrived'] ?? 0 }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="text-2xl">🏢</div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    On Site
                                </dt>
                                <dd class="text-lg font-medium text-blue-600">
                                    {{ $stats['on_site'] ?? 0 }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Second Row of Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8 max-w-4xl mx-auto">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="text-2xl">🕒</div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Departed
                                </dt>
                                <dd class="text-lg font-medium text-gray-600">
                                    {{ $stats['departed'] ?? 0 }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="text-2xl">⏳</div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Outstanding
                                </dt>
                                <dd class="text-lg font-medium text-orange-600">
                                    {{ $stats['outstanding'] ?? 0 }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="text-2xl">🚨</div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">
                                    Late Runners
                                </dt>
                                <dd class="text-lg font-medium text-red-600">
                                    {{ $stats['late_runners'] ?? 0 }}
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Upcoming Bookings -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        🚛 Your Upcoming Arrivals (Next 3 Hours)
                    </h3>
                    @if($upcomingBookings->isEmpty())
                        <p class="text-gray-500 text-sm">No upcoming bookings in the next 3 hours.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($upcomingBookings as $booking)
                                <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-mono text-xs bg-blue-100 px-2 py-1 rounded">
                                                {{ $booking->booking_reference }}
                                            </span>
                                            <span class="font-medium">{{ $booking->customer->name ?? 'N/A' }}</span>
                                        </div>
                                        <div class="text-sm text-gray-600 mt-1">
                                            📍 {{ $booking->slot->depot->name }} • 
                                            ⏰ {{ $booking->slot->start_at->format('H:i') }}
                                            @if($booking->vehicle_registration)
                                                • 🚛 {{ $booking->vehicle_registration }}
                                            @endif
                                            @if($booking->container_number)
                                                • 📦 {{ $booking->container_number }}
                                            @endif
                                        </div>
                                        @if($booking->special_instructions)
                                            <div class="text-xs text-orange-600 mt-1">
                                                ⚠️ {{ Str::limit($booking->special_instructions, 50) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium">
                                            {{ $booking->slot->start_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Late Runners -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        🚨 Your Late Runners (Overdue)
                    </h3>
                    @if($lateRunnersData->isEmpty())
                        <p class="text-gray-500 text-sm">No overdue bookings.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($lateRunnersData as $booking)
                                <div class="flex items-center justify-between p-3 bg-red-50 border-l-4 border-red-400 rounded-lg">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-mono text-xs bg-red-100 px-2 py-1 rounded">
                                                {{ $booking->booking_reference }}
                                            </span>
                                            <span class="font-medium">{{ $booking->customer->name ?? 'N/A' }}</span>
                                        </div>
                                        <div class="text-sm text-gray-600 mt-1">
                                            📍 {{ $booking->slot->depot->name }}
                                        </div>
                                        @if($booking->estimated_arrival)
                                            <div class="text-sm text-blue-600 mt-1 font-medium">
                                                💬 Updated ETA: {{ \Carbon\Carbon::parse($booking->estimated_arrival)->format('d-M H:i') }}
                                            </div>
                                        @endif
                                        <div class="text-sm text-red-600 mt-1 font-medium">
                                            ⏰ Original: {{ $booking->slot->start_at->format('d-M H:i') }}
                                            @if($booking->vehicle_registration)
                                                • 🚛 {{ $booking->vehicle_registration }}
                                            @endif
                                            @if($booking->container_number)
                                                • 📦 {{ $booking->container_number }}
                                            @endif
                                        </div>
                                        @if($booking->special_instructions)
                                            <div class="text-xs text-orange-600 mt-1">
                                                ⚠️ {{ Str::limit($booking->special_instructions, 50) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-medium text-red-600">
                                            {{ $booking->slot->start_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Current Arrivals -->
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                        🏢 Your Vehicles Currently On Site
                    </h3>
                    @if($currentArrivals->isEmpty())
                        <p class="text-gray-500 text-sm">No vehicles currently on site.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($currentArrivals as $booking)
                                @php
                                    $isFactory = isset($booking->type) && $booking->type === 'factory';
                                    $bgColor = $isFactory ? 'bg-purple-50' : 'bg-green-50';
                                    $borderColor = $isFactory ? 'border-l-4 border-purple-400' : '';
                                @endphp
                                <div class="flex items-center justify-between p-3 {{ $bgColor }} {{ $borderColor }} rounded-lg">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            @if($isFactory)
                                                <span class="font-mono text-xs bg-purple-100 px-2 py-1 rounded">
                                                    🏭 {{ $booking->booking_reference }}
                                                </span>
                                            @else
                                                <span class="font-mono text-xs bg-blue-100 px-2 py-1 rounded">
                                                    {{ $booking->booking_reference }}
                                                </span>
                                            @endif
                                            <span class="font-medium">{{ $booking->customer->name ?? 'N/A' }}</span>
                                            @if($isFactory)
                                                <span class="text-xs bg-purple-500 text-white px-2 py-1 rounded">Factory</span>
                                            @endif
                                        </div>
                                        <div class="text-sm text-gray-600 mt-1">
                                            📍 {{ $booking->slot->depot->name }} • 
                                            ✅ Arrived: {{ $booking->arrived_at->format('H:i') }}
                                            @if($booking->vehicle_registration)
                                                • 🚛 {{ $booking->vehicle_registration }}
                                            @endif
                                            @if($booking->trailer_registration)
                                                • 🚚 {{ $booking->trailer_registration }}
                                            @endif
                                            @if($booking->container_number)
                                                • 📦 {{ $booking->container_number }}
                                            @endif
                                        </div>
                                        @if(!$isFactory && ($booking->gate_number || $booking->bay_number))
                                            <div class="text-xs text-blue-600 mt-1">
                                                @if($booking->gate_number)🚪 Gate {{ $booking->gate_number }} @endif
                                                @if($booking->bay_number)🏗️ Bay {{ $booking->bay_number }}@endif
                                            </div>
                                        @endif
                                    </div>
                                    <div class="text-right">
                                        <div class="text-xs text-gray-500">
                                            On site: {{ $booking->arrived_at->diffForHumans() }}
                                        </div>
                                        @if($isFactory)
                                            <span class="text-xs bg-gray-400 text-white px-2 py-1 rounded">
                                                Factory Vehicle
                                            </span>
                                        @else
                                            <a href="{{ route('customer.bookings.show', $booking) }}" 
                                               class="text-xs bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600">
                                                View Details
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="mt-8 bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                    ⚡ Quick Actions
                </h3>
                <div class="flex flex-wrap gap-4">
                    <a href="{{ route('customer.bookings.create') }}" 
                       class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        📦 Book a Slot
                    </a>
                    <a href="{{ route('customer.bookings.index') }}" 
                       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        📋 View My Bookings
                    </a>
                    @if($userCustomers->count() > 1)
                        <a href="#" class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700">
                            🏢 Switch Company
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Auto-refresh the page every 5 minutes (300,000 milliseconds)
        setTimeout(function() {
            window.location.reload();
        }, 300000);
        // Show a refresh indicator in page title
        let refreshCounter = 300; // 5 minutes in seconds
        const originalTitle = document.title;
        function updateRefreshIndicator() {
            const minutes = Math.floor(refreshCounter / 60);
            const seconds = refreshCounter % 60;
            document.title = `${originalTitle} - Auto-refresh in ${minutes}:${seconds.toString().padStart(2, '0')}`;
            if (refreshCounter <= 0) {
                document.title = `${originalTitle} - Refreshing...`;
                return;
            }
            refreshCounter--;
        }
        // Update countdown every second
        setInterval(updateRefreshIndicator, 1000);
        updateRefreshIndicator(); // Initial call
    </script>
</x-app-layout>
