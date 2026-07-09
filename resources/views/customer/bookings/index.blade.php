{{-- resources/views/customer/bookings/index.blade.php --}}
<x-app-layout>
  @include('layouts.customer-nav')

  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl">My Bookings</h2>
      <div class="flex items-center gap-2">
        <a href="{{ route('customer.bookings.export.pdf', request()->query()) }}"
           class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs" target="_blank" title="Export PDF">
          📄 PDF
        </a>
        <a href="{{ route('customer.bookings.export.excel', request()->query()) }}"
           class="px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-xs" title="Export Excel">
          📊 Excel
        </a>
        <a href="{{ route('customer.bookings.export.csv', request()->query()) }}"
           class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs" title="Export CSV">
          📝 CSV
        </a>
        <a href="{{ route('customer.bookings.create') }}"
           class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
          + New Booking
        </a>
      </div>
    </div>
  </x-slot>

  <div class="py-6 max-w-7xl mx-auto">
    @if(session('success'))
      <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
        {{ session('success') }}
      </div>
    @endif

    {{-- Quick Filter Buttons --}}
    <div class="mb-4 flex flex-wrap gap-2">
      <a href="{{ route('customer.bookings.index', ['filter' => 'today']) }}" 
         class="px-3 py-1 rounded text-sm {{ request('filter') == 'today' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
        📅 Today
      </a>
      <a href="{{ route('customer.bookings.index', ['filter' => 'tomorrow']) }}" 
         class="px-3 py-1 rounded text-sm {{ request('filter') == 'tomorrow' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
        🗓️ Tomorrow
      </a>
      <a href="{{ route('customer.bookings.index', ['filter' => 'last_week']) }}" 
         class="px-3 py-1 rounded text-sm {{ request('filter') == 'last_week' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
        📉 Last Week
      </a>
      <a href="{{ route('customer.bookings.index', ['filter' => 'this_week']) }}" 
         class="px-3 py-1 rounded text-sm {{ request('filter') == 'this_week' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
        📊 This Week
      </a>
      <a href="{{ route('customer.bookings.index', ['filter' => 'next_week']) }}" 
         class="px-3 py-1 rounded text-sm {{ request('filter') == 'next_week' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
        📈 Next Week
      </a>
    </div>

    {{-- Filters --}}
    <form method="GET" class="mb-4 flex flex-wrap gap-4 items-end bg-gray-50 p-4 rounded">
      <div>
        <label class="block text-sm font-medium">Depot</label>
        <select name="depot_id" class="border rounded px-2 py-1 text-sm">
          <option value="">All</option>
          @foreach($depots as $depot)
            <option value="{{ $depot->id }}" @selected(request('depot_id') == $depot->id)>{{ $depot->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium">Type</label>
        <select name="booking_type_id" class="border rounded px-2 py-1 text-sm">
          <option value="">All</option>
          @foreach($types as $type)
            <option value="{{ $type->id }}" @selected(request('booking_type_id') == $type->id)>{{ $type->name }}</option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium">Week Number</label>
        <select name="week_number" class="border rounded px-2 py-1 text-sm">
          <option value="">All</option>
          @foreach($weeks as $week)
            <option value="{{ $week['number'] }}" @selected(request('week_number') == $week['number'])>
              Week {{ $week['number'] }} ({{ $week['start'] }} - {{ $week['end'] }})
            </option>
          @endforeach
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium">From</label>
        <input type="date" name="from" value="{{ request('from') }}" class="border rounded px-2 py-1 text-sm">
      </div>
      <div>
        <label class="block text-sm font-medium">To</label>
        <input type="date" name="to" value="{{ request('to') }}" class="border rounded px-2 py-1 text-sm">
      </div>
      <div>
        <label class="block text-sm font-medium">Arrival Status</label>
        <select name="arrival" class="border rounded px-2 py-1 text-sm">
          <option value="">All</option>
          <option value="not_arrived" @selected(request('arrival')=='not_arrived')>📋 Not Arrived</option>
          <option value="late_runners" @selected(request('arrival')=='late_runners')>⏰ Late Runners</option>
          <option value="arrived" @selected(request('arrival')=='arrived')>✅ Arrived</option>
          <option value="on_time" @selected(request('arrival')=='on_time')>🎯 Arrived On Time</option>
          <option value="arrived_late" @selected(request('arrival')=='arrived_late')>🔶 Arrived Late</option>
          <option value="onsite" @selected(request('arrival')=='onsite')>🚛 On Site</option>
          <option value="completed" @selected(request('arrival')=='completed')>✅ Completed</option>
        </select>
      </div>
      <div class="flex space-x-2">
        <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Filter</button>
        <a href="{{ route('customer.bookings.index') }}" class="px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm">Clear</a>
      </div>
    </form>

    @if($bookings->isEmpty())
      <p class="text-gray-500">No bookings found matching your criteria.</p>
    @else
      <table class="min-w-full bg-white shadow rounded overflow-hidden text-sm">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-2 text-left">Booking Ref</th>
            <th class="px-4 py-2 text-left">Customer</th>
            <th class="px-4 py-2 text-left">Depot</th>
            <th class="px-4 py-2 text-left">Start → End</th>
            <th class="px-4 py-2 text-left">Type</th>
            <th class="px-4 py-2 text-left">Vehicle/Container</th>
            <th class="px-4 py-2 text-left">Expected / Actual</th>
            <th class="px-4 py-2 text-left">Status</th>
            <th class="px-4 py-2 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($bookings as $booking)
            @php
              $isFactory = isset($booking->type) && $booking->type === 'factory';
              $rowBg = $isFactory ? 'bg-purple-25' : '';
            @endphp
            <tr class="border-t hover:bg-gray-50 {{ $rowBg }}">
              <td class="px-4 py-2">
                @if($isFactory)
                  <span class="font-mono text-sm font-semibold text-purple-600">
                    🏭 {{ $booking->booking_reference ?? 'N/A' }}
                  </span>
                  <br><span class="text-xs bg-purple-100 px-2 py-1 rounded text-purple-700">Factory</span>
                @else
                  <span class="font-mono text-sm font-semibold text-blue-600">
                    {{ $booking->booking_reference ?? 'N/A' }}
                  </span>
                @endif
                @if($booking->reference && !$isFactory)
                  <br><span class="text-xs text-gray-500">{{ $booking->reference }}</span>
                @endif
              </td>
              <td class="px-4 py-2">{{ optional($booking->customer)->name ?? '-' }}</td>
              <td class="px-4 py-2">{{ $booking->slot->depot->name }}</td>
              <td class="px-4 py-2">
                @php
                  $slotStart = $booking->slot->start_at;
                  $now = now();
                  $arrivedAt = $booking->arrived_at;
                  $isLateNotArrived = $now->greaterThan($slotStart) && !$arrivedAt;
                  $isLateArrived = $arrivedAt && $arrivedAt->greaterThan($slotStart);

                  // Factory bookings are wrapped in a plain stdClass pseudo-slot
                  // (see CustomerBookingController::combineAndSortBookings) which
                  // has no getScheduledEndTime() method of its own — use the
                  // real underlying model for factory bookings instead.
                  $scheduledEndTime = $isFactory
                      ? optional($booking->original_factory_booking)->getScheduledEndTime()
                      : $booking->getScheduledEndTime();
                  $displayEndTime = $scheduledEndTime ?? $booking->slot->end_at;
                @endphp
                
                @if($isLateNotArrived)
                  @if($booking->estimated_arrival)
                    <div class="text-blue-600 text-xs font-semibold">
                      💬 Updated ETA: {{ \Carbon\Carbon::parse($booking->estimated_arrival)->format('d-M H:i') }}
                    </div>
                  @endif
                  <div class="text-red-600 text-xs font-semibold">
                    Original: {{ $slotStart->format('d-M H:i') }}
                  </div>
                  <div class="text-red-600 text-xs">
                    Late by: {{ $slotStart->diffForHumans() }}
                  </div>
                @elseif($isLateArrived)
                  @if($booking->estimated_arrival)
                    <div class="text-blue-600 text-xs font-semibold">
                      💬 Updated ETA: {{ \Carbon\Carbon::parse($booking->estimated_arrival)->format('d-M H:i') }}
                    </div>
                  @endif
                  <div class="text-orange-600 text-xs font-semibold">
                    Original: {{ $slotStart->format('d-M H:i') }}
                  </div>
                @elseif($booking->estimated_arrival)
                  <div class="text-blue-600 text-xs font-semibold">
                    💬 Updated ETA: {{ \Carbon\Carbon::parse($booking->estimated_arrival)->format('d-M H:i') }}
                  </div>
                @endif
                
                {{ \Carbon\Carbon::parse($booking->slot->start_at)->format('d-M H:i') }} →
                {{ $displayEndTime->format('d-M H:i') }}
              </td>
              <td class="px-4 py-2">{{ optional($booking->bookingType)->name ?? '-' }}</td>
              <td class="px-4 py-2">
                @if($booking->vehicle_registration)
                  🚛 {{ $booking->vehicle_registration }}<br>
                @endif
                @if($isFactory && $booking->trailer_registration)
                  🚚 {{ $booking->trailer_registration }}<br>
                @endif
                @if($booking->container_number)
                  📦 {{ $booking->container_number }}<br>
                @endif
              </td>
              <td class="px-4 py-2">
                <div class="text-xs">
                  @if($isFactory)
                    <div class="text-purple-600">
                      🏭 Factory Delivery
                      @if($booking->original_factory_booking->tipping_type)
                        <br><span class="text-gray-600">{{ ucfirst($booking->original_factory_booking->tipping_type) }}</span>
                      @endif
                    </div>
                  @elseif($booking->poNumbers->count() > 0)
                    @foreach($booking->poNumbers as $po)
                      <div class="mb-2">
                        <strong>PO: {{ $po->po_number }}</strong><br>
                        <div class="text-gray-600">{{ $po->expected_summary_text }}</div>
                        @if($po->total_actual_units > 0 || $po->total_actual_pallets > 0)
                          <div class="text-green-600 font-medium">{{ $po->actual_summary_text }}</div>
                        @endif
                      </div>
                    @endforeach
                  @else
                    <div class="text-gray-500">No PO numbers specified</div>
                  @endif
                </div>
              </td>
              <td class="px-4 py-2">
                @if($booking->cancelled_at && (!$booking->cancellation_reason || !str_contains($booking->cancellation_reason, 'Rebooked')))
                  <span class="inline-block px-2 py-1 bg-black text-white rounded text-xs font-semibold">
                    ❌ Cancelled
                  </span>
                  <br><div class="text-xs text-gray-500 mt-1">
                    {{ $booking->cancelled_at->format('d-M H:i') }}
                  </div>
                  @if($booking->cancellation_reason)
                    <div class="text-xs text-gray-600 mt-1" title="{{ $booking->cancellation_reason }}">
                      {{ Str::limit($booking->cancellation_reason, 25) }}
                    </div>
                  @endif
                @else
                  @if($booking->arrived_at)
                    ✅ Arrived: {{ $booking->arrived_at->format('d-M H:i') }}<br>
                  @endif
                  @if($booking->departed_at)
                    🕒 Departed: {{ $booking->departed_at->format('d-M H:i') }}<br>
                    @php
                      $arr = \Carbon\Carbon::parse($booking->arrived_at);
                      $dep = \Carbon\Carbon::parse($booking->departed_at);
                      $dur = $arr->diffInMinutes($dep);
                      $slotDur = $booking->slot->start_at->diffInMinutes($booking->slot->end_at);
                      $badge = $dur > $slotDur
                        ? ['Over Time', 'bg-red-600']
                        : ['On Time', 'bg-green-600'];
                      $d = floor($dur / 1440);
                      $h = floor(($dur % 1440) / 60);
                      $m = $dur % 60;
                    @endphp
                    <div class="text-xs text-gray-700 mt-1">
                      ⏱ Duration: {{ "$d d $h h $m m" }}
                      <span class="ml-2 inline-block px-2 py-0.5 rounded text-white text-xs font-semibold {{ $badge[1] }}">
                        {{ $badge[0] }}
                      </span>
                    </div>
                  @endif
                  @if($booking->status)
                    <span class="inline-block px-2 py-1 mt-1 rounded text-white text-xs font-semibold
                      {{ $booking->status === 'early' ? 'bg-blue-500' : ($booking->status === 'on time' ? 'bg-green-500' : 'bg-red-600') }}">
                      {{ ucfirst($booking->status) }}
                    </span>
                  @endif
                @endif
              </td>
              <td class="px-4 py-2">
                <div class="flex space-x-1">
                  @if($isFactory)
                    {{-- Factory bookings are read-only for customers --}}
                    <a href="{{ route('customer.factory-bookings.show', $booking->original_factory_booking) }}"
                       class="px-2 py-1 bg-purple-500 text-white rounded hover:bg-purple-600 text-xs">
                      View
                    </a>
                  @else
                    {{-- Show History button FIRST if booking has been rebooked/has history --}}
                    @php
                      $hasHistory = false;
                      // Check if this booking has history (was rebooked or is part of a rebook chain)
                      try {
                        $hasHistory = $booking->original_booking_id || 
                                     $booking->is_rebooked || 
                                     ($booking->cancellation_reason && str_contains($booking->cancellation_reason, 'Rebooked'));
                      } catch (\Exception $e) {
                        $hasHistory = false;
                      }
                    @endphp
                    
                    @if($hasHistory)
                      <a href="{{ route('customer.bookings.history', $booking) }}"
                         class="px-2 py-1 bg-purple-500 text-white rounded hover:bg-purple-600 text-xs" 
                         title="This booking has history - view rebook/cancel history">
                        📋 History
                      </a>
                    @endif
                    
                    {{-- Always show View button for regular bookings --}}
                    <a href="{{ route('customer.bookings.show', $booking) }}"
                       class="px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600 text-xs">View</a>
                    
                    {{-- Show Edit button only if booking can be edited --}}
                    @php
                      $isLocked = $booking->slot->locked_at && $booking->slot->locked_at->isPast();
                      $hasArrived = $booking->arrived_at;
                      $isCancelled = $booking->cancelled_at && (!$booking->cancellation_reason || !str_contains($booking->cancellation_reason, 'Rebooked'));
                    @endphp
                    
                    @if(!$isCancelled && !$hasArrived && !$isLocked && auth()->user()->can('update', $booking))
                      <a href="{{ route('customer.bookings.edit', $booking) }}"
                         class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-xs">Edit</a>
                    @elseif(!$isCancelled && $isLocked && !$hasArrived)
                      <span class="px-2 py-1 bg-orange-500 text-white rounded text-xs" title="Cut-off time passed">🔒 Locked</span>
                    @elseif(!$isCancelled && $hasArrived)
                      <span class="px-2 py-1 bg-green-500 text-white rounded text-xs" title="Vehicle has arrived">⚫ Final</span>
                    @endif
                  @endif
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <div class="mt-4">
        {{ $bookings->links() }}
      </div>
    @endif
  </div>
</x-app-layout>
