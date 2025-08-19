@extends('layouts.admin')

@section('title', $carrier->name . ' - Carrier Details')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    🚛 {{ $carrier->name }}
                    @if($carrier->trashed())
                        <span class="ml-2 px-3 py-1 text-sm bg-gray-100 text-gray-800 rounded">DELETED</span>
                    @elseif(!$carrier->is_active && str_contains($carrier->name, '(MERGED INTO:'))
                        <span class="ml-2 px-3 py-1 text-sm bg-orange-100 text-orange-800 rounded">MERGED</span>
                    @elseif(!$carrier->is_active)
                        <span class="ml-2 px-3 py-1 text-sm bg-red-100 text-red-800 rounded">Inactive</span>
                    @endif
                    @if($carrier->requires_approval)
                        <span class="ml-2 px-3 py-1 text-sm bg-amber-100 text-amber-800 rounded">Pending Approval</span>
                    @endif
                </h1>
                <p class="mt-2 text-gray-600">Carrier details and booking history</p>
                @if($carrier->trashed())
                    <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-sm text-yellow-800">
                            ⚠️ <strong>This carrier has been deleted.</strong> Some actions may be limited. You can restore it if needed.
                        </p>
                    </div>
                @elseif(!$carrier->is_active && str_contains($carrier->name, '(MERGED INTO:'))
                    <div class="mt-3 p-3 bg-orange-50 border border-orange-200 rounded-lg">
                        <p class="text-sm text-orange-800">
                            🔄 <strong>This carrier has been merged into another carrier.</strong> Historical data is preserved for audit purposes.
                        </p>
                    </div>
                @endif
            </div>
            <div class="flex gap-3">
                <a href="{{ route('admin.carriers.edit', $carrier) }}" 
                   class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition-colors">
                    ✏️ Edit
                </a>
                <a href="{{ route('admin.carriers.index') }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    ← Back to Carriers
                </a>
            </div>
        </div>
    </div>

    <!-- Overview Stats -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-white rounded-lg shadow-sm border p-4 text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $carrier->bookings()->count() }}</div>
            <div class="text-sm text-gray-600">📦 Total Bookings</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4 text-center">
            <div class="text-2xl font-bold text-green-600">{{ $bookingsByDepot->count() }}</div>
            <div class="text-sm text-gray-600">🏢 Depots Used</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4 text-center">
            <div class="text-2xl font-bold text-purple-600">{{ $carrier->depots()->count() }}</div>
            <div class="text-sm text-gray-600">⚙️ Configured Depots</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4 text-center">
            <div class="text-2xl font-bold text-amber-600">
                {{ $carrier->last_used_at ? $carrier->last_used_at->diffInDays(now()) : '∞' }}
            </div>
            <div class="text-sm text-gray-600">📅 Days Since Used</div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border p-4 text-center">
            <div class="text-2xl font-bold {{ $carrier->is_active ? 'text-green-600' : 'text-red-600' }}">
                {{ $carrier->is_active ? 'Active' : 'Inactive' }}
            </div>
            <div class="text-sm text-gray-600">🔴 Status</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            
            <!-- Contact Information -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">📞 Contact Information</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <div class="mt-1 text-gray-900">
                                {{ $carrier->contact_email ?: 'Not provided' }}
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Phone</label>
                            <div class="mt-1 text-gray-900">
                                {{ $carrier->contact_phone ?: 'Not provided' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Depot Configuration -->
            @if($carrier->depots()->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">🏢 Depot Configuration</h2>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        @foreach($carrier->depots as $depot)
                        <div class="p-4 border rounded-lg {{ $depot->pivot->is_enabled ? 'bg-green-50 border-green-200' : 'bg-gray-50 border-gray-200' }}">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-medium text-gray-900">{{ $depot->name }}</h3>
                                <span class="px-2 py-1 text-xs rounded {{ $depot->pivot->is_enabled ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $depot->pivot->is_enabled ? 'Enabled' : 'Disabled' }}
                                </span>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Auto-disable:</span>
                                    <span class="font-medium">
                                        {{ $depot->pivot->auto_disable_unused ? 'After ' . $depot->pivot->auto_disable_months . ' months' : 'Disabled' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Customer restrictions:</span>
                                    <span class="font-medium">
                                        @php
                                            $allowedCustomerIds = json_decode($depot->pivot->allowed_customer_ids, true);
                                        @endphp
                                        {{ $allowedCustomerIds ? count($allowedCustomerIds) . ' customers' : 'All customers' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Booking History by Depot -->
            @if($bookingsByDepot->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">📊 Bookings by Depot</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($bookingsByDepot as $depot)
                        <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
                            <div class="font-medium text-blue-900">{{ $depot->name }}</div>
                            <div class="text-2xl font-bold text-blue-600">{{ $depot->count }}</div>
                            <div class="text-sm text-blue-700">bookings</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Recent Bookings -->
            @if($recentBookings->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">📋 Recent Bookings</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Depot</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reference</th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($recentBookings as $booking)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $booking->created_at->format('M j, Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $booking->slot->depot->name }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900">
                                    {{ $booking->customer->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 text-sm font-mono text-gray-900">
                                    {{ $booking->booking_reference }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('admin.bookings.show', $booking) }}" 
                                       class="text-blue-600 hover:text-blue-800 text-sm">
                                        View →
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">⚡ Quick Actions</h2>
                </div>
                <div class="p-4 space-y-3">
                    @if($carrier->trashed())
                        <form action="{{ route('admin.carriers.restore', $carrier->id) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                                🔄 Restore Carrier
                            </button>
                        </form>
                    @else
                        <button onclick="toggleCarrier({{ $carrier->id }})" 
                                class="w-full px-4 py-2 {{ $carrier->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-lg transition-colors">
                            {{ $carrier->is_active ? '❌ Deactivate' : '✅ Activate' }}
                        </button>
                        
                        <a href="{{ route('admin.carriers.edit', $carrier) }}" 
                           class="w-full inline-block text-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg transition-colors">
                            ✏️ Edit Details
                        </a>
                        
                        @if($carrier->bookings()->count() === 0)
                        <form action="{{ route('admin.carriers.destroy', $carrier) }}" method="POST" 
                              onsubmit="return confirm('Are you sure you want to delete this carrier? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                                🗑️ Delete Carrier
                            </button>
                        </form>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Merge History -->
            @if($carrier->mergesAsSource->count() > 0 || $carrier->mergesAsTarget->count() > 0)
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">🔄 Merge History</h2>
                </div>
                <div class="p-4 space-y-3">
                    @foreach($carrier->mergesAsTarget as $merge)
                    <div class="p-3 bg-green-50 rounded border text-sm">
                        <div class="font-medium text-green-800">Merged from: {{ $merge->source_carrier_name }}</div>
                        <div class="text-green-600">{{ $merge->created_at->diffForHumans() }}</div>
                        <div class="text-green-600">{{ $merge->bookings_moved }} bookings moved</div>
                    </div>
                    @endforeach
                    
                    @foreach($carrier->mergesAsSource as $merge)
                    <div class="p-3 bg-blue-50 rounded border text-sm">
                        <div class="font-medium text-blue-800">Merged into: {{ $merge->target_carrier_name }}</div>
                        <div class="text-blue-600">{{ $merge->created_at->diffForHumans() }}</div>
                        <div class="text-blue-600">{{ $merge->bookings_moved }} bookings moved</div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Metadata -->
            <div class="bg-white rounded-lg shadow-sm border">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">📝 Metadata</h2>
                </div>
                <div class="p-4 space-y-3 text-sm">
                    <div>
                        <span class="text-gray-600">Created:</span>
                        <span class="font-medium">{{ $carrier->created_at->format('M j, Y H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Last Updated:</span>
                        <span class="font-medium">{{ $carrier->updated_at->format('M j, Y H:i') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Last Used:</span>
                        <span class="font-medium">{{ $carrier->last_used_at ? $carrier->last_used_at->format('M j, Y H:i') : 'Never' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">ID:</span>
                        <span class="font-mono">{{ $carrier->id }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleCarrier(carrierId) {
    if (confirm('Are you sure you want to toggle this carrier\'s status?')) {
        fetch(`/admin/carriers/${carrierId}/toggle`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            location.reload();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the carrier.');
        });
    }
}
</script>
@endsection