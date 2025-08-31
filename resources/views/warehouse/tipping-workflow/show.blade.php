<x-warehouse-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Tipping Workflow</h2>
                @if($booking instanceof \App\Models\FactoryBooking)
                    <p class="text-sm text-gray-600 mt-1">
                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium mr-2">📍 {{ $booking->depot->name }}</span>
                        <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded text-xs font-medium mr-2">FACTORY</span>
                        {{ $booking->reference }} - {{ $booking->customer->name }}
                    </p>
                @else
                    <p class="text-sm text-gray-600 mt-1">
                        <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium mr-2">📍 {{ $booking->slot->depot->name }}</span>
                        Booking {{ $booking->booking_reference ?: '#' . $booking->id }} - {{ $booking->customer->name }}
                    </p>
                @endif
            </div>
            <div class="flex space-x-2">
                @if($booking instanceof \App\Models\FactoryBooking)
                    <a href="{{ route('app.factory-bookings.show', $booking) }}" 
                       class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        ← Back to Factory Booking
                    </a>
                @else
                    <a href="{{ route('app.bookings.show', $booking) }}" 
                       class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        ← Back to Booking
                    </a>
                @endif
                <a href="{{ route('app.tipping-workflow.dashboard') }}" 
                   class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    📊 Tipping Dashboard
                </a>
            </div>
        </div>
    </x-slot>
    <div class="py-6 max-w-6xl mx-auto">
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <h4 class="font-medium">Please fix the following errors:</h4>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{-- Booking Information --}}
        <div class="mb-6 p-6 bg-blue-50 border border-blue-200 rounded-lg">
            <h3 class="text-lg font-semibold text-blue-800 mb-3">📋 Booking Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Booking Reference</p>
                    <p class="font-medium">{{ $booking->booking_reference ?: '#' . $booking->id }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Customer</p>
                    <p class="font-medium">{{ $booking->customer->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">PO References</p>
                    <p class="font-medium">
                        @if($booking->poNumbers && $booking->poNumbers->count() > 0)
                            {{ $booking->poNumbers->pluck('po_number')->join(', ') }}
                        @else
                            Not provided
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Slot Time</p>
                    <p class="font-medium">{{ $booking->slot->start_at->format('D, d M Y - H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Container</p>
                    <p class="font-medium">{{ $booking->container_number ?: 'Not specified' }}</p>
                </div>
            </div>
        </div>
        {{-- PO Numbers & Load Details --}}
        @if($booking->poNumbers && $booking->poNumbers->count() > 0)
            <div class="mb-6 p-6 bg-gray-50 border border-gray-200 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">📦 PO Numbers & Load Details</h3>
                <div class="space-y-4">
                    @foreach($booking->poNumbers as $poNumber)
                        <div class="border border-gray-300 rounded-lg p-4 bg-white">
                            <div class="flex justify-between items-start mb-3">
                                <h4 class="font-medium text-lg text-gray-800">PO: {{ $poNumber->po_number }}</h4>
                                <div class="flex space-x-2">
                                    @if($poNumber->hasVariance())
                                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">
                                            ⚠️ Has Variance
                                        </span>
                                    @endif
                                    @if($poNumber->hasTypeVariances())
                                        <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded-full">
                                            🔄 Type Variance
                                        </span>
                                    @endif
                                </div>
                            </div>
                            {{-- PO Summary --}}
                            <div class="mb-3 p-3 bg-gray-50 rounded border">
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-600">Expected:</span>
                                        <span class="font-semibold">{{ number_format($poNumber->total_expected_units) }} units, {{ number_format($poNumber->total_expected_pallets) }} pallets</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Actual:</span>
                                        <span class="font-semibold {{ $poNumber->total_actual_units > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                            {{ $poNumber->total_actual_units > 0 ? number_format($poNumber->total_actual_units) . ' units' : 'Not recorded' }}, 
                                            @if($poNumber->total_actual_pallets > 0)
                                                @if(!empty($poNumber->actual_pallet_breakdown))
                                                    @foreach($poNumber->actual_pallet_breakdown as $index => $breakdown)
                                                        {{ $breakdown['count'] }} {{ $breakdown['type'] }}{{ $index < count($poNumber->actual_pallet_breakdown) - 1 ? ', ' : '' }}
                                                    @endforeach
                                                    ({{ number_format($poNumber->total_actual_pallets) }} total)
                                                @else
                                                    {{ number_format($poNumber->total_actual_pallets) }} pallets
                                                @endif
                                            @else
                                                Not recorded
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>
                            {{-- PO Lines Summary --}}
                            @if($poNumber->lines->count() > 0)
                                <div class="text-sm text-gray-600">
                                    <span class="font-medium">{{ $poNumber->lines->count() }} line(s)</span>
                                    @if($poNumber->lines->where('actual_cases', '>', 0)->count() > 0)
                                        <span class="ml-2 text-green-600">• {{ $poNumber->lines->where('actual_cases', '>', 0)->count() }} recorded</span>
                                    @endif
                                    @if($poNumber->lines->filter(fn($line) => $line->hasVariance())->count() > 0)
                                        <span class="ml-2 text-red-600">• {{ $poNumber->lines->filter(fn($line) => $line->hasVariance())->count() }} with variance</span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
                {{-- Summary Totals --}}
                @if($booking->poNumbers->count() > 1)
                    <div class="border-t pt-4 mt-4 bg-white p-3 rounded">
                        <h5 class="font-medium text-gray-800 mb-2">📊 Summary Totals</h5>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">Total Expected:</span>
                                <span class="font-semibold">{{ number_format($booking->total_expected_cases) }} units, {{ number_format($booking->total_expected_pallets) }} pallets</span>
                            </div>
                            <div>
                                <span class="text-gray-600">Total Actual:</span>
                                <span class="font-semibold {{ $booking->total_actual_cases > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ $booking->total_actual_cases > 0 ? number_format($booking->total_actual_cases) . ' units' : 'Not recorded' }}, 
                                    {{ $booking->total_actual_pallets > 0 ? number_format($booking->total_actual_pallets) . ' pallets' : 'Not recorded' }}
                                </span>
                                @if($booking->total_actual_pallets > 0)
                                    <div class="text-xs text-gray-500 mt-1">
                                        @php
                                            $allPalletBreakdown = [];
                                            foreach($booking->poNumbers as $po) {
                                                foreach($po->actual_pallet_breakdown as $breakdown) {
                                                    if (!isset($allPalletBreakdown[$breakdown['type']])) {
                                                        $allPalletBreakdown[$breakdown['type']] = 0;
                                                    }
                                                    $allPalletBreakdown[$breakdown['type']] += $breakdown['count'];
                                                }
                                            }
                                        @endphp
                                        @if(!empty($allPalletBreakdown))
                                            Breakdown: 
                                            @foreach($allPalletBreakdown as $type => $count)
                                                {{ $count }} {{ $type }}{{ !$loop->last ? ', ' : '' }}
                                            @endforeach
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                        @if($booking->hasPoVariances())
                            <div class="mt-2 text-sm text-red-600">
                                ⚠️ This booking has quantity or type variances
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @else
            <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex items-center text-yellow-800">
                    <span class="text-2xl mr-2">📦</span>
                    <div>
                        <h4 class="font-medium">No PO Numbers</h4>
                        <p class="text-sm text-yellow-700">No purchase order numbers have been added to this booking.</p>
                    </div>
                </div>
            </div>
        @endif
        {{-- Workflow Status Notice --}}
        @if(!$workflowEnabled)
            <div class="mb-6 p-4 bg-yellow-100 border border-yellow-400 text-yellow-800 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <h4 class="font-medium">Manual Tipping Mode</h4>
                        <p class="text-sm mt-1">Workflow enforcement is disabled. You can perform actions in any order without restrictions.</p>
                    </div>
                </div>
            </div>
        @endif
        {{-- Tipping Status Progress --}}
        <div class="mb-6 bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800">🚛 Tipping Progress</h3>
                @php
                    $movement = $booking->movements()->first();
                    $currentStatus = $movement ? $movement->current_status : 'scheduled';
                    // Create status badge based on movement status
                    $statusLabels = [
                        'scheduled' => ['🕐 Scheduled', 'bg-gray-100 text-gray-800'],
                        'arrived' => ['✅ Arrived', 'bg-blue-100 text-blue-800'], 
                        'in_parking' => ['🚛 Unit & Trailer in Parking', 'bg-blue-100 text-blue-800'],
                        'at_bay' => ['🏗️ At Tipping Bay - Full', 'bg-orange-100 text-orange-800'],
                        'unloading' => ['⚡ Tipping in Progress', 'bg-orange-100 text-orange-800'],
                        'empty' => ['✅ Tipped - Ready for Collection', 'bg-green-100 text-green-800'],
                        'back_to_parking' => ['📍 In Parking Area', 'bg-purple-100 text-purple-800'],
                        'departed' => ['🏁 Departed', 'bg-green-100 text-green-800'],
                        'trailer_dropped' => ['📍 Trailer Dropped', 'bg-yellow-100 text-yellow-800'],
                    ];
                    $statusConfig = $statusLabels[$currentStatus] ?? ['❓ Unknown Status', 'bg-gray-100 text-gray-800'];
                @endphp
                <p class="text-sm text-gray-600 mt-1">
                    Current Status: <span class="px-2 py-1 rounded text-xs font-medium {{ $statusConfig[1] }}">{{ $statusConfig[0] }}</span>
                    @if(!$workflowEnabled)
                        <span class="ml-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Manual Mode</span>
                    @endif
                </p>
            </div>
            {{-- Progress Timeline --}}
            <div class="p-6">
                <div class="flex items-center justify-between mb-8">
                    @php
                        $isEmptyTrailer = $movement && in_array($movement->current_status, ['empty', 'departed']) && $movement->unloading_completed_at;
                        // Dynamic stages based on whether trailer is empty or not
                        if ($isEmptyTrailer) {
                            $stages = [
                                'scheduled' => ['label' => '⏳ Not Started', 'icon' => 'text-gray-400'],
                                'in_parking' => ['label' => '✅ Empty Unit Positioned', 'icon' => 'text-green-500'],
                                'back_to_parking' => ['label' => '✅ Empty Trailer in Parking', 'icon' => 'text-green-500'],
                                'departed' => ['label' => '🏁 Departed', 'icon' => 'text-purple-500']
                            ];
                        } else {
                            $stages = [
                                'scheduled' => ['label' => '⏳ Not Started', 'icon' => 'text-gray-400'],
                                'in_parking' => ['label' => '🚛 Unit & Trailer in Parking', 'icon' => 'text-blue-500'],
                                'at_bay' => ['label' => '⚡ Tipping (Auto-started)', 'icon' => 'text-orange-500'],
                                'unloading' => ['label' => '⚡ Tipping (Auto-started)', 'icon' => 'text-orange-500'],
                                'empty' => ['label' => '✅ Tipped - Ready for Collection', 'icon' => 'text-green-500'],
                                'back_to_parking' => ['label' => '📍 In Parking Area', 'icon' => 'text-purple-500'],
                                'departed' => ['label' => '🏁 Collected', 'icon' => 'text-blue-600']
                            ];
                        }
                        // Use actual movement status instead of booking tipping_status
                        $currentIndex = array_search($currentStatus, array_keys($stages));
                        // If status not found in current stage set, find the closest match
                        if ($currentIndex === false) {
                            if ($currentStatus === 'arrived') $currentIndex = 1; // Map to in_parking stage
                            elseif ($currentStatus === 'scheduled') $currentIndex = 0;
                            else $currentIndex = -1;
                        }
                    @endphp
                    @foreach($stages as $status => $config)
                        @php 
                            $stepIndex = array_search($status, array_keys($stages));
                            $isCompleted = $stepIndex <= $currentIndex;
                            $isCurrent = $stepIndex === $currentIndex;
                            // Dynamic step colors based on stage and completion
                            if ($isCurrent) {
                                $stepClass = 'bg-blue-500 text-white ring-2 ring-blue-200';
                            } elseif ($isCompleted) {
                                if ($isEmptyTrailer) {
                                    $stepClass = 'bg-green-500 text-white';
                                } else {
                                    $stepClass = 'bg-green-500 text-white';
                                }
                            } else {
                                $stepClass = 'bg-gray-200 text-gray-500';
                            }
                        @endphp
                        <div class="flex flex-col items-center relative {{ !$loop->last ? 'flex-1' : '' }}">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-semibold {{ $stepClass }}">
                                @if($isCompleted && !$isCurrent)
                                    ✓
                                @else
                                    {{ $loop->iteration }}
                                @endif
                            </div>
                            <p class="text-xs mt-2 text-center max-w-20 {{ $isCurrent ? 'font-bold text-blue-600' : ($isCompleted ? 'font-medium text-gray-700' : 'text-gray-500') }}">
                                {{ $config['label'] }}
                            </p>
                            @if(!$loop->last)
                                @php
                                    $nextStepIndex = $stepIndex + 1;
                                    $lineCompleted = $nextStepIndex <= $currentIndex;
                                @endphp
                                <div class="absolute top-5 left-1/2 w-full h-0.5 {{ $lineCompleted ? 'bg-green-400' : 'bg-gray-300' }}"></div>
                            @endif
                        </div>
                    @endforeach
                </div>
                {{-- Workflow Actions --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Move to Location Action --}}
                    @if($workflowEnabled ? in_array($currentStatus, ['scheduled', 'arrived']) : true)
                        <div class="p-4 border border-blue-200 rounded-lg bg-blue-50">
                            <h4 class="font-medium text-blue-800 mb-3">🚛 Move to Location (Attached)</h4>
                            <p class="text-xs text-blue-700 mb-3">Vehicle with trailer attached moves to a location on-site</p>
                            <form action="{{ route('app.tipping-workflow.drop-trailer', $booking) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                                    <select name="tipping_location_id" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                        <option value="">Select location...</option>
                                        @foreach($availableLocations as $location)
                                            <option value="{{ $location->id }}">
                                                {{ $location->name }} ({{ $location->getAvailableCapacity() }}/{{ $location->capacity }} available)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                    <textarea name="notes" class="w-full px-3 py-2 border border-gray-300 rounded-md" rows="2" placeholder="Optional notes..."></textarea>
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                                    Move to Location
                                </button>
                            </form>
                        </div>
                    @endif
                    {{-- Drop Trailer Detached Action --}}
                    @php
                        $canDropTrailer = in_array($currentStatus, ['scheduled', 'arrived', 'in_parking']);
                    @endphp
                    @if($canDropTrailer)
                        <div class="p-4 border border-red-200 rounded-lg bg-red-50">
                            <h4 class="font-medium text-red-800 mb-3">📍 Drop Trailer (Detached)</h4>
                            <p class="text-xs text-red-700 mb-3">Detach trailer from unit and leave at location</p>
                            <form action="{{ route('app.tipping-workflow.drop-trailer-detached', $booking) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Drop Location</label>
                                    <select name="tipping_location_id" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                        <option value="">Select location...</option>
                                        @foreach($availableLocations as $location)
                                            <option value="{{ $location->id }}">
                                                {{ $location->name }} ({{ $location->getAvailableCapacity() }}/{{ $location->capacity }} available)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                    <textarea name="notes" class="w-full px-3 py-2 border border-gray-300 rounded-md" rows="2" placeholder="Optional notes..."></textarea>
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                                    Drop Trailer (Detached)
                                </button>
                            </form>
                        </div>
                    @endif
                    {{-- Move Between Locations Action --}}
                    @php
                        $canMoveToLocation = in_array($currentStatus, ['in_parking']);
                    @endphp
                    @if($canMoveToLocation)
                        <div class="p-4 border border-cyan-200 rounded-lg bg-cyan-50">
                            <h4 class="font-medium text-cyan-800 mb-3">🔄 Move Between Locations</h4>
                            <p class="text-xs text-cyan-700 mb-3">Move vehicle to a different location on-site</p>
                            <form action="{{ route('app.tipping-workflow.move-to-location', $booking) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">New Location</label>
                                    <select name="tipping_location_id" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                        <option value="">Select location...</option>
                                        @foreach($availableLocations as $location)
                                            <option value="{{ $location->id }}" 
                                                    @if($booking->tippingLocation && $booking->tippingLocation->id == $location->id) disabled @endif>
                                                {{ $location->name }} 
                                                @if($booking->tippingLocation && $booking->tippingLocation->id == $location->id) 
                                                    (Current Location)
                                                @else
                                                    ({{ $location->getAvailableCapacity() }}/{{ $location->capacity }} available)
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                    <textarea name="notes" class="w-full px-3 py-2 border border-gray-300 rounded-md" rows="2" placeholder="Reason for move..."></textarea>
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-cyan-500 text-white rounded hover:bg-cyan-600">
                                    Move to New Location
                                </button>
                            </form>
                        </div>
                    @endif
                    {{-- Move to Bay Action --}}
                    @php
                        $tippingAlreadyCompleted = $movement && $movement->unloading_completed_at;
                        // Show "Move to Bay" action for trailers in parking that haven't completed tipping
                        $canMoveToBay = in_array($currentStatus, ['in_parking']) && !$tippingAlreadyCompleted;
                    @endphp
                    @if($canMoveToBay)
                        <div class="p-4 border border-yellow-200 rounded-lg bg-yellow-50">
                            <h4 class="font-medium text-yellow-800 mb-3">🚛 Move to Tipping Bay</h4>
                            <form action="{{ route('app.tipping-workflow.move-to-bay', $booking) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipping Bay</label>
                                    <select name="tipping_bay_id" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                        <option value="">Select bay...</option>
                                        @foreach($availableBays as $bay)
                                            <option value="{{ $bay->id }}">
                                                {{ $bay->name }}
                                                @if(!empty($bay->equipment))
                                                    ({{ implode(', ', $bay->equipment) }})
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                    <textarea name="notes" class="w-full px-3 py-2 border border-gray-300 rounded-md" rows="2" placeholder="Optional notes..."></textarea>
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                    Move to Bay
                                </button>
                            </form>
                        </div>
                    @endif
                    {{-- Tipping automatically starts when trailer is moved to bay --}}
                    {{-- Complete Tipping Action --}}
                    @php
                        $canCompleteTipping = in_array($currentStatus, ['at_bay', 'unloading']) && !$tippingAlreadyCompleted;
                    @endphp
                    @if($canCompleteTipping)
                        <div class="col-span-2 p-4 border border-green-200 rounded-lg bg-green-50">
                            <h4 class="font-medium text-green-800 mb-3">✅ Complete Tipping</h4>
                            <p class="text-sm text-green-700 mb-3">Complete this form only after tipping has finished to record the actual quantities received.</p>
                            @if($booking->poNumbers && $booking->poNumbers->count() > 0)
                                <form action="{{ route('app.tipping-workflow.complete-tipping', $booking) }}" method="POST" id="complete-tipping-form">
                                    @csrf
                                    {{-- PO Lines Entry Section --}}
                                    <div class="mb-6 bg-white p-4 rounded border">
                                        <h5 class="font-medium text-gray-800 mb-4">📦 Record Actual Quantities Received <span class="text-red-500">*</span></h5>
                                        @foreach($booking->poNumbers as $poNumber)
                                            <div class="mb-6 border border-gray-300 rounded p-4">
                                                <h6 class="font-medium text-gray-800 mb-3">PO: {{ $poNumber->po_number }}</h6>
                                                @foreach($poNumber->lines as $line)
                                                    <div class="mb-4 p-3 bg-gray-50 rounded border" data-line-id="{{ $line->id }}">
                                                        <div class="flex justify-between items-start mb-3">
                                                            <div>
                                                                <h7 class="font-medium text-gray-700">Line {{ $line->line_number }}</h7>
                                                                <p class="text-sm text-gray-600">
                                                                    Expected: {{ number_format($line->expected_cases) }} units, 
                                                                    {{ number_format($line->expected_pallets) }} {{ $line->expectedPalletType?->name ?? 'pallets' }}
                                                                </p>
                                                            </div>
                                                        </div>
                                                        {{-- Actual Units Entry --}}
                                                        <div class="mb-3">
                                                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                                                Actual Units/Cases <span class="text-red-500">*</span>
                                                            </label>
                                                            <input type="number" 
                                                                   name="po_lines[{{ $line->id }}][actual_cases]" 
                                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md" 
                                                                   min="0" 
                                                                   value="{{ $line->actual_cases ?? '' }}"
                                                                   required>
                                                        </div>
                                                        {{-- Actual Pallets Entry --}}
                                                        <div class="mb-3">
                                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                                Actual Pallets <span class="text-red-500">*</span>
                                                            </label>
                                                            <div class="pallet-entries" data-line-id="{{ $line->id }}">
                                                                {{-- Show existing actual pallets if any --}}
                                                                @if($line->actualPallets->count() > 0)
                                                                    @foreach($line->actualPallets as $index => $actualPallet)
                                                                        <div class="flex items-center space-x-2 mb-2 pallet-entry">
                                                                            <select name="po_lines[{{ $line->id }}][actual_pallets][{{ $index }}][pallet_type_id]" 
                                                                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-md" required>
                                                                                <option value="">Select pallet type...</option>
                                                                                @foreach($palletTypes as $palletType)
                                                                                    <option value="{{ $palletType->id }}" 
                                                                                            {{ $actualPallet->pallet_type_id == $palletType->id ? 'selected' : '' }}>
                                                                                        {{ $palletType->display_name }}
                                                                                    </option>
                                                                                @endforeach
                                                                            </select>
                                                                            <input type="number" 
                                                                                   name="po_lines[{{ $line->id }}][actual_pallets][{{ $index }}][quantity]" 
                                                                                   class="w-24 px-3 py-2 border border-gray-300 rounded-md" 
                                                                                   placeholder="Qty" 
                                                                                   min="1" 
                                                                                   value="{{ $actualPallet->quantity }}"
                                                                                   required>
                                                                            <button type="button" onclick="removePalletEntry(this)" class="px-2 py-1 bg-red-100 text-red-600 rounded text-sm hover:bg-red-200">
                                                                                ✕
                                                                            </button>
                                                                        </div>
                                                                    @endforeach
                                                                @else
                                                                    {{-- Default single pallet entry --}}
                                                                    <div class="flex items-center space-x-2 mb-2 pallet-entry">
                                                                        <select name="po_lines[{{ $line->id }}][actual_pallets][0][pallet_type_id]" 
                                                                                class="flex-1 px-3 py-2 border border-gray-300 rounded-md" required>
                                                                            <option value="">Select pallet type...</option>
                                                                            @foreach($palletTypes as $palletType)
                                                                                <option value="{{ $palletType->id }}" 
                                                                                        {{ $line->expected_pallet_type_id == $palletType->id ? 'selected' : '' }}>
                                                                                    {{ $palletType->display_name }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                        <input type="number" 
                                                                               name="po_lines[{{ $line->id }}][actual_pallets][0][quantity]" 
                                                                               class="w-24 px-3 py-2 border border-gray-300 rounded-md" 
                                                                               placeholder="Qty" 
                                                                               min="1" 
                                                                               value="{{ $line->expected_pallets ?? '' }}"
                                                                               required>
                                                                        <button type="button" onclick="removePalletEntry(this)" class="px-2 py-1 bg-red-100 text-red-600 rounded text-sm hover:bg-red-200">
                                                                            ✕
                                                                        </button>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <button type="button" onclick="addPalletEntry({{ $line->id }})" class="text-sm text-blue-600 hover:text-blue-800 mt-2">
                                                                + Add another pallet type
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>
                                    {{-- Issues and Notes Section --}}
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Issues (if any)</label>
                                        <div id="issues-container">
                                            <input type="text" name="issues[]" class="w-full px-3 py-2 border border-gray-300 rounded-md mb-2" placeholder="Describe any issues...">
                                        </div>
                                        <button type="button" onclick="addIssueField()" class="text-sm text-blue-600 hover:text-blue-800">+ Add another issue</button>
                                    </div>
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                        <textarea name="notes" class="w-full px-3 py-2 border border-gray-300 rounded-md" rows="2" placeholder="Completion notes..."></textarea>
                                    </div>
                                    <button type="submit" class="w-full px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                                        Complete Tipping
                                    </button>
                                </form>
                            @else
                                {{-- No PO Numbers - Simple Completion Form --}}
                                <form action="{{ route('app.tipping-workflow.complete-tipping', $booking) }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Issues (if any)</label>
                                        <div id="issues-container">
                                            <input type="text" name="issues[]" class="w-full px-3 py-2 border border-gray-300 rounded-md mb-2" placeholder="Describe any issues...">
                                        </div>
                                        <button type="button" onclick="addIssueField()" class="text-sm text-blue-600 hover:text-blue-800">+ Add another issue</button>
                                    </div>
                                    <div class="mb-3">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                        <textarea name="notes" class="w-full px-3 py-2 border border-gray-300 rounded-md" rows="2" placeholder="Completion notes..."></textarea>
                                    </div>
                                    <button type="submit" class="w-full px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                                        Complete Tipping
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endif
                    {{-- Unit Depart Action (during tipping) --}}
                    @php
                        $isUnitStillOnSite = !$movement || !$movement->unit_departed_at;
                    @endphp
                    @if($currentStatus === 'unloading' && $isUnitStillOnSite)
                        <div class="p-4 border border-purple-200 rounded-lg bg-purple-50">
                            <h4 class="font-medium text-purple-800 mb-3">🚛 Unit Depart (Leave Trailer)</h4>
                            <p class="text-xs text-purple-700 mb-3">Record when the vehicle leaves site while trailer continues tipping process</p>
                            <form action="{{ route('app.tipping-workflow.unit-depart', $booking) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Departure Notes</label>
                                    <textarea name="notes" class="w-full px-3 py-2 border border-gray-300 rounded-md" rows="2" placeholder="Optional notes about unit departure..."></textarea>
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600">
                                    🚛 Record Unit Departure
                                </button>
                            </form>
                        </div>
                    @endif
                    {{-- Post-Tipping Actions - Simplified --}}
                    @php
                        $isUnitStillOnSite = !$movement || !$movement->unit_departed_at;
                        $isTrailerWaitingCollection = $movement && $movement->current_status === 'trailer_dropped';
                        $isCollectionInProgress = $movement && $movement->current_status === 'trailer_collected';
                    @endphp
                    {{-- Move Empty Trailer to Parking Area --}}
                    @if($currentStatus === 'empty')
                        <div class="col-span-2 p-6 border border-purple-200 rounded-lg bg-purple-50">
                            <h4 class="font-medium text-purple-800 mb-4 text-center">🏁 Tipping Complete - Move to Parking Area</h4>
                            <p class="text-sm text-purple-600 mb-4 text-center">Select which specific parking area to move the empty trailer to for organized pickup.</p>
                            <form action="{{ route('app.operations.move-to-collection-zone', $booking) }}" method="POST" class="max-w-md mx-auto">
                                @csrf
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        📍 Select Specific Parking Area <span class="text-red-500">*</span>
                                    </label>
                                    <select name="location_id" class="w-full px-3 py-2 border border-gray-300 rounded-md" required>
                                        <option value="">Choose which parking area...</option>
                                        @forelse($parkingAreas as $location)
                                            <option value="{{ $location->id }}">
                                                📦 {{ $location->name }} 
                                                @if($location->code) 
                                                    ({{ $location->code }}) 
                                                @endif
                                                - {{ $location->getAvailableCapacity() }}/{{ $location->capacity }} spaces
                                            </option>
                                        @empty
                                            <option value="" disabled>❌ No parking areas available</option>
                                        @endforelse
                                    </select>
                                    <p class="text-xs text-gray-600 mt-1">Trailer will be positioned in the selected zone for transport pickup</p>
                                </div>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                    <textarea name="notes" class="w-full px-3 py-2 border border-gray-300 rounded-md" rows="2" placeholder="parking area notes..."></textarea>
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600">
                                    Move to Parking Area
                                </button>
                            </form>
                            <div class="mt-4 text-center">
                                <p class="text-xs text-gray-600">💡 Empty trailer will be positioned for collection by transport company</p>
                            </div>
                        </div>
                    @endif
                    {{-- Collection Unit Arrival --}}
                    @if($isTrailerWaitingCollection)
                        <div class="p-4 border border-green-200 rounded-lg bg-green-50">
                            <h4 class="font-medium text-green-800 mb-3">🚚 Collection Unit Arrival</h4>
                            <form action="{{ route('app.tipping-workflow.collection-arrival', $booking) }}" method="POST">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mb-3">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Collection Unit Registration *</label>
                                        <input type="text" name="collection_unit_registration" class="w-full px-3 py-2 border border-gray-300 rounded-md" required placeholder="AB12 XYZ">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Carrier Company *</label>
                                        <div class="relative">
                                            <input type="text" 
                                                   id="collection-carrier-search" 
                                                   name="carrier_name"
                                                   placeholder="Search or type carrier name..."
                                                   required
                                                   autocomplete="off"
                                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500 pr-10">
                                            {{-- Hidden carrier_id field --}}
                                            <input type="hidden" 
                                                   id="collection-carrier-id" 
                                                   name="carrier_id" 
                                                   value="">
                                            {{-- Search dropdown --}}
                                            <div id="collection-carrier-dropdown" 
                                                 class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                              {{-- Results will be populated by JavaScript --}}
                                            </div>
                                            {{-- Status indicators --}}
                                            <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                              <span id="collection-carrier-status" class="text-xs"></span>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1">Search existing carriers or type to create new</p>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Trailer Location</label>
                                    <div class="px-3 py-2 bg-gray-50 border border-gray-300 rounded-md">
                                        @if($booking->tippingLocation)
                                            📍 {{ $booking->tippingLocation->name }}
                                            @if($booking->tippingLocation->description)
                                                <span class="text-gray-500 text-sm">- {{ $booking->tippingLocation->description }}</span>
                                            @endif
                                        @elseif($booking->tippingBay)
                                            🏗️ {{ $booking->tippingBay->name }}
                                            @if($booking->tippingBay->description)
                                                <span class="text-gray-500 text-sm">- {{ $booking->tippingBay->description }}</span>
                                            @endif
                                        @else
                                            📍 Location not specified
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Current trailer location for collection</p>
                                </div>
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                    <textarea name="notes" class="w-full px-3 py-2 border border-gray-300 rounded-md" rows="2" placeholder="Collection arrival notes..."></textarea>
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                                    Record Collection Arrival
                                </button>
                            </form>
                        </div>
                    @endif
                    {{-- Collection Unit Departure --}}
                    @if($isCollectionInProgress)
                        <div class="p-4 border border-purple-200 rounded-lg bg-purple-50">
                            <h4 class="font-medium text-purple-800 mb-3">🏁 Collection Departure</h4>
                            <form action="{{ route('app.tipping-workflow.collection-depart', $booking) }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                    <textarea name="notes" class="w-full px-3 py-2 border border-gray-300 rounded-md" rows="2" placeholder="Collection departure notes..."></textarea>
                                </div>
                                <button type="submit" class="w-full px-4 py-2 bg-purple-500 text-white rounded hover:bg-purple-600">
                                    Complete Collection
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        {{-- Current Status Details --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800">📊 Status Details</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Location Info --}}
                    @if($booking->tippingLocation)
                        <div>
                            <h4 class="font-medium text-gray-800 mb-2">Drop Location</h4>
                            <p class="text-sm text-gray-600">{{ $booking->tippingLocation->name }}</p>
                            @if($booking->trailer_dropped_at)
                                <p class="text-xs text-gray-500">Dropped: {{ $booking->trailer_dropped_at->format('M j, H:i') }}</p>
                            @endif
                        </div>
                    @endif
                    {{-- Bay Info --}}
                    @if($booking->tippingBay)
                        <div>
                            <h4 class="font-medium text-gray-800 mb-2">Tipping Bay</h4>
                            <p class="text-sm text-gray-600">{{ $booking->tippingBay->name }}</p>
                            @if($booking->moved_to_bay_at)
                                <p class="text-xs text-gray-500">Moved: {{ $booking->moved_to_bay_at->format('M j, H:i') }}</p>
                            @endif
                        </div>
                    @endif
                    {{-- Timing Info --}}
                    <div>
                        <h4 class="font-medium text-gray-800 mb-2">Timing</h4>
                        @if($booking->tipping_started_at)
                            <p class="text-sm text-gray-600">Started: {{ $booking->tipping_started_at->format('M j, H:i') }}</p>
                        @endif
                        @if($booking->tipping_completed_at && $booking->actual_tipping_duration)
                            <p class="text-sm text-gray-600">Duration: {{ $booking->actual_tipping_duration }} minutes</p>
                        @endif
                        @if($movement && $movement->unit_departed_at)
                            <p class="text-sm text-gray-600">Unit Departed: {{ $movement->unit_departed_at->format('M j, H:i') }}</p>
                        @endif
                        @if($movement && $movement->collection_unit_arrived_at)
                            <p class="text-sm text-gray-600">Collection Arrived: {{ $movement->collection_unit_arrived_at->format('M j, H:i') }}</p>
                        @endif
                        @if($movement && $movement->departed_at)
                            <p class="text-sm text-gray-600">Trailer Departed: {{ $movement->departed_at->format('M j, H:i') }}</p>
                        @endif
                        @if($movement && $movement->collection_unit_departed_at)
                            <p class="text-sm text-gray-600">Collection Departed: {{ $movement->collection_unit_departed_at->format('M j, H:i') }}</p>
                        @endif
                        @if($booking->trailer_departed_at)
                            <p class="text-sm text-gray-600">Departed: {{ $booking->trailer_departed_at->format('M j, H:i') }}</p>
                        @endif
                    </div>
                </div>
                {{-- Notes --}}
                @if($booking->tipping_notes)
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                        <h4 class="font-medium text-gray-800 mb-2">Notes</h4>
                        <p class="text-sm text-gray-600 whitespace-pre-line">{{ $booking->tipping_notes }}</p>
                    </div>
                @endif
                {{-- Issues --}}
                @if($booking->tipping_issues)
                    <div class="mt-6 p-4 bg-red-50 rounded-lg">
                        <h4 class="font-medium text-red-800 mb-2">Issues Reported</h4>
                        <ul class="text-sm text-red-600 list-disc list-inside">
                            @foreach($booking->tipping_issues as $issue)
                                <li>{{ $issue }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <script>
        // Pallet types data for JavaScript
        const palletTypes = @json($palletTypes->map(fn($pt) => ['id' => $pt->id, 'name' => $pt->display_name]));
        function addIssueField() {
            const container = document.getElementById('issues-container');
            const input = document.createElement('input');
            input.type = 'text';
            input.name = 'issues[]';
            input.className = 'w-full px-3 py-2 border border-gray-300 rounded-md mb-2';
            input.placeholder = 'Describe any issues...';
            container.appendChild(input);
        }
        function addPalletEntry(lineId) {
            const container = document.querySelector(`.pallet-entries[data-line-id="${lineId}"]`);
            const existingEntries = container.querySelectorAll('.pallet-entry').length;
            const palletEntry = document.createElement('div');
            palletEntry.className = 'flex items-center space-x-2 mb-2 pallet-entry';
            // Create select options
            let optionsHtml = '<option value="">Select pallet type...</option>';
            palletTypes.forEach(palletType => {
                optionsHtml += `<option value="${palletType.id}">${palletType.name}</option>`;
            });
            palletEntry.innerHTML = `
                <select name="po_lines[${lineId}][actual_pallets][${existingEntries}][pallet_type_id]" 
                        class="flex-1 px-3 py-2 border border-gray-300 rounded-md" required>
                    ${optionsHtml}
                </select>
                <input type="number" 
                       name="po_lines[${lineId}][actual_pallets][${existingEntries}][quantity]" 
                       class="w-24 px-3 py-2 border border-gray-300 rounded-md" 
                       placeholder="Qty" 
                       min="1" 
                       required>
                <button type="button" onclick="removePalletEntry(this)" class="px-2 py-1 bg-red-100 text-red-600 rounded text-sm hover:bg-red-200">
                    ✕
                </button>
            `;
            container.appendChild(palletEntry);
        }
        function removePalletEntry(button) {
            const entry = button.closest('.pallet-entry');
            const container = entry.parentElement;
            // Don't allow removing the last entry
            if (container.querySelectorAll('.pallet-entry').length > 1) {
                entry.remove();
                // Reindex remaining entries
                reindexPalletEntries(container);
            }
        }
        function reindexPalletEntries(container) {
            const lineId = container.getAttribute('data-line-id');
            const entries = container.querySelectorAll('.pallet-entry');
            entries.forEach((entry, index) => {
                const select = entry.querySelector('select');
                const input = entry.querySelector('input[type="number"]');
                select.name = `po_lines[${lineId}][actual_pallets][${index}][pallet_type_id]`;
                input.name = `po_lines[${lineId}][actual_pallets][${index}][quantity]`;
            });
        }
        // Collection Carrier Search Logic
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('collection-carrier-search');
            const carrierIdInput = document.getElementById('collection-carrier-id');
            const dropdown = document.getElementById('collection-carrier-dropdown');
            const statusSpan = document.getElementById('collection-carrier-status');
            if (!searchInput) return; // Exit if elements don't exist
            let searchTimeout;
            let selectedCarrierId = carrierIdInput.value;
            let isLoading = false;
            // Update status based on current state
            function updateStatus() {
                if (selectedCarrierId) {
                    statusSpan.textContent = '✓';
                    statusSpan.className = 'text-xs text-green-600';
                } else if (searchInput.value.trim()) {
                    statusSpan.textContent = '+';
                    statusSpan.className = 'text-xs text-blue-600';
                    statusSpan.title = 'Will create new carrier';
                } else {
                    statusSpan.textContent = '';
                    statusSpan.className = 'text-xs';
                }
            }
            // Search carriers
            function searchCarriers(query) {
                if (query.length < 2) {
                    dropdown.classList.add('hidden');
                    return;
                }
                if (isLoading) return;
                isLoading = true;
                fetch(`{{ route('api.carriers.search') }}?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        populateDropdown(data, query);
                        isLoading = false;
                    })
                    .catch(error => {
                        console.error('Search failed:', error);
                        dropdown.classList.add('hidden');
                        isLoading = false;
                    });
            }
            function populateDropdown(data, query) {
                dropdown.innerHTML = '';
                dropdown.classList.remove('hidden');
                if (data.carriers && data.carriers.length > 0) {
                    data.carriers.forEach(carrier => {
                        const item = document.createElement('div');
                        item.className = 'px-3 py-2 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-0';
                        item.innerHTML = `
                            <div class="font-medium text-sm">${carrier.name}</div>
                            <div class="text-xs text-gray-500">${carrier.is_active ? 'Active' : 'Inactive'} carrier</div>
                        `;
                        item.addEventListener('click', () => selectCarrier(carrier));
                        dropdown.appendChild(item);
                    });
                }
                // Add option to create new carrier
                if (query.trim()) {
                    const createItem = document.createElement('div');
                    createItem.className = 'px-3 py-2 hover:bg-blue-50 cursor-pointer border-t-2 border-blue-200 bg-blue-25';
                    createItem.innerHTML = `
                        <div class="font-medium text-sm text-blue-600">+ Create "${query}"</div>
                        <div class="text-xs text-blue-500">Add as new carrier company</div>
                    `;
                    createItem.addEventListener('click', () => createNewCarrier(query));
                    dropdown.appendChild(createItem);
                }
            }
            function selectCarrier(carrier) {
                searchInput.value = carrier.name;
                carrierIdInput.value = carrier.id;
                selectedCarrierId = carrier.id;
                dropdown.classList.add('hidden');
                updateStatus();
            }
            function createNewCarrier(name) {
                searchInput.value = name;
                carrierIdInput.value = '';
                selectedCarrierId = '';
                dropdown.classList.add('hidden');
                updateStatus();
            }
            // Event listeners
            searchInput.addEventListener('input', function(e) {
                const query = e.target.value.trim();
                selectedCarrierId = '';
                carrierIdInput.value = '';
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => searchCarriers(query), 300);
                updateStatus();
            });
            searchInput.addEventListener('focus', function() {
                if (this.value.length >= 2) {
                    searchCarriers(this.value);
                }
            });
            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });
            updateStatus();
        });

        // Handle move-to-collection-zone form via AJAX
        document.addEventListener('DOMContentLoaded', function() {
            const moveToCollectionForm = document.querySelector('form[action*="move-to-collection-zone"]');
            
            if (moveToCollectionForm) {
                moveToCollectionForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(this);
                    const submitButton = this.querySelector('button[type="submit"]');
                    const originalText = submitButton.textContent;
                    
                    // Disable button and show loading state
                    submitButton.disabled = true;
                    submitButton.textContent = 'Moving...';
                    
                    fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Show success message
                            const successMessage = document.createElement('div');
                            successMessage.className = 'mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded';
                            successMessage.innerHTML = `
                                <div class="flex items-center">
                                    <span class="mr-2">✅</span>
                                    <div>
                                        <strong>${data.message}</strong>
                                        ${data.next_action ? `<br><small>Next: ${data.next_action}</small>` : ''}
                                    </div>
                                </div>
                            `;
                            
                            // Insert success message at the top of the page
                            const content = document.querySelector('.py-6');
                            content.insertBefore(successMessage, content.firstChild);
                            
                            // Scroll to top to show the message
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                            
                            // Refresh the page after 2 seconds to show updated state
                            setTimeout(() => {
                                window.location.reload();
                            }, 2000);
                            
                        } else {
                            // Show error message
                            const errorMessage = document.createElement('div');
                            errorMessage.className = 'mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded';
                            errorMessage.innerHTML = `
                                <div class="flex items-center">
                                    <span class="mr-2">❌</span>
                                    <strong>${data.error || 'An error occurred'}</strong>
                                </div>
                            `;
                            
                            const content = document.querySelector('.py-6');
                            content.insertBefore(errorMessage, content.firstChild);
                            window.scrollTo({ top: 0, behavior: 'smooth' });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        
                        // Show generic error message
                        const errorMessage = document.createElement('div');
                        errorMessage.className = 'mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded';
                        errorMessage.innerHTML = `
                            <div class="flex items-center">
                                <span class="mr-2">❌</span>
                                <strong>Network error occurred. Please try again.</strong>
                            </div>
                        `;
                        
                        const content = document.querySelector('.py-6');
                        content.insertBefore(errorMessage, content.firstChild);
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                    })
                    .finally(() => {
                        // Re-enable button
                        submitButton.disabled = false;
                        submitButton.textContent = originalText;
                    });
                });
            }
        });
    </script>
</x-warehouse-layout>