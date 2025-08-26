<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl">Pallet Types Management</h2>
      <div class="flex gap-2">
        <a href="{{ route('app.settings.pallet-types.create') }}"
           class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
          + Add Pallet Type
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
    @if(session('error'))
      <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
        {{ session('error') }}
      </div>
    @endif
    {{-- Filters --}}
    <form method="GET" class="mb-4 flex flex-wrap gap-4 items-end bg-gray-50 p-4 rounded">
      <div>
        <label class="block text-sm font-medium">Search</label>
        <input type="text" name="search" value="{{ request('search') }}" 
               placeholder="Name, code, or description..."
               class="border rounded px-2 py-1 text-sm w-64">
      </div>
      <div>
        <label class="block text-sm font-medium">Status</label>
        <select name="status" class="border rounded px-2 py-1 text-sm">
          <option value="">All</option>
          <option value="active" @selected(request('status') === 'active')>Active</option>
          <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
        </select>
      </div>
      <div class="flex space-x-2">
        <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">Filter</button>
        <a href="{{ route('app.settings.pallet-types') }}" class="px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm">Clear</a>
      </div>
    </form>
    {{-- Pallet Types Table --}}
    <div class="bg-white shadow rounded overflow-hidden">
      <table class="min-w-full">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-2 text-left">Name</th>
            <th class="px-4 py-2 text-left">Code</th>
            <th class="px-4 py-2 text-left">Description</th>
            <th class="px-4 py-2 text-left">Status</th>
            <th class="px-4 py-2 text-left">Usage</th>
            <th class="px-4 py-2 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($palletTypes as $palletType)
            <tr class="border-t hover:bg-gray-50 {{ !$palletType->is_active ? 'opacity-60' : '' }}">
              <td class="px-4 py-2">
                <div class="font-medium">{{ $palletType->name }}</div>
              </td>
              <td class="px-4 py-2">
                <span class="font-mono bg-gray-100 px-2 py-1 rounded text-sm">{{ $palletType->code }}</span>
              </td>
              <td class="px-4 py-2">
                <div class="text-sm text-gray-600">{{ $palletType->description ?: 'No description' }}</div>
              </td>
              <td class="px-4 py-2">
                @if($palletType->is_active)
                  <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full">Active</span>
                @else
                  <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">Inactive</span>
                @endif
              </td>
              <td class="px-4 py-2">
                @php
                  $expectedCount = $palletType->poLinesExpected()->count();
                  $actualCount = $palletType->poLinesActual()->count();
                  $totalUsage = $expectedCount + $actualCount;
                @endphp
                @if($totalUsage > 0)
                  <div class="text-sm">
                    <div>{{ $totalUsage }} PO lines</div>
                    <div class="text-xs text-gray-500">
                      {{ $expectedCount }} expected, {{ $actualCount }} actual
                    </div>
                  </div>
                @else
                  <span class="text-gray-400 text-sm">Not used</span>
                @endif
              </td>
              <td class="px-4 py-2">
                <div class="flex items-center space-x-2">
                  <a href="{{ route('app.settings.pallet-types.show', $palletType) }}"
                     class="text-blue-600 hover:text-blue-800 text-sm">View</a>
                  <a href="{{ route('app.settings.pallet-types.edit', $palletType) }}"
                     class="text-green-600 hover:text-green-800 text-sm">Edit</a>
                  <form method="POST" action="{{ route('app.settings.pallet-types.toggle-active', $palletType) }}" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" 
                            class="text-yellow-600 hover:text-yellow-800 text-sm">
                      {{ $palletType->is_active ? 'Deactivate' : 'Activate' }}
                    </button>
                  </form>
                  @if($totalUsage === 0)
                    <form method="POST" action="{{ route('app.settings.pallet-types.destroy', $palletType) }}" 
                          class="inline" onsubmit="return confirm('Are you sure you want to delete this pallet type?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Delete</button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                No pallet types found.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    {{-- Pagination --}}
    @if($palletTypes->hasPages())
      <div class="mt-4">
        {{ $palletTypes->appends(request()->query())->links() }}
      </div>
    @endif
  </div>
</x-app-layout>