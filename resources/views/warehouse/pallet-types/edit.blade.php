<x-warehouse-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl">Edit Pallet Type: {{ $palletType->name }}</h2>
      <div class="flex gap-2">
        <a href="{{ route('app.settings.pallet-types.show', $palletType) }}"
           class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
          View Details
        </a>
        <a href="{{ route('app.settings.pallet-types') }}"
           class="px-3 py-1 bg-gray-500 text-white rounded hover:bg-gray-600 text-sm">
          Back to List
        </a>
      </div>
    </div>
  </x-slot>
  <div class="py-6 max-w-2xl mx-auto">
    <div class="bg-white p-6 rounded-lg shadow">
      <form method="POST" action="{{ route('app.settings.pallet-types.update', $palletType) }}">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 gap-6">
          {{-- Name --}}
          <div>
            <label class="block text-sm font-medium text-gray-700">Name *</label>
            <input type="text" name="name" value="{{ old('name', $palletType->name) }}" required
                   class="mt-1 block w-full border-gray-300 rounded-lg">
            @error('name')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
          </div>
          {{-- Code --}}
          <div>
            <label class="block text-sm font-medium text-gray-700">Code *</label>
            <input type="text" name="code" value="{{ old('code', $palletType->code) }}" required
                   maxlength="10" style="text-transform: uppercase;"
                   class="mt-1 block w-full border-gray-300 rounded-lg">
            <p class="text-xs text-gray-500 mt-1">Short code (max 10 characters, will be uppercase)</p>
            @error('code')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
          </div>
          {{-- Description --}}
          <div>
            <label class="block text-sm font-medium text-gray-700">Description</label>
            <textarea name="description" rows="3" 
                      class="mt-1 block w-full border-gray-300 rounded-lg">{{ old('description', $palletType->description) }}</textarea>
            @error('description')<p class="text-red-600 text-sm mt-1">{{ $message }}</p>@enderror
          </div>
          {{-- Active Status --}}
          <div>
            <div class="flex items-center">
              <input type="checkbox" name="is_active" value="1" 
                     {{ old('is_active', $palletType->is_active) ? 'checked' : '' }}
                     class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
              <label class="ml-2 text-sm text-gray-700">Active</label>
            </div>
            <p class="text-xs text-gray-500 mt-1">Inactive pallet types won't appear in selection lists</p>
          </div>
        </div>
        <div class="flex justify-end space-x-3 mt-6">
          <a href="{{ route('app.settings.pallet-types') }}"
             class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
            Cancel
          </a>
          <button type="submit"
                  class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            Update Pallet Type
          </button>
        </div>
      </form>
    </div>
    {{-- Usage Information --}}
    @php
      $expectedCount = $palletType->poLinesExpected()->count();
      $actualCount = $palletType->poLinesActual()->count();
      $totalUsage = $expectedCount + $actualCount;
    @endphp
    @if($totalUsage > 0)
      <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <h3 class="font-medium text-yellow-800 mb-2">⚠️ Usage Warning</h3>
        <p class="text-yellow-700 text-sm">
          This pallet type is currently used in {{ $totalUsage }} PO lines 
          ({{ $expectedCount }} expected, {{ $actualCount }} actual). 
          Changes may affect existing booking data.
        </p>
      </div>
    @endif
  </div>
  <script>
    // Auto-uppercase code field
    document.querySelector('input[name="code"]').addEventListener('input', function(e) {
      e.target.value = e.target.value.toUpperCase();
    });
  </script>
</x-warehouse-layout>