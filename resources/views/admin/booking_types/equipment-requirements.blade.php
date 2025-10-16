@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Equipment Requirements</h1>
                <p class="text-gray-600 mt-1">Configure equipment requirements for <strong>{{ $bookingType->name }}</strong> bookings</p>
            </div>
            <a href="{{ route('app.booking-types.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                ← Back to Booking Types
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Info Box --}}
    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
        <h3 class="text-sm font-semibold text-blue-900 mb-2">ℹ️ How Equipment Requirements Work</h3>
        <ul class="text-sm text-blue-800 space-y-1">
            <li><strong>Required Equipment:</strong> Booking cannot be made if no bay has this equipment</li>
            <li><strong>Priority Boost:</strong> Bays with required equipment get priority (0-100 points added)</li>
            <li><strong>Examples:</strong> "Ramp" required for unloading, "Cold Storage" for frozen goods</li>
            <li><strong>Bay Matching:</strong> System automatically assigns bays with required equipment first</li>
        </ul>
    </div>

    <form action="{{ route('app.booking-types.equipment.update', $bookingType) }}" method="POST" class="space-y-6">
        @csrf

        {{-- Equipment Requirements --}}
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-4 border-b pb-2">
                <h2 class="text-lg font-bold text-gray-900">🛠️ Equipment Requirements</h2>
                <a href="{{ route('app.equipment-types.index') }}" target="_blank" class="text-xs text-blue-600 hover:underline">
                    Manage Equipment Types
                </a>
            </div>

            <div class="space-y-4">
                @if($equipmentTypes->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($equipmentTypes as $type)
                            @php
                                $requirement = $requirements->get($type->key);
                            @endphp
                            <div class="border rounded-lg p-4 hover:border-blue-300 transition @if($requirement?->is_required) bg-blue-50 border-blue-300 @else bg-gray-50 @endif">
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="font-semibold text-gray-800">{{ $type->name }}</h3>
                                    <span class="text-xs text-gray-500">{{ $type->key }}</span>
                                </div>

                                @if($type->description)
                                    <p class="text-xs text-gray-600 mb-3">{{ $type->description }}</p>
                                @endif

                                {{-- Required Checkbox --}}
                                <div class="mb-3">
                                    <label class="flex items-center">
                                        <input type="checkbox"
                                               name="equipment[{{ $type->key }}][required]"
                                               value="1"
                                               @checked($requirement?->is_required ?? false)
                                               class="mr-2 h-4 w-4">
                                        <span class="text-sm font-medium">Required for this booking type</span>
                                    </label>
                                </div>

                                {{-- Priority Boost --}}
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Priority Boost (0-100)</label>
                                    <input type="number"
                                           name="equipment[{{ $type->key }}][priority_boost]"
                                           value="{{ $requirement?->priority_boost ?? 10 }}"
                                           min="0"
                                           max="100"
                                           class="block w-full border-gray-300 rounded text-sm py-1">
                                    <p class="text-[10px] text-gray-500 mt-0.5">Points added to bay priority</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">
                        No equipment types configured.
                        <a href="{{ route('app.equipment-types.create') }}" class="text-blue-600 hover:underline">Create equipment types</a> to configure requirements.
                    </p>
                @endif
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('app.booking-types.index') }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                💾 Save Equipment Requirements
            </button>
        </div>
    </form>
</div>
@endsection
