@extends('layouts.admin')

@section('title', 'Create Arrival Time Setting')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                    ➕ Create Arrival Time Setting
                </h1>
                <p class="mt-2 text-gray-600">Define early/late arrival tolerances for a specific level</p>
            </div>
            <div>
                <a href="{{ route('admin.arrival-time-settings.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    ← Back to Settings
                </a>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">⚙️ New Arrival Time Setting</h3>
            <p class="text-sm text-gray-600 mt-1">Configure when arrivals are considered early, on-time, or late</p>
        </div>
        
        <form method="POST" action="{{ route('admin.arrival-time-settings.store') }}" class="p-6">
            @csrf
            
            <!-- Level Selection -->
            <div class="mb-6">
                <label for="level" class="block text-sm font-medium text-gray-700 mb-2">Setting Level *</label>
                <select name="level" id="level" required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('level') border-red-500 @enderror">
                    <option value="">Select setting level...</option>
                    @foreach($levels as $key => $label)
                        <option value="{{ $key }}" {{ old('level', $level) == $key ? 'selected' : '' }}>
                            {{ $key == 'global' ? '🌐' : ($key == 'depot' ? '🏢' : '👤') }} {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('level')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Choose the scope for this setting</p>
            </div>

            <!-- Depot Selection (shown for depot level) -->
            <div class="mb-6" id="depot-section" style="display: none;">
                <label for="depot_id" class="block text-sm font-medium text-gray-700 mb-2">Depot *</label>
                <select name="depot_id" id="depot_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('depot_id') border-red-500 @enderror">
                    <option value="">Select depot...</option>
                    @foreach($depots as $depot)
                        <option value="{{ $depot->id }}" {{ old('depot_id', $depotId) == $depot->id ? 'selected' : '' }}>
                            {{ $depot->name }}
                        </option>
                    @endforeach
                </select>
                @error('depot_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Customer Selection (shown for customer level) -->
            <div class="mb-6" id="customer-section" style="display: none;">
                <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-2">Customer *</label>
                <select name="customer_id" id="customer_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('customer_id') border-red-500 @enderror">
                    <option value="">Select customer...</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ old('customer_id', $customerId) == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
                @error('customer_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Threshold Settings -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="early_threshold_minutes" class="block text-sm font-medium text-gray-700 mb-2">Early Threshold (minutes) *</label>
                    <div class="relative">
                        <input type="number" name="early_threshold_minutes" id="early_threshold_minutes" 
                               min="0" max="1440" step="1" required
                               value="{{ old('early_threshold_minutes', 0) }}"
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
                               value="{{ old('late_threshold_minutes', 0) }}"
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
                          placeholder="Optional description of this setting and when it applies...">{{ old('description') }}</textarea>
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

            <!-- Actions -->
            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('admin.arrival-time-settings.index') }}" 
                   class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" 
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium">
                    ➕ Create Setting
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const levelSelect = document.getElementById('level');
    const depotSection = document.getElementById('depot-section');
    const customerSection = document.getElementById('customer-section');
    const earlyInput = document.getElementById('early_threshold_minutes');
    const lateInput = document.getElementById('late_threshold_minutes');

    function updateVisibility() {
        const level = levelSelect.value;
        
        // Show/hide sections based on level
        depotSection.style.display = level === 'depot' ? 'block' : 'none';
        customerSection.style.display = level === 'customer' ? 'block' : 'none';
        
        // Clear values when hiding
        if (level !== 'depot') {
            document.getElementById('depot_id').value = '';
        }
        if (level !== 'customer') {
            document.getElementById('customer_id').value = '';
        }
    }

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
    levelSelect.addEventListener('change', updateVisibility);
    earlyInput.addEventListener('input', updatePreview);
    lateInput.addEventListener('input', updatePreview);

    // Initial setup
    updateVisibility();
    updatePreview();
});
</script>
@endsection