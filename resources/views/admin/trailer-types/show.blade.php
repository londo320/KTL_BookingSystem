@extends('layouts.admin')

@section('title', 'Trailer Type Details')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">🚛 {{ $trailerType->name }}</h1>
                <p class="mt-2 text-gray-600">Trailer type details and usage statistics</p>
            </div>
            <div class="flex gap-3">
                @if(!$trailerType->trashed())
                    <a href="{{ route('admin.trailer-types.edit', $trailerType) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                        ✏️ Edit
                    </a>
                @endif
                <a href="{{ route('admin.trailer-types.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    ← Back to List
                </a>
            </div>
        </div>
    </div>

    <!-- Status Alerts -->
    @if($trailerType->trashed())
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm text-red-800">
                    🗑️ <strong>Deleted:</strong> This trailer type has been deleted and is no longer available for new bookings.
                </p>
            </div>
            <div class="ml-3">
                <form action="{{ route('admin.trailer-types.restore', $trailerType->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        ♻️ Restore
                    </button>
                </form>
            </div>
        </div>
    </div>
    @elseif(!$trailerType->is_active)
    <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-lg">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div class="ml-3 flex-1">
                <p class="text-sm text-amber-800">
                    ⏸️ <strong>Inactive:</strong> This trailer type is inactive and won't appear in booking forms.
                </p>
            </div>
            <div class="ml-3">
                <form action="{{ route('admin.trailer-types.toggle', $trailerType->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" 
                            class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                        ▶️ Activate
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Details Panel -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-medium text-gray-900">Details</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <p class="mt-1 text-lg text-gray-900">{{ $trailerType->name }}</p>
                    </div>
                    
                    @if($trailerType->description)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <p class="mt-1 text-gray-900">{{ $trailerType->description }}</p>
                    </div>
                    @endif
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <div class="mt-1">
                            @if($trailerType->trashed())
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    🗑️ Deleted
                                </span>
                            @elseif($trailerType->is_active)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    ✅ Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                    ❌ Inactive
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Total Bookings</label>
                        <p class="mt-1 text-2xl font-bold text-blue-600">{{ $trailerType->bookings_count }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Created</label>
                        <p class="mt-1 text-gray-900">{{ $trailerType->created_at->format('M j, Y g:i A') }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Last Updated</label>
                        <p class="mt-1 text-gray-900">{{ $trailerType->updated_at->format('M j, Y g:i A') }}</p>
                    </div>
                    
                    @if($trailerType->deleted_at)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Deleted</label>
                        <p class="mt-1 text-gray-900">{{ $trailerType->deleted_at->format('M j, Y g:i A') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions Panel -->
            @if(!$trailerType->trashed())
            <div class="mt-6 bg-white rounded-lg shadow-sm border">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-medium text-gray-900">Actions</h3>
                </div>
                <div class="p-6 space-y-3">
                    <form action="{{ route('admin.trailer-types.toggle', $trailerType->id) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="w-full inline-flex items-center justify-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white {{ $trailerType->is_active ? 'bg-amber-600 hover:bg-amber-700' : 'bg-green-600 hover:bg-green-700' }} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500">
                            @if($trailerType->is_active)
                                ⏸️ Deactivate
                            @else
                                ▶️ Activate
                            @endif
                        </button>
                    </form>
                    
                    @if($trailerType->canBeDeleted())
                        <form action="{{ route('admin.trailer-types.destroy', $trailerType) }}" method="POST"
                              onsubmit="return confirm('Are you sure you want to delete this trailer type? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full inline-flex items-center justify-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                🗑️ Delete
                            </button>
                        </form>
                    @else
                        <div class="text-center p-4 bg-gray-50 rounded-md">
                            <span class="text-sm text-gray-500">
                                🔒 Cannot delete: has {{ $trailerType->bookings_count }} associated bookings
                            </span>
                        </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Bookings Panel -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="p-6 border-b">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Recent Bookings</h3>
                        <span class="text-sm text-gray-500">{{ $trailerType->bookings_count }} total</span>
                    </div>
                </div>
                
                @if($recentBookings->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Booking ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Depot</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($recentBookings as $booking)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    #{{ $booking->id }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $booking->customer->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $booking->slot->depot->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $booking->created_at->format('M j, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('admin.bookings.show', $booking) }}" 
                                       class="text-blue-600 hover:text-blue-900">
                                        View
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($trailerType->bookings_count > 10)
                <div class="p-4 border-t bg-gray-50 text-center">
                    <a href="{{ route('admin.bookings.index', ['trailer_type_id' => $trailerType->id]) }}" 
                       class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                        View all {{ $trailerType->bookings_count }} bookings →
                    </a>
                </div>
                @endif
                
                @else
                <div class="p-12 text-center">
                    <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p class="text-gray-500 text-lg font-medium mb-2">No bookings yet</p>
                    <p class="text-gray-400">This trailer type hasn't been used in any bookings.</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection