@extends('layouts.admin')

@section('title', 'Outbound Loads')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Outbound Loads</h1>
                    <p class="text-gray-600 mt-1">Manage delivery loads and consignments</p>
                </div>
                <a href="{{ route('outbound.loads.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md font-medium">
                    New Load
                </a>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow mb-8">
            <div class="p-6">
                <form method="GET" action="{{ route('outbound.loads.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="form-select w-full rounded-md">
                            <option value="">All Statuses</option>
                            <option value="planning" {{ request('status') === 'planning' ? 'selected' : '' }}>Planning</option>
                            <option value="ready_for_collection" {{ request('status') === 'ready_for_collection' ? 'selected' : '' }}>Ready for Collection</option>
                            <option value="collecting" {{ request('status') === 'collecting' ? 'selected' : '' }}>Collecting</option>
                            <option value="in_transit" {{ request('status') === 'in_transit' ? 'selected' : '' }}>In Transit</option>
                            <option value="delivering" {{ request('status') === 'delivering' ? 'selected' : '' }}>Delivering</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Vehicle</label>
                        <select name="vehicle" class="form-select w-full rounded-md">
                            <option value="">All Vehicles</option>
                            @foreach($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}" {{ request('vehicle') == $vehicle->id ? 'selected' : '' }}>
                                    {{ $vehicle->registration }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                        <input type="date" name="date_from" value="{{ request('date_from') }}" 
                               class="form-input w-full rounded-md">
                    </div>
                    <div class="flex items-end space-x-2">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" 
                                   class="form-input w-full rounded-md">
                        </div>
                        <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md">
                            Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Loads Table -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Load Reference
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Orders/Customers
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Vehicle/Driver
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Collections
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($loads as $load)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $load->load_reference }}
                                            </div>
                                            @if($load->load_name)
                                                <div class="text-sm text-gray-500">{{ $load->load_name }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs font-medium rounded-full 
                                        @switch($load->status)
                                            @case('planning')
                                                bg-gray-100 text-gray-800
                                                @break
                                            @case('ready_for_collection')
                                                bg-blue-100 text-blue-800
                                                @break
                                            @case('collecting')
                                                bg-yellow-100 text-yellow-800
                                                @break
                                            @case('in_transit')
                                                bg-orange-100 text-orange-800
                                                @break
                                            @case('delivering')
                                                bg-purple-100 text-purple-800
                                                @break
                                            @case('completed')
                                                bg-green-100 text-green-800
                                                @break
                                            @case('cancelled')
                                                bg-red-100 text-red-800
                                                @break
                                            @default
                                                bg-gray-100 text-gray-800
                                        @endswitch">
                                        {{ ucfirst(str_replace('_', ' ', $load->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center space-x-4">
                                        <div>
                                            <div class="font-medium">{{ $load->total_orders }} orders</div>
                                            <div class="text-gray-500">{{ $load->total_customers }} customers</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div>
                                        @if($load->plannedVehicle)
                                            <div class="font-medium">{{ $load->plannedVehicle->registration }}</div>
                                        @else
                                            <div class="text-gray-500 italic">Not assigned</div>
                                        @endif
                                        @if($load->assignedDriver)
                                            <div class="text-gray-500">{{ $load->assignedDriver->name }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $load->total_collection_points }} points
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $load->created_at->format('M j, Y') }}
                                    <div class="text-xs">{{ $load->created_at->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <a href="{{ route('outbound.loads.show', $load) }}" 
                                           class="text-blue-600 hover:text-blue-900">View</a>
                                        <a href="{{ route('outbound.loads.edit', $load) }}" 
                                           class="text-indigo-600 hover:text-indigo-900">Edit</a>
                                        @if($load->status === 'planning')
                                            <form method="POST" action="{{ route('outbound.loads.destroy', $load) }}" 
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
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <p>No loads found</p>
                                        <p class="text-sm">Create your first load to get started</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($loads->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $loads->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection