@extends('layouts.admin')

@section('title', 'Edit Arrival Time Setting')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    ✏️ Edit Arrival Time Setting
                </h1>
                <p class="mt-2 text-gray-600">Modify early/late arrival tolerances</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('app.arrival-time-settings.show', $arrivalTimeSetting) }}" 
                   class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    👁️ View Details
                </a>
                <a href="{{ route('app.arrival-time-settings.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    ← Back to Settings
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">⚙️ Edit Setting</h3>
            <div class="mt-2">
                @php
                    $levelData = [
                        'global' => ['emoji' => '🌐', 'class' => 'bg-blue-100 text-blue-800', 'label' => 'Global'],
                        'depot' => ['emoji' => '🏢', 'class' => 'bg-green-100 text-green-800', 'label' => 'Depot'],
                        'customer' => ['emoji' => '👤', 'class' => 'bg-purple-100 text-purple-800', 'label' => 'Customer'],
                    ];
                    $data = $levelData[$arrivalTimeSetting->level] ?? $levelData['global'];
                @endphp
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $data['class'] }}">
                        {{ $data['emoji'] }} {{ $data['label'] }} Level
                    </span>
                    <span class="text-sm text-gray-600">
                        @if($arrivalTimeSetting->level === 'global')
                            (Applied to all bookings as fallback)
                        @elseif($arrivalTimeSetting->level === 'depot')
                            for {{ $arrivalTimeSetting->depot->name ?? 'Unknown Depot' }}
                        @elseif($arrivalTimeSetting->level === 'customer')
                            for {{ $arrivalTimeSetting->customer->name ?? 'Unknown Customer' }}
                        @endif
                    </span>
                </div>
            </div>
        </div>
        
        <form method="POST" action="{{ route('app.arrival-time-settings.update', $arrivalTimeSetting) }}" class="p-6">
            @csrf
            @method('PUT')
            
            <!-- Note about level -->
            <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h4 class="text-sm font-medium text-yellow-800">📝 Note</h4>
                        <p class="mt-1 text-sm text-yellow-700">
                            The level and scope (depot/customer) cannot be changed once created. Only the threshold values and description can be modified.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Threshold Settings -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="early_threshold_minutes" class="block text-sm font-medium text-gray-700 mb-2">Early Threshold (minutes) *</label>
                    <div class="relative">
                        <input type="number" name="early_threshold_minutes" id="early_threshold_minutes" 
                               min="0" max="1440" step="1" required
                               value="{{ old('early_threshold_minutes', $arrivalTimeSetting->early_threshold_minutes) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('early_threshold_minutes') border-red-500 @enderror">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">min</span>
                        </div>
                    </div>
                    @error('early_threshold_minutes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">⏪ Arrivals more than this many minutes early will be flagged as "early"</p>
                </div>

                <div>
                    <label for="late_threshold_minutes" class="block text-sm font-medium text-gray-700 mb-2">Late Threshold (minutes) *</label>
                    <div class="relative">
                        <input type="number" name="late_threshold_minutes" id="late_threshold_minutes" 
                               min="0" max="1440" step="1" required
                               value="{{ old('late_threshold_minutes', $arrivalTimeSetting->late_threshold_minutes) }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('late_threshold_minutes') border-red-500 @enderror">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">min</span>
                        </div>
                    </div>
                    @error('late_threshold_minutes')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">⏰ Arrivals more than this many minutes late will be flagged as "late"</p>
                </div>
            </div>

            <!-- Description -->
            <div class="mb-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                <textarea name="description" id="description" rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('description') border-red-500 @enderror"
                          placeholder="Optional description of this setting and when it applies...">{{ old('description', $arrivalTimeSetting->description) }}</textarea>
                @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Preview -->
            <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <h4 class="text-sm font-medium text-blue-800 mb-2">📊 Preview Example</h4>
                <div id="preview-content" class="text-sm text-blue-700">
                    <p>For a 10:00 AM booking:</p>
                    <ul class="list-disc list-inside mt-1 space-y-1">
                        <li><span class="font-medium">Early:</span> Before <span id="early-time">9:45 AM</span></li>
                        <li><span class="font-medium">On-time:</span> <span id="ontime-window">9:45 AM - 10:15 AM</span></li>
                        <li><span class="font-medium">Late:</span> After <span id="late-time">10:15 AM</span></li>
                    </ul>
                </div>
            </div>

            <!-- Current vs New Comparison -->
            <div class="mb-6 p-4 bg-gray-50 border border-gray-200 rounded-lg">
                <h4 class="text-sm font-medium text-gray-800 mb-2">📊 Current Settings</h4>
                <div class="text-sm text-gray-700">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="font-medium">Early Threshold:</span> {{ $arrivalTimeSetting->early_threshold_minutes }} minutes
                        </div>
                        <div>
                            <span class="font-medium">Late Threshold:</span> {{ $arrivalTimeSetting->late_threshold_minutes }} minutes
                        </div>
                    </div>
                    @if($arrivalTimeSetting->description)
                    <div class="mt-2">
                        <span class="font-medium">Description:</span> {{ $arrivalTimeSetting->description }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('app.arrival-time-settings.show', $arrivalTimeSetting) }}" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium">
                    💾 Update Setting
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const earlyInput = document.getElementById('early_threshold_minutes');
    const lateInput = document.getElementById('late_threshold_minutes');

    function updatePreview() {
        const earlyMin = parseInt(earlyInput.value) || 0;
        const lateMin = parseInt(lateInput.value) || 0;
        
        // Calculate example times for 10:00 AM booking
        const baseTime = new Date();
        baseTime.setHours(10, 0, 0, 0);
        
        const earlyTime = new Date(baseTime.getTime() - (earlyMin * 60000));
        const lateTime = new Date(baseTime.getTime() + (lateMin * 60000));
        
        // Format times
        const formatTime = (date) => date.toLocaleTimeString('en-US', { 
            hour: 'numeric', 
            minute: '2-digit',
            hour12: true 
        });
        
        document.getElementById('early-time').textContent = formatTime(earlyTime);
        document.getElementById('late-time').textContent = formatTime(lateTime);
        document.getElementById('ontime-window').textContent = 
            `${formatTime(earlyTime)} - ${formatTime(lateTime)}`;
    }

    // Event listeners
    earlyInput.addEventListener('input', updatePreview);
    lateInput.addEventListener('input', updatePreview);

    // Initial setup
    updatePreview();
});
</script>
@endsection