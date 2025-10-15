@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Bay Capacity Rules</h1>
            <p class="text-gray-600 mt-1">Control maximum concurrent bookings by type, time, and depot</p>
        </div>
        <a href="{{ route('app.bay-capacity-rules.create') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            + New Rule
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Info Box --}}
    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <h3 class="text-sm font-semibold text-blue-900 mb-2">ℹ️ How Bay Capacity Rules Work</h3>
        <ul class="text-sm text-blue-800 space-y-1">
            <li><strong>Example:</strong> "Max 3 handball bookings at Depot A between 08:00-15:00"</li>
            <li><strong>Example:</strong> "Max 4 handball bookings at Depot B between 08:00-17:00"</li>
            <li><strong>Depot-Specific:</strong> Each depot can have different limits for the same booking type</li>
            <li><strong>Capacity Weight:</strong> Some booking types can use more capacity (e.g., 2.0 = uses double)</li>
        </ul>
    </div>

    {{-- Rules Table --}}
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Depot</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Booking Type</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Time Window</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Days</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Max Concurrent</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Weight</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($rules as $rule)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <span class="font-medium">{{ $rule->depot->name }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @if($rule->bookingType)
                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">
                                    {{ $rule->bookingType->name }}
                                </span>
                            @else
                                <span class="text-gray-500 text-xs">All Types</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm">
                            {{ \Carbon\Carbon::parse($rule->time_start)->format('H:i') }} -
                            {{ \Carbon\Carbon::parse($rule->time_end)->format('H:i') }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if($rule->days_of_week && count($rule->days_of_week) > 0)
                                <span class="text-xs">
                                    {{ implode(', ', array_map(fn($d) => substr($d, 0, 3), $rule->days_of_week)) }}
                                </span>
                            @else
                                <span class="text-gray-500 text-xs">All Days</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-semibold text-lg">{{ $rule->max_concurrent_bookings }}</span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            {{ $rule->capacity_weight }}x
                        </td>
                        <td class="px-4 py-3">
                            @if($rule->is_active)
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs">Active</span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs">Inactive</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 space-x-2">
                            <a href="{{ route('app.bay-capacity-rules.edit', $rule) }}"
                               class="text-blue-600 hover:underline text-sm">Edit</a>
                            <form action="{{ route('app.bay-capacity-rules.destroy', $rule) }}"
                                  method="POST"
                                  class="inline-block"
                                  onsubmit="return confirm('Delete this rule?');">
                                @csrf
                                @method('DELETE')
                                <button class="text-red-600 hover:underline text-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                            No bay capacity rules configured yet. Click "+ New Rule" to create one.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
