<x-app-layout>
  <x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800">Create Equipment Type</h2>
  </x-slot>

  <div class="py-6 max-w-4xl mx-auto">
    <form method="POST" action="{{ route('app.equipment-types.store') }}">
      @csrf

      <div class="bg-white p-6 rounded shadow space-y-4">
        <div>
          <label class="block text-sm font-medium mb-1">Name <span class="text-red-500">*</span></label>
          <input type="text" name="name" class="w-full border rounded p-2" value="{{ old('name') }}" required>
          <p class="text-xs text-gray-500 mt-1">Display name (e.g., "Ramp", "Dock Leveler")</p>
          @error('name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Key <span class="text-red-500">*</span></label>
          <input type="text" name="key" class="w-full border rounded p-2 font-mono" value="{{ old('key') }}" required pattern="[a-z0-9_]+">
          <p class="text-xs text-gray-500 mt-1">Machine-readable key (lowercase, underscores only: e.g., "ramp", "dock_leveler")</p>
          @error('key')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Description</label>
          <textarea name="description" class="w-full border rounded p-2" rows="3">{{ old('description') }}</textarea>
          <p class="text-xs text-gray-500 mt-1">What this equipment is used for</p>
          @error('description')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="block text-sm font-medium mb-1">Sort Order <span class="text-red-500">*</span></label>
          <input type="number" name="sort_order" class="w-full border rounded p-2" value="{{ old('sort_order', $maxSortOrder + 1) }}" required min="0">
          <p class="text-xs text-gray-500 mt-1">Display order in dropdowns (lower numbers appear first)</p>
          @error('sort_order')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
          <label class="flex items-center">
            <input type="checkbox" name="is_active" value="1" class="mr-2" @checked(old('is_active', true))>
            <span class="text-sm font-medium">Active</span>
          </label>
          <p class="text-xs text-gray-500 mt-1">Inactive equipment types won't appear in dropdowns</p>
        </div>

        <div class="flex justify-end gap-4 pt-4">
          <a href="{{ route('app.equipment-types.index') }}" class="text-sm text-gray-600 hover:underline">Cancel</a>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Create Equipment Type
          </button>
        </div>
      </div>
    </form>
  </div>
</x-app-layout>
