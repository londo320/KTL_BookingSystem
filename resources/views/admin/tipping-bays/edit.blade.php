<x-app-layout>
    @include('layouts.admin-nav')

    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800">Edit Tipping Bay</h2>
                <p class="text-sm text-gray-600 mt-1">{{ $tippingBay->name }} - {{ $tippingBay->depot->name }}</p>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('admin.tipping-bays.show', $tippingBay) }}" 
                   class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    View Bay
                </a>
                <a href="{{ route('admin.tipping-bays.index') }}" 
                   class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                    ← Back to Bays
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto">
        @if (session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                <h4 class="font-medium">Please fix the following errors:</h4>
                <ul class="mt-2 list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-gray-800">🚛 Edit Tipping Bay</h3>
                <p class="text-sm text-gray-600 mt-1">Update bay settings and equipment</p>
            </div>

            <form method="POST" action="{{ route('admin.tipping-bays.update', $tippingBay) }}" class="p-6">
                @csrf
                @method('PUT')

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="depot_id">
                                Depot <span class="text-red-500">*</span>
                            </label>
                            <select name="depot_id" id="depot_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" required>
                                <option value="">Select depot...</option>
                                @foreach($depots as $depot)
                                    <option value="{{ $depot->id }}" {{ (old('depot_id', $tippingBay->depot_id) == $depot->id) ? 'selected' : '' }}>
                                        {{ $depot->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="name">
                                Bay Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $tippingBay->name) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="e.g., Bay 1, Tipping Bay A" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2" for="code">
                                Bay Code
                            </label>
                            <input type="text" name="code" id="code" value="{{ old('code', $tippingBay->code) }}" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                   placeholder="e.g., BAY-1, TB-A">
                            <p class="text-xs text-gray-500 mt-1">Short code for easy identification (optional)</p>
                        </div>

                        <div>
                            <div class="flex items-center mt-8">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" id="is_active" value="1" 
                                       {{ old('is_active', $tippingBay->is_active) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label class="ml-2 block text-sm text-gray-700" for="is_active">
                                    Active (available for use)
                                </label>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                Inactive bays won't be available for tipping
                                @if($tippingBay->currentBooking())
                                    <br><span class="text-orange-600">⚠️ Warning: This bay currently has a booking</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2" for="description">
                            Description
                        </label>
                        <textarea name="description" id="description" rows="3" 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                  placeholder="Additional details about this bay...">{{ old('description', $tippingBay->description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Equipment Available
                        </label>
                        <div id="equipment-container">
                            @php
                                $equipmentData = old('equipment', $tippingBay->equipment ?? []);
                            @endphp
                            @if($equipmentData && count($equipmentData) > 0)
                                @foreach($equipmentData as $index => $equipment)
                                    @if($equipment)
                                        <div class="flex items-center mb-2 equipment-item">
                                            <input type="text" name="equipment[]" value="{{ $equipment }}" 
                                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                                   placeholder="e.g., Forklift, Crane, Conveyor">
                                            <button type="button" onclick="removeEquipment(this)" 
                                                    class="ml-2 px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                                                Remove
                                            </button>
                                        </div>
                                    @endif
                                @endforeach
                            @endif
                        </div>
                        <button type="button" onclick="addEquipment()" 
                                class="mt-2 text-sm text-blue-600 hover:text-blue-800">
                            + Add Equipment
                        </button>
                        <p class="text-xs text-gray-500 mt-1">List any special equipment available at this bay</p>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('admin.tipping-bays.show', $tippingBay) }}" 
                       class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        Update Bay
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function addEquipment() {
            const container = document.getElementById('equipment-container');
            const div = document.createElement('div');
            div.className = 'flex items-center mb-2 equipment-item';
            div.innerHTML = `
                <input type="text" name="equipment[]" value="" 
                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                       placeholder="e.g., Forklift, Crane, Conveyor">
                <button type="button" onclick="removeEquipment(this)" 
                        class="ml-2 px-3 py-2 bg-red-500 text-white rounded hover:bg-red-600">
                    Remove
                </button>
            `;
            container.appendChild(div);
        }

        function removeEquipment(button) {
            button.parentElement.remove();
        }
    </script>
</x-app-layout>