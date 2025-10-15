@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Booking Duration Rules</h1>
            <p class="text-gray-600 mt-1">Configure duration based on case count, depot, and customer</p>
        </div>
        <a href="{{ route('app.duration-rules.create') }}"
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
        <h3 class="text-sm font-semibold text-blue-900 mb-2">ℹ️ How Duration Rules Work</h3>
        <ul class="text-sm text-blue-800 space-y-1">
            <li><strong>Example:</strong> "Handball: 0-5000 cases = 180 minutes (3 hours)"</li>
            <li><strong>Example:</strong> "Handball: 5001+ cases = 240 minutes (4 hours)"</li>
            <li><strong>Priority:</strong> Higher priority rules are checked first (customer-specific > depot > global)</li>
            <li><strong>Fallback:</strong> If no rule matches, uses booking type default duration</li>
        </ul>
    </div>

    {{-- Rules Table --}}
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Booking Type</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Case Range</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Duration</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Depot</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Customer</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Priority</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-600 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($rules as $rule)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs font-medium">
                                {{ $rule->bookingType->name }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            {{ number_format($rule->min_cases) }} -
                            @if($rule->max_cases)
                                {{ number_format($rule->max_cases) }}
                            @else
                                <span class="text-gray-500">∞</span>
                            @endif
                            cases
                        </td>
                        <td class="px-4 py-3">
                            <span class="font-semibold text-lg">{{ $rule->duration_minutes }}</span>
                            <span class="text-sm text-gray-500">mins</span>
                            <span class="text-xs text-gray-400">({{ round($rule->duration_minutes / 60, 1) }}h)</span>
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if($rule->depot)
                                {{ $rule->depot->name }}
                            @else
                                <span class="text-gray-500 text-xs">All Depots</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-sm">
                            @if($rule->customer)
                                {{ $rule->customer->name }}
                            @else
                                <span class="text-gray-500 text-xs">All Customers</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($rule->priority > 0)
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs">
                                    {{ $rule->priority }}
                                </span>
                            @else
                                <span class="text-gray-400 text-xs">0</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 space-x-2">
                            <a href="{{ route('app.duration-rules.edit', $rule) }}"
                               class="text-blue-600 hover:underline text-sm">Edit</a>
                            <form action="{{ route('app.duration-rules.destroy', $rule) }}"
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
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            No duration rules configured yet. Click "+ New Rule" to create one.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
