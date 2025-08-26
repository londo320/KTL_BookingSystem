@extends('layouts.admin')

@section('title', 'Driver Arrivals Dashboard')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Driver Arrivals</h1>
                    <p class="text-gray-600 mt-1">Register driver arrivals and match with WMS orders</p>
                </div>
                <div class="flex space-x-3">
                    <form method="POST" action="{{ route('outbound.arrivals.process-all') }}" class="inline">
                        @csrf
                        <button type="submit" 
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md font-medium">
                            Process All Pending
                        </button>
                    </form>
                    <a href="{{ route('outbound.arrivals.create') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">
                        Register New Arrival
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM21 17a2 2 0 11-4 0 2 2 0 014 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 17h16a2 2 0 002-2V7a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500">Today's Arrivals</h3>
                        <p class="text-2xl font-semibold text-gray-900">{{ $statistics['today_arrivals'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500">Awaiting Matching</h3>
                        <p class="text-2xl font-semibold text-gray-900">{{ $statistics['awaiting_matching'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-orange-100 rounded-lg">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500">Pending WMS Orders</h3>
                        <p class="text-2xl font-semibold text-gray-900">{{ $statistics['pending_wms_orders'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500">Matched Today</h3>
                        <p class="text-2xl font-semibold text-gray-900">{{ $statistics['matched_today'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Today's Arrivals -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Today's Arrivals</h2>
                </div>
                <div class="p-6">
                    @if($todayArrivals->count() > 0)
                        <div class="space-y-4">
                            @foreach($todayArrivals as $arrival)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <h3 class="font-medium text-gray-900">{{ $arrival->load_reference }}</h3>
                                            <span class="px-2 py-1 text-xs rounded-full {{ $arrival->status_badge }}">
                                                {{ ucfirst(str_replace('_', ' ', $arrival->status)) }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{ $arrival->vehicle_registration }} • {{ $arrival->driver_name }}
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            {{ $arrival->carrier_company }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            {{ $arrival->arrival_time->format('H:i') }} at {{ $arrival->arrivalDepot->name }}
                                        </p>
                                        @if($arrival->expected_orders > 0)
                                            <div class="mt-2">
                                                <div class="flex items-center text-xs text-gray-500">
                                                    <span>Orders: {{ $arrival->matched_orders }} / {{ $arrival->expected_orders }}</span>
                                                    <div class="ml-2 flex-1 bg-gray-200 rounded-full h-1">
                                                        <div class="bg-blue-600 h-1 rounded-full" style="width: {{ $arrival->matching_progress }}%"></div>
                                                    </div>
                                                    <span class="ml-2">{{ round($arrival->matching_progress) }}%</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="ml-4">
                                        <a href="{{ route('outbound.physical-loads.show', $arrival) }}" 
                                           class="text-blue-600 hover:text-blue-900">View</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No arrivals today</p>
                    @endif
                </div>
            </div>

            <!-- Awaiting Matching -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Awaiting Order Matching</h2>
                </div>
                <div class="p-6">
                    @if($awaitingMatching->count() > 0)
                        <div class="space-y-4">
                            @foreach($awaitingMatching as $load)
                                <div class="flex items-center justify-between p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <h3 class="font-medium text-gray-900">{{ $load->load_reference }}</h3>
                                            <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">
                                                Waiting
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{ $load->vehicle_registration }} • {{ $load->driver_name }}
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            Arrived {{ $load->arrival_time->diffForHumans() }}
                                        </p>
                                        @if($load->expected_orders > 0)
                                            <p class="text-xs text-gray-500 mt-1">
                                                Expected {{ $load->expected_orders }} orders
                                            </p>
                                        @endif
                                    </div>
                                    <div class="ml-4 space-y-2">
                                        <form method="POST" action="{{ route('outbound.physical-loads.trigger-matching', $load) }}" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="text-blue-600 hover:text-blue-900 text-sm">
                                                Try Match
                                            </button>
                                        </form>
                                        <br>
                                        <a href="{{ route('outbound.physical-loads.show', $load) }}" 
                                           class="text-gray-600 hover:text-gray-900 text-sm">View</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">No loads awaiting matching</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-start">
                <svg class="w-6 h-6 text-blue-400 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="ml-4 flex-1">
                    <h3 class="text-lg font-medium text-blue-900">Driver Arrival Workflow</h3>
                    <div class="mt-2 text-blue-700">
                        <ol class="list-decimal list-inside space-y-1">
                            <li><strong>Driver Arrives:</strong> Click "Register New Arrival" and enter load details from paperwork</li>
                            <li><strong>Upload WMS Files:</strong> Go to <a href="{{ route('outbound.imports.dashboard') }}" class="underline">WMS File Imports</a> and upload order files</li>
                            <li><strong>Automatic Matching:</strong> System matches orders to physical loads by reference number</li>
                            <li><strong>Ready for Collection:</strong> Driver can proceed once orders are matched and verified</li>
                        </ol>
                    </div>
                    <div class="mt-4 space-x-4">
                        <a href="{{ route('outbound.arrivals.create') }}" 
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm">
                            Register Driver Arrival
                        </a>
                        <a href="{{ route('outbound.imports.dashboard') }}" 
                           class="bg-white hover:bg-blue-50 text-blue-600 border border-blue-300 px-4 py-2 rounded-md text-sm">
                            Upload WMS Files
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection