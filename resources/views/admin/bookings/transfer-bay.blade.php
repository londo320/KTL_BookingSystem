<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Transfer Booking to New Bay</h2>
                <p class="text-sm text-gray-600 mt-1">Booking #{{ $booking->id }} - {{ $booking->customer->name ?? 'No Customer' }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('app.bookings.show', $booking) }}" 
                   class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                    ← Back to Booking
                </a>
            </div>
        </div>
    </x-slot>
    <div class="py-6 max-w-4xl mx-auto">
        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <h4 class="font-bold">Transfer Failed</h4>
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{-- Current Bay Information --}}
        <div class="mb-6 p-6 bg-blue-50 border border-blue-200 rounded-lg">
            <h3 class="text-lg font-semibold text-blue-800 mb-3">🚛 Current Bay Assignment</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Current Bay</p>
                    <p class="font-medium">{{ $booking->tippingBay->name ?? 'Not assigned' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Depot</p>
                    <p class="font-medium">{{ $booking->tippingBay->depot->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Current Status</p>
                    <div>{!! $booking->tipping_status_badge !!}</div>
                </div>
            </div>
            @if($booking->moved_to_bay_at)
                <div class="mt-4">
                    <p class="text-sm text-gray-600">Moved to Current Bay</p>
                    <p class="text-gray-800">{{ $booking->moved_to_bay_at->format('M j, Y H:i') }}</p>
                </div>
            @endif
            @if($booking->bay_transferred_at)
                <div class="mt-4 p-3 bg-orange-50 border border-orange-200 rounded">
                    <p class="text-sm font-medium text-orange-800">Previous Transfer</p>
                    <p class="text-sm text-orange-700">
                        Transferred {{ $booking->bay_transferred_at->format('M j, Y H:i') }}
                        @if($booking->bay_transfer_reason)
                            - {{ $booking->bay_transfer_reason }}
                        @endif
                    </p>
                </div>
            @endif
        </div>
        {{-- Transfer Form --}}
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800">🔄 Transfer to New Bay</h3>
            </div>
            <form method="POST" action="{{ route('app.bookings.transfer-bay', $booking) }}" class="p-6">
                @csrf
                {{-- New Bay Selection --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select New Bay</label>
                    <select name="new_bay_id" required class="w-full border-gray-300 rounded-lg">
                        <option value="">– Choose Available Bay –</option>
                        @foreach($availableBays as $bay)
                            <option value="{{ $bay->id }}" @selected(old('new_bay_id') == $bay->id)>
                                {{ $bay->name }} ({{ $bay->depot->name }})
                                @if($bay->is_occupied)
                                    - Occupied
                                @else
                                    - Available
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('new_bay_id')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                {{-- Transfer Reason --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Transfer Reason</label>
                    <select name="transfer_reason" required class="w-full border-gray-300 rounded-lg">
                        <option value="">– Select Reason –</option>
                        <option value="Equipment failure" @selected(old('transfer_reason') == 'Equipment failure')>Equipment failure</option>
                        <option value="Maintenance required" @selected(old('transfer_reason') == 'Maintenance required')>Maintenance required</option>
                        <option value="Operational efficiency" @selected(old('transfer_reason') == 'Operational efficiency')>Operational efficiency</option>
                        <option value="Health and safety concern" @selected(old('transfer_reason') == 'Health and safety concern')>Health and safety concern</option>
                        <option value="Customer request" @selected(old('transfer_reason') == 'Customer request')>Customer request</option>
                        <option value="Other" @selected(old('transfer_reason') == 'Other')>Other</option>
                    </select>
                    @error('transfer_reason')
                        <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                {{-- Warning Message --}}
                <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <div class="text-yellow-600 text-xl">⚠️</div>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-yellow-800">Transfer Confirmation</h3>
                            <div class="mt-2 text-sm text-yellow-700">
                                <p>This action will:</p>
                                <ul class="list-disc list-inside mt-1">
                                    <li>Mark the current bay as available</li>
                                    <li>Assign the booking to the new bay</li>
                                    <li>Record the transfer time and reason</li>
                                    <li>Add a note to the booking's tipping log</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- Action Buttons --}}
                <div class="flex justify-end space-x-3">
                    <a href="{{ route('app.bookings.show', $booking) }}" 
                       class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-orange-500 text-white rounded hover:bg-orange-600">
                        🔄 Transfer Bay
                    </button>
                </div>
            </form>
        </div>
        {{-- Available Bays Information --}}
        @if($availableBays->isEmpty())
            <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-red-800 font-medium">⚠️ No Available Bays</p>
                <p class="text-red-700 text-sm">There are currently no available bays for transfer. Please wait for a bay to become available or contact an administrator.</p>
            </div>
        @else
            <div class="mt-6 bg-white rounded-lg shadow overflow-hidden">
                <div class="p-4 border-b border-gray-200">
                    <h4 class="font-medium text-gray-800">Available Bays ({{ $availableBays->count() }})</h4>
                </div>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($availableBays as $bay)
                            <div class="p-3 border border-gray-200 rounded">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-medium">{{ $bay->name }}</p>
                                        <p class="text-sm text-gray-600">{{ $bay->depot->name }}</p>
                                        @if($bay->description)
                                            <p class="text-xs text-gray-500 mt-1">{{ $bay->description }}</p>
                                        @endif
                                    </div>
                                    <span class="px-2 py-1 text-xs font-medium rounded 
                                        {{ $bay->is_occupied ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $bay->is_occupied ? 'Occupied' : 'Available' }}
                                    </span>
                                </div>
                                @if(!empty($bay->equipment))
                                    <div class="mt-2">
                                        <p class="text-xs text-gray-600">Equipment:</p>
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @foreach($bay->equipment as $equipment)
                                                <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded text-xs">{{ $equipment }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>