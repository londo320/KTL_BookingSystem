<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <div>
        <h2 class="font-semibold text-xl text-gray-800">Factory Bookings</h2>
        <p class="text-sm text-gray-600 mt-1">Ad-hoc deliveries registered on arrival</p>
      </div>
      <div class="flex gap-2">
        <a href="{{ route('app.bookings.index') }}"
           class="px-3 py-1 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 text-sm">
          📋 Scheduled Bookings
        </a>
        <a href="{{ route('app.factory-bookings.create') }}"
           class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
          + Register Factory Delivery
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
    {{-- Quick Search and Filters --}}
    <div class="mb-6 bg-white rounded-lg shadow-sm border p-4">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        {{-- Search Box --}}
        <div>
          <form method="GET" action="{{ route('app.factory-bookings.index') }}" class="flex gap-2">
            {{-- Preserve existing filters --}}
            @foreach(request()->except(['search', 'page']) as $key => $value)
              <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}"
                   placeholder="🔍 Search reference, vehicle, driver, customer..."
                   class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 text-sm">
              Search
            </button>
          </form>
        </div>
        {{-- Depot Filter --}}
        <div>
          <form method="GET" action="{{ route('app.factory-bookings.index') }}" class="flex gap-2">
            {{-- Preserve existing filters --}}
            @foreach(request()->except(['depot_id', 'page']) as $key => $value)
              <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
            <select name="depot_id" onchange="this.form.submit()" class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm">
              <option value="">All Depots</option>
              @foreach($depots as $depot)
                <option value="{{ $depot->id }}" {{ request('depot_id') == $depot->id ? 'selected' : '' }}>
                  {{ $depot->name }}
                </option>
              @endforeach
            </select>
          </form>
        </div>
        {{-- Status Filter --}}
        <div>
          <form method="GET" action="{{ route('app.factory-bookings.index') }}" class="flex gap-2">
            {{-- Preserve existing filters --}}
            @foreach(request()->except(['status', 'page']) as $key => $value)
              <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
            <select name="status" onchange="this.form.submit()" class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm">
              <option value="">All Status</option>
              <option value="arrived" {{ request('status') == 'arrived' ? 'selected' : '' }}>Arrived</option>
              <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
              <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
              <option value="departed" {{ request('status') == 'departed' ? 'selected' : '' }}>Departed</option>
            </select>
          </form>
        </div>
      </div>
      @if(request()->hasAny(['search', 'depot_id', 'status']))
        <div class="mt-3 pt-3 border-t border-gray-200">
          <div class="flex items-center justify-between">
            <div class="text-sm text-gray-600">
              @if(request('search'))
                <span class="mr-3">🔍 <strong>Search:</strong> "{{ request('search') }}"</span>
              @endif
              @if(request('depot_id'))
                <span class="mr-3">🏭 <strong>Depot:</strong> {{ $depots->find(request('depot_id'))->name ?? 'Unknown' }}</span>
              @endif
              @if(request('status'))
                <span class="mr-3">📊 <strong>Status:</strong> {{ ucfirst(request('status')) }}</span>
              @endif
            </div>
            <a href="{{ route('app.factory-bookings.index') }}" class="text-sm text-blue-600 hover:text-blue-800">
              Clear Filters
            </a>
          </div>
        </div>
      @endif
    </div>
    {{-- Priority Guide --}}
    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
      <h3 class="text-sm font-semibold text-blue-800 mb-2">🎯 Priority System</h3>
      <div class="grid grid-cols-1 md:grid-cols-5 gap-2 text-xs">
        <div class="flex items-center space-x-1">
          <span class="w-6 h-6 rounded-full bg-red-500 text-white flex items-center justify-center font-bold">!</span>
          <span>80-100: Urgent</span>
        </div>
        <div class="flex items-center space-x-1">
          <span class="w-6 h-6 rounded-full bg-orange-500 text-white flex items-center justify-center font-bold">↑</span>
          <span>60-79: High</span>
        </div>
        <div class="flex items-center space-x-1">
          <span class="w-6 h-6 rounded-full bg-yellow-500 text-white flex items-center justify-center font-bold">=</span>
          <span>40-59: Normal</span>
        </div>
        <div class="flex items-center space-x-1">
          <span class="w-6 h-6 rounded-full bg-blue-500 text-white flex items-center justify-center font-bold">↓</span>
          <span>20-39: Low</span>
        </div>
        <div class="flex items-center space-x-1">
          <span class="w-6 h-6 rounded-full bg-gray-500 text-white flex items-center justify-center font-bold">...</span>
          <span>0-19: Deferred</span>
        </div>
      </div>
    </div>
    {{-- Factory Bookings Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <table class="min-w-full">
        <thead class="bg-gray-100">
          <tr>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Reference</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Priority</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Customer</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Vehicle Details</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Arrived</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Status</th>
            <th class="px-4 py-3 text-left text-sm font-medium text-gray-700">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
          @forelse($factoryBookings as $factoryBooking)
            <tr class="hover:bg-gray-50 
              @if($factoryBooking->status === 'departed') bg-gray-50 
              @elseif($factoryBooking->status === 'completed') bg-green-50 
              @elseif($factoryBooking->status === 'processing') bg-blue-50 
              @endif">
              {{-- Reference --}}
              <td class="px-4 py-3">
                <div class="flex items-center">
                  @php
                    $priorityColor = match(true) {
                      $factoryBooking->priority >= 80 => 'bg-red-500',
                      $factoryBooking->priority >= 60 => 'bg-orange-500',
                      $factoryBooking->priority >= 40 => 'bg-yellow-500',
                      $factoryBooking->priority >= 20 => 'bg-blue-500',
                      default => 'bg-gray-500'
                    };
                  @endphp
                  <div class="w-3 h-3 {{ $priorityColor }} rounded-full mr-2"></div>
                  <div>
                    <div class="font-mono text-sm font-semibold text-blue-600">{{ $factoryBooking->reference }}</div>
                    <div class="text-xs text-gray-500">{{ $factoryBooking->depot->name }}</div>
                  </div>
                </div>
              </td>
              {{-- Priority --}}
              <td class="px-4 py-3">
                <div class="text-center">
                  <span class="inline-flex items-center justify-center w-8 h-8 {{ $priorityColor }} text-white rounded-full text-sm font-bold">
                    {{ $factoryBooking->priority }}
                  </span>
                </div>
              </td>
              {{-- Customer --}}
              <td class="px-4 py-3">
                <div class="text-sm font-medium text-gray-900">{{ $factoryBooking->customer->name }}</div>
                @if($factoryBooking->carrier)
                  <div class="text-xs text-gray-500">via {{ $factoryBooking->carrier->name }}</div>
                @endif
              </td>
              {{-- Vehicle Details --}}
              <td class="px-4 py-3">
                <div class="text-sm">
                  <div class="font-medium">🚛 {{ $factoryBooking->vehicle_registration }}</div>
                  @if($factoryBooking->trailer_registration)
                    <div class="text-xs text-gray-600">📦 {{ $factoryBooking->trailer_registration }}</div>
                  @endif
                  @if($factoryBooking->driver_name)
                    <div class="text-xs text-gray-600">👤 {{ $factoryBooking->driver_name }}</div>
                  @endif
                </div>
              </td>
              {{-- Arrived --}}
              <td class="px-4 py-3">
                <div class="text-sm">
                  <div>{{ $factoryBooking->arrived_at->format('M j, H:i') }}</div>
                  <div class="text-xs text-gray-500">{{ $factoryBooking->getTimeOnSite() }} on site</div>
                </div>
              </td>
              {{-- Status --}}
              <td class="px-4 py-3">
                <div>
                  {!! $factoryBooking->tipping_status_badge !!}
                  @if($factoryBooking->processing_started_at && $factoryBooking->status === 'processing')
                    <div class="text-xs text-gray-500 mt-1">
                      Started {{ $factoryBooking->processing_started_at->diffForHumans() }}
                    </div>
                  @endif
                </div>
              </td>
              {{-- Actions --}}
              <td class="px-4 py-3">
                <div class="flex flex-col space-y-1">
                  <a href="{{ route('app.factory-bookings.show', $factoryBooking) }}" 
                     class="text-sm text-blue-600 hover:text-blue-800">
                    View Details
                  </a>
                  @if($factoryBooking->status === 'arrived')
                    <form method="POST" action="{{ route('app.factory-bookings.start-processing', $factoryBooking) }}" class="inline">
                      @csrf
                      <button type="submit" class="text-sm text-green-600 hover:text-green-800">
                        Start Processing
                      </button>
                    </form>
                  @endif
                  @if(in_array($factoryBooking->status, ['arrived', 'processing']))
                    <a href="{{ route('app.factory-bookings.workflow.show', $factoryBooking) }}" 
                       class="text-sm text-orange-600 hover:text-orange-800">
                      🚛 Manage Workflow
                    </a>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                <div class="flex flex-col items-center">
                  <div class="text-6xl mb-4">📋</div>
                  <div class="text-lg font-medium mb-2">No Factory Bookings Found</div>
                  <div class="text-sm text-gray-400 mb-4">
                    @if(request()->hasAny(['search', 'depot_id', 'status']))
                      Try adjusting your filters or
                      <a href="{{ route('app.factory-bookings.index') }}" class="text-blue-600 hover:text-blue-800">clear all filters</a>
                    @else
                      Register the first factory delivery to get started
                    @endif
                  </div>
                  <a href="{{ route('app.factory-bookings.create') }}" 
                     class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    + Register Factory Delivery
                  </a>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
      {{-- Pagination --}}
      @if($factoryBookings->hasPages())
        <div class="px-4 py-3 border-t border-gray-200">
          {{ $factoryBookings->links() }}
        </div>
      @endif
    </div>
    {{-- Summary Stats --}}
    @if($factoryBookings->count() > 0)
      <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
          <div class="text-sm text-gray-600">Total Factory Bookings</div>
          <div class="text-2xl font-bold text-gray-900">{{ $factoryBookings->total() }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
          <div class="text-sm text-gray-600">Currently On Site</div>
          <div class="text-2xl font-bold text-blue-600">
            {{ $factoryBookings->where('status', 'arrived')->count() + $factoryBookings->where('status', 'processing')->count() }}
          </div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
          <div class="text-sm text-gray-600">Processing</div>
          <div class="text-2xl font-bold text-orange-600">{{ $factoryBookings->where('status', 'processing')->count() }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
          <div class="text-sm text-gray-600">Completed Today</div>
          <div class="text-2xl font-bold text-green-600">{{ $factoryBookings->where('status', 'completed')->count() }}</div>
        </div>
      </div>
    @endif
  </div>
</x-app-layout>