<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="font-semibold text-xl text-gray-800">Equipment Types</h2>
        <p class="text-sm text-gray-600 mt-1">Manage equipment types for bays and booking type requirements</p>
      </div>
      <a href="{{ route('app.equipment-types.create') }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
        ➕ Add Equipment Type
      </a>
    </div>
  </x-slot>

  <div class="py-6 max-w-7xl mx-auto">
    @if(session('success'))
      <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
        {{ session('success') }}
      </div>
    @endif

    <div class="bg-white shadow rounded overflow-hidden">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-2 text-left">Name</th>
            <th class="px-4 py-2 text-left">Key</th>
            <th class="px-4 py-2 text-left">Description</th>
            <th class="px-4 py-2 text-left">Sort Order</th>
            <th class="px-4 py-2 text-left">Status</th>
            <th class="px-4 py-2 text-left">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($equipmentTypes as $type)
            <tr class="border-t hover:bg-gray-50">
              <td class="px-4 py-2 font-medium">{{ $type->name }}</td>
              <td class="px-4 py-2">
                <code class="text-xs bg-gray-100 px-2 py-1 rounded">{{ $type->key }}</code>
              </td>
              <td class="px-4 py-2 text-gray-600">{{ $type->description ?? '—' }}</td>
              <td class="px-4 py-2 text-center">{{ $type->sort_order }}</td>
              <td class="px-4 py-2">
                @if($type->is_active)
                  <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Active</span>
                @else
                  <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">Inactive</span>
                @endif
              </td>
              <td class="px-4 py-2 space-x-2">
                <a href="{{ route('app.equipment-types.edit', $type) }}" class="text-blue-600 hover:text-blue-800 text-xs">Edit</a>
                <form action="{{ route('app.equipment-types.destroy', $type) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure? This will affect bays and booking types using this equipment.');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="text-red-600 hover:text-red-800 text-xs">Delete</button>
                </form>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-4 text-gray-500">No equipment types found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</x-app-layout>
