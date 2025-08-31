<x-warehouse-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Tipping Workflow</h2>
                <p class="text-sm text-gray-600 mt-1">
                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium mr-2">📍 {{ $factoryBooking->depot->name }}</span>
                    <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded text-xs font-medium mr-2">FACTORY</span>
                    {{ $factoryBooking->reference }} - {{ $factoryBooking->customer->name }}
                </p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('app.factory-bookings.show', $factoryBooking) }}" 
                   class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                    ← Back to Factory Booking
                </a>
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
            <h3 class="text-lg font-semibold text-blue-800 mb-3">📋 Factory Booking Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Factory Reference</p>
                    <p class="font-medium">{{ $factoryBooking->reference }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Customer</p>
                    <p class="font-medium">{{ $factoryBooking->customer->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">PO References</p>
                    <p class="font-medium">
                        @if($factoryBooking->poNumbers && $factoryBooking->poNumbers->count() > 0)
                            {{ $factoryBooking->poNumbers->pluck('po_number')->join(', ') }}
                        @else
                            Not provided
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Delivery Type</p>
                    <p class="font-medium">Factory Delivery</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Trailer</p>
                    <p class="font-medium">{{ $factoryBooking->trailer_registration ?: 'Not specified' }}</p>
                </div>
            </div>
        </div>
        
        {{-- PO Numbers & Load Details --}}
        @if($factoryBooking->poNumbers && $factoryBooking->poNumbers->count() > 0)
            <div class="mb-6 p-6 bg-gray-50 border border-gray-200 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">📦 PO Numbers & Load Details</h3>
                <div class="space-y-4">
                    @foreach($factoryBooking->poNumbers as $poNumber)
                        <div class="border border-gray-300 rounded-lg p-4 bg-white">
                            <div class="flex justify-between items-start mb-3">
                                <h4 class="font-medium text-lg text-gray-800">PO: {{ $poNumber->po_number }}</h4>
                                <div class="flex space-x-2">
                                    @if($poNumber->hasVariance())
                                        <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">
                                            ⚠️ Has Variance
                                        </span>
                                    @endif
                                    @if($poNumber->isComplete())
                                        <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                                            ✅ Complete
                                        </span>
                                    @endif
                                </div>
                            </div>
                            {{-- PO Summary --}}
                            <div class="mb-3 p-3 bg-gray-50 rounded border">
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-600">Expected:</span>
                                        <span class="font-semibold">{{ number_format($poNumber->total_expected_cases) }} units, {{ number_format($poNumber->total_expected_pallets) }} pallets</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-600">Actual:</span>
                                        <span class="font-semibold {{ $poNumber->total_actual_cases > 0 ? 'text-green-600' : 'text-gray-400' }}">
                                            {{ $poNumber->total_actual_cases > 0 ? number_format($poNumber->total_actual_cases) . ' units' : 'Not recorded' }}, 
                                            {{ $poNumber->total_actual_pallets > 0 ? number_format($poNumber->total_actual_pallets) . ' pallets' : 'Not recorded' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            {{-- Individual PO Lines --}}
                            @if($poNumber->lines->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full text-sm">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="px-3 py-2 text-left font-medium text-gray-700">Line</th>
                                                <th class="px-3 py-2 text-left font-medium text-gray-700">Expected Units</th>
                                                <th class="px-3 py-2 text-left font-medium text-gray-700">Expected Pallets</th>
                                                <th class="px-3 py-2 text-left font-medium text-gray-700">Actual Units</th>
                                                <th class="px-3 py-2 text-left font-medium text-gray-700">Actual Pallets</th>
                                                <th class="px-3 py-2 text-left font-medium text-gray-700">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach($poNumber->lines as $line)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-3 py-2 font-medium">{{ $line->line_number }}</td>
                                                    <td class="px-3 py-2">{{ number_format($line->expected_cases) }}</td>
                                                    <td class="px-3 py-2">{{ number_format($line->expected_pallets) }}</td>
                                                    <td class="px-3 py-2 {{ $line->actual_cases > 0 ? 'text-green-600 font-medium' : 'text-gray-400' }}">
                                                        {{ $line->actual_cases > 0 ? number_format($line->actual_cases) : 'Not recorded' }}
                                                    </td>
                                                    <td class="px-3 py-2 {{ $line->actual_pallets > 0 ? 'text-green-600 font-medium' : 'text-gray-400' }}">
                                                        {{ $line->actual_pallets > 0 ? number_format($line->actual_pallets) : 'Not recorded' }}
                                                    </td>
                                                    <td class="px-3 py-2">
                                                        @if($line->hasVariance())
                                                            <span class="px-2 py-1 bg-red-100 text-red-600 text-xs rounded">Variance</span>
                                                        @elseif($line->actual_cases > 0)
                                                            <span class="px-2 py-1 bg-green-100 text-green-600 text-xs rounded">Complete</span>
                                                        @else
                                                            <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded">Pending</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Progress Workflow --}}
        <div class="mb-6 bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800">🚛 Factory Tipping Progress</h3>
                <p class="text-sm text-gray-600 mt-1">Track your factory delivery through the tipping process</p>
            </div>
            <div class="p-6">
                @php
                    $movement = $factoryBooking->movements->last();
                    $currentStatus = $movement ? $movement->current_status : 'arrived';
                    $currentLocation = $movement?->tippingLocation;
                    $currentBay = $movement?->tippingBay;
                    
                    // Factory workflow stages
                    $stages = [
                        'arrived' => ['label' => '⏳ Arrived', 'icon' => 'text-blue-500'],
                        'in_parking' => ['label' => '🚛 In Parking Area', 'icon' => 'text-blue-500'],
                        'at_bay' => ['label' => '⚡ At Tipping Bay', 'icon' => 'text-orange-500'],
                        'unloading' => ['label' => '⚡ Tipping in Progress', 'icon' => 'text-orange-500'],
                        'empty' => ['label' => '✅ Tipping Complete', 'icon' => 'text-green-500'],
                        'back_to_parking' => ['label' => '📍 Back in Parking', 'icon' => 'text-purple-500'],
                        'departed' => ['label' => '🏁 Departed', 'icon' => 'text-blue-600']
                    ];
                    
                    $stageOrder = ['arrived', 'in_parking', 'at_bay', 'unloading', 'empty', 'back_to_parking', 'departed'];
                    $currentIndex = array_search($currentStatus, $stageOrder);
                @endphp
                
                {{-- Progress Steps Visual --}}
                <div class="flex items-center justify-between space-x-4 mb-6">
                    @foreach($stageOrder as $stepIndex => $status)
                        @php
                            $config = $stages[$status] ?? ['label' => 'Unknown', 'icon' => 'text-gray-400'];
                            $isCompleted = $stepIndex < $currentIndex;
                            $isCurrent = $stepIndex === $currentIndex;
                            
                            if ($isCurrent) {
                                $stepClass = 'bg-orange-500 text-white';
                            } elseif ($isCompleted) {
                                $stepClass = 'bg-green-500 text-white';
                            } else {
                                $stepClass = 'bg-gray-200 text-gray-500';
                            }
                        @endphp
                        <div class="flex flex-col items-center relative {{ !$loop->last ? 'flex-1' : '' }}">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center text-sm font-semibold {{ $stepClass }}">
                                @if($isCompleted && !$isCurrent)
                                    ✓
                                @else
                                    {{ $loop->iteration }}
                                @endif
                            </div>
                            <p class="text-xs mt-3 text-center max-w-24 {{ $isCurrent ? 'font-bold text-orange-600' : ($isCompleted ? 'font-medium text-gray-700' : 'text-gray-500') }}">
                                {{ $config['label'] }}
                            </p>
                            @if(!$loop->last)
                                @php
                                    $nextStepIndex = $stepIndex + 1;
                                    $lineCompleted = $nextStepIndex <= $currentIndex;
                                @endphp
                                <div class="absolute top-6 left-1/2 w-full h-0.5 {{ $lineCompleted ? 'bg-green-400' : 'bg-gray-300' }}"></div>
                            @endif
                        </div>
                    @endforeach
                </div>

                {{-- Current Status Display --}}
                @php
                    $statusLabels = [
                        'arrived' => ['🚛 Vehicle Arrived', 'bg-blue-100 text-blue-800'],
                        'in_parking' => ['📍 In Parking Area', 'bg-blue-100 text-blue-800'], 
                        'at_bay' => ['🏗️ At Tipping Bay - Full', 'bg-orange-100 text-orange-800'],
                        'unloading' => ['⚡ Tipping in Progress', 'bg-orange-100 text-orange-800'],
                        'empty' => ['✅ Tipping Complete - Empty', 'bg-green-100 text-green-800'],
                        'back_to_parking' => ['📍 Back in Parking Area', 'bg-purple-100 text-purple-800'],
                        'departed' => ['🏁 Departed', 'bg-gray-100 text-gray-800'],
                    ];
                    $statusConfig = $statusLabels[$currentStatus] ?? ['❓ Unknown Status', 'bg-gray-100 text-gray-800'];
                @endphp
                
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg mb-6">
                    <div>
                        <p class="text-sm text-gray-600">Current Status</p>
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-medium {{ $statusConfig[1] }}">
                            {{ $statusConfig[0] }}
                        </span>
                    </div>
                    @if($currentLocation)
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Location</p>
                            <p class="font-medium">{{ $currentLocation->name }}</p>
                        </div>
                    @endif
                    @if($currentBay)
                        <div class="text-right">
                            <p class="text-sm text-gray-600">Tipping Bay</p>
                            <p class="font-medium">{{ $currentBay->name }}</p>
                        </div>
                    @endif
                </div>

                {{-- Factory Workflow Actions (Read-only for warehouse operators) --}}
                <div class="p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <h4 class="font-medium text-yellow-800 mb-2">ℹ️ Factory Workflow Information</h4>
                    <p class="text-sm text-yellow-700">
                        Factory deliveries are managed through automated processes. 
                        Warehouse operators can view the current status but cannot perform workflow actions. 
                        Contact admin users to move vehicles or complete tipping operations.
                    </p>
                </div>
            </div>
        </div>
        
        {{-- Movement History --}}
        @if($factoryBooking->movements && $factoryBooking->movements->count() > 0)
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-800">📊 Movement History</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($factoryBooking->movements->sortByDesc('created_at') as $movement)
                            <div class="flex items-start space-x-4 p-4 border border-gray-200 rounded-lg">
                                <div class="flex-shrink-0 mt-1">
                                    @php
                                        $statusIcon = match($movement->current_status) {
                                            'arrived' => '🚛',
                                            'in_parking' => '📍',
                                            'at_bay' => '🏗️',
                                            'unloading' => '⚡',
                                            'empty' => '✅',
                                            'back_to_parking' => '📍',
                                            'departed' => '🏁',
                                            default => '📋'
                                        };
                                    @endphp
                                    <span class="text-2xl">{{ $statusIcon }}</span>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-medium text-gray-900">
                                            {{ $statusLabels[$movement->current_status][0] ?? ucwords(str_replace('_', ' ', $movement->current_status)) }}
                                        </h4>
                                        <span class="text-sm text-gray-500">
                                            {{ $movement->created_at->format('d M Y, H:i') }}
                                        </span>
                                    </div>
                                    @if($movement->operation_notes)
                                        <p class="text-sm text-gray-600 mt-1">{{ $movement->operation_notes }}</p>
                                    @endif
                                    @if($movement->tippingLocation)
                                        <p class="text-xs text-gray-500 mt-1">Location: {{ $movement->tippingLocation->name }}</p>
                                    @endif
                                    @if($movement->tippingBay)
                                        <p class="text-xs text-gray-500 mt-1">Bay: {{ $movement->tippingBay->name }}</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-warehouse-layout>