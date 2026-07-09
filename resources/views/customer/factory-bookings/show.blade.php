<x-app-layout>
  @include('layouts.customer-nav')

  <x-slot name="header">
    <div class="bg-white border-b border-gray-200 px-6 py-4">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-xl font-bold text-gray-900">🏭 Factory Delivery</h1>
          <p class="text-sm text-gray-600 mt-1">
            <span class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs font-medium mr-2">{{ $factoryBooking->depot->name }}</span>
            {{ $factoryBooking->reference }}
          </p>
        </div>
        <a href="{{ route('customer.bookings.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 transition-colors">
          ← Back to Bookings
        </a>
      </div>
    </div>
  </x-slot>

  <div class="py-6 max-w-4xl mx-auto">
    @php
      $movement = $factoryBooking->movements->last();
      $currentStatus = $movement ? $movement->current_status : 'arrived';
      if ($movement && $movement->unloading_completed_at && $currentStatus === 'unloading') {
          $currentStatus = 'empty';
      }
      $statusLabels = [
          'arrived' => ['🚛 Vehicle Arrived', 'bg-blue-100 text-blue-800'],
          'in_parking' => ['📍 In Parking Area', 'bg-blue-100 text-blue-800'],
          'at_bay' => ['🏗️ At Tipping Bay', 'bg-orange-100 text-orange-800'],
          'unloading' => ['⚡ Tipping in Progress', 'bg-orange-100 text-orange-800'],
          'empty' => ['✅ Tipping Complete', 'bg-green-100 text-green-800'],
          'back_to_parking' => ['📍 Back in Parking Area', 'bg-purple-100 text-purple-800'],
          'departed' => ['🏁 Departed', 'bg-gray-100 text-gray-800'],
          'trailer_collected' => ['🔄 Collected', 'bg-gray-100 text-gray-800'],
      ];
      $statusConfig = $statusLabels[$currentStatus] ?? ['❓ Unknown Status', 'bg-gray-100 text-gray-800'];
    @endphp

    {{-- Booking Information --}}
    <div class="mb-6 p-6 bg-blue-50 border border-blue-200 rounded-lg">
      <h3 class="text-lg font-semibold text-blue-800 mb-3">📋 Delivery Information</h3>
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <p class="text-sm text-gray-600">Reference</p>
          <p class="font-medium">{{ $factoryBooking->reference }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Depot</p>
          <p class="font-medium">{{ $factoryBooking->depot->name }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Vehicle</p>
          <p class="font-medium">{{ $factoryBooking->vehicle_registration ?: 'Not specified' }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Arrived</p>
          <p class="font-medium">{{ $factoryBooking->arrived_at?->format('d M Y, H:i') ?? 'Not yet arrived' }}</p>
        </div>
      </div>
    </div>

    {{-- Current Status --}}
    <div class="mb-6 p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm text-gray-600">Current Status</p>
          <span class="inline-block mt-1 px-3 py-1 rounded-full text-sm font-medium {{ $statusConfig[1] }}">
            {{ $statusConfig[0] }}
          </span>
        </div>
        @if($movement?->tippingLocation)
          <div class="text-right">
            <p class="text-sm text-gray-600">Location</p>
            <p class="font-medium">{{ $movement->tippingLocation->name }}</p>
          </div>
        @endif
        @if($movement?->tippingBay)
          <div class="text-right">
            <p class="text-sm text-gray-600">Tipping Bay</p>
            <p class="font-medium">{{ $movement->tippingBay->name }}</p>
          </div>
        @endif
      </div>
    </div>

    {{-- PO Numbers & Load Details --}}
    @if($factoryBooking->poNumbers && $factoryBooking->poNumbers->count() > 0)
      <div class="mb-6 p-6 bg-gray-50 border border-gray-200 rounded-lg">
        <h3 class="text-lg font-semibold text-gray-800 mb-3">📦 PO Numbers &amp; Load Details</h3>
        <div class="space-y-4">
          @foreach($factoryBooking->poNumbers as $poNumber)
            <div class="border border-gray-300 rounded-lg p-4 bg-white">
              <h4 class="font-medium text-lg text-gray-800 mb-3">PO: {{ $poNumber->po_number }}</h4>
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
                      </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                      @foreach($poNumber->lines as $line)
                        <tr>
                          <td class="px-3 py-2 font-medium">{{ $line->line_number }}</td>
                          <td class="px-3 py-2">{{ number_format($line->expected_cases) }}</td>
                          <td class="px-3 py-2">{{ number_format($line->expected_pallets) }}</td>
                          <td class="px-3 py-2 {{ $line->actual_cases > 0 ? 'text-green-600 font-medium' : 'text-gray-400' }}">
                            {{ $line->actual_cases > 0 ? number_format($line->actual_cases) : 'Not recorded' }}
                          </td>
                          <td class="px-3 py-2 {{ $line->actual_pallets > 0 ? 'text-green-600 font-medium' : 'text-gray-400' }}">
                            {{ $line->actual_pallets > 0 ? number_format($line->actual_pallets) : 'Not recorded' }}
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

    {{-- Movement History --}}
    @if($factoryBooking->movements && $factoryBooking->movements->count() > 0)
      <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b border-gray-200">
          <h3 class="text-xl font-semibold text-gray-800">📊 Movement History</h3>
        </div>
        <div class="p-6">
          <div class="space-y-4">
            @foreach($factoryBooking->movements->sortByDesc('created_at') as $historyMovement)
              @php
                $statusIcon = match($historyMovement->current_status) {
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
              <div class="flex items-start space-x-4 p-4 border border-gray-200 rounded-lg">
                <div class="flex-shrink-0 mt-1">
                  <span class="text-2xl">{{ $statusIcon }}</span>
                </div>
                <div class="flex-1">
                  <div class="flex items-center justify-between">
                    <h4 class="font-medium text-gray-900">
                      {{ $statusLabels[$historyMovement->current_status][0] ?? ucwords(str_replace('_', ' ', $historyMovement->current_status)) }}
                    </h4>
                    <span class="text-sm text-gray-500">
                      {{ $historyMovement->created_at->format('d M Y, H:i') }}
                    </span>
                  </div>
                  @if($historyMovement->tippingLocation)
                    <p class="text-xs text-gray-500 mt-1">Location: {{ $historyMovement->tippingLocation->name }}</p>
                  @endif
                  @if($historyMovement->tippingBay)
                    <p class="text-xs text-gray-500 mt-1">Bay: {{ $historyMovement->tippingBay->name }}</p>
                  @endif
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    @endif
  </div>
</x-app-layout>
