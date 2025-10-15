<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="font-semibold text-xl text-gray-800">Time Slots Management</h2>
        <p class="text-sm text-gray-600 mt-1">Manage available booking time slots across depots</p>
      </div>
      <div class="text-sm">
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
  </x-slot>
<div class="py-6 max-w-7xl mx-auto">
  {{-- Success Message --}}
  @if(session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
      {{ session('success') }}
    </div>
  @endif
  {{-- Filters --}}
  <form method="GET" action="{{ route('app.slots.index') }}" class="flex flex-wrap gap-4 items-end mb-4">
    <div>
      <label for="depot" class="block text-sm font-medium">View</label>
      <select name="depot_id" id="depot" class="border rounded px-2 py-1">
        <option value="" {{ !$currentDepotId ? 'selected' : '' }}>All Depots (View Only)</option>
        @foreach($allDepots as $depot)
          <option value="{{ $depot->id }}" {{ $currentDepotId == $depot->id ? 'selected' : '' }}>
            {{ $depot->name }} {{ $depot->id == $defaultDepotId ? '(Default - Actions Enabled)' : '(View Only)' }}
          </option>
        @endforeach
      </select>
    </div>
    <div>
      <label for="date" class="block text-sm font-medium">Date</label>
      <input type="date" name="date" id="date" class="border rounded px-2 py-1" value="{{ request('date') }}">
    </div>
    <div>
      <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">Filter</button>
      <a href="{{ route('app.slots.index') }}" class="text-sm text-gray-600 ml-2 hover:underline">Clear Filters</a>
    </div>
    <div class="ml-auto flex gap-2">
      @if(request()->has('show_past'))
        <a href="{{ route('app.slots.index', request()->except('show_past')) }}" class="text-sm text-blue-600 hover:underline">Hide Past Slots</a>
      @else
        <a href="{{ route('app.slots.index', array_merge(request()->all(), ['show_past' => true])) }}" class="text-sm text-blue-600 hover:underline">Show Past Slots</a>
      @endif
      <span class="text-gray-300">|</span>
      @if($groupedView)
        <a href="{{ route('app.slots.index', request()->except('grouped')) }}" class="text-sm text-blue-600 hover:underline">📋 Detailed View</a>
      @else
        <a href="{{ route('app.slots.index', array_merge(request()->all(), ['grouped' => true])) }}" class="text-sm text-blue-600 hover:underline">📊 Grouped View</a>
      @endif
    </div>
  </form>

  @if($groupedView && $groupedSlots)
    {{-- Grouped View --}}
    <div class="overflow-x-auto bg-white shadow rounded">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-2 text-left">Depot</th>
            <th class="px-4 py-2 text-left">Date & Time</th>
            <th class="px-4 py-2 text-left">Total Capacity</th>
            <th class="px-4 py-2 text-left">Used / Available</th>
            <th class="px-4 py-2 text-left">Details</th>
          </tr>
        </thead>
        <tbody>
          @forelse($groupedSlots as $group)
            <tr class="border-t hover:bg-gray-50">
              <td class="px-4 py-2">{{ $group['depot_name'] }}</td>
              <td class="px-4 py-2">
                <div class="font-medium">{{ \Carbon\Carbon::parse($group['start_at'])->format('D d-M-Y') }}</div>
                <div class="text-xs text-gray-600">{{ $group['time'] }} - {{ \Carbon\Carbon::parse($group['end_at'])->format('H:i') }}</div>
              </td>
              <td class="px-4 py-2">
                <span class="text-lg font-semibold">{{ $group['total_capacity'] }}</span>
                <span class="text-xs text-gray-500">bays</span>
              </td>
              <td class="px-4 py-2">
                @php
                  $available = $group['total_capacity'] - $group['total_used'];
                  $percentage = $group['total_capacity'] > 0 ? ($group['total_used'] / $group['total_capacity']) * 100 : 0;
                @endphp
                <div class="flex items-center gap-2">
                  <span class="font-medium {{ $available > 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $group['total_used'] }} / {{ $group['total_capacity'] }}
                  </span>
                  <div class="flex-1 h-2 bg-gray-200 rounded overflow-hidden max-w-[100px]">
                    <div class="h-full {{ $percentage >= 100 ? 'bg-red-500' : ($percentage >= 75 ? 'bg-yellow-500' : 'bg-green-500') }}"
                         style="width: {{ min($percentage, 100) }}%"></div>
                  </div>
                </div>
                <div class="text-xs text-gray-500 mt-1">
                  {{ $available }} available
                </div>
              </td>
              <td class="px-4 py-2">
                <button onclick="toggleDetails({{ $loop->index }})" class="text-blue-600 hover:underline text-xs">
                  <span id="details-btn-{{ $loop->index }}">👁️ View Bays</span>
                </button>
              </td>
            </tr>
            <tr id="details-{{ $loop->index }}" class="hidden bg-gray-50 border-t">
              <td colspan="5" class="px-4 py-3">
                <div class="text-sm font-medium text-gray-700 mb-2">Bay Breakdown:</div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                  @foreach($group['bays'] as $bay)
                    <div class="bg-white border border-gray-200 rounded p-2">
                      <div class="font-medium text-gray-900">{{ $bay['name'] }}</div>
                      <div class="text-xs text-gray-600">
                        {{ $bay['used'] }} / {{ $bay['capacity'] }} used
                        @if($bay['capacity'] - $bay['used'] > 0)
                          <span class="text-green-600">({{ $bay['capacity'] - $bay['used'] }} free)</span>
                        @else
                          <span class="text-red-600">(full)</span>
                        @endif
                      </div>
                    </div>
                  @endforeach
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="text-center py-4 text-gray-500">No slots found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <script>
    function toggleDetails(index) {
      const detailsRow = document.getElementById(`details-${index}`);
      const btn = document.getElementById(`details-btn-${index}`);
      if (detailsRow.classList.contains('hidden')) {
        detailsRow.classList.remove('hidden');
        btn.textContent = '👁️ Hide Bays';
      } else {
        detailsRow.classList.add('hidden');
        btn.textContent = '👁️ View Bays';
      }
    }
    </script>

  @else
    {{-- Detailed View (Original) --}}
    <div class="overflow-x-auto bg-white shadow rounded">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-2 text-left">Depot</th>
            <th class="px-4 py-2 text-left">Bay</th>
            <th class="px-4 py-2 text-left">Start</th>
            <th class="px-4 py-2 text-left">End</th>
            <th class="px-4 py-2 text-left">Capacity</th>
            <th class="px-4 py-2 text-left">Usage</th>
            <th class="px-4 py-2 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($slots as $slot)
            <tr class="border-t hover:bg-gray-50">
              <td class="px-4 py-2">{{ $slot->depot->name }}</td>
              <td class="px-4 py-2">
                @if($slot->tippingBay)
                  <span class="font-medium">{{ $slot->tippingBay->name }}</span>
                  @if($slot->tippingBay->code)
                    <span class="text-xs text-gray-500">({{ $slot->tippingBay->code }})</span>
                  @endif
                @else
                  <span class="text-gray-400">No Bay</span>
                @endif
              </td>
              <td class="px-4 py-2">{{ \Carbon\Carbon::parse($slot->start_at)->format('d-M H:i') }}</td>
              <td class="px-4 py-2">{{ \Carbon\Carbon::parse($slot->end_at)->format('d-M H:i') }}</td>
              <td class="px-4 py-2">{{ $slot->capacity }}</td>
              <td class="px-4 py-2">
                @php
                  $available = $slot->capacity - $slot->occupying_bookings_count;
                @endphp
                <span class="{{ $available > 0 ? 'text-green-600' : 'text-red-600' }} font-medium">
                  {{ $slot->occupying_bookings_count }} / {{ $slot->capacity }}
                </span>
              </td>
              <td class="px-4 py-2 space-x-2">
                @php $canTakeAction = $slot->depot_id == $defaultDepotId; @endphp
                @if($canTakeAction)
                  <a href="{{ route('app.slots.edit', $slot) }}" class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-xs">Edit</a>
                  <form action="{{ route('app.slots.destroy', $slot) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-2 py-1 bg-red-600 text-white rounded hover:bg-red-700 text-xs">Delete</button>
                  </form>
                @else
                  <span class="px-2 py-1 bg-gray-300 text-gray-500 rounded text-xs cursor-not-allowed"
                        title="Actions only available for your default depot">Edit</span>
                  <span class="px-2 py-1 bg-gray-300 text-gray-500 rounded text-xs cursor-not-allowed"
                        title="Actions only available for your default depot">Delete</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center py-4 text-gray-500">No slots found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  @endif
</div>
</x-app-layout>
