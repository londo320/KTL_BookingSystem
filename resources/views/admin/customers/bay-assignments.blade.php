@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Bay Assignment Configuration</h1>
                <p class="text-gray-600 mt-1">Configure bay access and priority for <strong>{{ $customer->name }}</strong></p>
            </div>
            <a href="{{ route('app.customers.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                ← Back to Customers
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
        <h3 class="text-sm font-semibold text-blue-900 mb-2">ℹ️ How Bay Assignments Work</h3>
        <ul class="text-sm text-blue-800 space-y-1">
            <li><strong>Allowed/Blocked:</strong> Control which bays this customer can use</li>
            <li><strong>Priority:</strong> Higher priority (0-100) = bay is preferred for this customer</li>
            <li><strong>Default Behavior:</strong> If no assignments configured, customer can use any available bay</li>
            <li><strong>Equipment Matching:</strong> System automatically prioritizes bays with required equipment</li>
        </ul>
    </div>

    <form action="{{ route('app.customers.bay-assignments.update', $customer) }}" method="POST" class="space-y-6">
        @csrf

        {{-- Bay Assignments by Depot --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">🚪 Bay Assignments by Depot</h2>

            <div class="space-y-6">
                @foreach($depots as $depot)
                    @if($depot->bays->count() > 0)
                        <div class="border-2 border-gray-200 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-800 mb-3 text-lg">{{ $depot->name }}</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($depot->bays as $bay)
                                    @php
                                        $assignment = $assignments->get($bay->id);
                                    @endphp
                                    <div class="border rounded-lg p-3 hover:border-blue-300 transition bg-gray-50">
                                        <div class="flex items-center justify-between mb-2">
                                            <h4 class="font-semibold text-gray-800">{{ $bay->name }}</h4>
                                            @if($bay->code)
                                                <span class="text-xs px-2 py-0.5 bg-blue-100 text-blue-800 rounded">
                                                    {{ $bay->code }}
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Equipment Tags --}}
                                        @if($bay->equipment && count($bay->equipment) > 0)
                                            <div class="mb-2 flex flex-wrap gap-1">
                                                @foreach($bay->equipment as $equip)
                                                    <span class="text-[10px] px-1.5 py-0.5 bg-green-100 text-green-800 rounded">
                                                        {{ $equip }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif

                                        {{-- Access Control --}}
                                        <div class="mb-2">
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Access</label>
                                            <select name="bays[{{ $bay->id }}][is_allowed]"
                                                    class="block w-full border-gray-300 rounded text-sm py-1">
                                                <option value="">Default (No Restriction)</option>
                                                <option value="1" @selected(($assignment?->is_active ?? null) === true)>✅ Allowed</option>
                                                <option value="0" @selected(($assignment?->is_active ?? null) === false)>🚫 Blocked</option>
                                            </select>
                                        </div>

                                        {{-- Priority --}}
                                        <div>
                                            <label class="block text-xs font-medium text-gray-600 mb-1">Priority (0-100)</label>
                                            <input type="number"
                                                   name="bays[{{ $bay->id }}][priority]"
                                                   value="{{ $assignment?->priority ?? 50 }}"
                                                   min="0"
                                                   max="100"
                                                   class="block w-full border-gray-300 rounded text-sm py-1">
                                            <p class="text-[10px] text-gray-500 mt-0.5">Higher = More preferred</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="border rounded-lg p-4 bg-gray-50">
                            <h3 class="font-semibold text-gray-800 mb-2">{{ $depot->name }}</h3>
                            <p class="text-sm text-gray-500">No bays configured for this depot</p>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="flex justify-end gap-3">
            <a href="{{ route('app.customers.index') }}" class="px-6 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                💾 Save Bay Assignments
            </button>
        </div>
    </form>
</div>
@endsection
