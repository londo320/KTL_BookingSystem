<x-warehouse-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl">🔧 Fix Historical Departure Records</h2>
      <div class="flex space-x-2">
        @php
          $routePrefix = request()->route()->getPrefix() === 'depot-admin' ? 'app.' : 'app.';
        @endphp
        <a href="{{ route($routePrefix . 'bookings.index') }}" 
           class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
          ← Back to Bookings
        </a>
      </div>
    </div>
  </x-slot>
  <div class="py-6 max-w-7xl mx-auto">
    @if(session('success'))
      <div class="mb-6 p-4 bg-green-100 border border-green-300 rounded-lg">
        <p class="text-green-800">{{ session('success') }}</p>
      </div>
    @endif
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
      <div class="flex">
        <div class="flex-shrink-0">
          <div class="text-yellow-600 text-xl">⚠️</div>
        </div>
        <div class="ml-3">
          <h3 class="text-sm font-medium text-yellow-800">Historical Data Fix Required</h3>
          <div class="mt-2 text-sm text-yellow-700">
            <p>Some bookings have collected trailers but are missing departure times. This causes them to still show as "on site" in reports.</p>
            <p class="mt-1">This tool will set the <strong>departed_at</strong> time to match the trailer collection time.</p>
          </div>
        </div>
      </div>
    </div>
    @if($bookingsNeedingFix->count() > 0)
      <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
          <div>
            <h3 class="text-lg font-medium text-gray-900">Bookings Needing Fix</h3>
            <p class="text-sm text-gray-600 mt-1">{{ $bookingsNeedingFix->count() }} bookings with collected trailers but no departure time</p>
          </div>
          <form method="POST" action="{{ route($routePrefix . 'bookings.fix-historical-departures.process') }}">
            @csrf
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
                    onclick="return confirm('Fix {{ $bookingsNeedingFix->count() }} booking records?')">
              🔧 Fix All Records
            </button>
          </form>
        </div>
        <div class="overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Arrived</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Collection Time</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Missing</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              @foreach($bookingsNeedingFix as $booking)
                @php
                  $movement = $booking->movements()->first();
                  $collectionTime = $movement?->trailer_collected_at ?? $movement?->actual_departure ?? $movement?->updated_at;
                @endphp
                <tr class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-medium text-blue-600">
                      <a href="{{ route($routePrefix . 'bookings.show', $booking) }}" class="hover:underline">
                        {{ $booking->booking_reference }}
                      </a>
                    </div>
                    <div class="text-xs text-gray-500">{{ $booking->slot->depot->name }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ $booking->customer->name ?? 'Unknown' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                    {{ $booking->arrived_at->format('M j, Y H:i') }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">
                      {{ ucwords(str_replace('_', ' ', $movement?->current_status ?? 'unknown')) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                    @if($collectionTime)
                      {{ \Carbon\Carbon::parse($collectionTime)->format('M j, Y H:i') }}
                      <div class="text-xs text-gray-400">
                        {{ \Carbon\Carbon::parse($collectionTime)->diffForHumans($booking->arrived_at, true) }} after arrival
                      </div>
                    @else
                      <span class="text-red-600">No collection time found</span>
                    @endif
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">
                      departed_at is null
                    </span>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    @else
      <div class="bg-white rounded-lg shadow">
        <div class="text-center py-12">
          <div class="text-gray-400 text-6xl mb-4">✅</div>
          <h3 class="text-lg font-medium text-gray-900 mb-2">All Records Up to Date</h3>
          <p class="text-gray-600">No bookings found with collected trailers missing departure times.</p>
        </div>
      </div>
    @endif
    <!-- Information Panel -->
    <div class="mt-6 bg-gray-50 rounded-lg p-6">
      <h4 class="text-sm font-medium text-gray-800 mb-3">How this fix works:</h4>
      <div class="text-sm text-gray-600 space-y-2">
        <div class="flex items-start">
          <span class="mr-2">🔍</span>
          <div>Finds bookings that have <code class="bg-gray-200 px-1 rounded">trailer_collected</code> or <code class="bg-gray-200 px-1 rounded">departed</code> movement status but no <code class="bg-gray-200 px-1 rounded">departed_at</code> time</div>
        </div>
        <div class="flex items-start">
          <span class="mr-2">⏰</span>
          <div>Uses the trailer collection time, actual departure time, or movement update time as the departure time</div>
        </div>
        <div class="flex items-start">
          <span class="mr-2">📝</span>
          <div>Adds a note indicating this was a system fix and records the action in booking history</div>
        </div>
        <div class="flex items-start">
          <span class="mr-2">✅</span>
          <div>Once fixed, these bookings will no longer appear as "on site" in dashboard reports</div>
        </div>
      </div>
    </div>
  </div>
</x-warehouse-layout>