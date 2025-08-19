<x-app-layout>
  @include('layouts.admin-nav')

  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl">Create Pallet Type</h2>
      <a href="{{ route('admin.pallet-types.index') }}"
         class="px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm">
        Back to Pallet Types
      </a>
    </div>
  </x-slot>

  <div class="py-6 max-w-2xl mx-auto">
    <div class="bg-white p-6 rounded-lg shadow">
      <form method="POST" action="{{ route('admin.pallet-types.store') }}">
        @csrf

        <div class="grid grid-cols-1 gap-6">
          {{-- Name --}}
          <div>
            <label class="block text-sm font-medium text-gray-700">Name *</label>
            <input type="text" name="name" value="{{ old('name') }}" required
                   class="mt-1 block w-full border-gray-300 rounded-lg">
            @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
          </div>

          {{-- Code --}}
          <div>
            <label class="block text-sm font-medium text-gray-700">Code *</label>
            <input type="text" name="code" value="{{ old('code') }}" required
                   maxlength="10" style="text-transform: uppercase;"
                   class="mt-1 block w-full border-gray-300 rounded-lg">
            <p class="text-xs text-gray-500 mt-1">Short code (max 10 characters, will be uppercase)</p>
            @error('code')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
          </div>

          {{-- Description --}}
          <div>
            <label class="block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" rows="3" 
                      class="mt-1 block w-full border-gray-300 rounded-lg">{{ old('description') }}</textarea>
            @error('description')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
          </div>

          {{-- Active Status --}}
          <div>
            <div class="flex items-center">
              <input type="checkbox" name="is_active" value="1" 
                     {{ old('is_active', true) ? 'checked' : '' }}
                     class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
              <label class="ml-2 text-sm text-gray-700">Active</label>
            </div>
            <p class="text-xs text-gray-500 mt-1">Inactive pallet types won't appear in selection lists</p>
          </div>
        </div>

        <div class="flex justify-end space-x-3 mt-6">
          <a href="{{ route('admin.pallet-types.index') }}"
             class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
            Cancel
          </a>
          <button type="submit"
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Create Pallet Type
          </button>
        </div>
      </form>
    </div>
  </div>

  <script>
    // Auto-uppercase code field
    document.querySelector('input[name="code"]').addEventListener('input', function(e) {
      e.target.value = e.target.value.toUpperCase();
    });
  </script>
</x-app-layout>