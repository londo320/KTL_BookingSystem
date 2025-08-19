<x-app-layout>
  @include('layouts.admin-nav')

  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl">Booking History #{{ $booking->id }}</h2>
      <a href="{{ route('admin.bookings.show', $booking) }}"
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
            {{ $actualRebookCount ?? $booking->rebook_count ?? 0 }} / 3
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
              <a href="{{ route('admin.bookings.history', $booking) }}?sort=asc" 
                 class="px-3 py-1 text-xs rounded-md {{ $sortOrder === 'asc' ? 'bg-blue-500 text-white' : 'text-gray-600 hover:text-gray-800' }}">
                📈 Oldest First
              </a>
              <a href="{{ route('admin.bookings.history', $booking) }}?sort=desc" 
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
        {{-- Main Movement Timeline (Simplified) --}}
        <div class="mb-6">
          <div class="bg-white rounded-lg p-4 border">
            <h4 class="text-lg font-semibold text-gray-800 mb-4">🚛 Movement Timeline</h4>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
              {{-- Booking Created --}}
              <div class="text-center p-3 bg-green-50 rounded-lg border">
                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
                  <span class="text-green-600 text-sm">📅</span>
                </div>
                <div class="text-sm font-medium text-green-800">Booking Created</div>
                <div class="text-xs text-gray-600">{{ $booking->created_at->format('d M Y, H:i') }}</div>
                <div class="text-xs text-gray-500">{{ $booking->slot->depot->name }}</div>
              </div>

              {{-- Arrival --}}
              @if($booking->arrived_at)
                <div class="text-center p-3 bg-blue-50 rounded-lg border">
                  <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-blue-600 text-sm">🚪</span>
                  </div>
                  <div class="text-sm font-medium text-blue-800">Site Arrival</div>
                  <div class="text-xs text-gray-600">{{ $booking->arrived_at->format('d M Y, H:i') }}</div>
                  @if($booking->vehicle_registration)
                    <div class="text-xs text-gray-500">{{ $booking->vehicle_registration }}</div>
                  @endif
                </div>
              @else
                <div class="text-center p-3 bg-gray-50 rounded-lg border border-dashed">
                  <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-gray-400 text-sm">🚪</span>
                  </div>
                  <div class="text-sm font-medium text-gray-500">Awaiting Arrival</div>
                  <div class="text-xs text-gray-400">Expected: {{ $booking->slot->start_at->format('d M Y, H:i') }}</div>
                </div>
              @endif

              {{-- Tipping Status --}}
              @if($booking->tipping_completed_at)
                <div class="text-center p-3 bg-purple-50 rounded-lg border">
                  <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-purple-600 text-sm">🏗️</span>
                  </div>
                  <div class="text-sm font-medium text-purple-800">Tipping Complete</div>
                  <div class="text-xs text-gray-600">{{ $booking->tipping_completed_at->format('d M Y, H:i') }}</div>
                  @if($booking->tipping_started_at)
                    <div class="text-xs text-gray-500">Duration: {{ $booking->tipping_started_at->diffInMinutes($booking->tipping_completed_at) }}min</div>
                  @endif
                  @if($booking->bay_number)
                    <div class="text-xs text-green-600">Bay {{ $booking->bay_number }}</div>
                  @endif
                </div>
              @elseif($booking->tipping_started_at)
                <div class="text-center p-3 bg-yellow-50 rounded-lg border">
                  <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-yellow-600 text-sm">🏗️</span>
                  </div>
                  <div class="text-sm font-medium text-yellow-800">Tipping In Progress</div>
                  <div class="text-xs text-gray-600">{{ $booking->tipping_started_at->format('d M Y, H:i') }}</div>
                  <div class="text-xs text-gray-500">{{ $booking->tipping_started_at->diffForHumans() }}</div>
                  @if($booking->bay_number)
                    <div class="text-xs text-green-600">Bay {{ $booking->bay_number }}</div>
                  @endif
                </div>
              @elseif($booking->moved_to_bay_at)
                <div class="text-center p-3 bg-blue-50 rounded-lg border">
                  <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-blue-600 text-sm">🏗️</span>
                  </div>
                  <div class="text-sm font-medium text-blue-800">Moved to Bay</div>
                  <div class="text-xs text-gray-600">{{ $booking->moved_to_bay_at->format('d M Y, H:i') }}</div>
                  @if($booking->bay_number)
                    <div class="text-xs text-green-600">Bay {{ $booking->bay_number }}</div>
                  @endif
                </div>
              @else
                <div class="text-center p-3 bg-gray-50 rounded-lg border border-dashed">
                  <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-gray-400 text-sm">🏗️</span>
                  </div>
                  <div class="text-sm font-medium text-gray-500">Awaiting Tipping</div>
                  @if($booking->arrived_at)
                    <div class="text-xs text-gray-400">On site {{ $booking->arrived_at->diffForHumans() }}</div>
                  @endif
                </div>
              @endif

              {{-- Departure --}}
              @if($booking->departed_at)
                <div class="text-center p-3 bg-gray-50 rounded-lg border">
                  <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-gray-600 text-sm">🚛</span>
                  </div>
                  <div class="text-sm font-medium text-gray-800">Site Departure</div>
                  <div class="text-xs text-gray-600">{{ $booking->departed_at->format('d M Y, H:i') }}</div>
                  @if($booking->arrived_at)
                    <div class="text-xs text-gray-500">Total time: {{ $booking->arrived_at->diffInMinutes($booking->departed_at) }}min</div>
                  @endif
                </div>
              @elseif($booking->isCancelled())
                <div class="text-center p-3 bg-red-50 rounded-lg border">
                  <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-red-600 text-sm">❌</span>
                  </div>
                  <div class="text-sm font-medium text-red-800">Cancelled</div>
                  <div class="text-xs text-gray-600">{{ $booking->cancelled_at->format('d M Y, H:i') }}</div>
                </div>
              @else
                <div class="text-center p-3 bg-gray-50 rounded-lg border border-dashed">
                  <div class="w-8 h-8 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <span class="text-gray-400 text-sm">🚛</span>
                  </div>
                  <div class="text-sm font-medium text-gray-500">Awaiting Departure</div>
                  @if($booking->arrived_at)
                    <div class="text-xs text-gray-400">On site {{ $booking->arrived_at->diffForHumans() }}</div>
                  @endif
                </div>
              @endif
            </div>
          </div>
        </div>

        {{-- Simple Timeline (Essential Events Only) --}}
        <div class="bg-white rounded-lg border">
          <div class="p-4 border-b border-gray-200">
            <h4 class="text-lg font-semibold text-gray-800">📋 Event Timeline</h4>
            <p class="text-sm text-gray-600">Key milestones and movements in chronological order</p>
          </div>

          <div class="p-4">
            @php
              // Create simplified timeline with only essential events
              $timeline = collect();
              
              // Add booking creation
              $timeline->push((object)[
                'timestamp' => $booking->created_at,
                'type' => 'created',
                'title' => 'Booking Created',
                'description' => 'Scheduled for ' . $booking->slot->start_at->format('d M Y, H:i') . ' at ' . $booking->slot->depot->name,
                'icon' => '📅',
                'color' => 'green'
              ]);
              
              // Add arrival if available
              if($booking->arrived_at) {
                $timeline->push((object)[
                  'timestamp' => $booking->arrived_at,
                  'type' => 'arrival',
                  'title' => 'Site Arrival',
                  'description' => ($booking->vehicle_registration ? $booking->vehicle_registration . ' ' : '') . 'arrived on site',
                  'icon' => '🚪',
                  'color' => 'blue'
                ]);
              }
              
              // Add trailer drop if available
              if($booking->trailer_dropped_at) {
                $timeline->push((object)[
                  'timestamp' => $booking->trailer_dropped_at,
                  'type' => 'dropped',
                  'title' => 'Trailer Dropped',
                  'description' => 'Trailer unhitched and positioned',
                  'icon' => '🔻',
                  'color' => 'purple'
                ]);
              }
              
              // Add bay movement if available
              if($booking->moved_to_bay_at) {
                $timeline->push((object)[
                  'timestamp' => $booking->moved_to_bay_at,
                  'type' => 'moved_to_bay',
                  'title' => 'Moved to Bay',
                  'description' => 'Positioned at Bay ' . ($booking->bay_number ?: 'TBD'),
                  'icon' => '➡️',
                  'color' => 'indigo'
                ]);
              }
              
              // Add tipping start if available
              if($booking->tipping_started_at) {
                $timeline->push((object)[
                  'timestamp' => $booking->tipping_started_at,
                  'type' => 'tipping_start',
                  'title' => 'Tipping Started',
                  'description' => 'Unloading commenced' . ($booking->bay_number ? ' in Bay ' . $booking->bay_number : ''),
                  'icon' => '🏗️',
                  'color' => 'yellow'
                ]);
              }
              
              // Add tipping completion if available
              if($booking->tipping_completed_at) {
                $duration = $booking->tipping_started_at ? $booking->tipping_started_at->diffInMinutes($booking->tipping_completed_at) : null;
                $timeline->push((object)[
                  'timestamp' => $booking->tipping_completed_at,
                  'type' => 'tipping_complete',
                  'title' => 'Tipping Complete',
                  'description' => 'Unloading finished' . ($duration ? ' (' . $duration . ' minutes)' : ''),
                  'icon' => '✅',
                  'color' => 'green'
                ]);
              }
              
              // Add departure if available
              if($booking->departed_at) {
                $totalTime = $booking->arrived_at ? $booking->arrived_at->diffInMinutes($booking->departed_at) : null;
                $timeline->push((object)[
                  'timestamp' => $booking->departed_at,
                  'type' => 'departure',
                  'title' => 'Site Departure',
                  'description' => 'Left site' . ($totalTime ? ' (total time: ' . $totalTime . ' minutes)' : ''),
                  'icon' => '🚛',
                  'color' => 'gray'
                ]);
              }
              
              // Add cancellation if applicable
              if($booking->cancelled_at) {
                $timeline->push((object)[
                  'timestamp' => $booking->cancelled_at,
                  'type' => 'cancelled',
                  'title' => 'Booking Cancelled',
                  'description' => $booking->cancellation_reason ?: 'Booking was cancelled',
                  'icon' => '❌',
                  'color' => 'red'
                ]);
              }
              
              // Add any rebooks from history (only major ones)
              foreach($history->where('action', 'rebooked') as $rebook) {
                if($rebook->originalSlot && $rebook->newSlot) {
                  $timeline->push((object)[
                    'timestamp' => $rebook->created_at,
                    'type' => 'rebook',
                    'title' => 'Rebooked',
                    'description' => 'Moved from ' . $rebook->originalSlot->start_at->format('d M, H:i') . ' to ' . $rebook->newSlot->start_at->format('d M, H:i'),
                    'icon' => '🔄',
                    'color' => 'orange'
                  ]);
                }
              }
              
              // Sort timeline chronologically
              $timeline = $timeline->sortBy('timestamp');
            @endphp

            <div class="space-y-3">
              @foreach($timeline as $event)
                <div class="flex items-start space-x-3">
                  {{-- Timeline dot --}}
                  <div class="flex-shrink-0 mt-1">
                    <div class="w-8 h-8 bg-{{ $event->color }}-100 rounded-full flex items-center justify-center">
                      <span class="text-{{ $event->color }}-600 text-sm">{{ $event->icon }}</span>
                    </div>
                  </div>
                  
                  {{-- Event content --}}
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                      <h5 class="text-sm font-medium text-gray-900">{{ $event->title }}</h5>
                      <span class="text-xs text-gray-500">{{ $event->timestamp->format('d M Y, H:i') }}</span>
                    </div>
                    <p class="text-xs text-gray-600 mt-1">{{ $event->description }}</p>
                  </div>
                </div>
              @endforeach
              
              @if($timeline->isEmpty())
                <div class="text-center py-8 text-gray-500">
                  <p class="text-sm">No timeline events recorded yet.</p>
                </div>
              @endif
            </div>
          </div>
          
          {{-- Full History Toggle --}}
          @if($history->count() > 0)
            <div class="border-t border-gray-200 p-4">
              <button 
                id="toggleFullHistory" 
                class="flex items-center justify-between w-full text-left hover:bg-gray-50 p-2 rounded"
                onclick="toggleFullHistory()"
              >
                <div>
                  <h5 class="text-sm font-medium text-gray-700">📄 Full Administrative History</h5>
                  <p class="text-xs text-gray-500">All {{ $history->count() }} booking changes and system entries</p>
                </div>
                <svg id="fullHistoryIcon" class="w-4 h-4 text-gray-400 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </button>
              
              <div id="fullHistoryContent" class="hidden mt-3 pt-3 border-t border-gray-100">
                <div class="space-y-2 max-h-96 overflow-y-auto">
                  @foreach($history->sortBy('created_at') as $record)
                    <div class="flex items-start space-x-2 text-xs bg-gray-50 rounded p-2">
                      <span class="text-gray-400 mt-0.5">•</span>
                      <div class="flex-1">
                        <div class="flex items-center justify-between">
                          <span class="font-medium text-gray-700 capitalize">{{ $record->action }}</span>
                          <span class="text-gray-400">{{ $record->created_at->format('d M H:i') }}</span>
                        </div>
                        @if($record->reason)
                          <p class="text-gray-600 mt-0.5">{{ Str::limit($record->reason, 60) }}</p>
                        @endif
                      </div>
                    </div>
                  @endforeach
                </div>
              </div>
            </div>
          @endif
        </div>

        <script>
          function toggleFullHistory() {
            const content = document.getElementById('fullHistoryContent');
            const icon = document.getElementById('fullHistoryIcon');
            
            if (content.classList.contains('hidden')) {
              content.classList.remove('hidden');
              icon.classList.add('rotate-180');
            } else {
              content.classList.add('hidden');
              icon.classList.remove('rotate-180');
            }
          }
        </script>
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