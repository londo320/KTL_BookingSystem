@extends('layouts.admin')

@section('title', 'Edit Carrier')

@section('content')
<div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">✏️ Edit Carrier</h1>
                <p class="mt-2 text-gray-600">Update carrier: {{ $carrier->name }}</p>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('app.carriers.show', $carrier) }}" 
                   class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors">
                    👁️ View Details
                </a>
                <a href="{{ route('app.carriers.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    ← Back to Carriers
                </a>
            </div>
        </div>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-sm border">
        <form action="{{ route('app.carriers.update', $carrier) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-900">Carrier Information</h2>
            </div>

            <div class="p-6 space-y-6">
                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Carrier Name -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Carrier Name <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $carrier->name) }}"
                               required
                               placeholder="e.g., DHL, FedEx, Knowles Logistics"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('name')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contact Email -->
                    <div>
                        <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">
                            Contact Email
                        </label>
                        <input type="email" 
                               id="contact_email" 
                               name="contact_email" 
                               value="{{ old('contact_email', $carrier->contact_email) }}"
                               placeholder="contact@carrier.com"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('contact_email')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Contact Phone -->
                    <div>
                        <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">
                            Contact Phone
                        </label>
                        <input type="tel" 
                               id="contact_phone" 
                               name="contact_phone" 
                               value="{{ old('contact_phone', $carrier->contact_phone) }}"
                               placeholder="+44 1234 567890"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('contact_phone')
                            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="is_active" 
                               value="1" 
                               {{ old('is_active', $carrier->is_active) ? 'checked' : '' }}
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm text-gray-700">
                            <strong>Active</strong> - Carrier is available for new bookings
                        </span>
                    </label>
                    @error('is_active')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Usage Statistics -->
                @if($carrier->bookings()->count() > 0)
                <div class="bg-green-50 p-4 rounded-lg border border-green-200">
                    <h3 class="text-lg font-medium text-green-900 mb-2">📊 Usage Statistics</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <div class="font-medium text-green-800">Total Bookings</div>
                            <div class="text-green-600">{{ $carrier->bookings()->count() }}</div>
                        </div>
                        <div>
                            <div class="font-medium text-green-800">Last Used</div>
                            <div class="text-green-600">{{ $carrier->last_used_at ? $carrier->last_used_at->diffForHumans() : 'Never' }}</div>
                        </div>
                        <div>
                            <div class="font-medium text-green-800">Depots</div>
                            <div class="text-green-600">{{ $carrier->depots()->count() }}</div>
                        </div>
                        <div>
                            <div class="font-medium text-green-800">Created</div>
                            <div class="text-green-600">{{ $carrier->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Depot Configuration -->
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <h3 class="text-lg font-medium text-blue-900 mb-4">🏢 Depot Configuration</h3>
                    <p class="text-sm text-blue-700 mb-4">Configure which depots this carrier can operate with and any restrictions.</p>
                    
                    <div id="depot-configs" class="space-y-4">
                        @foreach($depots as $depot)
                        @php
                            $config = $carrier->depots()->where('depot_id', $depot->id)->first();
                            $isEnabled = $config ? $config->pivot->is_enabled : false;
                            $autoDisableMonths = $config ? $config->pivot->auto_disable_months : 6;
                            $autoDisableUnused = $config ? $config->pivot->auto_disable_unused : true;
                            $allowedCustomerIds = $config ? json_decode($config->pivot->allowed_customer_ids, true) : [];
                        @endphp
                        <div class="bg-white p-4 rounded border">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-medium text-gray-900">{{ $depot->name }}</h4>
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="depot_configs[{{ $depot->id }}][enabled]"
                                           value="1"
                                           {{ $isEnabled ? 'checked' : '' }}
                                           class="rounded">
                                    <span class="ml-2 text-sm">Enable for this depot</span>
                                </label>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Auto-disable after (months)
                                    </label>
                                    <input type="number" 
                                           name="depot_configs[{{ $depot->id }}][auto_disable_months]"
                                           value="{{ $autoDisableMonths }}"
                                           min="1" max="24"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 text-sm">
                                </div>
                                
                                <div>
                                    <label class="flex items-center">
                                        <input type="checkbox" 
                                               name="depot_configs[{{ $depot->id }}][auto_disable_unused]"
                                               value="1"
                                               {{ $autoDisableUnused ? 'checked' : '' }}
                                               class="rounded">
                                        <span class="ml-2 text-sm">Auto-disable when unused</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Restrict to specific customers (optional)
                                </label>
                                <select name="depot_configs[{{ $depot->id }}][allowed_customer_ids][]" 
                                        multiple
                                        class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-blue-500 text-sm">
                                    @foreach($customers as $customer)
                                        <option value="{{ $customer->id }}" 
                                                {{ in_array($customer->id, $allowedCustomerIds ?: []) ? 'selected' : '' }}>
                                            {{ $customer->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Leave empty to allow all customers</p>
                            </div>
                            
                            <input type="hidden" name="depot_configs[{{ $depot->id }}][depot_id]" value="{{ $depot->id }}">
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                <a href="{{ route('app.carriers.show', $carrier) }}" 
                   class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-700 rounded-lg font-medium transition-colors">
                    Cancel
                </a>
                
                <button type="submit" 
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                    💾 Update Carrier
                </button>
            </div>
        </form>
    </div>
</div>
@endsection