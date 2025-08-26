@extends('layouts.admin')

@section('title', 'Create Customer Address')

@section('content')
<div class="py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center space-x-4">
                <a href="{{ route('outbound.addresses.index') }}" 
                   class="text-gray-600 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Create Customer Address</h1>
                    <p class="text-gray-600 mt-1">Add a new delivery address</p>
                </div>
            </div>
        </div>

        <form action="{{ route('outbound.addresses.store') }}" method="POST" class="space-y-8">
            @csrf

            <!-- Customer Selection -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Customer</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Customer <span class="text-red-500">*</span>
                            </label>
                            <select name="customer_id" id="customer_id" 
                                    class="form-select w-full rounded-md @error('customer_id') border-red-500 @enderror" required>
                                <option value="">Select Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="address_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Address Name (Optional)
                            </label>
                            <input type="text" name="address_name" id="address_name" 
                                   value="{{ old('address_name') }}"
                                   class="form-input w-full rounded-md @error('address_name') border-red-500 @enderror"
                                   placeholder="e.g., Main Warehouse, Head Office">
                            @error('address_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_default" value="1" 
                                   {{ old('is_default') ? 'checked' : '' }}
                                   class="form-checkbox rounded">
                            <span class="ml-2 text-sm text-gray-700">Set as default address for this customer</span>
                        </label>
                        @error('is_default')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Contact Information</h2>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="contact_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Contact Name
                            </label>
                            <input type="text" name="contact_name" id="contact_name" 
                                   value="{{ old('contact_name') }}"
                                   class="form-input w-full rounded-md @error('contact_name') border-red-500 @enderror"
                                   placeholder="Site contact person">
                            @error('contact_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="contact_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                Contact Phone
                            </label>
                            <input type="tel" name="contact_phone" id="contact_phone" 
                                   value="{{ old('contact_phone') }}"
                                   class="form-input w-full rounded-md @error('contact_phone') border-red-500 @enderror"
                                   placeholder="01234 567890">
                            @error('contact_phone')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="contact_email" class="block text-sm font-medium text-gray-700 mb-2">
                                Contact Email
                            </label>
                            <input type="email" name="contact_email" id="contact_email" 
                                   value="{{ old('contact_email') }}"
                                   class="form-input w-full rounded-md @error('contact_email') border-red-500 @enderror"
                                   placeholder="contact@company.com">
                            @error('contact_email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Address Details -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Address Details</h2>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Company Name
                        </label>
                        <input type="text" name="company_name" id="company_name" 
                               value="{{ old('company_name') }}"
                               class="form-input w-full rounded-md @error('company_name') border-red-500 @enderror"
                               placeholder="Company trading name">
                        @error('company_name')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="address_line_1" class="block text-sm font-medium text-gray-700 mb-2">
                                Address Line 1 <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="address_line_1" id="address_line_1" 
                                   value="{{ old('address_line_1') }}"
                                   class="form-input w-full rounded-md @error('address_line_1') border-red-500 @enderror"
                                   placeholder="Building number and street name" required>
                            @error('address_line_1')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="address_line_2" class="block text-sm font-medium text-gray-700 mb-2">
                                Address Line 2
                            </label>
                            <input type="text" name="address_line_2" id="address_line_2" 
                                   value="{{ old('address_line_2') }}"
                                   class="form-input w-full rounded-md @error('address_line_2') border-red-500 @enderror"
                                   placeholder="Additional address info">
                            @error('address_line_2')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div>
                            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
                                City <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="city" id="city" 
                                   value="{{ old('city') }}"
                                   class="form-input w-full rounded-md @error('city') border-red-500 @enderror"
                                   placeholder="City" required>
                            @error('city')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="county" class="block text-sm font-medium text-gray-700 mb-2">
                                County
                            </label>
                            <input type="text" name="county" id="county" 
                                   value="{{ old('county') }}"
                                   class="form-input w-full rounded-md @error('county') border-red-500 @enderror"
                                   placeholder="County">
                            @error('county')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="postcode" class="block text-sm font-medium text-gray-700 mb-2">
                                Postcode <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="postcode" id="postcode" 
                                   value="{{ old('postcode') }}"
                                   class="form-input w-full rounded-md @error('postcode') border-red-500 @enderror"
                                   placeholder="AB1 2CD" required>
                            @error('postcode')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="country" class="block text-sm font-medium text-gray-700 mb-2">
                                Country
                            </label>
                            <select name="country" id="country" 
                                    class="form-select w-full rounded-md @error('country') border-red-500 @enderror">
                                <option value="GB" {{ old('country', 'GB') === 'GB' ? 'selected' : '' }}>United Kingdom</option>
                                <option value="IE" {{ old('country') === 'IE' ? 'selected' : '' }}>Ireland</option>
                                <option value="FR" {{ old('country') === 'FR' ? 'selected' : '' }}>France</option>
                            </select>
                            @error('country')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delivery Requirements -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Delivery Requirements</h2>
                </div>
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" name="requires_appointment" value="1" 
                                       {{ old('requires_appointment') ? 'checked' : '' }}
                                       class="form-checkbox rounded">
                                <span class="ml-2 text-sm text-gray-700">Requires appointment</span>
                            </label>

                            <label class="flex items-center">
                                <input type="checkbox" name="requires_signature" value="1" 
                                       {{ old('requires_signature', true) ? 'checked' : '' }}
                                       class="form-checkbox rounded">
                                <span class="ml-2 text-sm text-gray-700">Requires signature</span>
                            </label>

                            <label class="flex items-center">
                                <input type="checkbox" name="requires_photo_proof" value="1" 
                                       {{ old('requires_photo_proof') ? 'checked' : '' }}
                                       class="form-checkbox rounded">
                                <span class="ml-2 text-sm text-gray-700">Requires photo proof</span>
                            </label>
                        </div>

                        <div>
                            <label for="latest_delivery_time" class="block text-sm font-medium text-gray-700 mb-2">
                                Latest Delivery Time
                            </label>
                            <input type="time" name="latest_delivery_time" id="latest_delivery_time" 
                                   value="{{ old('latest_delivery_time') }}"
                                   class="form-input w-full rounded-md @error('latest_delivery_time') border-red-500 @enderror">
                            @error('latest_delivery_time')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="unloading_duration_minutes" class="block text-sm font-medium text-gray-700 mb-2">
                                Unloading Time (minutes)
                            </label>
                            <input type="number" name="unloading_duration_minutes" id="unloading_duration_minutes" 
                                   value="{{ old('unloading_duration_minutes', 30) }}"
                                   class="form-input w-full rounded-md @error('unloading_duration_minutes') border-red-500 @enderror"
                                   min="5" max="240">
                            @error('unloading_duration_minutes')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="delivery_instructions" class="block text-sm font-medium text-gray-700 mb-2">
                                Delivery Instructions
                            </label>
                            <textarea name="delivery_instructions" id="delivery_instructions" rows="3"
                                      class="form-textarea w-full rounded-md @error('delivery_instructions') border-red-500 @enderror"
                                      placeholder="Special delivery requirements or instructions">{{ old('delivery_instructions') }}</textarea>
                            @error('delivery_instructions')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="access_notes" class="block text-sm font-medium text-gray-700 mb-2">
                                Site Access Notes
                            </label>
                            <textarea name="access_notes" id="access_notes" rows="3"
                                      class="form-textarea w-full rounded-md @error('access_notes') border-red-500 @enderror"
                                      placeholder="Access codes, gates, parking restrictions, etc.">{{ old('access_notes') }}</textarea>
                            @error('access_notes')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Status</h2>
                </div>
                <div class="p-6">
                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" value="1" 
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="form-checkbox rounded">
                            <span class="ml-2 text-sm text-gray-700">Address is active</span>
                        </label>
                        @error('is_active')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-sm text-gray-500 mt-1">
                            Inactive addresses cannot be used for new orders but existing orders are unaffected
                        </p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('outbound.addresses.index') }}" 
                   class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-2 rounded-md font-medium">
                    Cancel
                </a>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium">
                    Create Address
                </button>
            </div>
        </form>
    </div>
</div>
@endsection