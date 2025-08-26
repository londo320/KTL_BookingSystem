@extends('layouts.admin')

@section('title', 'Customer Addresses')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Customer Addresses</h1>
                    <p class="text-gray-600 mt-1">Manage delivery addresses and constraints</p>
                </div>
                <a href="{{ route('outbound.addresses.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">
                    New Address
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="p-6">
                <form method="GET" action="{{ route('outbound.addresses.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                        <select name="customer" class="form-select w-full rounded-md">
                            <option value="">All Customers</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ request('customer') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">City</label>
                        <input type="text" name="city" value="{{ request('city') }}" 
                               class="form-input w-full rounded-md" placeholder="Enter city">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Postcode</label>
                        <input type="text" name="postcode" value="{{ request('postcode') }}" 
                               class="form-input w-full rounded-md" placeholder="Enter postcode">
                    </div>
                    <div class="flex items-end space-x-2">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status" class="form-select w-full rounded-md">
                                <option value="">All</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                        <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                            Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Addresses Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Customer & Address
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Contact
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Location
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Delivery Constraints
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($addresses as $address)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div class="flex items-start">
                                        <div class="flex-1">
                                            <div class="text-sm font-medium text-gray-900 flex items-center">
                                                {{ $address->customer->name }}
                                                @if($address->is_default)
                                                    <span class="ml-2 px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Default</span>
                                                @endif
                                            </div>
                                            @if($address->address_name)
                                                <div class="text-sm text-blue-600">{{ $address->address_name }}</div>
                                            @endif
                                            @if($address->company_name)
                                                <div class="text-sm text-gray-600">{{ $address->company_name }}</div>
                                            @endif
                                            <div class="text-sm text-gray-900 mt-1">
                                                {{ $address->address_line_1 }}
                                                @if($address->address_line_2)
                                                    <br>{{ $address->address_line_2 }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        @if($address->contact_name)
                                            <div class="font-medium">{{ $address->contact_name }}</div>
                                        @endif
                                        @if($address->contact_phone)
                                            <div>{{ $address->contact_phone }}</div>
                                        @endif
                                        @if($address->contact_email)
                                            <div class="text-blue-600">{{ $address->contact_email }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <div class="font-medium">{{ $address->city }}</div>
                                        <div>{{ $address->postcode }}</div>
                                        @if($address->county)
                                            <div class="text-gray-500">{{ $address->county }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm">
                                        @php
                                            $constraints = [];
                                            if($address->requires_appointment) $constraints[] = 'Appointment';
                                            if($address->requires_signature) $constraints[] = 'Signature';
                                            if($address->requires_photo_proof) $constraints[] = 'Photo proof';
                                            if($address->latest_delivery_time) $constraints[] = 'Latest: ' . $address->latest_delivery_time->format('H:i');
                                        @endphp
                                        @if(count($constraints) > 0)
                                            @foreach($constraints as $constraint)
                                                <span class="inline-block px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded mr-1 mb-1">
                                                    {{ $constraint }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="text-gray-500">No constraints</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $address->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $address->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('outbound.addresses.show', $address) }}" 
                                           class="text-blue-600 hover:text-blue-900">View</a>
                                        <a href="{{ route('outbound.addresses.edit', $address) }}" 
                                           class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        @if(!$address->is_default)
                                            <form method="POST" action="{{ route('outbound.addresses.destroy', $address) }}" 
                                                  class="inline" onsubmit="return confirm('Are you sure?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        <p>No addresses found</p>
                                        <p class="text-sm">Create your first address to get started</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($addresses->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $addresses->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection