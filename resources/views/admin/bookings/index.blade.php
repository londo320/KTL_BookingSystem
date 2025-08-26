<x-app-layout>

  <x-slot name="header">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="font-semibold text-xl">Bookings</h2>
        <div class="text-sm mt-1">
          @if(!$currentDepotId)
            <span class="text-gray-600">Viewing: <span class="font-medium text-purple-600">All Depots</span></span>
            <span class="ml-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Actions Restricted</span>
          @else
            @php $currentDepot = $allDepots->firstWhere('id', $currentDepotId); @endphp
            <span class="text-gray-600">Viewing: <span class="font-medium text-blue-600">{{ $currentDepot?->name ?? 'Unknown Depot' }}</span></span>
            @if($currentDepotId == $defaultDepotId)
              <span class="ml-2 text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Actions Enabled</span>
            @else
              <span class="ml-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">View Only</span>
            @endif
          @endif
        </div>
      </div>
      <div class="flex gap-2">
        @php
          $routePrefix = 'app.';
        @endphp
        
        @canFunction('bookings.fix-historical-departures')
        <a href="{{ route($routePrefix . 'bookings.fix-historical-departures') }}"
           class="px-3 py-1 bg-orange-600 text-white rounded hover:bg-orange-700 text-sm">
          🔧 Fix Historical Data
        </a>
        @endcanFunction
        
        @canFunction('customer-behavior.view')
        <a href="{{ route('app.customer-behavior.index') }}"
           class="px-3 py-1 bg-purple-600 text-white rounded hover:bg-purple-700 text-sm">
          📊 Customer Analysis
        </a>
        @endcanFunction
        
        @canFunction('bookings.create')
        <a href="{{ route('app.bookings.create') }}"
           class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
          + New Booking
        </a>
        @endcanFunction
      </div>
    </div>
  </x-slot>

  <div class="py-6 max-w-7xl mx-auto">
    @if(session('success'))
      <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
        {{ session('success') }}
      </div>
    @endif

    {{-- Main Control Panel --}}
    <div class="mb-6 bg-white rounded-lg shadow-sm border">
      {{-- Top Row: Search, Quick Actions & Export --}}
      <div class="p-4 border-b border-gray-200">
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 items-center">
          {{-- Search Box --}}
          <div class="xl:col-span-1">
            <form method="GET" action="{{ route('app.bookings.index') }}" class="flex gap-2">
              {{-- Preserve existing filters --}}
              @foreach(request()->except(['search', 'page']) as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
              @endforeach
              
              <input type="text" 
                     name="search" 
                     value="{{ request('search') }}"
                     placeholder="🔍 Search booking ref, customer, vehicle, container..."
                     class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm">
              <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
                Search
              </button>
              @if(request('search'))
                <a href="{{ route('app.bookings.index', request()->except(['search', 'page'])) }}"
                   class="px-3 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm">
                  Clear
                </a>
              @endif
            </form>
          </div>
          
          {{-- Quick Actions --}}
          <div class="xl:col-span-1 flex justify-center gap-2 flex-wrap">
            <a href="{{ route('app.factory-bookings.index') }}" 
               class="px-3 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-sm font-medium">
              🏭 Factory Inbound
            </a>
            <button onclick="openTrailerCollectionModal()" 
                    class="px-3 py-2 bg-green-600 text-white rounded hover:bg-green-700 text-sm font-medium">
              🚛 Trailer Collection
            </button>
            <a href="{{ route('app.trailer-location-report') }}" 
               class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium">
              📍 Trailers on Site
            </a>
          </div>
          
          {{-- Export Actions --}}
          @hasAnyFunction(['bookings.export.pdf', 'bookings.export.excel', 'bookings.export.csv'])
          <div class="xl:col-span-1 flex justify-end gap-1">
            <div class="flex gap-1">
              @canFunction('bookings.export.pdf')
              <a href="{{ route('app.bookings.export.pdf', request()->query()) }}" 
                 class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs" target="_blank" title="Export PDF">
                📄 PDF
              </a>
              @endcanFunction
              
              @canFunction('bookings.export.excel')
              <a href="{{ route('app.bookings.export.excel', request()->query()) }}" 
                 class="px-2 py-1 bg-green-600 text-white rounded hover:bg-green-700 text-xs" title="Export Excel">
                📊 Excel
              </a>
              @endcanFunction
              
              @canFunction('bookings.export.csv')
              <a href="{{ route('app.bookings.export.csv', request()->query()) }}" 
                 class="px-2 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-xs" title="Export CSV">
                📝 CSV
              </a>
              @endcanFunction
            </div>
          </div>
          @endhasAnyFunction
        </div>
      </div>
      
      {{-- Quick Filters Row --}}
      <div class="p-4">
        <div class="flex flex-wrap items-center gap-4">
          {{-- Status Filter --}}
          <div class="flex gap-2 items-center">
            <span class="text-sm font-medium text-gray-700">Status:</span>
            <a href="{{ route('app.bookings.index', array_merge(request()->except(['status', 'page']), ['status' => 'outstanding'])) }}" 
               class="px-3 py-1 rounded text-sm {{ request('status', 'outstanding') == 'outstanding' ? 'bg-orange-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
              ⏳ Outstanding
            </a>
            <a href="{{ route('app.bookings.index', array_merge(request()->except(['status', 'page']), ['status' => 'completed'])) }}" 
               class="px-3 py-1 rounded text-sm {{ request('status') == 'completed' ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
              ✅ Completed
            </a>
            <a href="{{ route('app.bookings.index', array_merge(request()->except(['status', 'page']), ['status' => 'all'])) }}" 
               class="px-3 py-1 rounded text-sm {{ request('status') == 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
              📋 All
            </a>
          </div>
          
          {{-- Date Filter --}}
          <div class="flex flex-wrap gap-2 items-center">
            <span class="text-sm font-medium text-gray-700">Date:</span>
            <a href="{{ route('app.bookings.index', array_merge(request()->except(['filter', 'page']), ['filter' => 'yesterday'])) }}" 
               class="px-3 py-1 rounded text-sm {{ request('filter') == 'yesterday' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
              📅 Yesterday
            </a>
            <a href="{{ route('app.bookings.index', array_merge(request()->except(['filter', 'page']), ['filter' => 'today'])) }}" 
               class="px-3 py-1 rounded text-sm {{ request('filter') == 'today' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
              📅 Today
            </a>
            <a href="{{ route('app.bookings.index', array_merge(request()->except(['filter', 'page']), ['filter' => 'tomorrow'])) }}" 
               class="px-3 py-1 rounded text-sm {{ request('filter') == 'tomorrow' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
              🗓️ Tomorrow
            </a>
            <a href="{{ route('app.bookings.index', array_merge(request()->except(['filter', 'page']), ['filter' => 'last_week'])) }}" 
               class="px-3 py-1 rounded text-sm {{ request('filter') == 'last_week' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
              📉 Last Week
            </a>
            <a href="{{ route('app.bookings.index', array_merge(request()->except(['filter', 'page']), ['filter' => 'this_week'])) }}" 
               class="px-3 py-1 rounded text-sm {{ request('filter') == 'this_week' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
              📊 This Week
            </a>
            <a href="{{ route('app.bookings.index', array_merge(request()->except(['filter', 'page']), ['filter' => 'next_week'])) }}" 
               class="px-3 py-1 rounded text-sm {{ request('filter') == 'next_week' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
              📈 Next Week
            </a>
          </div>
          
          {{-- Reset Button --}}
          @if(request()->hasAny(['filter', 'status', 'search']))
            <a href="{{ route('app.bookings.index') }}"
               class="px-3 py-1 rounded text-sm bg-gray-500 text-white hover:bg-gray-600 ml-auto">
              🔄 Reset All
            </a>
          @endif
        </div>
      </div>
    </div>

    {{-- Advanced Filters (Collapsible) --}}
    <div class="mb-4 bg-white rounded-lg shadow-sm border">
      <button type="button" onclick="toggleAdvancedFilters()" class="w-full p-3 text-left flex items-center justify-between hover:bg-gray-50">
        <span class="text-sm font-medium text-gray-700">🔧 Advanced Filters</span>
        <svg id="advanced-filters-icon" class="w-4 h-4 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
        </svg>
      </button>
      
      <div id="advanced-filters-content" class="hidden border-t border-gray-200">
        <form method="GET" class="p-4">
          <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">🏭 Depot</label>
              <select name="depot_id" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                <option value="" {{ !$currentDepotId ? 'selected' : '' }}>All Depots</option>
                @foreach($allDepots as $depot)
                  <option value="{{ $depot->id }}" {{ $currentDepotId == $depot->id ? 'selected' : '' }}>
                    {{ $depot->name }}
                  </option>
                @endforeach
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">👥 Customer</label>
              <select name="customer_id" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                <option value="">All</option>
                @foreach($customers as $customer)
                  <option value="{{ $customer->id }}" @selected(request('customer_id') == $customer->id)>{{ $customer->name }}</option>
                @endforeach
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">📋 Type</label>
              <select name="booking_type_id" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                <option value="">All</option>
                @foreach($types as $type)
                  <option value="{{ $type->id }}" @selected(request('booking_type_id') == $type->id)>{{ $type->name }}</option>
                @endforeach
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">📅 Week</label>
              <select name="week_number" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                <option value="">All Weeks</option>
                @foreach($weeks as $week)
                  @php
                    $isCurrentWeek = $week['number'] === \Carbon\Carbon::now()->weekOfYear;
                  @endphp
                  <option value="{{ $week['number'] }}" @selected(request('week_number') == $week['number'])>
                    Week {{ $week['number'] }}{{ $isCurrentWeek ? ' (Current)' : '' }}
                  </option>
                @endforeach
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">📅 From Date</label>
              <input type="date" name="from" value="{{ request('from') }}" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">📅 To Date</label>
              <input type="date" name="to" value="{{ request('to') }}" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">🚛 Arrival Status</label>
              <select name="arrival" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                <option value="">All</option>
                <option value="not_arrived" @selected(request('arrival')=='not_arrived')>📋 Not Arrived</option>
                <option value="late_runners" @selected(request('arrival')=='late_runners')>⏰ Late Runners</option>
                <option value="arrived" @selected(request('arrival')=='arrived')>✅ Arrived</option>
                <option value="on_time" @selected(request('arrival')=='on_time')>🎯 On Time</option>
                <option value="arrived_late" @selected(request('arrival')=='arrived_late')>🔶 Arrived Late</option>
                <option value="onsite" @selected(request('arrival')=='onsite')>🚛 On Site</option>
                <option value="completed" @selected(request('arrival')=='completed')>✅ Completed</option>
              </select>
            </div>
            
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">📊 Booking Status</label>
              <select name="status" class="w-full border border-gray-300 rounded px-2 py-1 text-sm">
                <option value="" @selected(!request('status'))>🔍 Active Only</option>
                <option value="all" @selected(request('status') == 'all')>📋 Show All</option>
                <option value="pending" @selected(request('status') == 'pending')>⏳ Pending</option>
                <option value="confirmed" @selected(request('status') == 'confirmed')>✅ Confirmed</option>
                <option value="in_progress" @selected(request('status') == 'in_progress')>🚛 In Progress</option>
                <option value="completed" @selected(request('status') == 'completed')>✅ Completed</option>
                <option value="cancelled" @selected(request('status') == 'cancelled')>❌ Cancelled</option>
              </select>
            </div>
          </div>
          
          <div class="mt-4 flex justify-end gap-2">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm font-medium">
              🔍 Apply Filters
            </button>
            <a href="{{ route('app.bookings.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm font-medium">
              🔄 Reset All
            </a>
          </div>
        </form>
      </div>
    </div>

    {{-- Bookings Table --}}
    <table class="min-w-full bg-white shadow rounded overflow-hidden text-sm">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 text-left">Booking Reference</th>
          <th class="px-4 py-2 text-left">Start → End</th>
          <th class="px-4 py-2 text-left">Customer / Collection</th>
          <th class="px-4 py-2 text-left">Type / PO Numbers</th>
          <th class="px-4 py-2 text-left">Cases</th>
          <th class="px-4 py-2 text-left">Pallets</th>
          <th class="px-4 py-2 text-left">Arrival</th>
          <th class="px-4 py-2 text-left">Tipping Status</th>
          <th class="px-4 py-2 text-left">Actions</th>
        </tr>
      </thead>
      <tbody>
  @foreach($bookings->groupBy(fn($b) => $b->slot->depot->name) as $depotName => $group)
    <tr><td colspan="9" class="bg-gray-200 font-semibold px-4 py-2">Depot: {{ $depotName }}</td></tr>
    @foreach($group->sortBy(fn($b) => $b->slot->start_at) as $booking)
      <tr class="border-t hover:bg-gray-50 
        @if($booking->cancelled_at) bg-red-50 border-red-200 
        @elseif($booking->status === 'completed') bg-green-50 border-green-200 
        @elseif($booking->arrived_at && !$booking->departed_at) bg-blue-50 border-blue-200 
        @elseif($booking->trailer_left_on_site && !$booking->trailer_collected_at) bg-orange-50 border-orange-200 
        @endif">
        {{-- Booking Reference with Status Indicator --}}
        <td class="px-4 py-2 align-top">
          <div class="flex items-center">
            @if($booking->cancelled_at)
              <div class="w-3 h-3 bg-red-500 rounded-full mr-2" title="Cancelled"></div>
            @elseif($booking->status === 'completed')
              <div class="w-3 h-3 bg-green-500 rounded-full mr-2" title="Completed"></div>
            @elseif($booking->arrived_at && !$booking->departed_at)
              <div class="w-3 h-3 bg-blue-500 rounded-full mr-2 animate-pulse" title="On Site"></div>
            @elseif($booking->trailer_left_on_site && !$booking->trailer_collected_at)
              <div class="w-3 h-3 bg-orange-500 rounded-full mr-2" title="Trailer On Site"></div>
            @elseif($booking->status === 'confirmed')
              <div class="w-3 h-3 bg-yellow-500 rounded-full mr-2" title="Confirmed"></div>
            @else
              <div class="w-3 h-3 bg-gray-400 rounded-full mr-2" title="Pending"></div>
            @endif
            <div class="font-mono text-sm font-semibold text-blue-600">{{ $booking->booking_reference }}</div>
          </div>
        </td>

        {{-- Start → End with live Late timer --}}
              <td class="px-4 py-2 align-top">
              @php
                $slotStart = $booking->slot->start_at;
                $now = now();
                $arrivedAt = $booking->arrived_at;

                // Determine if booking is late
                $isLateNotArrived = $now->greaterThan($slotStart) && !$arrivedAt;
                $isLateArrived = $arrivedAt && $arrivedAt->greaterThan($slotStart);
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
                <div id="late-{{ $booking->id }}" class="text-red-600 text-xs font-semibold">Late by: calculating…</div>
                <script>
                  document.addEventListener('DOMContentLoaded', function() {
                    const start = new Date("{{ $slotStart->format('Y-m-d H:i:s') }}");
                    const el = document.getElementById('late-{{ $booking->id }}');
                    function update() {
                      const now = new Date();
                      let diff = Math.floor((now - start) / 60000);
                      const d = Math.floor(diff / 1440); diff %= 1440;
                      const h = Math.floor(diff / 60); const m = diff % 60;
                      el.textContent = `Late by: ${d}d ${h}h ${m}m`;
                    }
                    update(); setInterval(update, 60000);
                  });
                </script>
              @elseif($isLateArrived)
                @php
                  $lateMinutes = $arrivedAt->diffInMinutes($slotStart);
                  $d = intdiv($lateMinutes, 1440);
                  $h = intdiv($lateMinutes % 1440, 60);
                  $m = $lateMinutes % 60;
                @endphp
                @if($booking->estimated_arrival)
                  <div class="text-blue-600 text-xs font-semibold">
                    💬 Updated ETA: {{ \Carbon\Carbon::parse($booking->estimated_arrival)->format('d-M H:i') }}
                  </div>
                @endif
                <div class="text-orange-600 text-xs font-semibold">
                  Original: {{ $slotStart->format('d-M H:i') }}
                </div>
                <div class="text-yellow-600 text-xs font-semibold">
                  Arrived Late by: {{ $d }}d {{ $h }}h {{ $m }}m
                </div>
              @endif

              {{ $slotStart->format('d-M H:i') }} → {{ $booking->slot->end_at->format('d-M H:i') }}
</td>

        {{-- Customer / Collection --}}
        <td class="px-4 py-2 align-top">
          <div class="text-sm font-medium text-gray-900">{{  $booking->customer->name ?? '-' }}</div>
          @if($booking->reference)
            <div class="text-xs text-gray-600">Collection: {{ $booking->reference }}</div>
          @endif
        </td>

        {{-- Type & PO Numbers --}}
        <td class="px-4 py-2 align-top">
          <div class="text-sm font-medium">{{ optional($booking->bookingType)->name ?? '-' }}</div>
          @if($booking->poNumbers && $booking->poNumbers->count() > 0)
            <div class="text-xs text-gray-600 mt-1">
              <div class="font-medium text-blue-600">{{ $booking->poNumbers->count() }} PO{{ $booking->poNumbers->count() > 1 ? 's' : '' }}:</div>
              <div class="space-y-1">
                @foreach($booking->poNumbers->take(3) as $po)
                  <div class="flex items-center space-x-1">
                    <span class="font-mono">{{ $po->po_number }}</span>
                    @if($po->hasVariance())
                      <span class="w-2 h-2 bg-red-500 rounded-full" title="Has variance"></span>
                    @elseif($po->isComplete())
                      <span class="w-2 h-2 bg-green-500 rounded-full" title="Complete"></span>
                    @endif
                  </div>
                @endforeach
                @if($booking->poNumbers->count() > 3)
                  <div class="text-gray-500">+{{ $booking->poNumbers->count() - 3 }} more</div>
                @endif
              </div>
            </div>
          @else
            <div class="text-xs text-gray-400">No PO numbers</div>
          @endif
        </td>

        {{-- Cases --}}
        <td class="px-4 py-2 align-top">
          @php
            // Use PO number totals (new structure)
            $actualCases = $booking->total_actual_cases;
            $expectedCases = $booking->total_expected_cases;
            $caseVariance = $booking->total_case_variance;
          @endphp
          
          <div class="text-sm">
            {{ $actualCases > 0 ? number_format($actualCases) : '-' }} / {{ $expectedCases > 0 ? number_format($expectedCases) : '-' }}
          </div>
          @if($booking->poNumbers && $booking->poNumbers->count() > 0)
            <div class="text-xs text-gray-500">From {{ $booking->poNumbers->count() }} PO{{ $booking->poNumbers->count() > 1 ? 's' : '' }}</div>
          @endif
          
          @if($actualCases > 0 && $expectedCases > 0)
            @php
              if ($caseVariance < 0) {
                $icon = '↓';
                $color = 'text-red-600';
                $text = 'Under by ' . abs($caseVariance);
              } elseif ($caseVariance > 0) {
                $icon = '↑';
                $color = 'text-blue-600';
                $text = 'Over by ' . $caseVariance;
              } else {
                $icon = '=';
                $color = 'text-green-600';
                $text = 'Matched';
              }
            @endphp
            <div class="text-xs {{ $color }} font-medium">
              <span class="text-base">{{ $icon }}</span> {{ $text }}
            </div>
          @endif
        </td>

        {{-- Pallets --}}
        <td class="px-4 py-2 align-top">
          @php
            // Use PO number totals (new structure)
            $actualPallets = $booking->total_actual_pallets;
            $expectedPallets = $booking->total_expected_pallets;
            $palletVariance = $booking->total_pallet_variance;
          @endphp
          
          <div class="text-sm">
            {{ $actualPallets > 0 ? number_format($actualPallets) : '-' }} / {{ $expectedPallets > 0 ? number_format($expectedPallets) : '-' }}
          </div>
          @if($booking->poNumbers && $booking->poNumbers->count() > 0)
            <div class="text-xs text-gray-500">From {{ $booking->poNumbers->count() }} PO{{ $booking->poNumbers->count() > 1 ? 's' : '' }}</div>
          @endif
          
          @if($actualPallets > 0 && $expectedPallets > 0)
            @php
              if ($palletVariance < 0) {
                $icon = '↓';
                $color = 'text-red-600';
                $text = 'Under by ' . abs($palletVariance);
              } elseif ($palletVariance > 0) {
                $icon = '↑';
                $color = 'text-blue-600';
                $text = 'Over by ' . $palletVariance;
              } else {
                $icon = '=';
                $color = 'text-green-600';
                $text = 'Matched';
              }
            @endphp
            <div class="text-xs {{ $color }} font-medium">
              <span class="text-base">{{ $icon }}</span> {{ $text }}
            </div>
          @endif
        </td>

        {{-- Arrival / Departure / Duration Badge --}}
        <td class="px-4 py-2 align-top space-y-1">
          @if($booking->cancelled_at && (!$booking->cancellation_reason || !str_contains($booking->cancellation_reason, 'Rebooked')))
            <div class="inline-block px-2 py-1 bg-black text-white rounded text-xs font-semibold">
              ❌ Cancelled
            </div>
            <div class="text-xs text-gray-500">
              {{ $booking->cancelled_at->format('d-M H:i') }}
            </div>
            @if($booking->cancellation_reason)
              <div class="text-xs text-gray-600" title="{{ $booking->cancellation_reason }}">
                {{ Str::limit($booking->cancellation_reason, 25) }}
              </div>
            @endif
          @elseif(!$booking->arrived_at)
            @php $canTakeAction = $booking->slot->depot_id == $defaultDepotId; @endphp
            @if($canTakeAction)
              <a href="{{ route($routePrefix . 'bookings.arrival.form', $booking) }}" 
                 class="inline-block bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700 cursor-pointer">
                🚛 Process Arrival
              </a>
            @else
              <span class="inline-block bg-gray-300 text-gray-500 px-3 py-1 rounded text-xs cursor-not-allowed"
                    title="Actions only available for your default depot">
                🚛 Process Arrival
              </span>
            @endif
          @else
            <div>✅ Arrived: {{ $booking->arrived_at->format('d-M H:i') }}</div>
            @if($booking->vehicle_registration)
              <div class="text-xs text-gray-600">🚛 {{ $booking->vehicle_registration }}</div>
            @endif
            @if($booking->container_number)
              <div class="text-xs text-gray-600">📦 {{ $booking->container_number }}</div>
            @endif
            
            {{-- Show waiting area status and bay assignment button --}}
            @if($booking->current_location === 'waiting_area' && $booking->waiting_area_location)
              <div class="text-xs text-blue-600 mt-1">🅿️ Waiting: {{ $booking->waiting_area_location }}</div>
              @if(!$booking->tipping_bay_id)
                @php $canTakeAction = $booking->slot->depot_id == $defaultDepotId; @endphp
                @if($canTakeAction)
                  <button onclick="openBayAssignmentModal({{ $booking->id }}, '{{ $booking->booking_reference }}', '{{ $booking->waiting_area_location }}')" 
                          class="inline-block bg-blue-600 text-white px-2 py-1 rounded text-xs hover:bg-blue-700 cursor-pointer mt-1">
                    🏗️ Assign Bay
                  </button>
                @else
                  <span class="inline-block bg-gray-300 text-gray-500 px-2 py-1 rounded text-xs cursor-not-allowed mt-1"
                        title="Actions only available for your default depot">
                    🏗️ Assign Bay
                  </span>
                @endif
              @endif
            @elseif($booking->current_location === 'tipping_bay' && $booking->tippingBay)
              <div class="text-xs text-green-600 mt-1">🏗️ Bay: {{ $booking->tippingBay->name }}</div>
            @endif
            
            {{-- Show trailer status if left on site --}}
            @if($booking->trailer_left_on_site && !$booking->trailer_collected_at)
              <div class="text-xs mt-1">
                <span class="px-1 py-0.5 rounded text-xs
                  @if($booking->dropped_trailer_status === 'empty_available') bg-green-100 text-green-700
                  @elseif($booking->dropped_trailer_status === 'awaiting_collection') bg-orange-100 text-orange-700
                  @elseif($booking->dropped_trailer_status === 'being_tipped') bg-blue-100 text-blue-700
                  @else bg-gray-100 text-gray-700 @endif">
                  📦 {{ ucwords(str_replace('_', ' ', $booking->dropped_trailer_status ?? 'On Site')) }}
                </span>
                @if($booking->dropped_trailer_location)
                  <br><span class="text-xs text-gray-500">📍 {{ $booking->dropped_trailer_location }}</span>
                @endif
                @if($booking->trailer_collection_scheduled && $booking->trailer_collection_scheduled->isPast())
                  <br><span class="text-xs text-red-600">⚠️ Collection Overdue</span>
                @elseif($booking->trailer_collection_scheduled)
                  <br><span class="text-xs text-gray-500">📅 Due: {{ $booking->trailer_collection_scheduled->format('d-M H:i') }}</span>
                @endif
              </div>
            @elseif($booking->trailer_collected_at)
              <div class="text-xs text-green-600 mt-1">✅ Trailer Collected</div>
            @endif
          @endif

          @if(!$booking->cancelled_at && $booking->arrived_at && !$booking->departed_at)
            @php 
              $canTakeAction = $booking->slot->depot_id == $defaultDepotId;
              $movement = $booking->movements->first();
              $unitHasDeparted = $movement && $movement->unit_departed_at;
            @endphp
            
            @if($unitHasDeparted)
              {{-- Show unit departure time instead of Mark Departed button --}}
              <div class="text-xs text-orange-600 font-medium">
                🚗 Unit Left: {{ $movement->unit_departed_at->format('d-M H:i') }}
              </div>
            @elseif($canTakeAction)
              <button onclick="openDepartureModal({{ $booking->id }}, '{{ $booking->booking_reference }}', '{{ addslashes($booking->customer->name ?? 'N/A') }}', '{{ $booking->vehicle_registration ?? '' }}', 
                @php
                  $currentLocation = null;
                  $currentLocationName = 'Unknown';
                  $tippingStatus = $movement ? $movement->current_status : null;
                  
                  // Check movement status and determine location based on trailer status (full/empty)
                  if ($movement) {
                    $isEmptyTrailer = in_array($movement->current_status, ['empty', 'awaiting_collection']);
                    
                    if ($isEmptyTrailer) {
                      // EMPTY TRAILER OPTIONS:
                      
                      // 1. Collection Zone (awaiting pickup)
                      if ($movement->current_status === 'awaiting_collection' && $movement->tippingLocation) {
                        $currentLocation = 'COLLECTION_' . $movement->tippingLocation->id;
                        $currentLocationName = '📦 ' . $movement->tippingLocation->name . ' (Collection Zone)';
                      }
                      // 2. Still in Tipping Bay (needs to be moved)
                      elseif ($movement->tippingBay) {
                        $currentLocation = 'BAY_' . $movement->tippingBay->id;
                        $currentLocationName = '🚛 ' . $movement->tippingBay->name . ' (Empty - Ready to Move)';
                      }
                      // 3. Waiting Area
                      elseif ($movement->custom_fields && isset($movement->custom_fields['waiting_area_location'])) {
                        $waitingArea = $movement->custom_fields['waiting_area_location'];
                        $currentLocation = 'WAITING_' . $waitingArea;
                        $currentLocationName = '⏳ Waiting Area ' . $waitingArea . ' (Empty)';
                      }
                      // 4. General drop location
                      elseif ($movement->tippingLocation) {
                        $currentLocation = 'DROP_' . $movement->tippingLocation->id;
                        $currentLocationName = '📍 ' . $movement->tippingLocation->name . ' (Empty)';
                      }
                      else {
                        $currentLocation = 'ONSITE_EMPTY';
                        $currentLocationName = '✅ Empty Trailer On Site';
                      }
                    } else {
                      // FULL TRAILER OPTIONS:
                      
                      // 1. In Tipping Bay (being tipped)
                      if ($movement->tippingBay) {
                        $currentLocation = 'BAY_' . $movement->tippingBay->id;
                        $currentLocationName = '🏗️ ' . $movement->tippingBay->name . ' (Tipping)';
                      }
                      // 2. In Drop Zone (awaiting tipping)
                      elseif ($movement->tippingLocation) {
                        $currentLocation = 'DROP_' . $movement->tippingLocation->id;
                        $currentLocationName = '📍 ' . $movement->tippingLocation->name . ' (Full - Awaiting)';
                      }
                      // 3. Waiting Area
                      elseif ($movement->custom_fields && isset($movement->custom_fields['waiting_area_location'])) {
                        $waitingArea = $movement->custom_fields['waiting_area_location'];
                        $currentLocation = 'WAITING_' . $waitingArea;
                        $currentLocationName = '⏳ Waiting Area ' . $waitingArea . ' (Full)';
                      }
                      else {
                        $currentLocation = 'ONSITE_FULL';
                        $currentLocationName = '📦 Full Trailer On Site';
                      }
                    }
                  }
                @endphp
                '{{ $currentLocation }}', '{{ addslashes($currentLocationName) }}', '{{ $tippingStatus }}')" 
                      class="text-green-600 text-xs underline hover:text-green-800">
                🏁 Mark Departed
              </button>
            @else
              <span class="text-gray-400 text-xs cursor-not-allowed" 
                    title="Actions only available for your default depot">
                🏁 Mark Departed
              </span>
            @endif
          @elseif(!$booking->cancelled_at && $booking->departed_at)
            <div>🕒 Departed: {{ $booking->departed_at->format('d-M H:i') }}</div>
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
                Tip: {{ $badge[0] }}
              </span>
            </div>
          @endif
        </td>

        {{-- Tipping Status & Location --}}
        <td class="px-4 py-2 align-top">
          @if($booking->arrived_at && !$booking->departed_at)
            @php $movement = $booking->movements->first(); @endphp
            <div class="space-y-1">
              {{-- Tipping Status Badge --}}
              <div>{!! $booking->tipping_status_badge !!}</div>
              
              {{-- Location Info Based on Trailer Status --}}
              @if($movement && ($movement->tippingLocation || $movement->tippingBay))
                @php
                  $isEmptyTrailer = in_array($movement->current_status, ['empty', 'awaiting_collection']);
                @endphp
                <div class="text-xs text-gray-600">
                  @if($isEmptyTrailer)
                    {{-- EMPTY TRAILER LOCATIONS --}}
                    @if($movement->current_status === 'awaiting_collection' && $movement->tippingLocation)
                      <div class="text-green-700">📦 {{ $movement->tippingLocation->name }} (Collection Zone)</div>
                    @elseif($movement->tippingBay)
                      <div class="text-yellow-700">🚛 {{ $movement->tippingBay->name }} (Empty - Ready to Move)</div>
                    @elseif($movement->tippingLocation)
                      <div class="text-blue-700">📍 {{ $movement->tippingLocation->name }} (Empty)</div>
                    @endif
                  @else
                    {{-- FULL TRAILER LOCATIONS --}}
                    @if($movement->tippingBay)
                      <div class="text-yellow-700">🏗️ {{ $movement->tippingBay->name }} (Tipping)</div>
                    @elseif($movement->tippingLocation)
                      <div class="text-blue-700">📍 {{ $movement->tippingLocation->name }} (Full - Awaiting)</div>
                    @endif
                  @endif
                  <div class="text-gray-400">({{ $movement->tippingLocation?->depot?->name ?? $movement->tippingBay?->depot?->name }})</div>
                </div>
              @endif
              
              {{-- Workflow Link --}}
              @if($booking->tipping_status && $booking->tipping_status !== 'departed')
                @php $canTakeAction = $booking->slot->depot_id == $defaultDepotId; @endphp
                @if($canTakeAction)
                  <a href="{{ route('app.tipping-workflow.show', $booking) }}" 
                     class="text-xs text-blue-600 hover:text-blue-800 block">
                    Manage →
                  </a>
                @else
                  <span class="text-xs text-gray-400 cursor-not-allowed block" 
                        title="Actions only available for your default depot">
                    Manage →
                  </span>
                @endif
              @endif
            </div>
          @elseif($booking->status === 'completed')
            <span class="inline-block px-2 py-1 bg-green-100 text-green-800 rounded text-xs">
              ✅ Complete
            </span>
          @else
            <span class="text-xs text-gray-400">Not started</span>
          @endif
        </td>

        {{-- Actions --}}
        <td class="px-4 py-2 align-top space-y-1">
          <div class="flex flex-col space-y-1">
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
              <a href="{{ route('app.bookings.history', $booking) }}"
                 class="inline-block px-2 py-1 bg-purple-500 text-white rounded-full hover:bg-purple-600 text-xs text-center" 
                 title="This booking has history - view rebook/cancel history">
                📋 History
              </a>
            @endif
            
            {{-- Always show View button --}}
            <a href="{{ route('app.bookings.show', $booking) }}"
               class="inline-block px-2 py-1 bg-blue-500 text-white rounded-full hover:bg-blue-600 text-xs text-center">
              View
            </a>
            
            {{-- Show Edit button only if not cancelled --}}
            @php
              $isCancelled = $booking->cancelled_at && (!$booking->cancellation_reason || !str_contains($booking->cancellation_reason, 'Rebooked'));
            @endphp
            
            @if(!$isCancelled)
              @php $canTakeAction = $booking->slot->depot_id == $defaultDepotId; @endphp
              @if($canTakeAction)
                <a href="{{ route('app.bookings.edit', $booking) }}"
                   class="inline-block px-2 py-1 bg-yellow-500 text-white rounded-full hover:bg-yellow-600 text-xs text-center">
                  Edit
                </a>
              @else
                <span class="inline-block px-2 py-1 bg-gray-300 text-gray-500 rounded-full text-xs text-center cursor-not-allowed"
                      title="Actions only available for your default depot">
                  Edit
                </span>
              @endif
            @endif
            
          </div>
        </td>
      </tr>
    @endforeach
  @endforeach
</tbody>
    </table>

    <div class="mt-4">{{ $bookings->links() }}</div>

    {{-- Depot Summary --}}
    <div class="mt-10">
      @foreach($summaryByDepotCustomer as $dep => $custs)
        <h3 class="text-lg font-semibold mb-4 text-center bg-blue-600 text-white px-4 py-1 rounded">{{ $dep }}</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
          @foreach($custs as $name => $sum)
            <div class="bg-white border rounded shadow p-4">
              <h4 class="font-semibold mb-2">🧾 {{ $name==='_totals' ? 'Site Totals' : $name }}</h4>
              <div class="space-y-1 text-sm">
                <div>✅ Arrived: {{ $sum['arrived'] }}</div>
                <div>⏰ Late: {{ $sum['late'] }}</div>
                <div>🚚 Outstanding: {{ $sum['outstanding'] }}</div>
                @if($name==='__totals')
                  <div>🗓️ Slots Used: {{ $sum['arrived'] + $sum['late'] + $sum['outstanding'] }} of {{ $bookings->count() }}</div>
                @endif
                <div>📦 Exp Units: {{ number_format($sum['expected_cases']) }} / Act: {{ number_format($sum['actual_cases']) }}</div>
                <div>🔺 Δ: {{ number_format($sum['case_variance']) }}</div>
                <div>📦 Pal Exp: {{ number_format($sum['expected_pallets']) }} / Act: {{ number_format($sum['actual_pallets']) }}</div>
                <div>🔺 Δ Pal: {{ number_format($sum['pallet_variance']) }}</div>
              </div>
            </div>
          @endforeach
        </div>
      @endforeach
    </div>
  </div>

  <!-- Arrival Modal -->
  <div id="arrivalModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <!-- Modal Header -->
        <div class="flex justify-between items-center pb-4 border-b">
          <h3 class="text-lg font-semibold text-gray-900">🚛 Vehicle Arrival Processing</h3>
          <button onclick="closeArrivalModal()" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <!-- Booking Summary -->
        <div id="bookingSummary" class="mt-4 p-4 bg-gray-50 rounded-lg">
          <!-- Will be populated by JavaScript -->
        </div>

        <!-- Arrival Form -->
        <form id="arrivalForm" method="POST" class="mt-6">
          @csrf
          
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            
            <!-- Required Vehicle Registration -->
            <div class="md:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Vehicle Registration <span class="text-red-500">*</span>
              </label>
              <input type="text" id="vehicleRegistration" name="vehicle_registration" required
                     placeholder="e.g., AB12 CDE"
                     class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
              <p class="text-xs text-gray-500 mt-1">Required for arrival processing</p>
            </div>

            <!-- Container Number -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Container/Trailer Number</label>
              <input type="text" id="containerNumber" name="container_number"
                     placeholder="e.g., CONT123456 or TR123456"
                     class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
              <p class="text-xs text-gray-500 mt-1">Can be updated if different from booking</p>
            </div>

            <!-- Carrier Company -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                Carrier Company <span class="text-red-500">*</span>
              </label>
              <div class="relative">
                <input type="text" 
                       id="carrierCompany" 
                       name="carrier_name"
                       placeholder="Search or type carrier name..."
                       required
                       autocomplete="off"
                       class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500 pr-10">
                
                {{-- Hidden carrier_id field --}}
                <input type="hidden" 
                       id="carrierId" 
                       name="carrier_id">
                
                {{-- Search dropdown --}}
                <div id="carrierDropdown" 
                     class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                  {{-- Results will be populated by JavaScript --}}
                </div>
                
                {{-- Status indicators --}}
                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                  <span id="carrierStatus" class="text-xs"></span>
                </div>
              </div>
              <p class="text-xs text-gray-500 mt-1">Search existing carriers or type to create new</p>
            </div>


            <!-- Trailer Size -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Trailer Type</label>
              <select id="trailerType" name="trailer_type_id" class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                <option value="">– Select Trailer Type –</option>
                @foreach($trailerTypes as $trailerType)
                  <option value="{{ $trailerType->id }}" {{ request('trailer_type_id') == $trailerType->id ? 'selected' : '' }}>
                    {{ $trailerType->name }}
                  </option>
                @endforeach
              </select>
              <p class="text-xs text-gray-500 mt-1">Type and size of trailer/container</p>
            </div>

            <!-- Tipping Assignment Section -->
            <div class="md:col-span-2 p-4 bg-blue-50 border border-blue-200 rounded-lg">
              <h4 class="font-medium text-blue-800 mb-3">🚛 Vehicle Assignment</h4>
              <p class="text-sm text-blue-700 mb-4">Assign vehicle to location upon arrival:</p>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Drop Location -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">🚛 Drop Location (Optional)</label>
                  <select id="tippingLocation" name="tipping_location_id" class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                    <option value="">– Assign Drop Location –</option>
                    @if(isset($tippingLocations))
                      @foreach($tippingLocations as $location)
                        <option value="{{ $location->id }}">
                          {{ $location->name }} ({{ $location->getAvailableCapacity() }}/{{ $location->capacity }} available)
                        </option>
                      @endforeach
                    @endif
                  </select>
                  <p class="text-xs text-gray-600 mt-1">Pre-assign drop location if known</p>
                </div>

                <!-- Direct Bay Assignment -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-2">🏗️ Tipping Bay (Direct)</label>
                  <select id="tippingBay" name="tipping_bay_id" class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                    <option value="">– No Direct Assignment –</option>
                    @if(isset($tippingBays))
                      @foreach($tippingBays as $bay)
                        <option value="{{ $bay->id }}" @disabled($bay->is_occupied)>
                          {{ $bay->name }} ({{ $bay->depot->name }}) 
                          @if($bay->is_occupied)
                            - Occupied
                          @else
                            - Available Now
                          @endif
                        </option>
                      @endforeach
                    @endif
                  </select>
                  <p class="text-xs text-gray-600 mt-1">Assign directly to tipping bay</p>
                </div>
              </div>
            </div>

          </div>

          <!-- Special Instructions -->
          <div id="specialInstructions" class="mt-4 p-4 bg-yellow-50 rounded-lg border border-yellow-200 hidden">
            <h4 class="font-medium text-yellow-800 mb-2">⚠️ Special Instructions:</h4>
            <p id="specialInstructionsText" class="text-yellow-700"></p>
          </div>

          <!-- Arrival Time Display -->
          <div class="mt-4 p-4 bg-green-50 rounded-lg border border-green-200">
            <h4 class="font-medium text-green-800 mb-2">📅 Arrival Time:</h4>
            <p class="text-green-700 font-semibold" id="arrivalTime">Will be recorded as: [Current Time]</p>
          </div>

          <!-- Form Actions -->
          <div class="mt-6 flex justify-end space-x-4">
            <button type="button" onclick="closeArrivalModal()" 
                    class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
              Cancel
            </button>
            <button type="submit" onclick="return validateArrivalForm()"
                    class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
              🚛 Mark Vehicle Arrived
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Departure Modal -->
  <div id="departureModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <!-- Modal Header -->
        <div class="flex justify-between items-center pb-4 border-b">
          <h3 class="text-lg font-semibold text-gray-900">🏁 Vehicle Departure Processing</h3>
          <button onclick="closeDepartureModal()" class="text-gray-400 hover:text-gray-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <div id="departureSummary" class="mt-4 p-4 bg-gray-50 rounded-lg">
          <!-- Will be populated by JavaScript -->
        </div>

        <!-- Departure Form -->
        <form id="departureForm" method="POST" class="mt-6">
          @csrf
          @method('PATCH')
          
          <div class="grid grid-cols-1 gap-4">
            
            <!-- Simple Departure Options -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">
                What happened with the vehicle? <span class="text-red-500">*</span>
              </label>
              <p class="text-xs text-gray-600 mb-3">Select the departure scenario that matches what actually happened:</p>
              <div class="grid grid-cols-2 gap-3">
                <!-- Vehicle Left With Trailer -->
                <div>
                  <input type="radio" name="departure_scenario" id="leftWithTrailer" value="completed_with_trailer" required
                         class="hidden peer">
                  <label for="leftWithTrailer" 
                         class="flex flex-col items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-green-500 peer-checked:bg-green-50 hover:bg-gray-50">
                    <div class="text-3xl mb-2">🚛✅</div>
                    <div class="text-sm font-medium text-center">Vehicle Left<br>WITH Trailer</div>
                    <div class="text-xs text-gray-500 mt-1">Job complete - normal departure</div>
                  </label>
                </div>
                
                <!-- Vehicle Left Without Trailer -->
                <div>
                  <input type="radio" name="departure_scenario" id="leftWithoutTrailer" value="completed_dropped_trailer" required
                         class="hidden peer">
                  <label for="leftWithoutTrailer" 
                         class="flex flex-col items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-orange-500 peer-checked:bg-orange-50 hover:bg-gray-50">
                    <div class="text-3xl mb-2">🚛📦</div>
                    <div class="text-sm font-medium text-center">Vehicle Left<br>WITHOUT Trailer</div>
                    <div class="text-xs text-gray-500 mt-1">Trailer left for collection</div>
                  </label>
                </div>
                
                <!-- Vehicle Still On Site -->
                <div>
                  <input type="radio" name="departure_scenario" id="stillOnSite" value="drop_and_wait" required
                         class="hidden peer">
                  <label for="stillOnSite" 
                         class="flex flex-col items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-blue-500 peer-checked:bg-blue-50 hover:bg-gray-50">
                    <div class="text-3xl mb-2">🅿️🚛</div>
                    <div class="text-sm font-medium text-center">Vehicle Still<br>ON SITE</div>
                    <div class="text-xs text-gray-500 mt-1">Not yet departed</div>
                  </label>
                </div>
                
                <!-- Emergency/Problem -->
                <div>
                  <input type="radio" name="departure_scenario" id="problemDeparture" value="emergency_departure" required
                         class="hidden peer">
                  <label for="problemDeparture" 
                         class="flex flex-col items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-red-500 peer-checked:bg-red-50 hover:bg-gray-50">
                    <div class="text-3xl mb-2">🚨❌</div>
                    <div class="text-sm font-medium text-center">Problem/<br>Emergency</div>
                    <div class="text-xs text-gray-500 mt-1">Issue or early departure</div>
                  </label>
                </div>
              </div>
            </div>

            <!-- Departure Notes -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-2">Departure Notes</label>
              <textarea name="departure_notes" id="departureNotes"
                        placeholder="Optional notes about the departure..."
                        rows="3"
                        class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500"></textarea>
              <p class="text-xs text-gray-500 mt-1">Any additional information about the departure</p>
            </div>

            <!-- Trailer Left On Site -->
            <div id="trailerLeftSection" class="hidden p-4 bg-orange-50 border border-orange-200 rounded-lg">
              <h4 class="font-medium text-orange-800 mb-3">📦 Trailer Left On Site</h4>
              
              <div class="grid grid-cols-1 gap-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Where is the trailer located on site?</label>
                  <select id="dropped_trailer_location" name="dropped_trailer_location" required class="w-full border-gray-300 rounded-lg focus:ring-orange-500 focus:border-orange-500">
                    <option value="">– Select Location –</option>
                    {{-- Current Location (will be populated by JavaScript) --}}
                    <optgroup label="🏷️ Current Location">
                      {{-- Populated dynamically --}}
                    </optgroup>
                    {{-- Available Drop Zones --}}
                    <optgroup label="📦 Drop Zones">
                      @foreach($tippingLocations as $location)
                        <option value="DROP_{{ $location->id }}" data-type="location" data-name="{{ $location->name }}">
                          {{ $location->name }} ({{ $location->getAvailableCapacity() }}/{{ $location->capacity }} available)
                        </option>
                      @endforeach
                    </optgroup>
                    {{-- Available Tipping Bays --}}
                    <optgroup label="🏗️ Tipping Bays">
                      @foreach($tippingBays as $bay)
                        <option value="BAY_{{ $bay->id }}" data-type="bay" data-name="{{ $bay->name }}"
                                @if($bay->is_occupied) disabled @endif>
                          {{ $bay->name }} 
                          @if($bay->is_occupied) (Occupied) @else (Available) @endif
                        </option>
                      @endforeach
                    </optgroup>
                  </select>
                  <p class="text-xs text-gray-500 mt-1">
                    Default: Current location. Select a different location if trailer needs to be moved.
                  </p>
                  <div id="currentLocationInfo" class="mt-2 p-2 bg-blue-50 border border-blue-200 rounded text-sm">
                    {{-- Will be populated by JavaScript with current location info --}}
                  </div>
                  
                  <div id="tippingWarning" class="hidden mt-2 p-3 bg-yellow-50 border border-yellow-200 rounded text-sm">
                    <div class="text-yellow-800">
                      <strong>⚠️ Tipping In Progress:</strong><br>
                      <span class="text-yellow-700">• Trailer can remain at current bay until tipping completes</span><br>
                      <span class="text-yellow-700">• Moving to other bays is restricted during active tipping</span><br>
                      <span class="text-yellow-700">• Drop zones and collection zones are still available</span>
                    </div>
                  </div>
                </div>
              </div>
              
              <!-- Automatic Status Information -->
              <div class="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <h5 class="font-medium text-blue-800 mb-2">ℹ️ Automatic Status</h5>
                <ul class="text-sm text-blue-700 space-y-1">
                  <li>• Status will be set automatically based on tipping completion</li>
                  <li>• Collection scheduling managed by system workflow</li>
                  <li>• No driver communication required</li>
                </ul>
              </div>
            </div>
            
          </div>

          <!-- Departure Time Display -->
          <div class="mt-4 p-4 bg-green-50 rounded-lg border border-green-200">
            <h4 class="font-medium text-green-800 mb-2">📅 Departure Time:</h4>
            <p class="text-green-700 font-semibold" id="departureTime">Will be recorded as: [Current Time]</p>
          </div>

          <!-- Form Actions -->
          <div class="mt-6 flex justify-end space-x-4">
            <button type="button" onclick="closeDepartureModal()" 
                    class="px-6 py-3 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
              Cancel
            </button>
            <button type="submit" 
                    class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold">
              🏁 Mark Vehicle Departed
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Bay Assignment Modal -->
  <div id="bayAssignmentModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <h3 class="text-lg font-bold text-gray-900 mb-4">🏗️ Assign Bay from Waiting Area</h3>
        
        <form id="bayAssignmentForm" method="POST">
          @csrf
          
          <!-- Booking Info -->
          <div class="mb-4 p-3 bg-blue-50 rounded-lg">
            <p class="text-sm text-blue-800">
              <strong>Booking:</strong> <span id="bayBookingRef"></span><br>
              <strong>Currently in:</strong> <span id="bayCurrentLocation"></span>
            </p>
          </div>
          
          <!-- Bay Selection -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Select Tipping Bay <span class="text-red-500">*</span>
            </label>
            <select name="tipping_bay_id" required class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
              <option value="">– Select Available Bay –</option>
              @if(isset($tippingBays))
                @foreach($tippingBays as $bay)
                  <option value="{{ $bay->id }}" @disabled($bay->is_occupied)>
                    {{ $bay->name }} ({{ $bay->depot->name }}) 
                    @if($bay->is_occupied)
                      - Occupied
                    @else
                      - Available
                    @endif
                  </option>
                @endforeach
              @endif
            </select>
          </div>
          
          <!-- Assignment Notes -->
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Assignment Notes (Optional)</label>
            <textarea name="assignment_notes" rows="2" 
                      placeholder="e.g., Priority load, special handling required..."
                      class="w-full border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"></textarea>
          </div>
          
          <!-- Time Display -->
          <div class="mb-4 p-3 bg-green-50 rounded-lg border border-green-200">
            <p class="text-sm text-green-800">
              <strong>Assignment Time:</strong> <span id="bayAssignmentTime">Will be recorded as current time</span>
            </p>
          </div>
          
          <!-- Form Actions -->
          <div class="flex justify-end space-x-3">
            <button type="button" onclick="closeBayAssignmentModal()" 
                    class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
              Cancel
            </button>
            <button type="submit" 
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
              🏗️ Assign Bay
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Quick Trailer Collection Modal -->
  <div id="quickTrailerCollectionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
      <div class="mt-3">
        <h3 class="text-lg font-bold text-gray-900 mb-4">🚛 Quick Trailer Collection</h3>
        <p class="text-sm text-gray-600 mb-4">Record a vehicle arriving to collect a trailer (no booking required)</p>
        
        <form action="{{ route('app.empty-unit-collection.process') }}" method="POST">
          @csrf
          
          <div class="space-y-3">
            <!-- Vehicle Registration -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Vehicle Registration <span class="text-red-500">*</span>
              </label>
              <input type="text" name="vehicle_registration" required
                     placeholder="e.g., AB12 CDE"
                     class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
            </div>
            
            
            <!-- Available Trailers Dropdown -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Select Trailer to Collect <span class="text-red-500">*</span>
              </label>
              <select name="collected_from_booking_id" id="availableTrailerSelect" required
                      onchange="updateTrailerDetails(this)"
                      class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                <option value="">– Choose Trailer to Collect –</option>
                <!-- Options will be loaded dynamically -->
              </select>
              <p class="text-xs text-gray-500 mt-1">Shows trailer number and current location on site</p>
            </div>
            
            <!-- Trailer Location Display (read-only) -->
            <div id="trailerLocationDisplay" class="hidden p-3 bg-blue-50 border border-blue-200 rounded-lg">
              <h4 class="font-medium text-blue-800 mb-2">📍 Trailer Location</h4>
              <div class="text-sm">
                <div><strong>Trailer:</strong> <span id="displayTrailerNumber"></span></div>
                <div><strong>Current Location:</strong> <span id="displayTrailerLocation"></span></div>
                <div class="text-blue-700 mt-1">
                  <strong>→ Direct driver to this location</strong>
                </div>
              </div>
            </div>
            
            <!-- Company -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Company</label>
              <input type="text" name="carrier_company"
                     placeholder="e.g., ABC Transport"
                     class="w-full border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
            </div>
          </div>
          
          <div class="mt-6 flex justify-end space-x-3">
            <button type="button" onclick="closeQuickTrailerCollectionModal()" 
                    class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
              Cancel
            </button>
            <button type="submit" 
                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
              🚛 Record Collection
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    let currentBookingId = null;

    function openArrivalModal(bookingId, bookingRef, customer, depot, scheduledTime, vehicleReg, containerNum, carrierCompany, expectedCases, expectedPallets, specialInstructions) {
      currentBookingId = bookingId;
      
      // Update booking summary
      document.getElementById('bookingSummary').innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
          <div>
            <strong>Booking:</strong> ${bookingRef}<br>
            <strong>Customer:</strong> ${customer}
          </div>
          <div>
            <strong>Depot:</strong> ${depot}<br>
            <strong>Scheduled:</strong> ${scheduledTime}
          </div>
          <div>
            <strong>Expected:</strong> ${expectedCases} cases, ${expectedPallets} pallets
          </div>
        </div>
      `;

      // Update form action
      document.getElementById('arrivalForm').action = `/admin/bookings/${bookingId}/arrival`;

      // Populate form fields
      document.getElementById('vehicleRegistration').value = vehicleReg;
      document.getElementById('containerNumber').value = containerNum;
      // Set carrier name in search field
      document.getElementById('carrierCompany').value = carrierCompany;

      // Show special instructions if any
      if (specialInstructions && specialInstructions.trim() !== '') {
        document.getElementById('specialInstructionsText').textContent = specialInstructions;
        document.getElementById('specialInstructions').classList.remove('hidden');
      } else {
        document.getElementById('specialInstructions').classList.add('hidden');
      }

      // Update arrival time display
      const now = new Date();
      const timeString = now.toLocaleString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
      });
      document.getElementById('arrivalTime').textContent = `Will be recorded as: ${timeString}`;

      // Show modal
      document.getElementById('arrivalModal').classList.remove('hidden');
      
      // Focus on vehicle registration field
      setTimeout(() => {
        document.getElementById('vehicleRegistration').focus();
      }, 100);
    }

    function closeArrivalModal() {
      document.getElementById('arrivalModal').classList.add('hidden');
      currentBookingId = null;
    }

    // Close modal when clicking outside
    document.getElementById('arrivalModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeArrivalModal();
      }
    });

    // Update time display every second
    setInterval(() => {
      if (!document.getElementById('arrivalModal').classList.contains('hidden')) {
        const now = new Date();
        const timeString = now.toLocaleString('en-GB', {
          day: '2-digit',
          month: 'short',
          year: 'numeric',
          hour: '2-digit',
          minute: '2-digit',
          second: '2-digit'
        });
        document.getElementById('arrivalTime').textContent = `Will be recorded as: ${timeString}`;
      }
    }, 1000);

    // Carrier search functionality for arrival modal
    const searchInput = document.getElementById('carrierCompany');
    const carrierIdInput = document.getElementById('carrierId');
    const dropdown = document.getElementById('carrierDropdown');
    const statusSpan = document.getElementById('carrierStatus');
    
    if (searchInput) {
        let searchTimeout;
        let selectedCarrierId = '';
        let currentPage = 1;
        let isLoading = false;
        
        // Update status based on current state
        function updateCarrierStatus() {
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
        function searchCarriers(query, page = 1) {
            if (query.length < 2) {
                dropdown.classList.add('hidden');
                return;
            }
            
            if (isLoading) return;
            isLoading = true;
            
            fetch(`{{ route('api.carriers.search') }}?q=${encodeURIComponent(query)}&page=${page}`)
                .then(response => response.json())
                .then(data => {
                    if (page === 1) {
                        populateCarrierDropdown(data, query);
                    } else {
                        appendToCarrierDropdown(data, query);
                    }
                    currentPage = page;
                    isLoading = false;
                })
                .catch(error => {
                    console.error('Search failed:', error);
                    dropdown.classList.add('hidden');
                    isLoading = false;
                });
        }
        
        // Populate dropdown with results
        function populateCarrierDropdown(data, query) {
            dropdown.innerHTML = '';
            
            // Show existing carriers
            data.carriers.forEach(carrier => {
                const item = document.createElement('div');
                item.className = 'px-3 py-2 hover:bg-blue-50 cursor-pointer border-b border-gray-100';
                item.innerHTML = `
                    <div class="font-medium text-gray-900">${carrier.name}</div>
                    <div class="text-xs text-gray-500">
                        ${carrier.is_active ? 'Active carrier' : 'Inactive carrier - will be reactivated'}
                    </div>
                `;
                item.onclick = () => selectCarrier(carrier.id, carrier.name);
                dropdown.appendChild(item);
            });
            
            // Add "Create new" option if no exact match
            if (!data.exact_match && query.trim()) {
                const createItem = document.createElement('div');
                createItem.className = 'px-3 py-2 hover:bg-green-50 cursor-pointer border-t-2 border-green-200 bg-green-25';
                createItem.innerHTML = `
                    <div class="font-medium text-green-800">➕ Create "${query}"</div>
                    <div class="text-xs text-green-600">Add as new carrier and use immediately</div>
                `;
                createItem.onclick = () => createNewCarrier(query);
                dropdown.appendChild(createItem);
            }
            
            dropdown.classList.remove('hidden');
        }
        
        // Select existing carrier
        function selectCarrier(id, name) {
            selectedCarrierId = id;
            carrierIdInput.value = id;
            searchInput.value = name;
            dropdown.classList.add('hidden');
            updateCarrierStatus();
        }
        
        // Create new carrier (simple version for arrival modal)
        function createNewCarrier(name) {
            selectedCarrierId = '';
            carrierIdInput.value = '';
            searchInput.value = name;
            dropdown.classList.add('hidden');
            updateCarrierStatus();
        }
        
        // Search input handler
        searchInput.addEventListener('input', function() {
            const query = this.value.trim();
            
            // Reset selection when typing
            selectedCarrierId = '';
            carrierIdInput.value = '';
            currentPage = 1;
            
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchCarriers(query, 1);
            }, 300);
            
            updateCarrierStatus();
        });
        
        // Hide dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
                dropdown.classList.add('hidden');
            }
        });
        
        // Show dropdown on focus if there's content
        searchInput.addEventListener('focus', function() {
            if (this.value.length >= 2) {
                searchCarriers(this.value);
            }
        });
        
        // Initial status update
        updateCarrierStatus();
    }

    function validateArrivalForm() {
      const vehicleReg = document.getElementById('vehicleRegistration').value.trim();
      const carrierCompany = document.getElementById('carrierCompany').value;
      const tippingLocation = document.getElementById('tippingLocation').value;
      const tippingBay = document.getElementById('tippingBay').value;

      // Check required fields
      if (!vehicleReg) {
        alert('Vehicle Registration is required');
        document.getElementById('vehicleRegistration').focus();
        return false;
      }

      if (!carrierCompany) {
        alert('Carrier Company is required');
        document.getElementById('carrierCompany').focus();
        return false;
      }

      // Check tipping assignment (must select one)
      if (!tippingLocation && !tippingBay) {
        alert('You must select either a Drop Location or assign directly to a Tipping Bay');
        return false;
      }

      if (tippingLocation && tippingBay) {
        alert('Please select either Drop Location OR Tipping Bay, not both');
        return false;
      }

      console.log('Form validation passed, submitting...');
      return true;
    }

    // Departure Modal Functions
    let currentDepartureBookingId = null;

    function openDepartureModal(bookingId, bookingRef, customer, vehicleReg, currentLocation, currentLocationName, tippingStatus = null) {
      currentDepartureBookingId = bookingId;
      
      // Update departure summary
      document.getElementById('departureSummary').innerHTML = `
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
          <div>
            <strong>Booking:</strong> ${bookingRef}<br>
            <strong>Customer:</strong> ${customer}
          </div>
          <div>
            <strong>Vehicle:</strong> ${vehicleReg}<br>
            <strong>Departing:</strong> Now
          </div>
        </div>
      `;
      
      // Determine if tipping is in progress
      const isTippingInProgress = tippingStatus === 'unloading' || currentLocationName.includes('(Tipping)');
      const isAtBay = currentLocation && currentLocation.startsWith('BAY_');
      
      // Populate current location in the trailer dropdown
      const locationSelect = document.getElementById('dropped_trailer_location');
      const currentLocationInfo = document.getElementById('currentLocationInfo');
      const tippingWarning = document.getElementById('tippingWarning');
      const currentOptgroup = locationSelect.querySelector('optgroup[label="🏷️ Current Location"]');
      
      // Clear existing current location options
      currentOptgroup.innerHTML = '';
      
      // Apply smart restrictions for bay movements during tipping
      if (isTippingInProgress && isAtBay) {
        // Show tipping warning
        tippingWarning.classList.remove('hidden');
        
        // LOCK the dropdown entirely during active tipping
        locationSelect.disabled = true;
        locationSelect.style.backgroundColor = '#f3f4f6'; // Gray background
        locationSelect.style.cursor = 'not-allowed';
        
        // Hide all other option groups during tipping
        const allOptgroups = locationSelect.querySelectorAll('optgroup');
        allOptgroups.forEach(optgroup => {
          if (optgroup.label !== '🏷️ Current Location') {
            optgroup.style.display = 'none';
          }
        });
        
        // Update the help text
        const helpText = locationSelect.parentElement.querySelector('.text-xs.text-gray-500');
        if (helpText) {
          helpText.innerHTML = '<strong class="text-orange-600">🔒 Location locked during active tipping</strong> - Trailer must remain at current bay';
        }
      } else {
        // Unlock dropdown for normal operations
        tippingWarning.classList.add('hidden');
        locationSelect.disabled = false;
        locationSelect.style.backgroundColor = '';
        locationSelect.style.cursor = '';
        
        // Show all option groups again
        const allOptgroups = locationSelect.querySelectorAll('optgroup');
        allOptgroups.forEach(optgroup => {
          optgroup.style.display = '';
        });
        
        // Restore original help text
        const helpText = locationSelect.parentElement.querySelector('.text-xs.text-gray-500');
        if (helpText) {
          helpText.innerHTML = 'Default: Current location. Select a different location if trailer needs to be moved.';
        }
      }
      
      if (currentLocation && currentLocation !== 'null' && currentLocation !== '') {
        // Add current location as the first option and select it
        const currentOption = document.createElement('option');
        currentOption.value = currentLocation;
        currentOption.textContent = `${currentLocationName} (Current Location)`;
        currentOption.selected = true;
        currentOptgroup.appendChild(currentOption);
        
        // Update info display based on context
        let infoMessage = '';
        let infoClass = 'mt-2 p-3 bg-blue-50 border border-blue-200 rounded text-sm';
        
        if (isTippingInProgress && isAtBay) {
          infoMessage = `
            <strong>📍 Current Location:</strong> ${currentLocationName}<br>
            <span class="text-blue-700">✅ Default: Trailer will remain at bay during tipping</span><br>
            <span class="text-orange-700">⚠️ Other bay movements restricted until tipping completes</span>
          `;
        } else {
          infoMessage = `
            <strong>📍 Current Location:</strong> ${currentLocationName}<br>
            <span class="text-blue-700">✅ Default: Trailer will remain here unless you select a different location below</span>
          `;
        }
        
        currentLocationInfo.innerHTML = infoMessage;
        currentLocationInfo.style.display = 'block';
        currentLocationInfo.className = infoClass;
      } else {
        // No current location detected - vehicle might be in general area or not properly assigned
        if (currentLocation === 'ONSITE_GENERAL') {
          currentLocationInfo.innerHTML = `
            <strong>📍 Current Location:</strong> ${currentLocationName}<br>
            <span class="text-orange-700">⚠️ Vehicle is on site but not assigned to specific drop zone - please select location for trailer</span>
          `;
          currentLocationInfo.className = 'mt-2 p-3 bg-orange-50 border border-orange-200 rounded text-sm';
        } else {
          currentLocationInfo.innerHTML = `
            <strong>⚠️ Current Location Unknown:</strong> Vehicle location not detected<br>
            <span class="text-red-700">Please select where the trailer should be located for collection</span>
          `;
          currentLocationInfo.className = 'mt-2 p-3 bg-red-50 border border-red-200 rounded text-sm';
        }
        currentLocationInfo.style.display = 'block';
        locationSelect.selectedIndex = 0; // Reset to "Select Location"
      }

      // Update form action
      document.getElementById('departureForm').action = `/admin/bookings/${bookingId}/departure`;

      // Update departure time display
      updateDepartureTime();

      // Show modal
      document.getElementById('departureModal').classList.remove('hidden');
      
      // Focus on departure scenario dropdown
      setTimeout(() => {
        document.getElementById('departureScenario').focus();
      }, 100);
    }

    function closeDepartureModal() {
      document.getElementById('departureModal').classList.add('hidden');
      currentDepartureBookingId = null;
      
      // Reset form
      document.getElementById('departureForm').reset();
      document.getElementById('trailerLeftSection').classList.add('hidden');
    }

    function updateDepartureTime() {
      const now = new Date();
      const timeString = now.toLocaleString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
      });
      document.getElementById('departureTime').textContent = `Will be recorded as: ${timeString}`;
    }

    // Handle departure scenario change
    document.addEventListener('DOMContentLoaded', function() {
      const departureRadios = document.querySelectorAll('input[name="departure_scenario"]');
      departureRadios.forEach(radio => {
        radio.addEventListener('change', function() {
          const trailerSection = document.getElementById('trailerLeftSection');
          
          // Hide section initially
          trailerSection.classList.add('hidden');
          
          // Show trailer section only when vehicle leaves without trailer
          if (this.value === 'completed_dropped_trailer') {
            trailerSection.classList.remove('hidden');
          }
        });
      });

      // Close departure modal when clicking outside
      document.getElementById('departureModal').addEventListener('click', function(e) {
        if (e.target === this) {
          closeDepartureModal();
        }
      });

      // Update departure time every second
      setInterval(() => {
        if (!document.getElementById('departureModal').classList.contains('hidden')) {
          updateDepartureTime();
        }
      }, 1000);
    });
    
    // Bay Assignment Modal Functions
    function openBayAssignmentModal(bookingId, bookingRef, waitingArea) {
      document.getElementById('bayBookingRef').textContent = bookingRef;
      document.getElementById('bayCurrentLocation').textContent = waitingArea;
      
      // Set form action
      const form = document.getElementById('bayAssignmentForm');
      form.action = `/admin/bookings/${bookingId}/assign-bay`;
      
      // Update time display
      updateBayAssignmentTime();
      
      // Show modal
      document.getElementById('bayAssignmentModal').classList.remove('hidden');
    }

    function closeBayAssignmentModal() {
      document.getElementById('bayAssignmentModal').classList.add('hidden');
    }
    
    function updateBayAssignmentTime() {
      const now = new Date();
      const timeString = now.toLocaleString('en-GB', {
        day: '2-digit',
        month: 'short', 
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
      });
      document.getElementById('bayAssignmentTime').textContent = `Will be recorded as: ${timeString}`;
    }
    
    // Close bay assignment modal when clicking outside
    document.getElementById('bayAssignmentModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeBayAssignmentModal();
      }
    });
    
    // Quick Trailer Collection Modal Functions
    function openQuickTrailerCollectionModal() {
      document.getElementById('quickTrailerCollectionModal').classList.remove('hidden');
      loadAvailableTrailers();
    }

    function closeQuickTrailerCollectionModal() {
      document.getElementById('quickTrailerCollectionModal').classList.add('hidden');
      // Reset form
      document.getElementById('availableTrailerSelect').value = '';
      document.getElementById('trailerLocationDisplay').classList.add('hidden');
      
      // Clear hidden fields if they exist
      const trailerNumberInput = document.getElementById('hiddenTrailerNumber');
      const locationInput = document.getElementById('hiddenLocation');
      if (trailerNumberInput) trailerNumberInput.remove();
      if (locationInput) locationInput.remove();
    }
    
    // Load available trailers from server
    async function loadAvailableTrailers() {
      try {
        const response = await fetch('/admin/api/available-trailers');
        const trailers = await response.json();
        
        const select = document.getElementById('availableTrailerSelect');
        // Clear existing options except the first one
        select.innerHTML = '<option value="">– Choose Trailer to Collect –</option>';
        
        trailers.forEach(trailer => {
          const option = document.createElement('option');
          option.value = trailer.id;
          option.textContent = `🚛 ${trailer.container_number || 'No Container#'} → 📍 ${trailer.dropped_trailer_location || 'Unknown Location'}`;
          option.dataset.containerNumber = trailer.container_number || 'No container number';
          option.dataset.location = trailer.dropped_trailer_location || 'Unknown location';
          option.dataset.bookingRef = trailer.booking_reference || '';
          select.appendChild(option);
        });
        
        if (trailers.length === 0) {
          const option = document.createElement('option');
          option.value = '';
          option.textContent = '– No trailers available for collection –';
          option.disabled = true;
          select.appendChild(option);
        }
      } catch (error) {
        console.error('Failed to load available trailers:', error);
        const select = document.getElementById('availableTrailerSelect');
        select.innerHTML = '<option value="">– Error loading trailers –</option>';
      }
    }
    
    // Update trailer details when selection changes
    function updateTrailerDetails(selectElement) {
      const selectedOption = selectElement.options[selectElement.selectedIndex];
      const locationDisplay = document.getElementById('trailerLocationDisplay');
      const displayTrailerNumber = document.getElementById('displayTrailerNumber');
      const displayTrailerLocation = document.getElementById('displayTrailerLocation');
      
      if (selectedOption.value) {
        // Show the location display
        locationDisplay.classList.remove('hidden');
        displayTrailerNumber.textContent = selectedOption.dataset.containerNumber;
        displayTrailerLocation.textContent = selectedOption.dataset.location;
        
        // Set hidden fields for form submission
        // Create hidden inputs if they don't exist
        let trailerNumberInput = document.getElementById('hiddenTrailerNumber');
        let locationInput = document.getElementById('hiddenLocation');
        
        if (!trailerNumberInput) {
          trailerNumberInput = document.createElement('input');
          trailerNumberInput.type = 'hidden';
          trailerNumberInput.name = 'collected_trailer_number';
          trailerNumberInput.id = 'hiddenTrailerNumber';
          selectElement.form.appendChild(trailerNumberInput);
        }
        
        if (!locationInput) {
          locationInput = document.createElement('input');
          locationInput.type = 'hidden';
          locationInput.name = 'collection_location';
          locationInput.id = 'hiddenLocation';
          selectElement.form.appendChild(locationInput);
        }
        
        trailerNumberInput.value = selectedOption.dataset.containerNumber;
        locationInput.value = selectedOption.dataset.location;
      } else {
        // Hide the location display
        locationDisplay.classList.add('hidden');
        
        // Clear hidden fields
        const trailerNumberInput = document.getElementById('hiddenTrailerNumber');
        const locationInput = document.getElementById('hiddenLocation');
        if (trailerNumberInput) trailerNumberInput.value = '';
        if (locationInput) locationInput.value = '';
      }
    }
    
    // Close quick collection modal when clicking outside
    document.getElementById('quickTrailerCollectionModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeQuickTrailerCollectionModal();
      }
    });

    // Trailer Collection Modal Functions
    function openTrailerCollectionModal() {
      // Open the empty unit collection page in a new window/tab
      window.open('{{ route("app.empty-unit-collection") }}', '_blank', 'width=1200,height=800,scrollbars=yes,resizable=yes');
    }

    // Advanced Filters Toggle
    function toggleAdvancedFilters() {
      const content = document.getElementById('advanced-filters-content');
      const icon = document.getElementById('advanced-filters-icon');
      
      if (content.classList.contains('hidden')) {
        content.classList.remove('hidden');
        icon.style.transform = 'rotate(180deg)';
      } else {
        content.classList.add('hidden');
        icon.style.transform = 'rotate(0deg)';
      }
    }

    // Show advanced filters if any are active
    document.addEventListener('DOMContentLoaded', function() {
      const hasActiveFilters = {{ request()->hasAny(['depot_id', 'customer_id', 'booking_type_id', 'week_number', 'from', 'to', 'arrival']) ? 'true' : 'false' }};
      if (hasActiveFilters) {
        toggleAdvancedFilters();
      }
    });
  </script>
</x-app-layout>
