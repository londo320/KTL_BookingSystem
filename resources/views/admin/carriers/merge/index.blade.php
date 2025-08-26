@extends('layouts.admin')

@section('title', 'Merge Carriers')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">🔄 Merge Carriers</h1>
                <p class="mt-2 text-gray-600">Combine duplicate carriers and update all historical records</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('app.carriers.merge.history') }}" 
                   class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                    📋 Merge History
                </a>
                <a href="{{ route('app.carriers.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    ← Back to Carriers
                </a>
            </div>
        </div>
    </div>

    <!-- Suggested Merges -->
    @if($suggestedMerges->count() > 0)
        <div class="mb-8 p-6 bg-yellow-50 border border-yellow-200 rounded-lg">
            <h2 class="text-xl font-semibold text-yellow-800 mb-4">⚠️ Suggested Merges (Similar Names Detected)</h2>
            
            @foreach($suggestedMerges as $normalizedName => $group)
                <div class="mb-6 p-4 bg-white rounded-lg border">
                    <h3 class="font-medium text-gray-900 mb-3">Similar carriers found:</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 mb-4">
                        @foreach($group as $carrier)
                            <div class="p-3 border rounded {{ $carrier->is_active ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200' }}">
                                <div class="font-medium text-sm">{{ $carrier->name }}</div>
                                <div class="text-xs text-gray-600 mt-1">
                                    📦 {{ $carrier->bookings_count }} bookings
                                    @if($carrier->last_used_at)
                                        • Last used: {{ $carrier->last_used_at->diffForHumans() }}
                                    @else
                                        • Never used
                                    @endif
                                </div>
                                @if(!$carrier->is_active)
                                    <span class="inline-block mt-1 px-2 py-1 text-xs bg-red-100 text-red-800 rounded">Inactive</span>
                                @endif
                                @if($carrier->trashed())
                                    <span class="inline-block mt-1 px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded">Deleted</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <button onclick="setupQuickMerge({{ $group->pluck('id')->toJson() }}, {{ $group->toJson() }})" 
                            class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg text-sm font-medium transition-colors">
                        🚀 Quick Setup Merge
                    </button>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Manual Merge Form -->
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-xl font-semibold text-gray-900">🛠️ Manual Carrier Merge</h2>
            <p class="text-sm text-gray-600 mt-1">Manually select carriers to merge</p>
        </div>

        <div class="p-6">
            <form id="merge-form" action="{{ route('app.carriers.merge.execute') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <!-- Source Carrier -->
                    <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-red-800 mb-4">📤 Source Carrier (Merge FROM)</h3>
                        <p class="text-sm text-red-700 mb-4">This carrier will be merged into the target carrier</p>
                        
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Source Carrier</label>
                        <select name="source_carrier_id" id="source-carrier" required 
                                class="w-full px-3 py-2 border border-red-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 bg-white">
                            <option value="">Select carrier to merge from...</option>
                            @foreach($carriers as $carrier)
                                <option value="{{ $carrier->id }}" 
                                        data-bookings="{{ $carrier->bookings_count }}"
                                        data-active="{{ $carrier->is_active ? 'true' : 'false' }}"
                                        data-deleted="{{ $carrier->trashed() ? 'true' : 'false' }}">
                                    {{ $carrier->name }} 
                                    ({{ $carrier->bookings_count }} bookings)
                                    {{ $carrier->is_active ? '' : ' - INACTIVE' }}
                                    {{ $carrier->trashed() ? ' - DELETED' : '' }}
                                </option>
                            @endforeach
                        </select>
                        
                        <div id="source-info" class="mt-4 hidden p-3 bg-white rounded border">
                            <h4 class="font-medium text-gray-900 mb-2">Source Carrier Details:</h4>
                            <div id="source-details" class="text-sm text-gray-600 space-y-1"></div>
                        </div>
                    </div>

                    <!-- Target Carrier -->
                    <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-green-800 mb-4">📥 Target Carrier (Merge INTO)</h3>
                        <p class="text-sm text-green-700 mb-4">This carrier will receive all data from the source carrier</p>
                        
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select Target Carrier</label>
                        <select name="target_carrier_id" id="target-carrier" required 
                                class="w-full px-3 py-2 border border-green-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 bg-white">
                            <option value="">Select target carrier...</option>
                            @foreach($carriers->where('is_active', true)->where('deleted_at', null) as $carrier)
                                <option value="{{ $carrier->id }}" 
                                        data-bookings="{{ $carrier->bookings_count }}">
                                    {{ $carrier->name }} ({{ $carrier->bookings_count }} bookings)
                                </option>
                            @endforeach
                        </select>
                        
                        <div id="target-info" class="mt-4 hidden p-3 bg-white rounded border">
                            <h4 class="font-medium text-gray-900 mb-2">Target Carrier Details:</h4>
                            <div id="target-details" class="text-sm text-gray-600 space-y-1"></div>
                        </div>
                    </div>
                </div>

                <!-- Merge Options -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-blue-800 mb-4">⚙️ Merge Options</h3>
                    
                    <div class="space-y-4">
                        <label class="flex items-start">
                            <input type="checkbox" name="delete_source" value="1" class="mt-1 rounded">
                            <span class="ml-3">
                                <span class="text-sm font-medium text-gray-900">Delete source carrier after merge</span>
                                <span class="block text-xs text-gray-500 mt-1">
                                    Recommended for true duplicates. If unchecked, source carrier will be deactivated and renamed for audit trail.
                                </span>
                            </span>
                        </label>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Merge Reason (Optional)</label>
                            <textarea name="reason" rows="2" 
                                      placeholder="Explain why these carriers are being merged..."
                                      class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Preview Section -->
                <div id="merge-preview" class="hidden mb-6 p-6 bg-purple-50 border border-purple-200 rounded-lg">
                    <h3 class="text-lg font-semibold text-purple-800 mb-4">🔍 Merge Preview</h3>
                    <div id="preview-content"></div>
                    
                    <div id="warnings-section" class="hidden mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded">
                        <h4 class="font-medium text-yellow-800 mb-2">⚠️ Warnings:</h4>
                        <ul id="warnings-list" class="text-sm text-yellow-700 space-y-1"></ul>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between">
                    <button type="button" onclick="previewMerge()" id="preview-button" disabled
                            class="px-6 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium transition-colors disabled:bg-gray-400">
                        🔍 Preview Merge
                    </button>
                    
                    <div class="flex gap-3">
                        <a href="{{ route('app.carriers.index') }}" 
                           class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg font-medium transition-colors">
                            Cancel
                        </a>
                        
                        <button type="submit" id="submit-merge" disabled
                                class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors disabled:bg-gray-400">
                            🔄 Execute Merge
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Recent Merges -->
    @if($recentMerges->count() > 0)
        <div class="mt-8 bg-white rounded-lg shadow-sm border">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">📋 Recent Merges</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Source → Target</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Bookings Moved</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Merged By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($recentMerges as $merge)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $merge->created_at->format('M j, Y H:i') }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <div class="font-medium text-red-600">{{ $merge->source_carrier_name }}</div>
                                <div class="text-gray-500">→ {{ $merge->target_carrier_name }}</div>
                            </td>
                            <td class="px-6 py-4 text-center text-sm text-gray-900">
                                {{ $merge->bookings_moved }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $merge->mergedBy->name ?? 'Unknown' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sourceSelect = document.getElementById('source-carrier');
    const targetSelect = document.getElementById('target-carrier');
    const previewButton = document.getElementById('preview-button');
    const submitButton = document.getElementById('submit-merge');
    
    // Update button states when selections change
    [sourceSelect, targetSelect].forEach(select => {
        select.addEventListener('change', updateButtonStates);
    });
    
    sourceSelect.addEventListener('change', function() {
        updateCarrierInfo('source', this);
    });
    
    targetSelect.addEventListener('change', function() {
        updateCarrierInfo('target', this);
    });
    
    function updateButtonStates() {
        const hasSource = sourceSelect.value !== '';
        const hasTarget = targetSelect.value !== '';
        const bothSelected = hasSource && hasTarget && sourceSelect.value !== targetSelect.value;
        
        previewButton.disabled = !bothSelected;
        // Submit button only enabled after preview
        if (!bothSelected) {
            submitButton.disabled = true;
            document.getElementById('merge-preview').classList.add('hidden');
        }
    }
    
    function updateCarrierInfo(type, select) {
        const infoDiv = document.getElementById(`${type}-info`);
        const detailsDiv = document.getElementById(`${type}-details`);
        
        if (select.value) {
            const option = select.options[select.selectedIndex];
            const bookings = option.dataset.bookings;
            const active = option.dataset.active === 'true';
            const deleted = option.dataset.deleted === 'true';
            
            let html = `<div>📦 Bookings: <strong>${bookings}</strong></div>`;
            html += `<div>Status: <strong class="${active ? 'text-green-600' : 'text-red-600'}">${active ? 'Active' : 'Inactive'}</strong></div>`;
            if (deleted) {
                html += `<div class="text-red-600">⚠️ <strong>This carrier is deleted</strong></div>`;
            }
            
            detailsDiv.innerHTML = html;
            infoDiv.classList.remove('hidden');
        } else {
            infoDiv.classList.add('hidden');
        }
    }
    
    window.previewMerge = function() {
        const sourceId = sourceSelect.value;
        const targetId = targetSelect.value;
        
        if (!sourceId || !targetId) {
            alert('Please select both source and target carriers');
            return;
        }
        
        if (sourceId === targetId) {
            alert('Source and target carriers cannot be the same');
            return;
        }
        
        // Show loading
        const previewDiv = document.getElementById('merge-preview');
        const contentDiv = document.getElementById('preview-content');
        contentDiv.innerHTML = '<div class="text-center py-4">🔄 Loading preview...</div>';
        previewDiv.classList.remove('hidden');
        
        fetch(`{{ route('app.carriers.merge.preview') }}?source_carrier_id=${sourceId}&target_carrier_id=${targetId}`)
            .then(response => response.json())
            .then(data => {
                if (!data.can_merge) {
                    alert('Cannot merge these carriers. Check that target carrier is active and not deleted.');
                    previewDiv.classList.add('hidden');
                    return;
                }
                
                // Show preview details
                let html = '<div class="grid grid-cols-1 md:grid-cols-2 gap-6">';
                html += '<div>';
                html += `<h4 class="font-medium text-gray-900 mb-2">📊 Impact Summary:</h4>`;
                html += `<div class="space-y-2 text-sm">`;
                html += `<div>📦 Bookings to update: <strong>${data.impact.bookings_to_update}</strong></div>`;
                html += `<div>🏢 Depots affected: <strong>${data.impact.depots_affected}</strong></div>`;
                html += `<div>👥 Customers affected: <strong>${data.impact.customers_affected}</strong></div>`;
                if (data.impact.date_range.from) {
                    html += `<div>📅 Date range: <strong>${data.impact.date_range.from}</strong> to <strong>${data.impact.date_range.to}</strong></div>`;
                }
                html += '</div></div>';
                
                html += '<div>';
                html += `<h4 class="font-medium text-gray-900 mb-2">🔄 Merge Details:</h4>`;
                html += `<div class="space-y-2 text-sm">`;
                html += `<div>📤 Source: <strong class="text-red-600">${data.source.name}</strong></div>`;
                html += `<div>📥 Target: <strong class="text-green-600">${data.target.name}</strong></div>`;
                html += `<div>🏢 Depot configurations will be merged</div>`;
                html += `<div>👥 Customer restrictions will be combined</div>`;
                html += '</div></div>';
                html += '</div>';
                
                contentDiv.innerHTML = html;
                
                // Show warnings if any
                if (data.warnings && data.warnings.length > 0) {
                    const warningsSection = document.getElementById('warnings-section');
                    const warningsList = document.getElementById('warnings-list');
                    
                    warningsList.innerHTML = '';
                    data.warnings.forEach(warning => {
                        const li = document.createElement('li');
                        li.textContent = warning;
                        warningsList.appendChild(li);
                    });
                    
                    warningsSection.classList.remove('hidden');
                } else {
                    document.getElementById('warnings-section').classList.add('hidden');
                }
                
                // Enable submit button
                submitButton.disabled = false;
            })
            .catch(error => {
                console.error('Preview failed:', error);
                alert('Failed to generate preview. Please try again.');
                previewDiv.classList.add('hidden');
            });
    };
    
    // Form submission confirmation
    document.getElementById('merge-form').addEventListener('submit', function(e) {
        const sourceOption = sourceSelect.options[sourceSelect.selectedIndex];
        const targetOption = targetSelect.options[targetSelect.selectedIndex];
        
        if (!confirm(`Are you sure you want to merge "${sourceOption.text}" into "${targetOption.text}"?\n\nThis action cannot be undone!`)) {
            e.preventDefault();
        }
    });
    
    // Quick merge setup
    window.setupQuickMerge = function(carrierIds, carriers) {
        // Find the best target (active carrier with most bookings)
        let bestTarget = null;
        let bestTargetScore = -1;
        
        carriers.forEach(carrier => {
            let score = 0;
            if (carrier.is_active) score += 1000;
            score += carrier.bookings_count;
            
            if (score > bestTargetScore) {
                bestTargetScore = score;
                bestTarget = carrier;
            }
        });
        
        if (bestTarget) {
            targetSelect.value = bestTarget.id;
            targetSelect.dispatchEvent(new Event('change'));
            
            // Show available sources (excluding the target)
            const availableSources = carriers.filter(c => c.id !== bestTarget.id);
            if (availableSources.length === 1) {
                sourceSelect.value = availableSources[0].id;
                sourceSelect.dispatchEvent(new Event('change'));
            }
            
            // Scroll to form
            document.getElementById('merge-form').scrollIntoView({ behavior: 'smooth' });
        }
    };
});
</script>
@endsection