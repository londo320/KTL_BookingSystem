<x-app-layout>
  @include('layouts.admin-nav')

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
  <form method="GET" action="{{ route('admin.slots.index') }}" class="flex flex-wrap gap-4 items-end mb-4">
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
      <a href="{{ route('admin.slots.index') }}" class="text-sm text-gray-600 ml-2 hover:underline">Clear Filters</a>
    </div>

    <div class="ml-auto">
      @if(request()->has('show_past'))
        <a href="{{ route('admin.slots.index', request()->except('show_past')) }}" class="text-sm text-blue-600 hover:underline">Hide Past Slots</a>
      @else
        <a href="{{ route('admin.slots.index', array_merge(request()->all(), ['show_past' => true])) }}" class="text-sm text-blue-600 hover:underline">Show Past Slots</a>
      @endif
    </div>
  </form>

  {{-- Slots Table --}}
  <div class="overflow-x-auto bg-white shadow rounded">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-100">
        <tr>
          <th class="px-4 py-2 text-left">Depot</th>
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
<td class="px-4 py-2">{{ \Carbon\Carbon::parse($slot->start_at)->format('d-M H:i') }}</td>
<td class="px-4 py-2">{{ \Carbon\Carbon::parse($slot->end_at)->format('d-M H:i') }}</td>
            <td class="px-4 py-2">{{ $slot->capacity }}</td>
            <td class="px-4 py-2">{{ $slot->bookings_count }} / {{ $slot->capacity }}</td>
            <td class="px-4 py-2 space-x-2">
              @php $canTakeAction = $slot->depot_id == $defaultDepotId; @endphp
              @if($canTakeAction)
                <a href="{{ route('admin.slots.edit', $slot) }}" class="px-2 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 text-xs">Edit</a>
                <form action="{{ route('admin.slots.destroy', $slot) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure?');">
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
            <td colspan="6" class="text-center py-4 text-gray-500">No slots found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
    <div class="p-4">
      {{ $slots->appends(request()->query())->links() }}
    </div>
  </div>
</div>
</x-app-layout>
