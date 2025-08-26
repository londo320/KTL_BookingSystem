<x-warehouse-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Tipping Bays Management</h2>
                <p class="text-sm text-gray-600 mt-1">Manage tipping bays for each depot</p>
            </div>
            <div class="flex space-x-2">
                <form method="GET" class="flex items-center space-x-2">
                    <select name="depot_id" onchange="this.form.submit()" class="px-3 py-2 border border-gray-300 rounded-md text-sm">
                        <option value="">All Depots</option>
                        @foreach($allDepots as $depot)
                            <option value="{{ $depot->id }}" {{ request('depot_id') == $depot->id ? 'selected' : '' }}>
                                {{ $depot->name }}
                            </option>
                        @endforeach
                    </select>
                </form>
                <a href="{{ route('app.tipping-bays.create') }}" 
                   class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    + New Bay
                </a>
            </div>
        </div>
    </x-slot>
    <div class="py-6 max-w-7xl mx-auto">
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <h4 class="font-medium">Errors:</h4>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">🚛 Tipping Bays ({{ $bays->total() }})</h3>
            </div>
            @if($bays->isEmpty())
                <div class="p-8 text-center text-gray-500">
                    <div class="text-4xl mb-4">🚛</div>
                    <p class="text-lg mb-2">No tipping bays found</p>
                    <p class="text-sm mb-4">Create tipping bays where trailers can be processed and unloaded.</p>
                    <a href="{{ route('app.tipping-bays.create') }}" 
                       class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Create First Bay
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Bay
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Depot
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Equipment
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Current Booking
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($bays as $bay)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $bay->name }}</div>
                                            @if($bay->code)
                                                <div class="text-sm text-gray-500">Code: {{ $bay->code }}</div>
                                            @endif
                                            @if($bay->description)
                                                <div class="text-xs text-gray-400 mt-1">{{ Str::limit($bay->description, 50) }}</div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $bay->depot->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if(!empty($bay->equipment))
                                            <div class="space-y-1">
                                                @foreach($bay->equipment as $equipment)
                                                    <span class="inline-block px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded">{{ $equipment }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-gray-400">None specified</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(!$bay->is_active)
                                            <span class="px-2 py-1 text-xs bg-gray-100 text-gray-800 rounded-full">Inactive</span>
                                        @elseif($bay->is_occupied)
                                            <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Occupied</span>
                                        @else
                                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Available</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        @if($bay->currentBooking)
                                            <div>
                                                <div class="font-medium">{{ $bay->currentBooking->customer->name }}</div>
                                                <div class="text-xs text-gray-500">Booking #{{ $bay->currentBooking->id }}</div>
                                                <div class="text-xs">{!! $bay->currentBooking->tipping_status_badge !!}</div>
                                                @if($bay->currentBooking->tipping_started_at)
                                                    <div class="text-xs text-gray-400">{{ $bay->currentBooking->tipping_started_at->diffForHumans() }}</div>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <a href="{{ route('app.tipping-bays.show', $bay) }}" 
                                           class="text-blue-600 hover:text-blue-900">View</a>
                                        @if($bay->depot_id === $defaultDepotId)
                                            <a href="{{ route('app.tipping-bays.edit', $bay) }}" 
                                               class="text-yellow-600 hover:text-yellow-900">Edit</a>
                                        @else
                                            <span class="text-gray-400" title="Can only edit bays in your default depot">Edit</span>
                                        @endif
                                        @if($bay->currentBooking)
                                            <a href="{{ route('app.tipping-workflow.show', $bay->currentBooking) }}" 
                                               class="text-orange-600 hover:text-orange-900">Manage</a>
                                        @endif
                                        @if($bay->is_occupied)
                                            <form method="POST" action="{{ route('app.tipping-bays.mark-available', $bay) }}" 
                                                  class="inline-block" onsubmit="return confirm('Mark this bay as available?');">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-900">Free Up</button>
                                            </form>
                                        @endif
                                        @if(!$bay->is_occupied && $bay->depot_id === $defaultDepotId)
                                            <form method="POST" action="{{ route('app.tipping-bays.destroy', $bay) }}" 
                                                  class="inline-block" onsubmit="return confirm('Are you sure you want to delete this bay?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                            </form>
                                        @elseif($bay->is_occupied)
                                            <span class="text-gray-400" title="Cannot delete - bay is occupied">Delete</span>
                                        @else
                                            <span class="text-gray-400" title="Can only delete bays in your default depot">Delete</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $bays->links() }}
                </div>
            @endif
        </div>
    </div>
</x-warehouse-layout>