@extends('layouts.admin')

@section('content')
<div class="py-6 max-w-5xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">{{ $customRole->display_name }}</h1>
                <p class="text-sm text-gray-500 font-mono mt-1">{{ $customRole->name }}</p>
                @if($customRole->description)
                    <p class="text-sm text-gray-600 mt-2">{{ $customRole->description }}</p>
                @endif
            </div>
            <div class="flex gap-2">
                <a href="{{ route('app.custom-roles.edit', $customRole) }}"
                   class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm">
                    ✏️ Edit
                </a>
                <a href="{{ route('app.custom-roles.index') }}"
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 text-sm">
                    ← Back
                </a>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- Left: Status + Users --}}
            <div class="space-y-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-3 border-b pb-2">Status</h2>
                    @if($customRole->is_active)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            ✓ Active
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            ✗ Inactive
                        </span>
                    @endif
                </div>

                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-3 border-b pb-2">
                        👥 Assigned Users ({{ $customRole->users->count() }})
                    </h2>
                    @if($customRole->users->count() > 0)
                        <ul class="space-y-2">
                            @foreach($customRole->users as $user)
                                <li class="text-sm">
                                    <div class="text-gray-900">{{ $user->name }}</div>
                                    <div class="text-gray-500 text-xs">{{ $user->email }}</div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-sm text-gray-500">No users assigned to this role yet.</p>
                    @endif
                </div>
            </div>

            {{-- Right: Functions --}}
            <div class="lg:col-span-2">
                <h2 class="text-lg font-semibold text-gray-900 mb-3 border-b pb-2">
                    🔧 Functions ({{ count($customRole->getFunctionKeys()) }})
                </h2>
                @php
                    $assignedKeys = $customRole->getFunctionKeys();
                @endphp
                @if(count($assignedKeys) > 0)
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
                        @foreach($allFunctions as $category => $functions)
                            @php
                                $assignedInCategory = array_intersect(array_keys($functions), $assignedKeys);
                            @endphp
                            @if(count($assignedInCategory) > 0)
                                <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                                    <div class="font-medium text-gray-900 text-sm mb-2">{{ $category }}</div>
                                    <ul class="space-y-1">
                                        @foreach($assignedInCategory as $key)
                                            <li class="text-sm text-gray-700">
                                                {{ $functions[$key] }}
                                                <span class="text-xs text-gray-400 font-mono block">{{ $key }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-gray-500">No functions assigned to this role.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
