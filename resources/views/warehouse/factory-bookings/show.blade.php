<x-warehouse-layout>
  <x-slot name="header">
    <div class="bg-white border-b border-gray-200 px-6 py-4">
      {{-- Header with Factory Badge --}}
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-4">
          <div class="flex items-center space-x-3">
            <div class="bg-gradient-to-r from-orange-600 to-orange-700 p-3 rounded-lg shadow-lg">
              <span class="text-white text-xl font-bold">FAC</span>
            </div>
            <div>
              <h1 class="text-xl font-bold text-gray-900">Factory Delivery</h1>
              <p class="text-sm text-gray-600">Ad-hoc Arrival Management</p>
            </div>
          </div>
        </div>
        {{-- Reference Badge --}}
        <div class="text-right">
          <div class="text-sm text-gray-500">Factory Reference</div>
          <div class="text-2xl font-bold text-orange-600">#{{ $factoryBooking->reference }}</div>
        </div>
      </div>
      {{-- Action Buttons --}}
      <div class="flex flex-wrap gap-3">
        <div class="flex items-center space-x-2 bg-gray-50 p-2 rounded-lg border">
          <span class="text-xs font-medium text-gray-600 uppercase">Navigation</span>
          <a href="{{ route('app.factory-bookings.index') }}"
             class="inline-flex items-center px-3 py-1.5 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 transition-colors">
            ← Factory Bookings
          </a>
          <a href="{{ route('app.bookings.index') }}"
             class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
            📋 Scheduled Bookings
          </a>
        </div>
        {{-- Operational Actions --}}
        @if(!in_array($factoryBooking->status, ['departed']))
          <div class="flex items-center space-x-2 bg-orange-50 p-2 rounded-lg border border-orange-200">
            <span class="text-xs font-medium text-orange-700 uppercase">Operations</span>
            @if($factoryBooking->status === 'arrived')
              <form method="POST" action="{{ route('app.factory-bookings.start-processing', $factoryBooking) }}" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition-colors">
                  ▶️ Start Processing
                </button>
              </form>
            @endif
            @if(in_array($factoryBooking->status, ['arrived', 'processing']))
              <a href="{{ route('app.factory-booking-workflow.show', $factoryBooking) }}"
                 class="inline-flex items-center px-3 py-1.5 bg-orange-600 text-white text-sm font-medium rounded-md hover:bg-orange-700 transition-colors">
                🚛 Manage Workflow
              </a>
            @endif
            @if(in_array($factoryBooking->status, ['processing', 'arrived']))
              <form method="POST" action="{{ route('app.factory-bookings.complete', $factoryBooking) }}" class="inline">
                @csrf
                <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
                  ✅ Mark Complete
                </button>
              </form>
            @endif
            @if($factoryBooking->status === 'completed')
              <a href="{{ route('app.factory-booking-workflow.show', $factoryBooking) }}" 
                 class="inline-flex items-center px-3 py-1.5 bg-purple-600 text-white text-sm font-medium rounded-md hover:bg-purple-700 transition-colors">
                🏁 Handle Departure
              </a>
            @endif
            <a href="{{ route('app.factory-bookings.edit', $factoryBooking) }}"
               class="inline-flex items-center px-3 py-1.5 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 transition-colors">
              ✏️ Edit
            </a>
          </div>
        @endif
      </div>
    </div>
  </x-slot>
  <div class="py-6 max-w-7xl mx-auto px-4">
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
    {{-- Status Banner --}}
    <div class="mb-6 p-4 
      @if($factoryBooking->status === 'departed') bg-gray-100 border border-gray-300 
      @elseif($factoryBooking->status === 'completed') bg-green-100 border border-green-300 
      @elseif($factoryBooking->status === 'processing') bg-blue-100 border border-blue-300 
      @else bg-orange-100 border border-orange-300 
      @endif rounded-lg">
      <div class="flex items-center justify-between">
        <div class="flex items-center">
          @if($factoryBooking->status === 'departed')
            <span class="text-gray-600 text-2xl mr-3">🏁</span>
            <div>
              <h3 class="text-lg font-semibold text-gray-800">Vehicle Departed</h3>
              <p class="text-gray-700">Departed: {{ $factoryBooking->departed_at->format('d M Y, H:i') }}</p>
            </div>
          @elseif($factoryBooking->status === 'completed')
            <span class="text-green-600 text-2xl mr-3">✅</span>
            <div>
              <h3 class="text-lg font-semibold text-green-800">Delivery Completed</h3>
              <p class="text-green-700">Completed: {{ $factoryBooking->completed_at->format('d M Y, H:i') }}</p>
            </div>
          @elseif($factoryBooking->status === 'processing')
            <span class="text-blue-600 text-2xl mr-3">⚡</span>
            <div>
              <h3 class="text-lg font-semibold text-blue-800">Currently Processing</h3>
              <p class="text-blue-700">
                Started: {{ $factoryBooking->processing_started_at->format('d M Y, H:i') }}
                ({{ $factoryBooking->processing_started_at->diffForHumans() }})
              </p>
            </div>
          @else
            <span class="text-orange-600 text-2xl mr-3">📋</span>
            <div>
              <h3 class="text-lg font-semibold text-orange-800">Awaiting Processing</h3>
              <p class="text-orange-700">
                Arrived: {{ $factoryBooking->arrived_at->format('d M Y, H:i') }}
                ({{ $factoryBooking->getTimeOnSite() }} on site)
              </p>
            </div>
          @endif
        </div>
        {{-- Priority Badge --}}
        <div class="text-right">
          @php
            $priorityColor = match(true) {
              $factoryBooking->priority >= 80 => 'bg-red-500',
              $factoryBooking->priority >= 60 => 'bg-orange-500',
              $factoryBooking->priority >= 40 => 'bg-yellow-500',
              $factoryBooking->priority >= 20 => 'bg-blue-500',
              default => 'bg-gray-500'
            };
            $priorityLabel = match(true) {
              $factoryBooking->priority >= 80 => 'URGENT',
              $factoryBooking->priority >= 60 => 'HIGH',
              $factoryBooking->priority >= 40 => 'NORMAL',
              $factoryBooking->priority >= 20 => 'LOW',
              default => 'DEFERRED'
            };
          @endphp
          <div class="text-sm text-gray-600 mb-1">Priority</div>
          <div class="inline-flex items-center {{ $priorityColor }} text-white px-3 py-1 rounded-full">
            <span class="text-lg font-bold mr-2">{{ $factoryBooking->priority }}</span>
            <span class="text-xs font-medium">{{ $priorityLabel }}</span>
          </div>
        </div>
      </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      {{-- Main Information --}}
      <div class="lg:col-span-2 space-y-6">
        {{-- Basic Information --}}
        <div class="bg-white rounded-lg shadow-sm border p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">📋 Delivery Information</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Reference</label>
              <div class="mt-1 text-sm text-gray-900 font-mono">{{ $factoryBooking->reference }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Depot</label>
              <div class="mt-1 text-sm text-gray-900">{{ $factoryBooking->depot->name }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Customer</label>
              <div class="mt-1 text-sm text-gray-900">{{ $factoryBooking->customer->name }}</div>
            </div>
            @if($factoryBooking->carrier)
              <div>
                <label class="block text-sm font-medium text-gray-700">Carrier</label>
                <div class="mt-1 text-sm text-gray-900">{{ $factoryBooking->carrier->name }}</div>
              </div>
            @endif
            <div>
              <label class="block text-sm font-medium text-gray-700">Arrived</label>
              <div class="mt-1 text-sm text-gray-900">{{ $factoryBooking->arrived_at->format('d M Y, H:i') }}</div>
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Time on Site</label>
              <div class="mt-1 text-sm text-gray-900">{{ $factoryBooking->getTimeOnSite() }}</div>
            </div>
          </div>
        </div>
        {{-- Vehicle Information --}}
        <div class="bg-white rounded-lg shadow-sm border p-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-4">🚛 Vehicle Information</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Vehicle Registration</label>
              <div class="mt-1 text-sm text-gray-900 font-mono">{{ $factoryBooking->vehicle_registration }}</div>
            </div>
            @if($factoryBooking->trailer_registration)
              <div>
                <label class="block text-sm font-medium text-gray-700">Trailer Registration</label>
                <div class="mt-1 text-sm text-gray-900 font-mono">{{ $factoryBooking->trailer_registration }}</div>
              </div>
            @endif
            @if($factoryBooking->trailerType)
              <div>
                <label class="block text-sm font-medium text-gray-700">Trailer Type</label>
                <div class="mt-1 text-sm text-gray-900">{{ $factoryBooking->trailerType->name }}</div>
              </div>
            @endif
            @if($factoryBooking->driver_name)
              <div>
                <label class="block text-sm font-medium text-gray-700">Driver Name</label>
                <div class="mt-1 text-sm text-gray-900">{{ $factoryBooking->driver_name }}</div>
              </div>
            @endif
            @if($factoryBooking->driver_phone)
              <div>
                <label class="block text-sm font-medium text-gray-700">Driver Phone</label>
                <div class="mt-1 text-sm text-gray-900">{{ $factoryBooking->driver_phone }}</div>
              </div>
            @endif
          </div>
        </div>
        {{-- Notes --}}
        @if($factoryBooking->delivery_notes || $factoryBooking->gate_notes)
          <div class="bg-white rounded-lg shadow-sm border p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">📝 Notes</h3>
            @if($factoryBooking->delivery_notes)
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Notes</label>
                <div class="bg-gray-50 rounded-md p-3 text-sm text-gray-900 whitespace-pre-wrap">{{ $factoryBooking->delivery_notes }}</div>
              </div>
            @endif
            @if($factoryBooking->gate_notes)
              <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Gate Staff Notes</label>
                <div class="bg-blue-50 rounded-md p-3 text-sm text-gray-900 whitespace-pre-wrap">{{ $factoryBooking->gate_notes }}</div>
              </div>
            @endif
          </div>
        @endif
        {{-- PO Numbers Section --}}
        <div class="bg-white rounded-lg shadow-sm border p-6">
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">📦 PO Numbers</h3>
            @if($factoryBooking->poNumbers->count() === 0)
              <a href="#" class="text-sm text-blue-600 hover:text-blue-800">+ Add PO Numbers</a>
            @endif
          </div>
          @if($factoryBooking->poNumbers->count() > 0)
            <div class="space-y-3">
              @foreach($factoryBooking->poNumbers as $po)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-md">
                  <div>
                    <div class="font-medium">{{ $po->po_number }}</div>
                    @if($po->description)
                      <div class="text-sm text-gray-600">{{ $po->description }}</div>
                    @endif
                  </div>
                  <div class="text-right text-sm">
                    @if($po->expected_cases > 0)
                      <div>Cases: {{ $po->expected_cases }}</div>
                    @endif
                    @if($po->expected_pallets > 0)
                      <div>Pallets: {{ $po->expected_pallets }}</div>
                    @endif
                  </div>
                </div>
              @endforeach
            </div>
          @else
            <div class="text-center py-8 text-gray-500">
              <div class="text-4xl mb-2">📦</div>
              <div class="text-sm">No PO numbers added yet</div>
              <div class="text-xs text-gray-400 mt-1">PO numbers can be added once delivery details are confirmed</div>
            </div>
          @endif
        </div>
      </div>
      {{-- Sidebar --}}
      <div class="space-y-6">
        {{-- Tipping Operations --}}
        @if(in_array($factoryBooking->status, ['arrived', 'processing']))
          <div class="bg-white rounded-lg shadow-sm border p-6">
            <h4 class="font-medium text-gray-800 mb-2 flex items-center">
              <span class="mr-2">🚛</span>
              Tipping Operations
            </h4>
            <div class="text-sm text-gray-600 mb-3">
              Factory deliveries use the same tipping workflow as scheduled bookings.
            </div>
            <a href="{{ route('app.factory-booking-workflow.show', $factoryBooking) }}" 
               class="inline-flex items-center px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-md hover:bg-orange-700 transition-colors w-full justify-center">
              🚛 Manage Tipping Workflow
            </a>
          </div>
        @endif
        {{-- Registration Information --}}
        <div class="bg-white rounded-lg shadow-sm border p-6">
          <h4 class="font-medium text-gray-800 mb-3">👤 Registration Details</h4>
          <div class="space-y-3 text-sm">
            <div>
              <label class="block text-gray-700 font-medium">Registered By</label>
              <div class="text-gray-900">{{ $factoryBooking->registeredBy->name }}</div>
            </div>
            <div>
              <label class="block text-gray-700 font-medium">Registration Time</label>
              <div class="text-gray-900">{{ $factoryBooking->created_at->format('d M Y, H:i') }}</div>
            </div>
            <div>
              <label class="block text-gray-700 font-medium">Last Updated</label>
              <div class="text-gray-900">{{ $factoryBooking->updated_at->format('d M Y, H:i') }}</div>
            </div>
          </div>
        </div>
        {{-- Status Timeline --}}
        <div class="bg-white rounded-lg shadow-sm border p-6">
          <h4 class="font-medium text-gray-800 mb-3">📊 Status Timeline</h4>
          <div class="space-y-3">
            <div class="flex items-center">
              <div class="w-3 h-3 bg-orange-500 rounded-full mr-3"></div>
              <div class="text-sm">
                <div class="font-medium">Arrived</div>
                <div class="text-gray-500">{{ $factoryBooking->arrived_at->format('M j, H:i') }}</div>
              </div>
            </div>
            @if($factoryBooking->processing_started_at)
              <div class="flex items-center">
                <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                <div class="text-sm">
                  <div class="font-medium">Processing Started</div>
                  <div class="text-gray-500">{{ $factoryBooking->processing_started_at->format('M j, H:i') }}</div>
                </div>
              </div>
            @endif
            @if($factoryBooking->completed_at)
              <div class="flex items-center">
                <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                <div class="text-sm">
                  <div class="font-medium">Completed</div>
                  <div class="text-gray-500">{{ $factoryBooking->completed_at->format('M j, H:i') }}</div>
                </div>
              </div>
            @endif
            @if($factoryBooking->departed_at)
              <div class="flex items-center">
                <div class="w-3 h-3 bg-gray-500 rounded-full mr-3"></div>
                <div class="text-sm">
                  <div class="font-medium">Departed</div>
                  <div class="text-gray-500">{{ $factoryBooking->departed_at->format('M j, H:i') }}</div>
                </div>
              </div>
            @endif
          </div>
        </div>
        {{-- Quick Actions --}}
        @if(!in_array($factoryBooking->status, ['departed']))
          <div class="bg-orange-50 rounded-lg border border-orange-200 p-4">
            <h4 class="font-medium text-orange-800 mb-3">⚡ Quick Actions</h4>
            <div class="space-y-2">
              @if($factoryBooking->status === 'arrived')
                <form method="POST" action="{{ route('app.factory-bookings.start-processing', $factoryBooking) }}">
                  @csrf
                  <button type="submit" class="w-full px-3 py-2 bg-green-600 text-white text-sm rounded hover:bg-green-700">
                    ▶️ Start Processing
                  </button>
                </form>
              @endif
              @if(in_array($factoryBooking->status, ['processing', 'arrived']))
                <form method="POST" action="{{ route('app.factory-bookings.complete', $factoryBooking) }}">
                  @csrf
                  <button type="submit" class="w-full px-3 py-2 bg-blue-600 text-white text-sm rounded hover:bg-blue-700">
                    ✅ Mark Complete
                  </button>
                </form>
              @endif
              @if($factoryBooking->status === 'completed')
                <div class="space-y-3">
                  <h5 class="text-sm font-medium text-gray-800">🏁 Departure Flow</h5>
                  <form method="POST" action="{{ route('app.factory-booking-workflow.trailer-depart', $factoryBooking) }}" class="space-y-3">
                    @csrf
                    
                    <div>
                      <label class="block text-xs font-medium text-gray-700 mb-2">What happened?</label>
                      <div class="space-y-2">
                        <label class="flex items-center p-2 border rounded cursor-pointer hover:bg-gray-50">
                          <input type="radio" name="departure_scenario" value="completed_with_trailer" required class="mr-2">
                          <span class="text-xs">🚛✅ Left WITH trailer</span>
                        </label>
                        <label class="flex items-center p-2 border rounded cursor-pointer hover:bg-gray-50">
                          <input type="radio" name="departure_scenario" value="completed_dropped_trailer" required class="mr-2">
                          <span class="text-xs">🚛📦 Unit Left - Trailer DROPPED</span>
                        </label>
                      </div>
                    </div>
                    
                    <div>
                      <label class="block text-xs font-medium text-gray-700 mb-1">Notes (optional)</label>
                      <input type="text" name="departure_notes" placeholder="e.g., Driver requested early departure..." 
                             class="w-full px-2 py-1 border border-gray-300 rounded-md text-xs">
                    </div>
                    
                    <button type="submit" class="w-full px-3 py-2 bg-purple-600 text-white text-sm rounded hover:bg-purple-700">
                      🏁 Record Departure
                    </button>
                  </form>
                </div>
              @endif
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>
</x-warehouse-layout>