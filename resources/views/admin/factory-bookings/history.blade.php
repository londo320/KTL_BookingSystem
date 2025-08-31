<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl">Factory Booking History #{{ $factoryBooking->reference }}</h2>
      <a href="{{ route('app.factory-bookings.show', $factoryBooking) }}"
         class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
        Back to Factory Booking
      </a>
    </div>
  </x-slot>
  <div class="py-6 max-w-6xl mx-auto">
    {{-- Current Factory Booking Info --}}
    <div class="mb-6 p-6 bg-orange-50 border border-orange-200 rounded-lg">
      <h3 class="text-lg font-semibold text-orange-800 mb-3">🏭 Factory Delivery Details</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <p class="text-sm text-gray-600">Reference</p>
          <p class="font-medium">#{{ $factoryBooking->reference }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Customer</p>
          <p class="font-medium">{{ $factoryBooking->customer->name ?? 'Not assigned' }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Current Status</p>
          <p class="font-medium">
            @if($factoryBooking->departed_at)
              <span class="text-purple-600">🏁 Departed</span>
            @elseif($factoryBooking->completed_at)
              <span class="text-green-600">✅ Completed</span>
            @elseif($factoryBooking->processing_started_at)
              <span class="text-orange-600">⚡ Processing</span>
            @elseif($factoryBooking->arrived_at)
              <span class="text-blue-600">🚪 Arrived</span>
            @else
              <span class="text-gray-600">📅 Registered</span>
            @endif
          </p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Depot</p>
          <p class="font-medium">{{ $factoryBooking->depot->name }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Vehicle</p>
          <p class="font-medium">{{ $factoryBooking->vehicle_registration ?? 'Not specified' }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Trailer</p>
          <p class="font-medium">{{ $factoryBooking->trailer_registration ?? 'Not specified' }}</p>
        </div>
      </div>
    </div>

    {{-- Timeline --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
          <div>
            <h3 class="text-xl font-semibold text-gray-800">📋 Complete Timeline</h3>
            <p class="text-sm text-gray-600 mt-1">
              All actions and changes made to this factory booking ({{ $history->count() }} entries)
            </p>
          </div>
          <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-600">Sort:</span>
            <div class="flex bg-gray-100 rounded-lg p-1">
              <a href="{{ route('app.factory-bookings.history', $factoryBooking) }}?sort=asc" 
                 class="px-3 py-1 text-xs rounded-md {{ $sortOrder === 'asc' ? 'bg-blue-500 text-white' : 'text-gray-600 hover:text-gray-800' }}">
                📈 Oldest First
              </a>
              <a href="{{ route('app.factory-bookings.history', $factoryBooking) }}?sort=desc" 
                 class="px-3 py-1 text-xs rounded-md {{ $sortOrder === 'desc' ? 'bg-blue-500 text-white' : 'text-gray-600 hover:text-gray-800' }}">
                📉 Newest First
              </a>
            </div>
          </div>
        </div>
      </div>

      @if($history->isEmpty())
        <div class="p-6 text-center text-gray-500">
          <p>No history records found for this factory booking.</p>
        </div>
      @else
        {{-- Movement Timeline --}}
        <div class="mb-6">
          <div class="bg-white rounded-lg p-4 border">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">🚛 Movement Timeline</h4>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              {{-- Booking Created --}}
              <div class="text-center p-3 bg-orange-50 rounded-lg border">
                <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-2">
                  <span class="text-orange-600 text-sm">🏭</span>
                </div>
                <div class="text-sm font-medium text-orange-800">Factory Booking</div>
                <div class="text-xs text-gray-600">{{ $factoryBooking->created_at->format('d M Y, H:i') }}</div>
                <div class="text-xs text-gray-500">{{ $factoryBooking->depot->name }}</div>
              </div>

              {{-- Arrival --}}
              @if($factoryBooking->arrived_at)
                <div class="text-center p-3 bg-blue-50 rounded-lg border">
                  <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-blue-600 text-sm">🚪</span>
                  </div>
                  <div class="text-sm font-medium text-blue-800">Site Arrival</div>
                  <div class="text-xs text-gray-600">{{ $factoryBooking->arrived_at->format('d M Y, H:i') }}</div>
                  @if($factoryBooking->vehicle_registration)
                    <div class="text-xs text-gray-500">{{ $factoryBooking->vehicle_registration }}</div>
                  @endif
                </div>
              @else
                <div class="text-center p-3 bg-gray-50 rounded-lg border border-dashed">
                  <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-gray-400 text-sm">🚪</span>
                  </div>
                  <div class="text-sm font-medium text-gray-500">Awaiting Arrival</div>
                  <div class="text-xs text-gray-400">Factory delivery</div>
                </div>
              @endif

              {{-- Processing Status --}}
              @php
                $movement = $factoryBooking->movements->last();
                $tippingCompleted = $movement && $movement->unloading_completed_at;
                $tippingStarted = $movement && $movement->unloading_started_at;
              @endphp
              @if($tippingCompleted)
                <div class="text-center p-3 bg-purple-50 rounded-lg border">
                  <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-purple-600 text-sm">🏗️</span>
                  </div>
                  <div class="text-sm font-medium text-purple-800">Processing Complete</div>
                  <div class="text-xs text-gray-600">{{ $movement->unloading_completed_at->format('d M Y, H:i') }}</div>
                  @if($tippingStarted)
                    @php
                      $durationMinutes = round($movement->unloading_started_at->diffInMinutes($movement->unloading_completed_at));
                      if ($durationMinutes >= 10080) {
                        $weeks = floor($durationMinutes / 10080);
                        $days = floor(($durationMinutes % 10080) / 1440);
                        $formattedDuration = $weeks . 'w' . ($days > 0 ? ' ' . $days . 'd' : '');
                      } elseif ($durationMinutes >= 1440) {
                        $days = floor($durationMinutes / 1440);
                        $hours = floor(($durationMinutes % 1440) / 60);
                        $mins = $durationMinutes % 60;
                        $formattedDuration = $days . 'd ' . ($hours > 0 ? $hours . 'h ' : '') . ($mins > 0 ? $mins . 'm' : '');
                      } elseif ($durationMinutes >= 60) {
                        $hours = floor($durationMinutes / 60);
                        $mins = $durationMinutes % 60;
                        $formattedDuration = $hours . 'h ' . ($mins > 0 ? $mins . 'm' : '');
                      } else {
                        $formattedDuration = $durationMinutes . ' min';
                      }
                    @endphp
                    <div class="text-xs text-gray-500">Duration: {{ $formattedDuration }}</div>
                  @endif
                </div>
              @elseif($tippingStarted)
                <div class="text-center p-3 bg-yellow-50 rounded-lg border">
                  <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-yellow-600 text-sm">🏗️</span>
                  </div>
                  <div class="text-sm font-medium text-yellow-800">Processing</div>
                  <div class="text-xs text-gray-600">{{ $movement->unloading_started_at->format('d M Y, H:i') }}</div>
                  <div class="text-xs text-gray-500">{{ $movement->unloading_started_at->diffForHumans() }}</div>
                </div>
              @elseif($factoryBooking->processing_started_at)
                <div class="text-center p-3 bg-yellow-50 rounded-lg border">
                  <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-yellow-600 text-sm">⚡</span>
                  </div>
                  <div class="text-sm font-medium text-yellow-800">Processing Started</div>
                  <div class="text-xs text-gray-600">{{ $factoryBooking->processing_started_at->format('d M Y, H:i') }}</div>
                </div>
              @else
                <div class="text-center p-3 bg-gray-50 rounded-lg border border-dashed">
                  <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-gray-400 text-sm">🏗️</span>
                  </div>
                  <div class="text-sm font-medium text-gray-500">Awaiting Processing</div>
                  @if($factoryBooking->arrived_at)
                    <div class="text-xs text-gray-400">On site {{ $factoryBooking->arrived_at->diffForHumans() }}</div>
                  @endif
                </div>
              @endif

              {{-- Departure --}}
              @if($factoryBooking->departed_at)
                <div class="text-center p-3 bg-gray-50 rounded-lg border">
                  <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-gray-600 text-sm">🚛</span>
                  </div>
                  <div class="text-sm font-medium text-gray-800">Site Departure</div>
                  <div class="text-xs text-gray-600">{{ $factoryBooking->departed_at->format('d M Y, H:i') }}</div>
                  @if($factoryBooking->arrived_at)
                    @php
                      $totalMinutes = round($factoryBooking->arrived_at->diffInMinutes($factoryBooking->departed_at));
                      if ($totalMinutes >= 10080) {
                        $weeks = floor($totalMinutes / 10080);
                        $days = floor(($totalMinutes % 10080) / 1440);
                        $totalTimeFormatted = $weeks . 'w' . ($days > 0 ? ' ' . $days . 'd' : '');
                      } elseif ($totalMinutes >= 1440) {
                        $days = floor($totalMinutes / 1440);
                        $hours = floor(($totalMinutes % 1440) / 60);
                        $mins = $totalMinutes % 60;
                        $totalTimeFormatted = $days . 'd ' . ($hours > 0 ? $hours . 'h ' : '') . ($mins > 0 ? $mins . 'm' : '');
                      } elseif ($totalMinutes >= 60) {
                        $hours = floor($totalMinutes / 60);
                        $mins = $totalMinutes % 60;
                        $totalTimeFormatted = $hours . 'h ' . ($mins > 0 ? $mins . 'm' : '');
                      } else {
                        $totalTimeFormatted = $totalMinutes . ' min';
                      }
                    @endphp
                    <div class="text-xs text-gray-500">Total time: {{ $totalTimeFormatted }}</div>
                  @endif
                </div>
              @else
                <div class="text-center p-3 bg-gray-50 rounded-lg border border-dashed">
                  <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-gray-400 text-sm">🚛</span>
                  </div>
                  <div class="text-sm font-medium text-gray-500">Awaiting Departure</div>
                  @if($factoryBooking->arrived_at)
                    <div class="text-xs text-gray-400">On site {{ $factoryBooking->arrived_at->diffForHumans() }}</div>
                  @endif
                </div>
              @endif
            </div>
          </div>
        </div>

        {{-- Event Timeline --}}
        <div class="bg-white rounded-lg border">
          <div class="p-4 border-b border-gray-200">
            <h4 class="text-lg font-semibold text-gray-800">📋 Event Timeline</h4>
            <p class="text-sm text-gray-600">All activities in chronological order</p>
          </div>
          <div class="p-4">
            <div class="space-y-3">
              @foreach($history as $event)
                <div class="flex items-start space-x-3">
                  {{-- Timeline dot --}}
                  <div class="flex-shrink-0 mt-1">
                    @php
                      $color = match($event->action) {
                        'created' => 'orange',
                        'arrival' => 'blue',
                        'modified' => 'indigo',
                        'completed' => 'purple',
                        default => 'gray'
                      };
                      $icon = match($event->action) {
                        'created' => '🏭',
                        'arrival' => '🚪',
                        'modified' => '🔄',
                        'completed' => '🏁',
                        default => '•'
                      };
                    @endphp
                    <div class="w-8 h-8 bg-{{ $color }}-100 rounded-full flex items-center justify-center">
                      <span class="text-{{ $color }}-600 text-sm">{{ $icon }}</span>
                    </div>
                  </div>
                  {{-- Event content --}}
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                      <h5 class="text-sm font-medium text-gray-900 capitalize">{{ $event->action }}</h5>
                      <span class="text-xs text-gray-500">{{ $event->created_at->format('d M Y, H:i') }}</span>
                    </div>
                    @if($event->reason)
                      <p class="text-xs text-gray-600 mt-1">{{ $event->reason }}</p>
                    @endif
                  </div>
                </div>
              @endforeach
              @if($history->isEmpty())
                <div class="text-center py-8 text-gray-500">
                  <p class="text-sm">No timeline events recorded yet.</p>
                </div>
              @endif
            </div>
          </div>
        </div>
      @endif
    </div>

    {{-- Summary Statistics --}}
    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-white p-4 rounded-lg shadow text-center">
        <div class="text-2xl font-bold text-orange-600">{{ $history->where('action', 'created')->count() }}</div>
        <div class="text-sm text-gray-600">Created</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow text-center">
        <div class="text-2xl font-bold text-blue-600">{{ $history->where('action', 'modified')->count() }}</div>
        <div class="text-sm text-gray-600">Movements</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow text-center">
        <div class="text-2xl font-bold text-purple-600">{{ $history->where('action', 'completed')->count() }}</div>
        <div class="text-sm text-gray-600">Completed</div>
      </div>
    </div>
  </div>
</x-app-layout>