<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl">Pallet Type: {{ $palletType->name }}</h2>
      <div class="flex gap-2">
        <a href="{{ route('app.settings.pallet-types.edit', $palletType) }}"
           class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
          Edit
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
      <div class="grid grid-cols-1 gap-6">
        {{-- Name --}}
        <div>
          <label class="block text-sm font-medium text-gray-700">Name</label>
          <div class="mt-1 p-3 bg-gray-50 border border-gray-300 rounded-lg">
            {{ $palletType->name }}
          </div>
        </div>
        {{-- Code --}}
        <div>
          <label class="block text-sm font-medium text-gray-700">Code</label>
          <div class="mt-1 p-3 bg-gray-50 border border-gray-300 rounded-lg">
            <span class="font-mono">{{ $palletType->code }}</span>
          </div>
        </div>
        {{-- Description --}}
        <div>
          <label class="block text-sm font-medium text-gray-700">Description</label>
          <div class="mt-1 p-3 bg-gray-50 border border-gray-300 rounded-lg min-h-[100px]">
            {{ $palletType->description ?: 'No description provided' }}
          </div>
        </div>
        {{-- Active Status --}}
        <div>
          <label class="block text-sm font-medium text-gray-700">Status</label>
          <div class="mt-1">
            @if($palletType->is_active)
              <span class="px-3 py-1 bg-green-100 text-green-800 text-sm rounded-full">Active</span>
            @else
              <span class="px-3 py-1 bg-red-100 text-red-800 text-sm rounded-full">Inactive</span>
            @endif
          </div>
        </div>
      </div>
    </div>
    {{-- Usage Information --}}
    @php
      $expectedCount = $palletType->poLinesExpected()->count();
      $actualCount = $palletType->poLinesActual()->count();
      $totalUsage = $expectedCount + $actualCount;
    @endphp
    @if($totalUsage > 0)
      <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="font-medium text-blue-800 mb-2">📊 Usage Statistics</h3>
        <div class="text-blue-700 text-sm space-y-1">
          <p>This pallet type is used in <strong>{{ $totalUsage }}</strong> PO lines total:</p>
          <ul class="list-disc list-inside ml-4">
            <li>{{ $expectedCount }} as expected pallet type</li>
            <li>{{ $actualCount }} as actual pallet type</li>
          </ul>
        </div>
      </div>
    @else
      <div class="mt-6 bg-gray-50 border border-gray-200 rounded-lg p-4">
        <h3 class="font-medium text-gray-600 mb-2">📊 Usage Statistics</h3>
        <p class="text-gray-500 text-sm">This pallet type is not currently in use.</p>
      </div>
    @endif
  </div>
</x-app-layout>