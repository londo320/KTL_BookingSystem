<x-app-layout>
  <x-slot name="header">
    <div class="bg-white border-b border-gray-200 px-6 py-4">
      {{-- Header with Factory Badge --}}
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-4">
          <div class="flex items-center space-x-3">
            <div class="bg-gradient-to-r from-orange-600 to-orange-700 p-3 rounded-lg shadow-lg">
              <span class="text-white text-xl font-bold">🚛</span>
            </div>
            <div>
              <h1 class="text-xl font-bold text-gray-900">Factory Delivery Workflow</h1>
              <p class="text-sm text-gray-600">Tipping Operations Management</p>
            </div>
          </div>
        </div>
        {{-- Reference Badge --}}
        <div class="text-right">
          <div class="text-sm text-gray-500">Factory Reference</div>
          <div class="text-2xl font-bold text-orange-600">#{{ $factoryBooking->reference }}</div>
        </div>
      </div>
      {{-- Navigation --}}
      <div class="flex flex-wrap gap-3">
        <div class="flex items-center space-x-2 bg-gray-50 p-2 rounded-lg border">
          <span class="text-xs font-medium text-gray-600 uppercase">Navigation</span>
          <a href="{{ route('app.factory-bookings.show', $factoryBooking) }}"
             class="inline-flex items-center px-3 py-1.5 bg-orange-600 text-white text-sm font-medium rounded-md hover:bg-orange-700 transition-colors">
            ← Factory Booking Details
          </a>
          <a href="{{ route('app.tipping-workflow.dashboard') }}"
             class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
            📊 Tipping Dashboard
          </a>
        </div>
      </div>
    </div>
  </x-slot>
  <div class="py-6 max-w-7xl mx-auto px-4">
    @if(session('success'))
      <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
        {{ session('success') }}
      </div>
    @endif
    @if($errors->any())
      <div class="mb-4 p-3 bg-red-100 text-red-800 rounded">
        <ul class="list-disc pl-5">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif
    
    {{-- Factory Booking Information --}}
    <div class="mb-6 p-6 bg-blue-50 border border-blue-200 rounded-lg">
      <h3 class="text-lg font-semibold text-blue-800 mb-3">📋 Factory Booking Information</h3>
      <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
          <p class="text-sm text-gray-600">Factory Reference</p>
          <p class="font-medium">{{ $factoryBooking->reference }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Customer</p>
          <p class="font-medium">{{ $factoryBooking->customer->name }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Depot</p>
          <p class="font-medium">{{ $factoryBooking->depot->name }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Vehicle</p>
          <p class="font-medium">{{ $factoryBooking->vehicle_registration }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Arrived</p>
          <p class="font-medium">{{ $factoryBooking->arrived_at->format('D, d M Y - H:i') }}</p>
        </div>
      </div>
    </div>
    
    {{-- PO Numbers & Load Details --}}
    @if($factoryBooking->poNumbers->count() > 0)
      <div class="mb-6 p-6 bg-gray-50 border border-gray-200 rounded-lg">
        <h3 class="text-lg font-semibold text-gray-800 mb-3">📦 PO Numbers & Load Details</h3>
        <div class="space-y-4">
          @foreach($factoryBooking->poNumbers as $po)
            <div class="border border-gray-300 rounded-lg p-4 bg-white">
              <div class="flex justify-between items-start mb-3">
                <h4 class="font-medium text-lg text-gray-800">PO: {{ $po->po_number }}</h4>
                <button type="button" onclick="togglePoManagement({{ $po->id }})" class="text-sm text-blue-600 hover:text-blue-800">
                  <span id="toggleText-{{ $po->id }}">Manage Lines →</span>
                </button>
              </div>
              {{-- PO Summary --}}
              <div class="mb-3 p-3 bg-gray-50 rounded border">
                <div class="grid grid-cols-2 gap-4 text-sm">
                  <div>
                    <span class="text-gray-600">Expected:</span>
                    <span class="font-semibold">{{ number_format($po->total_expected_cases) }} cases, {{ number_format($po->total_expected_pallets) }} pallets</span>
                  </div>
                  <div>
                    <span class="text-gray-600">Actual:</span>
                    <span class="font-semibold {{ $po->total_actual_cases > 0 ? 'text-green-600' : 'text-gray-400' }}">
                      {{ $po->total_actual_cases > 0 ? number_format($po->total_actual_cases) . ' cases' : 'Not recorded' }}, 
                      {{ $po->total_actual_pallets > 0 ? number_format($po->total_actual_pallets) . ' pallets' : 'Not recorded' }}
                    </span>
                  </div>
                </div>
              </div>
              {{-- PO Lines Summary --}}
              @if($po->lines->count() > 0)
                <div class="text-sm text-gray-600 mb-3">
                  <span class="font-medium">{{ $po->lines->count() }} line(s)</span>
                  @if($po->lines->where('actual_cases', '>', 0)->count() > 0)
                    <span class="ml-2 text-green-600">• {{ $po->lines->where('actual_cases', '>', 0)->count() }} recorded</span>
                  @endif
                </div>
              @endif

              <!-- PO Line Management (Hidden by default) -->
              <div id="po-lines-{{ $po->id }}" class="po-lines-section hidden mt-4 border-t pt-4">
                <h6 class="font-medium text-gray-700 mb-3">Line Details</h6>
                
                @if($po->lines->count() > 0)
                  <div class="space-y-3 mb-4">
                    @foreach($po->lines as $line)
                      <div class="bg-gray-50 p-3 rounded border" data-line-id="{{ $line->id }}">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-3">
                          <div class="bg-blue-50 p-3 rounded">
                            <h6 class="text-xs font-semibold text-blue-800 mb-2">Expected (Read-only)</h6>
                            <div class="space-y-1 text-sm text-blue-700">
                              <div>Cases: {{ $line->expected_cases }}</div>
                              <div>Pallets: {{ $line->expected_pallets }}</div>
                              <div>Type: {{ $line->expectedPalletType->name ?? 'Not set' }}</div>
                            </div>
                          </div>
                          <div class="bg-green-50 p-3 rounded">
                            <h6 class="text-xs font-semibold text-green-800 mb-2">Actual (Editable)</h6>
                            <div class="grid grid-cols-2 gap-2">
                              <div>
                                <label class="block text-xs font-medium text-gray-700">Cases</label>
                                <input type="number" class="mt-1 block w-full border-gray-300 rounded-md text-sm actual-cases" 
                                       value="{{ $line->actual_cases }}" min="0">
                              </div>
                              <div>
                                <label class="block text-xs font-medium text-gray-700">Pallets</label>
                                <input type="number" class="mt-1 block w-full border-gray-300 rounded-md text-sm actual-pallets" 
                                       value="{{ $line->actual_pallets }}" min="0">
                              </div>
                            </div>
                            <div class="mt-2">
                              <label class="block text-xs font-medium text-gray-700">Pallet Type</label>
                              <select class="mt-1 block w-full border-gray-300 rounded-md text-sm actual-pallet-type">
                                <option value="">Select Type</option>
                                @foreach($palletTypes as $palletType)
                                  <option value="{{ $palletType->id }}" {{ $line->actual_pallet_type_id == $palletType->id ? 'selected' : '' }}>
                                    {{ $palletType->name }}
                                  </option>
                                @endforeach
                              </select>
                            </div>
                          </div>
                        </div>
                        <div class="flex justify-end">
                          <button type="button" onclick="updatePoLine({{ $line->id }})" class="text-xs bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                            Update Line
                          </button>
                        </div>
                      </div>
                    @endforeach
                  </div>
                @endif

                <!-- Add New Line Form -->
                <div class="bg-green-50 p-3 rounded border">
                  <h6 class="font-medium text-gray-700 mb-2">Add New Line (Actual Quantities Only)</h6>
                  <div class="grid grid-cols-1 lg:grid-cols-3 gap-3 items-end">
                    <div>
                      <label class="block text-xs font-medium text-gray-700">Actual Cases</label>
                      <input type="number" class="mt-1 block w-full border-gray-300 rounded-md text-sm" 
                             id="new-actual-cases-{{ $po->id }}" min="0">
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-gray-700">Actual Pallets</label>
                      <input type="number" class="mt-1 block w-full border-gray-300 rounded-md text-sm" 
                             id="new-actual-pallets-{{ $po->id }}" min="0">
                    </div>
                    <div>
                      <label class="block text-xs font-medium text-gray-700">Actual Pallet Type</label>
                      <select class="mt-1 block w-full border-gray-300 rounded-md text-sm" 
                              id="new-actual-pallet-type-{{ $po->id }}">
                        <option value="">Select Type</option>
                        @foreach($palletTypes as $palletType)
                          <option value="{{ $palletType->id }}">{{ $palletType->name }}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="mt-2 flex justify-end">
                    <button type="button" onclick="addPoLine({{ $po->id }})" class="text-xs bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700">
                      Add Line
                    </button>
                  </div>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endif

    {{-- Factory Tipping Progress --}}
    @php
      $movement = $factoryBooking->movements->last();
      $currentStatus = $movement ? $movement->current_status : 'arrived';
      $currentLocation = $movement?->tippingLocation;
      $currentBay = $movement?->tippingBay;
    @endphp
    <div class="mb-6 bg-white rounded-lg shadow overflow-hidden">
      <div class="p-6 border-b border-gray-200">
        <h3 class="text-xl font-semibold text-gray-800">🚛 Factory Tipping Progress</h3>
        @php
          $statusLabels = [
            'arrived' => ['⚡ Tipping in Progress', 'bg-orange-100 text-orange-800'],
            'in_location' => ['⚡ Tipping in Progress', 'bg-orange-100 text-orange-800'],
            'unloading' => ['⚡ Tipping in Progress', 'bg-orange-100 text-orange-800'],
            'empty' => ['✅ Tipped - Ready for Departure', 'bg-green-100 text-green-800'],
            'departed' => ['🏁 Departed', 'bg-green-100 text-green-800'],
          ];
          $statusConfig = $statusLabels[$currentStatus] ?? ['❓ Unknown Status', 'bg-gray-100 text-gray-800'];
        @endphp
        <p class="text-sm text-gray-600 mt-1">
          Current Status: <span class="px-2 py-1 rounded text-xs font-medium {{ $statusConfig[1] }}">{{ $statusConfig[0] }}</span>
        </p>
        @if($currentLocation)
          <p class="text-sm text-gray-600 mt-1">📍 Location: {{ $currentLocation->name }}</p>
        @endif
        @if($currentBay)
          <p class="text-sm text-gray-600 mt-1">🚛 Bay: {{ $currentBay->name }}</p>
        @endif
        @if($movement && $movement->operation_notes)
          <p class="text-sm text-gray-600 mt-1">📝 Notes: {{ $movement->operation_notes }}</p>
        @endif
      </div>
      
      {{-- Progress Timeline --}}
      <div class="p-6 border-b border-gray-200">
        @php
          $steps = [
            1 => ['✓', '⏳ Not Started', $factoryBooking->arrived_at ? 'completed' : 'pending'],
            2 => ['✓', '🚛 Unit Arrived', $factoryBooking->arrived_at ? 'completed' : 'pending'], 
            3 => ['3', in_array($currentStatus, ['unloading', 'empty']) ? '⚡ Tipping (Auto-started)' : '⚡ Tipping', 
                  in_array($currentStatus, ['unloading', 'empty']) ? 'completed' : ($currentBay ? 'current' : 'pending')],
            4 => ['4', '✅ Tipped - Ready for Departure', $currentStatus === 'empty' ? 'current' : ($movement && $movement->unloading_completed_at ? 'completed' : 'pending')],
            5 => ['5', '🏁 Departed', $currentStatus === 'departed' ? 'completed' : 'pending'],
          ];
        @endphp
        <div class="flex items-center space-x-2 mb-4">
          @foreach($steps as $stepNum => $step)
            @php
              $status = $step[2];
              $classes = match($status) {
                'completed' => 'bg-green-500 text-white',
                'current' => 'bg-orange-500 text-white',
                default => 'bg-gray-200 text-gray-600'
              };
            @endphp
            <div class="flex items-center">
              <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold {{ $classes }}">
                {{ $step[0] }}
              </div>
              @if($stepNum < count($steps))
                <div class="w-8 h-0.5 {{ $status === 'completed' ? 'bg-green-500' : 'bg-gray-200' }}"></div>
              @endif
            </div>
          @endforeach
        </div>
        <div class="grid grid-cols-1 md:grid-cols-5 gap-2 text-xs">
          @foreach($steps as $step)
            <div class="text-center">
              <p class="{{ $step[2] === 'completed' ? 'text-green-600 font-medium' : ($step[2] === 'current' ? 'text-orange-600 font-medium' : 'text-gray-500') }}">
                {{ $step[1] }}
              </p>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- Complete Tipping Form (Main Action) --}}
    @if(!$movement || !$movement->unloading_completed_at)
      <div class="mb-6 bg-white rounded-lg shadow overflow-hidden">
        <div class="p-6 border-b border-gray-200">
          <h3 class="text-xl font-semibold text-gray-800">✅ Complete Tipping</h3>
          <p class="text-sm text-gray-600 mt-1">Complete this form only after tipping has finished to record the actual quantities received.</p>
        </div>
        <div class="p-6">
          <form method="POST" action="{{ route('app.factory-booking-workflow.complete-tipping', $factoryBooking) }}">
            @csrf
            
            {{-- Record Actual Quantities Section --}}
            @if($factoryBooking->poNumbers->count() > 0)
              <div class="mb-6">
                <h4 class="text-lg font-semibold text-gray-800 mb-4">📦 Record Actual Quantities Received *</h4>
                @foreach($factoryBooking->poNumbers as $po)
                  <div class="border rounded-lg p-4 mb-4">
                    <h5 class="font-medium text-gray-800 mb-3">PO: {{ $po->po_number }}</h5>
                    @if($po->lines->count() > 0)
                      @foreach($po->lines as $line)
                        <div class="bg-gray-50 p-4 rounded border mb-3">
                          <h6 class="font-medium text-gray-700 mb-2">Line {{ $loop->iteration }}</h6>
                          <div class="text-sm text-gray-600 mb-3">
                            Expected: {{ number_format($line->expected_cases) }} cases, {{ number_format($line->expected_pallets) }} pallets
                          </div>
                          <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                              <label class="block text-sm font-medium text-gray-700 mb-1">Actual Cases *</label>
                              <input type="number" name="po_lines[{{ $line->id }}][actual_cases]" 
                                     class="w-full px-3 py-2 border border-gray-300 rounded-md" 
                                     value="{{ $line->actual_cases }}" min="0" required>
                            </div>
                            <div>
                              <label class="block text-sm font-medium text-gray-700 mb-1">Actual Pallets *</label>
                              <input type="number" name="po_lines[{{ $line->id }}][actual_pallets]" 
                                     class="w-full px-3 py-2 border border-gray-300 rounded-md" 
                                     value="{{ $line->actual_pallets }}" min="0" required>
                            </div>
                            <div>
                              <label class="block text-sm font-medium text-gray-700 mb-1">Pallet Type</label>
                              <select name="po_lines[{{ $line->id }}][actual_pallet_type_id]" 
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md">
                                <option value="">Select pallet type...</option>
                                @foreach($palletTypes as $palletType)
                                  <option value="{{ $palletType->id }}" {{ $line->actual_pallet_type_id == $palletType->id ? 'selected' : '' }}>
                                    {{ $palletType->name }}
                                  </option>
                                @endforeach
                              </select>
                            </div>
                          </div>
                        </div>
                      @endforeach
                    @endif
                  </div>
                @endforeach
              </div>
            @endif
            
            {{-- Issues Section --}}
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">Issues (if any)</label>
              <div class="space-y-2">
                <input type="text" name="issues[]" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Describe any issues...">
                <button type="button" onclick="addIssueField()" class="text-sm text-blue-600 hover:text-blue-800">+ Add another issue</button>
              </div>
            </div>
            
            {{-- Notes Section --}}
            <div class="mb-6">
              <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
              <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md" placeholder="Completion notes..."></textarea>
            </div>
            
            <button type="submit" class="w-full px-4 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700">
              Complete Tipping
            </button>
          </form>
        </div>
      </div>
    @else
      <div class="mb-6 p-4 bg-green-100 rounded-lg">
        <p class="text-green-800">✅ Tipping completed at {{ $movement->unloading_completed_at->format('d M Y, H:i') }}</p>
      </div>
    @endif

    {{-- Unit Departure --}}
    <div class="mb-6 bg-white rounded-lg shadow overflow-hidden">
      <div class="p-6 border-b border-gray-200">
        <h3 class="text-xl font-semibold text-gray-800">🚛 Unit Depart (Leave Trailer)</h3>
        <p class="text-sm text-gray-600 mt-1">Record when the vehicle leaves site while trailer continues tipping process</p>
      </div>
      <div class="p-6">
        <form method="POST" action="{{ route('app.factory-booking-workflow.trailer-depart', $factoryBooking) }}">
          @csrf
          <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Departure Notes</label>
            <textarea name="notes" class="w-full px-3 py-2 border border-gray-300 rounded-md" rows="3" placeholder="Optional notes about unit departure..."></textarea>
          </div>
          <button type="submit" class="w-full px-4 py-3 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700">
            🚛 Record Unit Departure
          </button>
        </form>
      </div>
    </div>
    
    {{-- Status Details --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="p-6 border-b border-gray-200">
        <h3 class="text-xl font-semibold text-gray-800">📊 Status Details</h3>
      </div>
      <div class="p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          {{-- Location Info --}}
          @if($currentLocation)
            <div>
              <h4 class="font-medium text-gray-800 mb-2">Drop Location</h4>
              <p class="text-sm text-gray-600">{{ $currentLocation->name }}</p>
              @if($movement && $movement->moved_to_location_at)
                <p class="text-xs text-gray-500">Moved: {{ $movement->moved_to_location_at->format('M j, H:i') }}</p>
              @endif
            </div>
          @endif
          {{-- Bay Info --}}
          @if($currentBay)
            <div>
              <h4 class="font-medium text-gray-800 mb-2">Tipping Bay</h4>
              <p class="text-sm text-gray-600">{{ $currentBay->name }}</p>
              @if($movement && $movement->moved_to_bay_at)
                <p class="text-xs text-gray-500">Moved: {{ $movement->moved_to_bay_at->format('M j, H:i') }}</p>
              @endif
            </div>
          @endif
          {{-- Timing Info --}}
          <div>
            <h4 class="font-medium text-gray-800 mb-2">Timing</h4>
            <p class="text-sm text-gray-600">Arrived: {{ $factoryBooking->arrived_at->format('M j, H:i') }}</p>
            <p class="text-sm text-gray-600">Time on Site: {{ $factoryBooking->getTimeOnSite() }}</p>
            @if($movement && $movement->unloading_started_at)
              <p class="text-sm text-gray-600">Tipping Started: {{ $movement->unloading_started_at->format('M j, H:i') }}</p>
            @endif
            @if($movement && $movement->unloading_completed_at)
              <p class="text-sm text-gray-600">Completed: {{ $movement->unloading_completed_at->format('M j, H:i') }}</p>
            @endif
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    function togglePoManagement(poId) {
      const section = document.getElementById(`po-lines-${poId}`);
      const toggleText = document.getElementById(`toggleText-${poId}`);
      const isHidden = section.classList.contains('hidden');
      
      if (isHidden) {
        section.classList.remove('hidden');
        toggleText.textContent = '← Hide Lines';
      } else {
        section.classList.add('hidden');
        toggleText.textContent = 'Manage Lines →';
      }
    }

    function addIssueField() {
      const container = event.target.parentElement;
      const newInput = document.createElement('input');
      newInput.type = 'text';
      newInput.name = 'issues[]';
      newInput.className = 'w-full px-3 py-2 border border-gray-300 rounded-md';
      newInput.placeholder = 'Additional issue...';
      container.insertBefore(newInput, event.target);
    }
  </script>
</x-app-layout>