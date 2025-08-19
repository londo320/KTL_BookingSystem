<x-app-layout>
  @include('layouts.admin-nav')

  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl">Container Sizes Management</h2>
      <a href="{{ route('admin.settings.dashboard') }}"
         class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
        Back to Settings
      </a>
    </div>
  </x-slot>

  <div class="py-6 max-w-7xl mx-auto">
    
    {{-- Container Sizes Information --}}
    <div class="bg-white rounded-lg shadow">
      <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-800">📦 Container Sizes</h3>
        <p class="text-sm text-gray-600 mt-1">Container sizes currently in use in the system (from existing bookings)</p>
      </div>
      
      <div class="p-6">
        @if($containerSizes->count() > 0)
          <div class="bg-blue-50 p-4 rounded-lg border border-blue-200 mb-6">
            <div class="flex items-start">
              <span class="text-blue-600 text-2xl mr-3">ℹ️</span>
              <div>
                <h4 class="font-medium text-blue-800">Information</h4>
                <p class="text-blue-700 text-sm mt-1">
                  Container sizes are automatically collected from existing bookings. These are the sizes currently in use in your system.
                  If you need to standardize or modify container size options, this will need to be done at the booking form level.
                </p>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($containerSizes as $size)
              <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                <div class="flex items-center">
                  <span class="text-gray-600 text-lg mr-3">📦</span>
                  <div>
                    <div class="font-medium text-gray-900">{{ $size }}</div>
                    <div class="text-sm text-gray-500">Container Size</div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>

          <div class="mt-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
            <div class="flex items-start">
              <span class="text-yellow-600 text-xl mr-3">⚠️</span>
              <div>
                <h4 class="font-medium text-yellow-800">Note</h4>
                <p class="text-yellow-700 text-sm mt-1">
                  To add standardized container size options for new bookings, you would need to modify the booking creation forms 
                  to include a dropdown with predefined options instead of free-text input.
                </p>
              </div>
            </div>
          </div>
        @else
          <div class="text-center py-8">
            <span class="text-gray-400 text-6xl">📦</span>
            <h3 class="text-lg font-medium text-gray-900 mt-4">No Container Sizes Found</h3>
            <p class="text-gray-500 mt-2">No container sizes have been recorded in bookings yet.</p>
          </div>
        @endif
      </div>
    </div>

    {{-- Usage Statistics --}}
    @if($containerSizes->count() > 0)
      <div class="mt-6 bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
          <h3 class="text-lg font-semibold text-gray-800">📊 Usage Statistics</h3>
          <p class="text-sm text-gray-600 mt-1">How often each container size is used in bookings</p>
        </div>
        
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Container Size</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usage Count</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              @php
                $totalBookings = \App\Models\Booking::whereNotNull('container_size')->count();
                $sizeUsage = \App\Models\Booking::whereNotNull('container_size')
                    ->selectRaw('container_size, COUNT(*) as count')
                    ->groupBy('container_size')
                    ->orderByDesc('count')
                    ->get();
              @endphp
              
              @foreach($sizeUsage as $usage)
                <tr>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="font-medium text-gray-900">{{ $usage->container_size }}</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">{{ number_format($usage->count) }} bookings</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    @php $percentage = $totalBookings > 0 ? ($usage->count / $totalBookings) * 100 : 0; @endphp
                    <div class="text-sm text-gray-900">{{ number_format($percentage, 1) }}%</div>
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-1">
                      <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    @endif
  </div>
</x-app-layout>