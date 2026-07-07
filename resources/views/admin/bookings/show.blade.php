<x-app-layout>
  <x-slot name="header">
    <div class="bg-white border-b border-gray-200 px-6 py-4">
      {{-- Corporate Header with Logo --}}
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center space-x-4">
          {{-- Company Logo/Brand --}}
          <div class="flex items-center space-x-3">
            <div class="bg-gradient-to-r from-blue-600 to-blue-700 p-3 rounded-lg shadow-lg">
              <span class="text-white text-xl font-bold">WM</span>
            </div>
            <div>
              <h1 class="text-xl font-bold text-gray-900">Warehouse Manager</h1>
              <p class="text-sm text-gray-600">Professional Booking System</p>
            </div>
          </div>
        </div>
        {{-- Booking Status Badge --}}
        <div class="text-right">
          <div class="text-sm text-gray-500">Booking Reference</div>
          <div class="text-2xl font-bold text-gray-900">#{{ $booking->id }}</div>
        </div>
      </div>
      {{-- Action Buttons - Organized by Category --}}
      <div class="flex flex-wrap gap-3">
        @php
          $isLocked = $booking->slot->locked_at && $booking->slot->locked_at->isPast();
          $hasArrived = $booking->arrived_at;
          // Action restriction logic
          $user = auth()->user();
          $allowedDepotIds = $user->depots()->pluck('depots.id')->toArray();
          if (empty($allowedDepotIds) && $user->hasRole('admin|site-admin')) {
              $allowedDepotIds = \App\Models\Depot::pluck('id')->toArray();
          }
          $defaultDepotId = $user->depot_id ?? $allowedDepotIds[0] ?? null;
          $canTakeAction = $booking->slot->depot_id == $defaultDepotId;
        @endphp
        {{-- Primary Actions Group --}}
        <div class="flex items-center space-x-2 bg-gray-50 p-2 rounded-lg border">
          <span class="text-xs font-medium text-gray-600 uppercase">Documents</span>
          <a href="{{ route('app.bookings.download-pdf', $booking) }}"
             class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
            📄 PDF
          </a>
          <button onclick="emailBookingPDF({{ $booking->id }})"
                  class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 transition-colors">
            📧 Email
          </button>
        </div>
        {{-- Operational Actions Group --}}
        @if($hasArrived && !$booking->cancelled_at)
          <div class="flex items-center space-x-2 bg-orange-50 p-2 rounded-lg border border-orange-200">
            <span class="text-xs font-medium text-orange-700 uppercase">Operations</span>
            @if($canTakeAction)
              <a href="{{ route('app.tipping-workflow.show', $booking) }}"
                 class="inline-flex items-center px-3 py-1.5 bg-orange-600 text-white text-sm font-medium rounded-md hover:bg-orange-700 transition-colors">
                🚛 Workflow
              </a>
            @else
              <span class="inline-flex items-center px-3 py-1.5 bg-gray-300 text-gray-500 text-sm font-medium rounded-md cursor-not-allowed"
                    title="Actions only available for your default depot">
                🚛 Workflow
              </span>
            @endif
            @if($booking->tipping_bay_id && in_array($booking->tipping_status, ['at_bay', 'unloading']))
              @if($canTakeAction)
                <a href="{{ route('app.bookings.transfer-bay.form', $booking) }}"
                   class="inline-flex items-center px-3 py-1.5 bg-yellow-600 text-white text-sm font-medium rounded-md hover:bg-yellow-700 transition-colors">
                  🔄 Transfer
                </a>
              @else
                <span class="inline-flex items-center px-3 py-1.5 bg-gray-300 text-gray-500 text-sm font-medium rounded-md cursor-not-allowed"
                      title="Actions only available for your default depot">
                  🔄 Transfer
                </span>
              @endif
            @endif
          </div>
        @endif
        {{-- Booking Management Group --}}
        <div class="flex items-center space-x-2 bg-blue-50 p-2 rounded-lg border border-blue-200">
          <span class="text-xs font-medium text-blue-700 uppercase">Management</span>
          @if($booking->cancelled_at)
            <span class="inline-flex items-center px-3 py-1.5 bg-gray-400 text-white text-sm font-medium rounded-md cursor-not-allowed">
              ❌ Cancelled
            </span>
          @else
            @if(!$hasArrived && !$booking->isCancelled())
              @if($canTakeAction)
                <a href="{{ route('app.bookings.edit', $booking) }}"
                   class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
                  ✏️ Edit
                </a>
              @else
                <span class="inline-flex items-center px-3 py-1.5 bg-gray-300 text-gray-500 text-sm font-medium rounded-md cursor-not-allowed"
                      title="Actions only available for your default depot">
                  ✏️ Edit
                </span>
              @endif
            @endif
            @if($canTakeAction)
              <a href="{{ route('app.bookings.rebook.show', $booking) }}"
                 class="inline-flex items-center px-3 py-1.5 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
                🔄 {{ $hasArrived ? 'Rebook/Reject' : 'Rebook' }}
              </a>
              <button onclick="showCancelModal()" 
                      class="inline-flex items-center px-3 py-1.5 bg-red-600 text-white text-sm font-medium rounded-md hover:bg-red-700 transition-colors">
                ❌ {{ $hasArrived ? 'Cancel/Reject' : 'Cancel' }}
              </button>
            @else
              <span class="inline-flex items-center px-3 py-1.5 bg-gray-300 text-gray-500 text-sm font-medium rounded-md cursor-not-allowed"
                    title="Actions only available for your default depot">
                🔄 {{ $hasArrived ? 'Rebook/Reject' : 'Rebook' }}
              </span>
              <span class="inline-flex items-center px-3 py-1.5 bg-gray-300 text-gray-500 text-sm font-medium rounded-md cursor-not-allowed"
                    title="Actions only available for your default depot">
                ❌ {{ $hasArrived ? 'Cancel/Reject' : 'Cancel' }}
              </span>
            @endif
          @endif
        </div>
        {{-- Information Group --}}
        @php
          $hasHistory = true; // Show for testing
          try {
            if (\Schema::hasTable('booking_history')) {
              $hasHistory = \App\Models\BookingHistory::where(function ($query) use ($booking) {
                $query->where('booking_id', $booking->id)
                      ->orWhere('original_booking_id', $booking->id);
              })->exists();
            }
          } catch (\Exception $e) {
            $hasHistory = true;
          }
        @endphp
        <div class="flex items-center space-x-2 bg-yellow-50 p-2 rounded-lg border border-yellow-200">
          <span class="text-xs font-medium text-yellow-700 uppercase">Information</span>
          @if($hasHistory)
            <a href="{{ route('app.bookings.history', $booking) }}"
               class="inline-flex items-center px-3 py-1.5 bg-yellow-600 text-white text-sm font-medium rounded-md hover:bg-yellow-700 transition-colors">
              📋 History
            </a>
          @endif
        </div>
        {{-- Navigation --}}
        <div class="flex items-center ml-auto">
          <a href="{{ route('app.bookings.index') }}"
             class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-md hover:bg-gray-700 transition-colors">
            ← Back to Bookings
          </a>
        </div>
      </div>
    </div>
  </x-slot>
  <div class="py-6 max-w-4xl mx-auto">
    {{-- Success/Info Messages --}}
    @if(session('success'))
      <div class="mb-6 p-4 bg-green-100 border border-green-300 rounded-lg">
        <p class="text-green-800">{{ session('success') }}</p>
      </div>
    @endif
    @if(session('info'))
      <div class="mb-6 p-4 bg-blue-100 border border-blue-300 rounded-lg">
        <p class="text-blue-800">{{ session('info') }}</p>
      </div>
    @endif
    {{-- Status Banner --}}
    @if($booking->cancelled_at && (!$booking->cancellation_reason || !str_contains($booking->cancellation_reason, 'Rebooked')))
      <div class="mb-6 p-4 bg-black text-white rounded-lg">
        <div class="flex items-center">
          <span class="text-white text-2xl mr-3">❌</span>
          <div>
            <h3 class="text-lg font-semibold text-white">Booking Cancelled</h3>
            <p class="text-white">
              Cancelled: {{ $booking->cancelled_at->format('d M Y, H:i') }}
              @if($booking->cancellation_reason)
                <br>Reason: {{ $booking->cancellation_reason }}
              @endif
            </p>
          </div>
        </div>
      </div>
    @elseif($hasArrived)
      <div class="mb-6 p-4 bg-green-100 border border-green-300 rounded-lg">
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <span class="text-green-600 text-2xl mr-3">✅</span>
            <div>
              <h3 class="text-lg font-semibold text-green-800">Vehicle Arrived</h3>
              <p class="text-green-700">
                Arrived: {{ $booking->arrived_at->format('d M Y, H:i') }}
                @if($booking->departed_at)
                  | Departed: {{ $booking->departed_at->format('d M Y, H:i') }}
                @else
                  @php
                    $movement = $booking->movements->first();
                    $collectionCompleted = $movement && ($movement->collection_unit_departed_at || $booking->trailer_collected_at);
                    $unitDeparted = $movement && $movement->unit_departed_at;
                  @endphp
                  @if($collectionCompleted)
                    | ✅ Collection Complete
                  @elseif($unitDeparted)
                    | 🚚 Unit Departed - Trailer on Site
                  @else
                    | Currently on-site
                  @endif
                @endif
              </p>
            </div>
          </div>
          {{-- Tipping Status --}}
          <div class="text-right">
            <p class="text-sm text-gray-600 mb-1">Tipping Status:</p>
            <div class="mb-2">{!! $booking->tipping_status_badge !!}</div>
            {{-- Location Information --}}
            @php $movement = $booking->movements->first(); @endphp
            @if($movement && ($movement->tippingLocation || $movement->tippingBay))
              <div class="text-xs text-gray-600 mb-2">
                @if($movement->tippingLocation)
                  <div>📍 {{ $movement->tippingLocation->name }}</div>
                  <div class="text-gray-400">({{ $movement->tippingLocation->depot->name }})</div>
                @endif
                @if($movement->tippingBay)
                  <div>🚛 {{ $movement->tippingBay->name }}</div>
                @endif
              </div>
            @endif
            {{-- Single Workflow Button --}}
            @if($booking->tipping_status && $booking->tipping_status !== 'departed')
              <a href="{{ route('app.tipping-workflow.show', $booking) }}" 
                 class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
                🚛 Manage Workflow
              </a>
            @endif
          </div>
        </div>
      </div>
    @elseif($isLocked)
      <div class="mb-6 p-4 bg-orange-100 border border-orange-300 rounded-lg">
        <div class="flex items-center">
          <span class="text-orange-600 text-2xl mr-3">🔒</span>
          <div>
            <h3 class="text-lg font-semibold text-orange-800">Booking Locked</h3>
            <p class="text-orange-700">
              Cut-off time: {{ $booking->slot->locked_at->format('d M Y, H:i') }}
            </p>
          </div>
        </div>
      </div>
    @else
      <div class="mb-6 p-4 bg-blue-100 border border-blue-300 rounded-lg">
        <div class="flex items-center">
          <span class="text-blue-600 text-2xl mr-3">📅</span>
          <div>
            <h3 class="text-lg font-semibold text-blue-800">Booking Active</h3>
            <p class="text-blue-700">This booking is active and can be edited.</p>
          </div>
        </div>
      </div>
    @endif
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      {{-- Booking Information --}}
      <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">📋 Booking Information</h3>
        <div class="space-y-3">
          <div>
            <label class="text-sm font-medium text-gray-600">Booking ID</label>
            <p class="text-lg font-mono">#{{ $booking->id }}</p>
          </div>
          <div>
            <label class="text-sm font-medium text-gray-600">Customer</label>
            <p class="text-lg">{{ $booking->customer->name ?? 'Not assigned' }}</p>
          </div>
          <div>
            <label class="text-sm font-medium text-gray-600">Created By</label>
            <p class="text-lg">{{ $booking->user->name ?? 'Unknown' }}</p>
          </div>
          <div>
            <label class="text-sm font-medium text-gray-600">Created At</label>
            <p class="text-lg">{{ $booking->created_at->format('d M Y, H:i') }}</p>
          </div>
          @if($booking->reference)
            <div>
              <label class="text-sm font-medium text-gray-600">Reference</label>
              <p class="text-lg font-mono">{{ $booking->reference }}</p>
            </div>
          @endif
        </div>
      </div>
      {{-- Slot & Location Details --}}
      <div class="bg-white p-6 rounded-lg shadow">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">📍 Slot & Location</h3>
        <div class="space-y-3">
          <div>
            <label class="text-sm font-medium text-gray-600">Depot</label>
            <p class="text-lg">{{ $booking->slot->depot->name }}</p>
            @if($booking->slot->depot->location)
              <p class="text-sm text-gray-500">{{ $booking->slot->depot->location }}</p>
            @endif
          </div>
          <div>
            <label class="text-sm font-medium text-gray-600">Date & Time</label>
            <p class="text-lg">
              {{ $booking->slot->start_at->format('l, d F Y') }}
            </p>
            <p class="text-lg font-semibold text-blue-600">
              {{ $booking->slot->start_at->format('H:i') }} - {{ $booking->slot->end_at->format('H:i') }}
            </p>
          </div>
          <div>
            <label class="text-sm font-medium text-gray-600">Booking Type</label>
            <p class="text-lg">{{ $booking->bookingType->name ?? 'Not specified' }}</p>
          </div>
          <div>
            <label class="text-sm font-medium text-gray-600">Slot Capacity</label>
            <p class="text-lg">{{ $booking->slot->capacity ?? 'Unlimited' }}</p>
          </div>
        </div>
      </div>
      {{-- PO Numbers & Load Details --}}
      <div class="bg-white p-6 rounded-lg shadow col-span-2">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">📦 PO Numbers & Load Details</h3>
        @if($booking->poNumbers && $booking->poNumbers->count() > 0)
          <div class="space-y-4">
            @foreach($booking->poNumbers as $poNumber)
              <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                <div class="flex justify-between items-start mb-3">
                  <h4 class="font-medium text-lg text-gray-800">PO: {{ $poNumber->po_number }}</h4>
                  <div class="flex space-x-2">
                    @if($poNumber->hasVariance())
                      <span class="px-2 py-1 bg-red-100 text-red-800 text-xs rounded-full">
                        Has Variance
                      </span>
                    @endif
                    @if($poNumber->hasTypeVariances())
                      <span class="px-2 py-1 bg-orange-100 text-orange-800 text-xs rounded-full">
                        Type Variance
                      </span>
                    @endif
                  </div>
                </div>
                {{-- PO Summary --}}
                <div class="mb-4 p-3 bg-white rounded border">
                  <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                      <span class="text-gray-600">Total Expected:</span>
                      <span class="font-semibold">{{ number_format($poNumber->total_expected_units) }} units, {{ number_format($poNumber->total_expected_pallets) }} pallets</span>
                    </div>
                    <div>
                      <span class="text-gray-600">Total Actual:</span>
                      <span class="font-semibold">{{ number_format($poNumber->total_actual_units) }} units, {{ number_format($poNumber->total_actual_pallets) }} pallets</span>
                    </div>
                  </div>
                  <div class="mt-2">
                    <span class="text-gray-600">Summary:</span>
                    <span class="text-sm">{{ $poNumber->expected_summary_text }}</span>
                  </div>
                </div>
                {{-- PO Lines --}}
                @if($poNumber->lines->count() > 0)
                  <div class="space-y-3">
                    <h5 class="font-medium text-gray-700">Lines ({{ $poNumber->lines->count() }})</h5>
                    @foreach($poNumber->lines as $line)
                      <div class="border border-gray-300 rounded p-3 bg-white">
                        <div class="flex justify-between items-start mb-2">
                          <span class="font-medium text-sm">Line {{ $line->line_number }}</span>
                          @if($line->hasVariance())
                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 text-xs rounded">
                              Variance
                            </span>
                          @endif
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                          {{-- Cases/Units --}}
                          <div>
                            <div class="font-medium text-gray-600 mb-1">Units/Cases</div>
                            <div class="flex items-center space-x-2">
                              <span class="text-gray-500">Expected:</span>
                              <span class="font-semibold">{{ number_format($line->expected_cases) }}</span>
                              @if($line->actual_cases !== null)
                                <span class="text-gray-400">→</span>
                                <span class="text-gray-500">Actual:</span>
                                <span class="font-semibold {{ $line->unit_variance == 0 ? 'text-green-600' : ($line->unit_variance > 0 ? 'text-blue-600' : 'text-red-600') }}">
                                  {{ number_format($line->actual_cases) }}
                                </span>
                                @if($line->unit_variance != 0)
                                  <span class="text-xs {{ $line->unit_variance > 0 ? 'text-blue-600' : 'text-red-600' }}">
                                    ({{ $line->unit_variance > 0 ? '+' : '' }}{{ number_format($line->unit_variance) }})
                                  </span>
                                @endif
                              @elseif($hasArrived)
                                <span class="text-gray-400">→ Not recorded</span>
                              @endif
                            </div>
                          </div>
                          {{-- Pallets --}}
                          <div>
                            <div class="font-medium text-gray-600 mb-1">Pallets</div>
                            <div class="flex items-center space-x-2">
                              <span class="text-gray-500">Expected:</span>
                              <span class="font-semibold">{{ number_format($line->expected_pallets) }}</span>
                              @if($line->expectedPalletType)
                                <span class="text-xs text-gray-600">({{ $line->expectedPalletType->name }})</span>
                              @endif
                              @if($line->total_actual_pallets > 0)
                                <span class="text-gray-400">→</span>
                                <span class="text-gray-500">Actual:</span>
                                <span class="font-semibold {{ $line->pallet_variance == 0 ? 'text-green-600' : ($line->pallet_variance > 0 ? 'text-blue-600' : 'text-red-600') }}">
                                  {{ number_format($line->total_actual_pallets) }}
                                </span>
                                @if($line->actualPallets->count() > 0)
                                  <span class="text-xs text-gray-600">
                                    @if($line->hasMultiplePalletTypes())
                                      ({{ $line->actualPallets->map(fn($p) => $p->quantity . ' ' . $p->palletType->name)->join(', ') }})
                                    @else
                                      ({{ $line->actualPallets->first()->palletType->name }})
                                    @endif
                                  </span>
                                @elseif($line->actualPalletType)
                                  <span class="text-xs text-gray-600">({{ $line->actualPalletType->name }})</span>
                                @endif
                                @if($line->pallet_variance != 0)
                                  <span class="text-xs {{ $line->pallet_variance > 0 ? 'text-blue-600' : 'text-red-600' }}">
                                    ({{ $line->pallet_variance > 0 ? '+' : '' }}{{ number_format($line->pallet_variance) }})
                                  </span>
                                @endif
                              @elseif($hasArrived)
                                <span class="text-gray-400">→ Not recorded</span>
                              @endif
                            </div>
                            {{-- Pallet Type Variance --}}
                            @if($line->pallet_type_variance)
                              <div class="mt-1 text-xs text-red-600">
                                <span class="font-medium">Type Change:</span> {{ $line->pallet_type_variance }}
                              </div>
                            @endif
                          </div>
                        </div>
                      </div>
                    @endforeach
                  </div>
                @endif
              </div>
            @endforeach
            {{-- Summary Totals --}}
            @if($booking->poNumbers->count() > 1)
              <div class="border-t pt-4 mt-4">
                <h5 class="font-medium text-gray-800 mb-3">Summary Totals</h5>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label class="text-sm font-medium text-gray-600">Total Cases</label>
                    <div class="flex items-center space-x-4 mt-1">
                      @if($booking->total_expected_cases > 0)
                        <div>
                          <span class="text-sm text-gray-500">Expected:</span>
                          <span class="text-xl font-bold">{{ number_format($booking->total_expected_cases) }}</span>
                        </div>
                      @endif
                      @if($booking->total_actual_cases > 0)
                        @if($booking->total_expected_cases > 0)
                          <div class="text-gray-400">→</div>
                        @endif
                        <div>
                          <span class="text-sm text-gray-500">Actual:</span>
                          <span class="text-xl font-bold {{ $booking->total_case_variance == 0 ? 'text-green-600' : ($booking->total_case_variance > 0 ? 'text-blue-600' : 'text-red-600') }}">
                            {{ number_format($booking->total_actual_cases) }}
                          </span>
                          @if($booking->total_expected_cases > 0 && $booking->total_case_variance != 0)
                            <span class="text-lg {{ $booking->total_case_variance > 0 ? 'text-blue-600' : 'text-red-600' }}">
                              ({{ $booking->total_case_variance > 0 ? '+' : '' }}{{ number_format($booking->total_case_variance) }})
                            </span>
                          @endif
                        </div>
                      @endif
                    </div>
                  </div>
                  <div>
                    <label class="text-sm font-medium text-gray-600">Total Pallets</label>
                    <div class="flex items-center space-x-4 mt-1">
                      @if($booking->total_expected_pallets > 0)
                        <div>
                          <span class="text-sm text-gray-500">Expected:</span>
                          <span class="text-xl font-bold">{{ number_format($booking->total_expected_pallets) }}</span>
                        </div>
                      @endif
                      @if($booking->total_actual_pallets > 0)
                        @if($booking->total_expected_pallets > 0)
                          <div class="text-gray-400">→</div>
                        @endif
                        <div>
                          <span class="text-sm text-gray-500">Actual:</span>
                          <span class="text-xl font-bold {{ $booking->total_pallet_variance == 0 ? 'text-green-600' : ($booking->total_pallet_variance > 0 ? 'text-blue-600' : 'text-red-600') }}">
                            {{ number_format($booking->total_actual_pallets) }}
                          </span>
                          @if($booking->total_expected_pallets > 0 && $booking->total_pallet_variance != 0)
                            <span class="text-lg {{ $booking->total_pallet_variance > 0 ? 'text-blue-600' : 'text-red-600' }}">
                              ({{ $booking->total_pallet_variance > 0 ? '+' : '' }}{{ number_format($booking->total_pallet_variance) }})
                            </span>
                          @endif
                        </div>
                      @endif
                    </div>
                  </div>
                </div>
              </div>
            @endif
          </div>
        @else
          <div class="text-center py-8 text-gray-500">
            <p>No PO numbers recorded for this booking</p>
          </div>
        @endif
        {{-- Additional Load Information --}}
        <div class="border-t pt-4 mt-6 space-y-3">
          @if($booking->container_size)
            <div>
              <label class="text-sm font-medium text-gray-600">Container Size</label>
              <p class="text-lg">{{ $booking->container_size }}ft</p>
            </div>
          @endif
          @if($booking->load_type)
            <div>
              <label class="text-sm font-medium text-gray-600">Load Type</label>
              <p class="text-lg">{{ $booking->load_type }}</p>
            </div>
          @endif
          @if($booking->hazmat)
            <div>
              <label class="text-sm font-medium text-gray-600">Special Requirements</label>
              <p class="text-lg text-red-600 font-semibold">⚠️ Hazardous Materials (HAZMAT)</p>
            </div>
          @endif
          @if($booking->temperature_requirements)
            <div>
              <label class="text-sm font-medium text-gray-600">Temperature Requirements</label>
              <p class="text-lg">{{ $booking->temperature_requirements }}</p>
            </div>
          @endif
        </div>
      </div>
      {{-- Transportation Details --}}
      @if($booking->vehicle_registration || $booking->container_number || $booking->carrier_company || $booking->trailerType)
        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-xl font-semibold mb-4 text-gray-800">🚛 Transportation & Vehicle Details</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Vehicle Information --}}
            <div class="space-y-3">
              <h4 class="font-medium text-gray-800">Vehicle Information</h4>
              @if($booking->vehicle_registration)
                <div>
                  <label class="text-sm font-medium text-gray-600">Vehicle Registration</label>
                  <p class="text-lg font-mono bg-gray-100 px-2 py-1 rounded">{{ $booking->vehicle_registration }}</p>
                </div>
              @endif
              @if($booking->carrier_company)
                <div>
                  <label class="text-sm font-medium text-gray-600">Carrier Company</label>
                  <p class="text-lg">{{ $booking->carrier_company }}</p>
                </div>
              @endif
              @if($booking->carrier_contact)
                <div>
                  <label class="text-sm font-medium text-gray-600">Carrier Contact</label>
                  <p class="text-lg">{{ $booking->carrier_contact }}</p>
                </div>
              @endif
            </div>
            {{-- Container/Trailer Information --}}
            <div class="space-y-3">
              <h4 class="font-medium text-gray-800">Container/Trailer Details</h4>
              @if($booking->container_number)
                <div>
                  <label class="text-sm font-medium text-gray-600">Container/Trailer Number</label>
                  <p class="text-lg font-mono bg-gray-100 px-2 py-1 rounded">{{ $booking->container_number }}</p>
                </div>
              @endif
              @if($booking->trailerType)
                <div>
                  <label class="text-sm font-medium text-gray-600">Trailer Type</label>
                  <p class="text-lg">{{ $booking->trailerType->name }}</p>
                  @if($booking->trailerType->description)
                    <p class="text-sm text-gray-500">{{ $booking->trailerType->description }}</p>
                  @endif
                </div>
              @endif
              @if($booking->container_size)
                <div>
                  <label class="text-sm font-medium text-gray-600">Container Size</label>
                  <p class="text-lg">{{ $booking->container_size }}ft</p>
                </div>
              @endif
            </div>
          </div>
          {{-- Additional Transportation Info --}}
          <div class="border-t mt-6 pt-4 space-y-3">
            @if($booking->gate_number)
              <div>
                <label class="text-sm font-medium text-gray-600">Gate Number</label>
                <p class="text-lg">{{ $booking->gate_number }}</p>
              </div>
            @endif
            @if($booking->manifest_number)
              <div>
                <label class="text-sm font-medium text-gray-600">Manifest Number</label>
                <p class="text-lg font-mono">{{ $booking->manifest_number }}</p>
              </div>
            @endif
            @if($booking->estimated_arrival)
              <div>
                <label class="text-sm font-medium text-gray-600">Estimated Arrival</label>
                <p class="text-lg">{{ $booking->estimated_arrival->format('d M Y, H:i') }}</p>
              </div>
            @endif
            @if($booking->waiting_area_location)
              <div>
                <label class="text-sm font-medium text-gray-600">🅿️ Parking Area</label>
                <p class="text-lg">{{ $booking->waiting_area_location }}</p>
              </div>
            @endif
            {{-- Tipping Type Information --}}
            @if($booking->tipping_type)
              <div>
                <label class="text-sm font-medium text-gray-600">Tipping Type</label>
                @php
                  $movement = $booking->movements->first();
                  // Determine actual tipping type based on what happened
                  $actualTippingType = $booking->tipping_type;
                  // Get collection vehicle from custom_fields or movement fields
                  $collectionVehicleUsed = null;
                  if ($movement && $movement->custom_fields && isset($movement->custom_fields['collected_by_vehicle'])) {
                    $collectionVehicleUsed = $movement->custom_fields['collected_by_vehicle'];
                  } elseif ($movement && $movement->collection_unit_registration) {
                    $collectionVehicleUsed = $movement->collection_unit_registration;
                  } elseif ($booking->departure_vehicle_registration) {
                    $collectionVehicleUsed = $booking->departure_vehicle_registration;
                  }
                  // If different collection vehicle, it was definitely a drop operation
                  if ($collectionVehicleUsed && $booking->vehicle_registration && 
                      $collectionVehicleUsed !== $booking->vehicle_registration) {
                    $actualTippingType = 'drop';
                  }
                  // If unit departed but collection happened later (different times), it was a drop
                  if ($movement && $movement->unit_departed_at && $movement->collection_unit_departed_at && 
                      $movement->unit_departed_at != $movement->collection_unit_departed_at) {
                    $actualTippingType = 'drop';
                  }
                  // If unit departed but trailer was collected later (booking level)
                  if ($movement && $movement->unit_departed_at && $booking->trailer_collected_at && 
                      $movement->unit_departed_at != $booking->trailer_collected_at) {
                    $actualTippingType = 'drop';
                  }
                  // If different carriers were involved (delivery vs collection)
                  if ($movement && $movement->custom_fields && isset($movement->custom_fields['collection_carrier']) && 
                      $booking->carrier_company && 
                      $movement->custom_fields['collection_carrier'] !== $booking->carrier_company) {
                    $actualTippingType = 'drop';
                  }
                @endphp
                <p class="text-lg">
                  @if($actualTippingType === 'live_tip')
                    🚛 Live Tip (Vehicle stays attached)
                  @elseif($actualTippingType === 'drop')
                    📍 Drop Trailer (Vehicle leaves trailer)
                    @if($actualTippingType !== $booking->tipping_type)
                      <span class="text-sm text-blue-600 ml-2">(Detected from collection data)</span>
                    @endif
                  @else
                    {{ ucwords(str_replace('_', ' ', $actualTippingType)) }}
                  @endif
                </p>
              </div>
            @endif
          </div>
          {{-- Collection Information --}}
          @php
            $movement = $booking->movements->first();
            $hasCollectionInfo = $movement && ($movement->collection_unit_arrived_at || $movement->collection_unit_registration || $movement->collection_driver_name || $booking->departure_vehicle_registration);
            $isCollected = $movement && in_array($movement->current_status, ['departed', 'trailer_collected']);
            $hasCollectionTimes = $movement && ($movement->collection_unit_departed_at || $booking->trailer_collected_at);
            // Check custom_fields for collection data from empty-unit-collection form
            $hasCustomFieldData = $movement && $movement->custom_fields && 
                                  (isset($movement->custom_fields['collected_by_vehicle']) || 
                                   isset($movement->custom_fields['collection_carrier']));
            // Show collection section if we have any collection information OR the trailer was actually collected
            $showCollectionSection = $hasCollectionInfo || $isCollected || $hasCollectionTimes || $hasCustomFieldData;
          @endphp
          @if($showCollectionSection)
            <div class="border-t mt-6 pt-6">
              <h4 class="font-medium text-gray-800 mb-4 flex items-center">
                <span class="mr-2">🚚</span>
                Collection Details
                @php
                  // Determine actual collection status based on data available
                  $actualCollectionStatus = 'recorded'; // Default: just recorded in system
                  $statusBadge = '';
                  if ($movement) {
                    // If collection unit actually arrived on site (not just system entry)
                    if ($movement->collection_unit_arrived_at && $movement->collection_unit_departed_at) {
                      $actualCollectionStatus = 'collected';
                      $statusBadge = '<span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">✅ Physically Collected</span>';
                    }
                    // If only system entry of collection (no actual arrival time)
                    elseif (($booking->trailer_collected_at || $movement->collection_unit_departed_at) && !$movement->collection_unit_arrived_at) {
                      $actualCollectionStatus = 'recorded';
                      $statusBadge = '<span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">📝 Collection Recorded</span>';
                    }
                    // Still awaiting collection
                    elseif (in_array($movement->current_status, ['empty', 'awaiting_collection'])) {
                      $actualCollectionStatus = 'awaiting';
                      $statusBadge = '<span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">📍 Awaiting Collection</span>';
                    }
                  }
                @endphp
                {!! $statusBadge !!}
              </h4>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Collection Vehicle Information --}}
                <div class="space-y-3">
                  <h5 class="text-sm font-semibold text-gray-700">Collection Vehicle</h5>
                  @php
                    $collectionVehicle = null;
                    $collectionCarrier = null;
                    // Check custom_fields first (from empty-unit-collection form)
                    if ($movement && $movement->custom_fields) {
                      $collectionVehicle = $movement->custom_fields['collected_by_vehicle'] ?? null;
                      $collectionCarrier = $movement->custom_fields['collection_carrier'] ?? null;
                    }
                    // Fallback to movement fields
                    if (!$collectionVehicle && $movement && $movement->collection_unit_registration) {
                      $collectionVehicle = $movement->collection_unit_registration;
                    }
                    // Fallback to booking departure vehicle
                    if (!$collectionVehicle && $booking->departure_vehicle_registration) {
                      $collectionVehicle = $booking->departure_vehicle_registration;
                    }
                  @endphp
                  @if($collectionVehicle)
                    <div>
                      <label class="text-sm font-medium text-gray-600">Vehicle Registration</label>
                      <p class="text-lg font-mono bg-gray-100 px-2 py-1 rounded">{{ $collectionVehicle }}</p>
                    </div>
                  @else
                    <div>
                      <label class="text-sm font-medium text-gray-600">Vehicle Registration</label>
                      <p class="text-lg text-gray-500 italic">Not recorded</p>
                    </div>
                  @endif
                  @if($collectionCarrier)
                    <div>
                      <label class="text-sm font-medium text-gray-600">Collection Carrier</label>
                      <p class="text-lg">{{ $collectionCarrier }}</p>
                    </div>
                  @elseif($booking->carrier_company)
                    <div>
                      <label class="text-sm font-medium text-gray-600">Collection Carrier</label>
                      <p class="text-lg">{{ $booking->carrier_company }}</p>
                    </div>
                  @endif
                  @if($movement && $movement->collection_driver_name)
                    <div>
                      <label class="text-sm font-medium text-gray-600">Driver Name</label>
                      <p class="text-lg">{{ $movement->collection_driver_name }}</p>
                    </div>
                  @endif
                  @if($movement && $movement->collection_driver_phone)
                    <div>
                      <label class="text-sm font-medium text-gray-600">Driver Phone</label>
                      <p class="text-lg font-mono">{{ $movement->collection_driver_phone }}</p>
                    </div>
                  @endif
                </div>
              </div>
              {{-- Collection Notes --}}
              @if(($movement && $movement->collection_notes) || $booking->departure_notes)
                <div class="mt-4 pt-3 border-t border-gray-100">
                  <label class="text-sm font-medium text-gray-600">Collection Notes</label>
                  <div class="text-sm text-gray-700 mt-1 bg-gray-50 p-3 rounded">
                    @if($movement && $movement->collection_notes)
                      {{ $movement->collection_notes }}
                    @elseif($booking->departure_notes)
                      {{ $booking->departure_notes }}
                    @endif
                  </div>
                </div>
              @endif
            </div>
          @endif
        </div>
      @endif
      {{-- Additional Information --}}
      @if($booking->special_instructions || $booking->notes)
        <div class="bg-white p-6 rounded-lg shadow">
          <h3 class="text-xl font-semibold mb-4 text-gray-800">📝 Additional Information</h3>
          <div class="space-y-3">
            @if($booking->special_instructions)
              <div>
                <label class="text-sm font-medium text-gray-600">Special Instructions</label>
                <p class="text-base leading-relaxed">{{ $booking->special_instructions }}</p>
              </div>
            @endif
            @if($booking->notes)
              <div>
                <label class="text-sm font-medium text-gray-600">Notes</label>
                <p class="text-base leading-relaxed">{{ $booking->notes }}</p>
              </div>
            @endif
          </div>
        </div>
      @endif
      {{-- Arrival Information (if arrived) --}}
      @if($hasArrived)
        <div class="bg-green-50 p-6 rounded-lg border border-green-200">
          <h3 class="text-xl font-semibold mb-4 text-green-800">✅ Arrival Information</h3>
          <div class="space-y-3">
            <div>
              <label class="text-sm font-medium text-gray-600">Arrived At</label>
              <p class="text-lg">{{ $booking->arrived_at->format('l, d F Y - H:i') }}</p>
              <p class="text-sm text-gray-600">Slot: {{ $booking->slot->start_at->format('H:i') }}</p>
            </div>
            @if($booking->departed_at)
              <div>
                <label class="text-sm font-medium text-gray-600">Departed At</label>
                <p class="text-lg">{{ $booking->departed_at->format('l, d F Y - H:i') }}</p>
              </div>
              <div>
                <label class="text-sm font-medium text-gray-600">Time On-Site</label>
                <div class="flex items-center space-x-2">
                  <p class="text-lg">{{ $booking->arrived_at->diffForHumans($booking->departed_at, true) }}</p>
                  @php
                    $slotStart = $booking->slot->start_at;
                    $arrivalTime = $booking->arrived_at;
                    $isLate = $arrivalTime->gt($slotStart);
                    $isEarly = $arrivalTime->lt($slotStart);
                    $timingText = '';
                    if ($isLate || $isEarly) {
                      $totalMinutes = abs($arrivalTime->diffInMinutes($slotStart));
                      if ($totalMinutes >= 1440) {
                        $days = floor($totalMinutes / 1440);
                        $remainingMinutes = $totalMinutes % 1440;
                        $hours = floor($remainingMinutes / 60);
                        $minutes = $remainingMinutes % 60;
                        $timingText .= $days . 'd ';
                        if ($hours > 0) $timingText .= $hours . 'h ';
                        if ($minutes > 0) $timingText .= $minutes . 'm';
                      } elseif ($totalMinutes >= 60) {
                        $hours = floor($totalMinutes / 60);
                        $minutes = $totalMinutes % 60;
                        $timingText .= $hours . 'h ';
                        if ($minutes > 0) $timingText .= $minutes . 'm';
                      } else {
                        $timingText .= $totalMinutes . 'm';
                      }
                      $timingText = trim($timingText);
                    }
                  @endphp
                  @if($isLate)
                    <span class="text-sm text-red-600 bg-red-50 px-2 py-1 rounded">
                      🚨 {{ $timingText }} late
                    </span>
                  @elseif($isEarly)
                    <span class="text-sm text-green-600 bg-green-50 px-2 py-1 rounded">
                      ✅ {{ $timingText }} early
                    </span>
                  @else
                    <span class="text-sm text-green-600 bg-green-50 px-2 py-1 rounded">
                      ✅ On time
                    </span>
                  @endif
                </div>
              </div>
            @else
              @php
                $movement = $booking->movements->first();
                $collectionCompleted = $movement && ($movement->collection_unit_departed_at || $booking->trailer_collected_at);
                $unitDeparted = $movement && $movement->unit_departed_at;
              @endphp
              @if($collectionCompleted)
                <div class="p-3 bg-green-100 rounded border border-green-300">
                  <p class="text-green-800 font-medium">✅ Collection Complete</p>
                  @if($movement->collection_unit_departed_at)
                    <p class="text-xs text-green-600">Physically collected: {{ $movement->collection_unit_departed_at->format('H:i') }}</p>
                  @elseif($booking->trailer_collected_at)
                    <p class="text-xs text-green-600">Recorded: {{ $booking->trailer_collected_at->format('H:i') }}</p>
                  @endif
                </div>
              @elseif($unitDeparted)
                <div class="p-3 bg-purple-100 rounded border border-purple-300">
                  <p class="text-purple-800 font-medium">🚚 Unit Departed</p>
                  <p class="text-xs text-purple-600">Trailer awaiting collection since {{ $movement->unit_departed_at->format('H:i') }}</p>
                </div>
              @else
                <div class="p-3 bg-blue-100 rounded border border-blue-300">
                  <p class="text-blue-800 font-medium">🚛 Currently on-site</p>
                </div>
              @endif
            @endif
          </div>
        </div>
      @endif
      {{-- Tipping Progress & Status Summary (if arrived) --}}
      @if($hasArrived)
        <div class="bg-gradient-to-r from-orange-50 to-blue-50 p-6 rounded-lg border border-orange-200 col-span-2">
          <h3 class="text-xl font-semibold mb-4 text-gray-800">🚛 Tipping Status & Progress</h3>
          {{-- Status Overview Cards --}}
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            {{-- Current Status --}}
            <div class="bg-white p-4 rounded-lg border shadow-sm">
              <div class="text-xs text-gray-500 mb-1">Current Status</div>
              <div class="text-lg">{!! $booking->tipping_status_badge !!}</div>
            </div>
            {{-- Current Location --}}
            <div class="bg-white p-4 rounded-lg border shadow-sm">
              <div class="text-xs text-gray-500 mb-1">
                @if($booking->departed_at || $booking->tipping_status === 'departed')
                  Last Location
                @else
                  Current Location
                @endif
              </div>
              <div class="text-sm font-semibold">
                @if($booking->tippingBay)
                  🏭 {{ $booking->tippingBay->name }}
                @elseif($booking->tippingLocation)
                  📍 {{ $booking->tippingLocation->name }}
                @else
                  <span class="text-gray-400">Not assigned</span>
                @endif
              </div>
            </div>
            {{-- Time on Site --}}
            <div class="bg-white p-4 rounded-lg border shadow-sm">
              <div class="text-xs text-gray-500 mb-1">Time on Site</div>
              <div class="text-sm font-semibold {{ $booking->arrived_at->diffInHours() > 4 ? 'text-orange-600' : 'text-gray-800' }}">
                {{ $booking->arrived_at->diffForHumans(null, true) }}
              </div>
              @php
                // Use ArrivalTimeSetting to determine proper timing status
                $statusDetails = \App\Models\ArrivalTimeSetting::getArrivalStatusDetails(
                  $booking->slot->start_at,
                  $booking->arrived_at,
                  $booking->customer_id,
                  $booking->slot->depot_id
                );
                $totalMinutes = $statusDetails['difference_minutes'];
                $status = $statusDetails['status'];
                // Format the timing text
                $timingText = '';
                if ($totalMinutes >= 1440) {
                  $days = floor($totalMinutes / 1440);
                  $remainingMinutes = $totalMinutes % 1440;
                  $hours = floor($remainingMinutes / 60);
                  $minutes = $remainingMinutes % 60;
                  $timingText .= $days . 'd ';
                  if ($hours > 0) $timingText .= $hours . 'h ';
                  if ($minutes > 0) $timingText .= $minutes . 'm';
                } elseif ($totalMinutes >= 60) {
                  $hours = floor($totalMinutes / 60);
                  $minutes = $totalMinutes % 60;
                  $timingText .= $hours . 'h ';
                  if ($minutes > 0) $timingText .= $minutes . 'm';
                } else {
                  $timingText .= $totalMinutes . 'm';
                }
                $timingText = trim($timingText);
              @endphp
              @if($status === \App\Models\ArrivalTimeSetting::STATUS_LATE)
                <div class="text-xs text-red-600 mt-1">
                  🚨 {{ $timingText }} late
                </div>
              @elseif($status === \App\Models\ArrivalTimeSetting::STATUS_EARLY)
                <div class="text-xs text-yellow-600 font-bold mt-1">
                  ✅ {{ $timingText }} early
                </div>
              @else
                <div class="text-xs text-green-600 mt-1">
                  ✅ On time
                </div>
              @endif
            </div>
            {{-- Tipping Performance --}}
            <div class="bg-white p-4 rounded-lg border shadow-sm">
              <div class="text-xs text-gray-500 mb-1">Tipping Performance</div>
              <div class="text-sm font-semibold">
                @php
                  // Calculate tipping performance with sophisticated rules
                  $slotStart = $booking->slot->start_at;
                  $slotEnd = $booking->slot->end_at;
                  $arrivalTime = $booking->arrived_at;
                  $actualTipStart = $booking->tipping_started_at;
                  $actualTipEnd = $booking->tipping_completed_at;
                  // Check if trailer was dropped on site (always ontime but show duration)
                  $movement = $booking->movements()->first();
                  $isDroppedTrailer = $movement && in_array($movement->current_status, ['trailer_dropped', 'empty']) && $actualTipEnd;
                  if ($isDroppedTrailer) {
                    $performanceStatus = 'ontime_tip';
                    $performanceText = '📍 Dropped Trailer - Always Ontime';
                    $performanceClass = 'text-blue-600 bg-blue-50 px-2 py-1 rounded text-xs';
                    // Show actual tipping duration for dropped trailers
                    if ($booking->actual_tipping_duration) {
                      $performanceText .= ' (' . $booking->actual_tipping_duration . ' mins)';
                    }
                  } elseif ($actualTipEnd && $arrivalTime) {
                    // Calculate extended deadline based on arrival delay
                    // Handle case where arrival is after slot start time (considering dates)
                    if ($arrivalTime->gt($slotStart)) {
                      $arrivalDelay = $slotStart->diffInMinutes($arrivalTime);
                    } else {
                      $arrivalDelay = 0; // Early or on-time
                    }
                    $adjustedDeadline = $slotEnd->copy()->addMinutes($arrivalDelay); // Extend deadline by delay
                    // Compare actual tipping completion to adjusted deadline
                    $onTime = $actualTipEnd->lte($adjustedDeadline);
                    $performanceStatus = $onTime ? 'ontime' : 'late';
                    if ($arrivalDelay > 0) {
                      // Late arrival - show extended time calculation
                      $delayHours = floor($arrivalDelay / 60);
                      $delayMins = $arrivalDelay % 60;
                      $delayText = $delayHours > 0 ? "{$delayHours}h {$delayMins}m" : "{$delayMins}m";
                      $performanceText = $onTime 
                        ? "✅ Ontime (Extended +{$delayText})" 
                        : "🚨 Late (Even with +{$delayText} extension)";
                      $performanceClass = $onTime ? 'text-green-600' : 'text-red-600';
                    } else {
                      // Early/on-time arrival - distinguish between early and exactly on time
                      if ($arrivalTime->lt($slotStart)) {
                        // Early arrival
                        $performanceText = $onTime ? '🟡 Early (Ontime)' : '🚨 Late';
                        $performanceClass = $onTime ? 'text-orange-600' : 'text-red-600';
                      } else {
                        // Exactly on time
                        $performanceText = $onTime ? '✅ Ontime' : '🚨 Late';
                        $performanceClass = $onTime ? 'text-green-600' : 'text-red-600';
                      }
                    }
                    // Override class if not set above
                    if (!isset($performanceClass)) {
                      $performanceClass = $onTime ? 'text-green-600' : 'text-red-600';
                    }
                  } elseif ($actualTipStart) {
                    $performanceText = $booking->actual_tipping_duration . ' mins (ongoing)';
                    $performanceClass = 'text-orange-600';
                  } else {
                    $performanceText = 'Not started';
                    $performanceClass = 'text-gray-400';
                  }
                @endphp
                <span class="{{ $performanceClass ?? 'text-gray-400' }}">
                  {{ $performanceText ?? 'Not started' }}
                </span>
@if($booking->actual_tipping_duration)
                  <div class="text-xs text-gray-500 mt-1">
                    @if($isDroppedTrailer)
                      Tipping Duration: {{ $booking->actual_tipping_duration }} minutes
                    @else
                      Duration: {{ $booking->actual_tipping_duration }} minutes
                    @endif
                  </div>
                @endif
                @if(!$isDroppedTrailer && $arrivalTime && $actualTipEnd)
                  <div class="text-xs text-gray-400 mt-1">
                    @php
                      $originalDeadline = $slotEnd;
                      $arrivalDelayMins = $arrivalTime->gt($slotStart) ? $arrivalTime->diffInMinutes($slotStart) : 0;
                      $adjustedDeadline = $slotEnd->copy()->addMinutes($arrivalDelayMins);
                    @endphp
                    Slot: {{ $slotStart->format('H:i') }}-{{ $originalDeadline->format('H:i') }}
                    @if($arrivalDelayMins > 0)
                      | Arrived: {{ $arrivalTime->format('H:i') }} (+{{ floor($arrivalDelayMins/60) ? floor($arrivalDelayMins/60).'h ' : '' }}{{ $arrivalDelayMins%60 }}m)
                      | Extended to: {{ $adjustedDeadline->format('H:i') }}
                    @endif
                    | Completed: {{ $actualTipEnd->format('H:i') }}
                  </div>
                @endif
              </div>
            </div>
          </div>
          {{-- Progress Timeline --}}
          <div class="bg-white p-4 rounded-lg border shadow-sm">
            <h4 class="font-medium text-gray-800 mb-3 flex items-center">
              <span class="mr-2">📋</span>
              Progress Timeline
            </h4>
            <div class="space-y-3">
              {{-- Arrived --}}
              <div class="flex items-center">
                <div class="flex items-center justify-center w-8 h-8 bg-green-100 text-green-600 rounded-full text-sm font-semibold mr-4">
                  ✓
                </div>
                <div class="flex-1">
                  <div class="text-sm font-medium">Vehicle Arrived</div>
                  <div class="text-xs text-gray-500">{{ $booking->arrived_at->format('M j, H:i') }}</div>
                </div>
              </div>
              {{-- Trailer Dropped --}}
              <div class="flex items-center {{ $booking->trailer_dropped_at ? '' : 'opacity-50' }}">
                <div class="flex items-center justify-center w-8 h-8 {{ $booking->trailer_dropped_at ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-400' }} rounded-full text-sm font-semibold mr-4">
                  {{ $booking->trailer_dropped_at ? '✓' : '2' }}
                </div>
                <div class="flex-1">
                  <div class="text-sm font-medium">Trailer Dropped</div>
                  <div class="text-xs text-gray-500">
                    @if($booking->trailer_dropped_at)
                      {{ $booking->trailer_dropped_at->format('M j, H:i') }}
                      @if($booking->tippingLocation)
                        at {{ $booking->tippingLocation->name }}
                      @endif
                    @else
                      Pending
                    @endif
                  </div>
                </div>
                @if($booking->tipping_status === 'arrived' && !$booking->trailer_dropped_at)
                  <div class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Next Step</div>
                @endif
              </div>
              {{-- Moved to Bay --}}
              <div class="flex items-center {{ $booking->moved_to_bay_at ? '' : 'opacity-50' }}">
                <div class="flex items-center justify-center w-8 h-8 {{ $booking->moved_to_bay_at ? 'bg-yellow-100 text-yellow-600' : 'bg-gray-100 text-gray-400' }} rounded-full text-sm font-semibold mr-4">
                  {{ $booking->moved_to_bay_at ? '✓' : '3' }}
                </div>
                <div class="flex-1">
                  <div class="text-sm font-medium">Moved to Tipping Bay</div>
                  <div class="text-xs text-gray-500">
                    @if($booking->moved_to_bay_at)
                      {{ $booking->moved_to_bay_at->format('M j, H:i') }}
                      @if($booking->tippingBay)
                        - {{ $booking->tippingBay->name }}
                      @endif
                    @else
                      Pending
                    @endif
                  </div>
                </div>
                @if($booking->tipping_status === 'trailer_dropped')
                  <div class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Next Step</div>
                @endif
              </div>
              {{-- Tipping Started --}}
              <div class="flex items-center {{ $booking->tipping_started_at ? '' : 'opacity-50' }}">
                <div class="flex items-center justify-center w-8 h-8 {{ $booking->tipping_started_at ? 'bg-orange-100 text-orange-600' : 'bg-gray-100 text-gray-400' }} rounded-full text-sm font-semibold mr-4">
                  {{ $booking->tipping_started_at ? '✓' : '4' }}
                </div>
                <div class="flex-1">
                  <div class="text-sm font-medium">Tipping Started</div>
                  <div class="text-xs text-gray-500">
                    @if($booking->tipping_started_at)
                      {{ $booking->tipping_started_at->format('M j, H:i') }}
                    @else
                      Pending
                    @endif
                  </div>
                </div>
                @if($booking->tipping_status === 'at_bay')
                  <div class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Next Step</div>
                @endif
              </div>
              {{-- Tipping Completed --}}
              <div class="flex items-center {{ $booking->tipping_completed_at ? '' : 'opacity-50' }}">
                <div class="flex items-center justify-center w-8 h-8 {{ $booking->tipping_completed_at ? 'bg-purple-100 text-purple-600' : 'bg-gray-100 text-gray-400' }} rounded-full text-sm font-semibold mr-4">
                  {{ $booking->tipping_completed_at ? '✓' : '5' }}
                </div>
                <div class="flex-1">
                  <div class="text-sm font-medium">Tipping Completed</div>
                  <div class="text-xs text-gray-500">
                    @if($booking->tipping_completed_at)
                      {{ $booking->tipping_completed_at->format('M j, H:i') }}
                      @if($booking->actual_tipping_duration)
                        ({{ $booking->actual_tipping_duration }} minutes)
                      @endif
                    @else
                      Pending
                    @endif
                  </div>
                </div>
                @if($booking->tipping_status === 'unloading')
                  <div class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded">Next Step</div>
                @endif
              </div>
              {{-- Ready for Collection / Departed --}}
              <div class="flex items-center {{ in_array($booking->tipping_status, ['empty', 'departed']) ? '' : 'opacity-50' }}">
                <div class="flex items-center justify-center w-8 h-8 {{ in_array($booking->tipping_status, ['empty', 'departed']) ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-gray-400' }} rounded-full text-sm font-semibold mr-4">
                  {{ in_array($booking->tipping_status, ['empty', 'departed']) ? '✓' : '6' }}
                </div>
                <div class="flex-1">
                  <div class="text-sm font-medium">
                    @if($booking->tipping_status === 'departed')
                      Departed from Site
                    @else
                      Ready for Collection
                    @endif
                  </div>
                  <div class="text-xs text-gray-500">
                    @if($booking->tipping_status === 'departed')
                      @if($booking->trailer_departed_at)
                        {{ $booking->trailer_departed_at->format('M j, H:i') }}
                        @if($booking->trailer_left_on_site)
                          (trailer left on site)
                        @else
                          (vehicle & trailer departed)
                        @endif
                      @else
                        Departed
                      @endif
                    @elseif($booking->tipping_status === 'empty')
                      Empty trailer awaiting collection
                    @else
                      Pending completion
                    @endif
                  </div>
                </div>
                @if(in_array($booking->tipping_status, ['empty', 'departed']))
                  <div class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">Complete</div>
                @endif
              </div>
            </div>
          </div>
          {{-- Quick Actions & Notes --}}
          @if($booking->tipping_notes || $booking->tipping_status !== 'not_started')
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
              @if($booking->tipping_notes)
                <div class="bg-white p-4 rounded-lg border shadow-sm">
                  <h5 class="font-medium text-gray-800 mb-2 flex items-center">
                    <span class="mr-2">📝</span>
                    Tipping Notes
                  </h5>
                  <div class="text-sm text-gray-700 whitespace-pre-line">{{ $booking->tipping_notes }}</div>
                </div>
              @endif
              @if($booking->tipping_status !== 'departed')
                <div class="bg-white p-4 rounded-lg border shadow-sm">
                  <h5 class="font-medium text-gray-800 mb-2 flex items-center">
                    <span class="mr-2">🚛</span>
                    Tipping Operations
                  </h5>
                  <div class="text-sm text-gray-600 mb-3">
                    All tipping operations are managed through the centralized workflow interface.
                  </div>
                  <a href="{{ route('app.tipping-workflow.show', $booking) }}" 
                     class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700 transition-colors">
                    🚛 Manage Tipping Workflow
                  </a>
                </div>
              @endif
            </div>
          @endif
          {{-- Complete Operational Timeline --}}
          @if($movement)
            <div class="mt-6 space-y-4">
              <h4 class="text-lg font-semibold text-green-700">📊 Complete Operational Timeline</h4>
              {{-- Vehicle Arrival & Duration --}}
              @if($booking->arrived_at)
                <div class="bg-blue-50 p-4 rounded-lg border-l-4 border-blue-500">
                  <div class="flex justify-between items-start">
                    <div class="flex-1">
                      <label class="text-sm font-medium text-blue-700">🚛 Vehicle Arrival</label>
                      <p class="text-lg font-mono">{{ $booking->arrived_at->format('l, d F Y - H:i') }}</p>
                      <p class="text-sm text-blue-600">Scheduled: {{ $booking->slot->start_at->format('H:i') }}</p>
                    </div>
                    @if($movement && $movement->unit_departed_at)
                      <div class="text-right">
                        <span class="text-sm text-blue-600">On-site Duration</span>
                        <p class="font-semibold text-blue-700 text-lg">{{ $booking->arrived_at->diffForHumans($movement->unit_departed_at, true) }}</p>
                      </div>
                    @endif
                  </div>
                </div>
              @endif
              {{-- Tipping Operations --}}
              @if($movement && $movement->unloading_started_at)
                <div class="bg-orange-50 p-4 rounded-lg border-l-4 border-orange-500">
                  <div class="flex justify-between items-start">
                    <div class="flex-1">
                      <label class="text-sm font-medium text-orange-700">🏗️ Tipping Operations</label>
                      <div class="text-lg">
                        <div class="font-mono">Start: {{ $movement->unloading_started_at->format('l, d F Y - H:i') }}</div>
                        @if($movement->unloading_completed_at)
                          <div class="font-mono">End: {{ $movement->unloading_completed_at->format('l, d F Y - H:i') }}</div>
                        @else
                          <div class="text-orange-600">End: In Progress</div>
                        @endif
                      </div>
                    </div>
                    @if($movement && $movement->unloading_started_at && $movement->unloading_completed_at)
                      <div class="text-right">
                        <span class="text-sm text-orange-600">Tipping Duration</span>
                        <p class="font-semibold text-orange-700 text-lg">{{ $movement->unloading_started_at->diffForHumans($movement->unloading_completed_at, true) }}</p>
                      </div>
                    @endif
                  </div>
                </div>
              @endif
              {{-- Unit Departure (Drop-off) --}}
              @if($movement && $movement->unit_departed_at)
                <div class="bg-purple-50 p-4 rounded-lg border-l-4 border-purple-500">
                  <div class="flex justify-between items-start">
                    <div class="flex-1">
                      <label class="text-sm font-medium text-purple-700">🚚 Original Unit Departed</label>
                      <p class="text-lg font-mono">{{ $movement->unit_departed_at->format('l, d F Y - H:i') }}</p>
                      <p class="text-sm text-purple-600">Trailer dropped on-site</p>
                    </div>
                    @if($booking->arrived_at)
                      <div class="text-right">
                        <span class="text-sm text-purple-600">Total On-site Time</span>
                        <p class="font-semibold text-purple-700 text-lg">{{ $booking->arrived_at->diffForHumans($movement->unit_departed_at, true) }}</p>
                      </div>
                    @endif
                  </div>
                </div>
              @endif
              {{-- Collection Information --}}
              @if($movement && ($movement->collection_unit_arrived_at || $movement->collection_unit_departed_at || $booking->trailer_collected_at))
                <div class="bg-green-50 p-4 rounded-lg border-l-4 border-green-500">
                  <div class="space-y-3">
                    <label class="text-sm font-medium text-green-700">🚛 Collection Operations</label>
                    @if($movement->collection_unit_arrived_at)
                      <div class="flex justify-between items-center">
                        <p class="text-sm font-medium">Collection Vehicle Arrived:</p>
                        <p class="font-mono font-semibold">{{ $movement->collection_unit_arrived_at->format('l, d F Y - H:i') }}</p>
                      </div>
                    @endif
                    @if($movement->collection_unit_departed_at)
                      <div class="flex justify-between items-center">
                        <p class="text-sm font-medium">Collection Completed:</p>
                        <p class="font-mono font-semibold">{{ $movement->collection_unit_departed_at->format('l, d F Y - H:i') }}</p>
                      </div>
                    @elseif($booking->trailer_collected_at)
                      <div class="flex justify-between items-center">
                        <p class="text-sm font-medium">📝 Collection Recorded:</p>
                        <div class="text-right">
                          <p class="font-mono font-semibold">{{ $booking->trailer_collected_at->format('l, d F Y - H:i') }}</p>
                          <span class="text-xs bg-orange-100 text-orange-700 px-2 py-1 rounded">System Entry Only</span>
                        </div>
                      </div>
                    @endif
                    {{-- Collection Duration --}}
                    @if($movement->collection_unit_arrived_at && $movement->collection_unit_departed_at)
                      <div class="flex justify-between items-center border-t pt-2 border-green-200">
                        <span class="text-sm text-green-600 font-medium">Collection Duration:</span>
                        <p class="font-semibold text-green-700 text-lg">{{ $movement->collection_unit_arrived_at->diffForHumans($movement->collection_unit_departed_at, true) }}</p>
                      </div>
                    @endif
                    {{-- Time Between Drop and Collection --}}
                    @if($movement->unit_departed_at && ($movement->collection_unit_departed_at || $booking->trailer_collected_at))
                      @php
                        $collectionTime = $movement->collection_unit_departed_at ?? $booking->trailer_collected_at;
                      @endphp
                      <div class="flex justify-between items-center border-t pt-2 border-green-200">
                        <span class="text-sm text-green-600 font-medium">Trailer Sat On-site:</span>
                        <p class="font-semibold text-green-700 text-lg">{{ $movement->unit_departed_at->diffForHumans($collectionTime, true) }}</p>
                      </div>
                    @endif
                  </div>
                </div>
              @endif
              {{-- Summary --}}
              @if($booking->arrived_at && ($movement->collection_unit_departed_at || $booking->trailer_collected_at))
                @php
                  $finalTime = $movement->collection_unit_departed_at ?? $booking->trailer_collected_at;
                @endphp
                <div class="bg-gray-100 p-4 rounded-lg border border-gray-300">
                  <div class="flex justify-between items-center">
                    <div class="flex-1">
                      <label class="text-sm font-medium text-gray-700">📈 Total Operation Time</label>
                      <p class="text-sm text-gray-600">From initial arrival to final collection</p>
                    </div>
                    <div class="text-right">
                      <p class="text-2xl font-bold text-gray-800">{{ $booking->arrived_at->diffForHumans($finalTime, true) }}</p>
                      <p class="text-sm text-gray-500">{{ $booking->arrived_at->format('H:i') }} → {{ $finalTime->format('H:i') }}</p>
                    </div>
                  </div>
                </div>
              @endif
            </div>
          @endif
        </div>
      @endif
    </div>
  </div>
  {{-- Cancel Booking Modal --}}
  <div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
      <h3 class="text-lg font-semibold mb-4 text-red-800">Cancel Booking</h3>
      <form id="cancelForm">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Cancellation *</label>
          <textarea id="cancellationReason" required rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500"
                    placeholder="Please provide a reason for cancellation..."></textarea>
        </div>
        <div class="flex justify-end space-x-3">
          <button type="button" onclick="closeCancelModal()"
                  class="px-4 py-2 text-gray-600 border border-gray-300 rounded hover:bg-gray-50">
            Cancel
          </button>
          <button type="submit"
                  class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
            Cancel Booking
          </button>
        </div>
      </form>
    </div>
  </div>
  {{-- Quick Departure Modal --}}
  <div id="departureModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
      <h3 class="text-lg font-semibold mb-4 text-purple-800">🏁 Record Departure</h3>
      <form id="departureForm" method="POST">
        @csrf
        @method('PATCH')
        {{-- Show tipping type if available --}}
        @if($booking->tipping_type)
          <div class="mb-4 p-3 bg-blue-50 rounded-lg">
            <div class="text-sm font-medium text-blue-800">
              Tipping Type: 
              @if($booking->tipping_type === 'live_tip')
                <span class="inline-flex items-center">🚛📦 Live Tip</span>
              @else
                <span class="inline-flex items-center">📦 Drop</span>
              @endif
            </div>
          </div>
        @endif
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Vehicle Departure *</label>
          <div class="space-y-2">
            <label class="flex items-center">
              <input type="radio" name="departure_scenario" value="completed_with_trailer" class="mr-2" checked>
              <span class="text-sm">🚛 Same vehicle & trailer departed together</span>
            </label>
            <label class="flex items-center">
              <input type="radio" name="departure_scenario" value="completed_dropped_trailer" class="mr-2" id="droppedTrailerOption">
              <span class="text-sm">📍 Trailer dropped - vehicle departed solo</span>
            </label>
            <label class="flex items-center">
              <input type="radio" name="departure_scenario" value="trailer_swap" class="mr-2" id="trailerSwapOption">
              <span class="text-sm">🔄 Vehicle collected different trailer</span>
            </label>
            <label class="flex items-center">
              <input type="radio" name="departure_scenario" value="emergency_departure" class="mr-2">
              <span class="text-sm text-red-600">🚨 Emergency departure</span>
            </label>
          </div>
        </div>
        <div id="trailerLocationField" class="mb-4 hidden">
          <label class="block text-sm font-medium text-gray-700 mb-2">Trailer Drop Location</label>
          <input type="text" name="dropped_trailer_location" 
                 class="w-full px-3 py-2 border border-gray-300 rounded-md"
                 placeholder="e.g., PARK1, YARD-A, etc.">
        </div>
        <div id="trailerSwapField" class="mb-4 hidden">
          <label class="block text-sm font-medium text-gray-700 mb-2">Collected Trailer Details</label>
          <input type="text" name="collected_trailer_number"
                 class="w-full px-3 py-2 border border-gray-300 rounded-md"
                 placeholder="Enter trailer/container number">
        </div>

        {{-- Actual Pallet/Case Entry - Only show when tipping workflow is disabled --}}
        @if($booking->poNumbers->count() > 0 && !$booking->tipping_status)
        <div class="mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
          <h4 class="text-sm font-semibold text-yellow-900 mb-3">📦 Enter Actual Quantities *</h4>
          <p class="text-xs text-yellow-700 mb-3">Enter the actual pallets and cases that were delivered/collected:</p>

          @foreach($booking->poNumbers as $poIndex => $po)
            <div class="mb-3 pb-3 @if(!$loop->last) border-b border-yellow-200 @endif">
              <div class="text-xs font-medium text-gray-700 mb-2">PO: {{ $po->po_number }}</div>
              @foreach($po->lines as $lineIndex => $line)
                <div class="mb-2 p-2 bg-white rounded">
                  <div class="text-xs text-gray-600 mb-1">
                    Line {{ $line->line_number }}: {{ $line->sku ?? 'N/A' }} - {{ $line->description ?? 'N/A' }}
                  </div>
                  <div class="grid grid-cols-3 gap-2">
                    <div>
                      <label class="block text-xs text-gray-600">Actual Pallets *</label>
                      <input type="number"
                             name="po_numbers[{{ $poIndex }}][lines][{{ $lineIndex }}][actual_pallets]"
                             value="{{ old('po_numbers.'.$poIndex.'.lines.'.$lineIndex.'.actual_pallets', $line->actual_pallets) }}"
                             class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                             min="0" required>
                      <input type="hidden" name="po_numbers[{{ $poIndex }}][lines][{{ $lineIndex }}][line_id]" value="{{ $line->id }}">
                    </div>
                    <div>
                      <label class="block text-xs text-gray-600">Actual Cases *</label>
                      <input type="number"
                             name="po_numbers[{{ $poIndex }}][lines][{{ $lineIndex }}][actual_cases]"
                             value="{{ old('po_numbers.'.$poIndex.'.lines.'.$lineIndex.'.actual_cases', $line->actual_cases) }}"
                             class="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                             min="0" required>
                    </div>
                    <div>
                      <label class="block text-xs text-gray-600">Pallet Type</label>
                      <select name="po_numbers[{{ $poIndex }}][lines][{{ $lineIndex }}][actual_pallet_type_id]"
                              class="w-full px-2 py-1 border border-gray-300 rounded text-sm">
                        <option value="">-</option>
                        @foreach(\App\Models\PalletType::active()->orderBy('name')->get() as $type)
                          <option value="{{ $type->id }}" {{ $line->actual_pallet_type_id == $type->id ? 'selected' : '' }}>
                            {{ $type->display_name }}
                          </option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  @if($line->expected_pallets || $line->expected_cases)
                    <div class="text-xs text-gray-500 mt-1">
                      Expected: {{ $line->expected_pallets ?? 0 }} pallets, {{ $line->expected_cases ?? 0 }} cases
                    </div>
                  @endif
                </div>
              @endforeach
            </div>
          @endforeach
        </div>
        @endif

        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Departure Notes</label>
          <textarea name="departure_notes" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md"
                    placeholder="Optional notes about the departure..."></textarea>
        </div>
        <div class="flex justify-end space-x-3">
          <button type="button" onclick="closeDepartureModal()"
                  class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
            Cancel
          </button>
          <button type="submit"
                  class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700">
            Record Departure
          </button>
        </div>
      </form>
    </div>
  </div>
  {{-- Email PDF Modal --}}
  <div id="emailModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
      <h3 class="text-lg font-semibold mb-4">Email Booking PDF</h3>
      <form id="emailForm">
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
          <div class="flex space-x-2">
            <input type="email" id="emailAddress" required
                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                   placeholder="Enter email address">
            <button type="button" onclick="useMyEmail()"
                    class="px-3 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 whitespace-nowrap">
              Use My Email
            </button>
          </div>
        </div>
        <div class="mb-4">
          <label class="block text-sm font-medium text-gray-700 mb-2">Message (Optional)</label>
          <textarea id="emailMessage" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Add a personal message..."></textarea>
        </div>
        <div class="flex justify-end space-x-3">
          <button type="button" onclick="closeEmailModal()"
                  class="px-4 py-2 text-gray-600 border border-gray-300 rounded hover:bg-gray-50">
            Cancel
          </button>
          <button type="submit"
                  class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
            Send PDF
          </button>
        </div>
      </form>
    </div>
  </div>
  <script>
    function emailBookingPDF(bookingId) {
      document.getElementById('emailModal').classList.remove('hidden');
      document.getElementById('emailModal').classList.add('flex');
    }
    function closeEmailModal() {
      document.getElementById('emailModal').classList.add('hidden');
      document.getElementById('emailModal').classList.remove('flex');
    }
    function useMyEmail() {
      document.getElementById('emailAddress').value = '{{ auth()->user()->email }}';
    }
    function showCancelModal() {
      document.getElementById('cancelModal').classList.remove('hidden');
      document.getElementById('cancelModal').classList.add('flex');
    }
    function closeCancelModal() {
      document.getElementById('cancelModal').classList.add('hidden');
      document.getElementById('cancelModal').classList.remove('flex');
    }
    document.getElementById('emailForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const email = document.getElementById('emailAddress').value;
      const message = document.getElementById('emailMessage').value;
      // Send request to email endpoint
      fetch('{{ route("app.bookings.email-pdf", $booking) }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          email: email,
          message: message
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          closeEmailModal();
          alert('PDF sent successfully!');
        } else {
          alert('Error sending PDF: ' + (data.message || 'Unknown error'));
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error sending PDF');
      });
    });
    document.getElementById('cancelForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const reason = document.getElementById('cancellationReason').value;
      // Send request to cancel endpoint
      fetch('{{ route("app.bookings.cancel", $booking) }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
          cancellation_reason: reason
        })
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          closeCancelModal();
          alert('Booking cancelled successfully!');
          location.reload(); // Refresh to show cancelled status
        } else {
          alert('Error cancelling booking: ' + (data.message || 'Unknown error'));
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Error cancelling booking');
      });
    });
    // Departure modal functions
    function openDepartureModal(bookingId) {
      document.getElementById('departureModal').classList.remove('hidden');
      document.getElementById('departureModal').classList.add('flex');
      // Update form action - determine correct prefix
      let routePrefix = '/admin'; // default
      if (window.location.pathname.includes('/app/')) {
        routePrefix = '/app';
      } else if (window.location.pathname.includes('/depot-admin/')) {
        routePrefix = '/depot-admin';
      } else if (window.location.pathname.includes('/admin/')) {
        routePrefix = '/admin';
      }
      document.getElementById('departureForm').action = `${routePrefix}/bookings/${bookingId}/departure`;
    }
    function closeDepartureModal() {
      document.getElementById('departureModal').classList.add('hidden');
      document.getElementById('departureModal').classList.remove('flex');
    }
    // Show/hide trailer location and swap fields based on radio selection
    document.querySelectorAll('input[name="departure_scenario"]').forEach(function(radio) {
      radio.addEventListener('change', function() {
        const trailerLocationField = document.getElementById('trailerLocationField');
        const trailerSwapField = document.getElementById('trailerSwapField');
        // Hide all fields first
        trailerLocationField.classList.add('hidden');
        trailerSwapField.classList.add('hidden');
        // Show appropriate field based on selection
        if (this.value === 'completed_dropped_trailer') {
          trailerLocationField.classList.remove('hidden');
        } else if (this.value === 'trailer_swap') {
          trailerSwapField.classList.remove('hidden');
        }
      });
    });
    // Close modal when clicking outside
    document.getElementById('emailModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeEmailModal();
      }
    });
    document.getElementById('cancelModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeCancelModal();
      }
    });
    document.getElementById('departureModal').addEventListener('click', function(e) {
      if (e.target === this) {
        closeDepartureModal();
      }
    });
  </script>
</x-app-layout>