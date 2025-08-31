<x-app-layout>
  @include('layouts.customer-nav')

  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl">Booking History #{{ $booking->id }}</h2>
      <a href="{{ route('customer.bookings.show', $booking) }}"
         class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
        Back to Booking
      </a>
    </div>
  </x-slot>

  <div class="py-6 max-w-6xl mx-auto">
    
    {{-- Current Booking Info --}}
    <div class="mb-6 p-6 bg-blue-50 border border-blue-200 rounded-lg">
      <h3 class="text-lg font-semibold text-blue-800 mb-3">📅 Current Booking Details</h3>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
          <p class="text-sm text-gray-600">Booking ID</p>
          <p class="font-medium">#{{ $booking->id }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Customer</p>
          <p class="font-medium">{{ $booking->customer->name ?? 'Not assigned' }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Current Status</p>
          <p class="font-medium">
            @if($booking->arrived_at)
              <span class="text-green-600">✅ Arrived</span>
            @elseif($booking->isCancelled())
              <span class="text-red-600">❌ Cancelled</span>
            @else
              <span class="text-blue-600">📅 Active</span>
            @endif
          </p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Depot</p>
          <p class="font-medium">{{ $booking->slot->depot->name }}</p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Current Slot</p>
          <p class="font-medium">
            {{ $booking->slot->start_at->format('D, d M Y - H:i') }} - {{ $booking->slot->end_at->format('H:i') }}
          </p>
        </div>
        <div>
          <p class="text-sm text-gray-600">Rebook Count</p>
          <p class="font-medium">
            {{ $actualRebookCount ?? $booking->rebook_count ?? 0 }} / {{ $maxRebooksPerBooking }}
            @if(isset($actualRebookCount) && $actualRebookCount != ($booking->rebook_count ?? 0))
              <span class="text-xs text-orange-600" title="Field: {{ $booking->rebook_count ?? 'null' }}, Calculated: {{ $actualRebookCount }}">
                (corrected)
              </span>
            @endif
          </p>
        </div>
      </div>
    </div>

    {{-- Timeline --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
      <div class="p-6 border-b border-gray-200">
        <div class="flex justify-between items-center">
          <div>
            <h3 class="text-xl font-semibold text-gray-800">📋 Complete Timeline</h3>
            <p class="text-sm text-gray-600 mt-1">
              All actions and changes made to this booking ({{ $history->count() }} entries)
            </p>
          </div>
          <div class="flex items-center space-x-2">
            <span class="text-sm text-gray-600">Sort:</span>
            <div class="flex bg-gray-100 rounded-lg p-1">
              <a href="{{ route('customer.bookings.history', $booking) }}?sort=asc" 
                 class="px-3 py-1 text-xs rounded-md {{ $sortOrder === 'asc' ? 'bg-blue-500 text-white' : 'text-gray-600 hover:text-gray-800' }}">
                📈 Oldest First
              </a>
              <a href="{{ route('customer.bookings.history', $booking) }}?sort=desc" 
                 class="px-3 py-1 text-xs rounded-md {{ $sortOrder === 'desc' ? 'bg-blue-500 text-white' : 'text-gray-600 hover:text-gray-800' }}">
                📉 Newest First
              </a>
            </div>
          </div>
        </div>
      </div>

      @if($history->isEmpty())
        <div class="p-6 text-center text-gray-500">
          <p>No history records found for this booking.</p>
        </div>
      @else
        <div class="divide-y divide-gray-200">
          @foreach($history as $record)
            <div class="p-6">
              <div class="flex items-start space-x-4">
                {{-- Action Icon --}}
                <div class="flex-shrink-0">
                  @if($record->action === 'created')
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                      <span class="text-green-600 text-sm">➕</span>
                    </div>
                  @elseif($record->action === 'rebooked')
                    <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                      <span class="text-orange-600 text-sm">🔄</span>
                    </div>
                  @elseif($record->action === 'cancelled')
                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                      <span class="text-red-600 text-sm">❌</span>
                    </div>
                  @elseif($record->action === 'modified')
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                      <span class="text-blue-600 text-sm">✏️</span>
                    </div>
                  @else
                    <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center">
                      <span class="text-gray-600 text-sm">📝</span>
                    </div>
                  @endif
                </div>

                {{-- Action Details --}}
                <div class="flex-1 min-w-0">
                  <div class="flex items-center justify-between mb-2">
                    <h4 class="text-lg font-medium text-gray-900 capitalize">
                      {{ $record->action }}
                      @if($record->is_last_minute)
                        <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                          ⚡ Last Minute
                        </span>
                      @endif
                    </h4>
                    <div class="text-right">
                      <p class="text-sm text-gray-500">
                        {{ $record->created_at->format('d M Y, H:i') }}
                      </p>
                      @if($record->hours_before_slot !== null)
                        <p class="text-xs text-gray-400">
                          {{ $record->hours_before_slot }}h before slot
                        </p>
                      @endif
                    </div>
                  </div>

                  {{-- Action Description --}}
                  @if($record->action === 'created')
                    <p class="text-sm text-gray-700 mb-2">
                      Booking was created for slot on 
                      <strong>{{ $record->booking->slot->start_at->format('D, d M Y - H:i') }}</strong>
                      at {{ $record->booking->slot->depot->name }}
                    </p>
                  @elseif($record->action === 'rebooked')
                    <div class="text-sm text-gray-700 mb-2">
                      <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-2">
                        <div class="flex items-center justify-between mb-2">
                          <span class="text-yellow-800 font-semibold">📅 Slot Change</span>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                          <div class="text-center">
                            <div class="text-xs text-gray-500 mb-1">FROM</div>
                            @if($record->originalSlot)
                              <div class="bg-red-100 border border-red-300 rounded p-2">
                                <div class="font-semibold text-red-800">{{ $record->originalSlot->depot->name }}</div>
                                <div class="text-sm text-red-700">{{ $record->originalSlot->start_at->format('D, d M Y') }}</div>
                                <div class="text-lg font-bold text-red-800">{{ $record->originalSlot->start_at->format('H:i') }}</div>
                              </div>
                            @endif
                          </div>
                          <div class="flex items-center justify-center md:mt-6">
                            <div class="text-2xl">➡️</div>
                          </div>
                          <div class="text-center">
                            <div class="text-xs text-gray-500 mb-1">TO</div>
                            @if($record->newSlot)
                              <div class="bg-green-100 border border-green-300 rounded p-2">
                                <div class="font-semibold text-green-800">{{ $record->newSlot->depot->name }}</div>
                                <div class="text-sm text-green-700">{{ $record->newSlot->start_at->format('D, d M Y') }}</div>
                                <div class="text-lg font-bold text-green-800">{{ $record->newSlot->start_at->format('H:i') }}</div>
                              </div>
                            @endif
                          </div>
                        </div>
                      </div>
                    </div>
                  @elseif($record->action === 'cancelled')
                    <p class="text-sm text-gray-700 mb-2">
                      Booking was cancelled
                      @if($record->originalSlot)
                        for slot on <strong>{{ $record->originalSlot->start_at->format('D, d M Y - H:i') }}</strong>
                        at {{ $record->originalSlot->depot->name }}
                      @endif
                    </p>
                  @elseif($record->action === 'modified')
                    <p class="text-sm text-gray-700 mb-2">
                      Booking details were updated
                      @if($record->changes)
                        <span class="text-xs text-gray-500">({{ count($record->changes) }} changes made)</span>
                      @endif
                    </p>
                  @endif

                  {{-- Reason --}}
                  @if($record->reason)
                    <div class="mb-2">
                      <p class="text-sm font-medium text-gray-600">Reason:</p>
                      <p class="text-sm text-gray-700 italic">{{ $record->reason }}</p>
                    </div>
                  @endif

                  {{-- Additional Details --}}
                  <div class="flex items-center space-x-4 text-xs text-gray-500">
                    @if($record->customer_rebook_count_30days > 0)
                      <span>Customer rebooks (30d): {{ $record->customer_rebook_count_30days }}</span>
                    @endif
                    @if($record->action === 'rebooked' && $record->is_last_minute)
                      <span class="text-red-600">⚡ Less than 24h notice</span>
                    @endif
                  </div>

                  {{-- Changes Details (for modifications) --}}
                  @if($record->action === 'modified' && $record->changes && count($record->changes) > 0)
                    <div class="mt-3 p-3 bg-gray-50 rounded border text-xs">
                      <p class="font-medium text-gray-600 mb-1">Changes made:</p>
                      <div class="space-y-1">
                        @foreach($record->changes as $field => $change)
                          @if(is_array($change) && (isset($change['old']) || isset($change['new'])))
                            {{-- New format: ['old' => ..., 'new' => ...] --}}
                            <p>
                              <strong>{{ ucfirst(str_replace('_', ' ', $field)) }}:</strong>
                              "{{ $change['old'] ?? '(empty)' }}" → "{{ $change['new'] ?? '(empty)' }}"
                            </p>
                          @elseif(!is_array($change))
                            {{-- Legacy format: direct values --}}
                            <p>
                              <strong>{{ ucfirst(str_replace('_', ' ', $field)) }}:</strong>
                              {{ $change ?: '(empty)' }}
                            </p>
                          @endif
                        @endforeach
                      </div>
                    </div>
                  @elseif($record->action === 'modified')
                    <div class="mt-3 p-3 bg-blue-50 rounded border text-xs">
                      <p class="text-blue-700 italic">
                        <strong>Note:</strong> Detailed change information not recorded for this update.
                      </p>
                    </div>
                  @endif
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </div>

    {{-- Summary Statistics --}}
    <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="bg-white p-4 rounded-lg shadow text-center">
        <div class="text-2xl font-bold text-blue-600">{{ $history->where('action', 'created')->count() }}</div>
        <div class="text-sm text-gray-600">Created</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow text-center">
        <div class="text-2xl font-bold text-orange-600">{{ $history->where('action', 'rebooked')->count() }}</div>
        <div class="text-sm text-gray-600">Rebooked</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow text-center">
        <div class="text-2xl font-bold text-red-600">{{ $history->where('action', 'cancelled')->count() }}</div>
        <div class="text-sm text-gray-600">Cancelled</div>
      </div>
      <div class="bg-white p-4 rounded-lg shadow text-center">
        <div class="text-2xl font-bold text-red-600">{{ $history->where('is_last_minute', true)->count() }}</div>
        <div class="text-sm text-gray-600">Last Minute</div>
      </div>
    </div>

  </div>
</x-app-layout>