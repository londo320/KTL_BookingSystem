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

                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" id="is_active" value="1" 
                                       {{ old('is_active', $tippingBay->is_active) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label class="ml-2 block text-sm text-gray-700" for="is_active">
                                    Active (available for use)
                                </label>
                            </div>
                            <p class="text-xs text-gray-500">
                                Inactive bays won't be available for tipping
                                @if($tippingBay->currentBooking())
                                    <br><span class="text-orange-600">⚠️ Warning: This bay currently has a booking</span>
                                @endif
                            </p>
                            
                            <div class="flex items-center">
                                <input type="hidden" name="show_on_map" value="0">
                                <input type="checkbox" name="show_on_map" id="show_on_map" value="1" 
                                       {{ old('show_on_map', $tippingBay->show_on_map) ? 'checked' : '' }}
                                       class="h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                <label class="ml-2 block text-sm text-gray-700" for="show_on_map">
                                    Show on depot map
                                </label>
                            </div>
                            <p class="text-xs text-gray-500">
                                Controls whether this bay appears on the visual depot map
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

                    <!-- Map Position Settings -->
                    <div class="border-t border-gray-200 pt-6">
                        <h4 class="text-lg font-medium text-gray-800 mb-4">🗺️ Map Position Settings</h4>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2" for="map_x">
                                    Map X Position (%)
                                </label>
                                <input type="number" name="map_x" id="map_x" 
                                       value="{{ old('map_x', $tippingBay->map_x) }}" 
                                       min="0" max="100" step="0.1"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                       placeholder="e.g., 25.5">
                                <p class="text-xs text-gray-500 mt-1">Horizontal position on map (0-100%)</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2" for="map_y">
                                    Map Y Position (%)
                                </label>
                                <input type="number" name="map_y" id="map_y" 
                                       value="{{ old('map_y', $tippingBay->map_y) }}" 
                                       min="0" max="100" step="0.1"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                       placeholder="e.g., 65.2">
                                <p class="text-xs text-gray-500 mt-1">Vertical position on map (0-100%)</p>
                            </div>
                        </div>
                        
                        <!-- Advanced Styling Section -->
                        <div class="border-t border-gray-200 pt-6 mt-6">
                            <h5 class="text-md font-medium text-gray-800 mb-4">🎨 Visual Styling</h5>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2" for="map_width">
                                        Width (px)
                                    </label>
                                    <input type="number" name="map_width" id="map_width" 
                                           value="{{ old('map_width', $tippingBay->map_width ?? 60) }}" 
                                           min="20" max="300"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                           placeholder="60">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2" for="map_height">
                                        Height (px)
                                    </label>
                                    <input type="number" name="map_height" id="map_height" 
                                           value="{{ old('map_height', $tippingBay->map_height ?? 40) }}" 
                                           min="15" max="200"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                           placeholder="40">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2" for="map_rotation">
                                        Rotation (°)
                                    </label>
                                    <input type="number" name="map_rotation" id="map_rotation" 
                                           value="{{ old('map_rotation', $tippingBay->map_rotation ?? 0) }}" 
                                           min="0" max="360" step="5"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                           placeholder="0">
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2" for="text_size">
                                        Text Size
                                    </label>
                                    <select name="text_size" id="text_size" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="xs" {{ old('text_size', $tippingBay->text_size ?? 'xs') === 'xs' ? 'selected' : '' }}>Extra Small</option>
                                        <option value="sm" {{ old('text_size', $tippingBay->text_size ?? 'xs') === 'sm' ? 'selected' : '' }}>Small</option>
                                        <option value="md" {{ old('text_size', $tippingBay->text_size ?? 'xs') === 'md' ? 'selected' : '' }}>Medium</option>
                                        <option value="lg" {{ old('text_size', $tippingBay->text_size ?? 'xs') === 'lg' ? 'selected' : '' }}>Large</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2" for="text_color">
                                    Text Color
                                </label>
                                <div class="flex items-center space-x-3">
                                    <input type="color" name="text_color" id="text_color" 
                                           value="{{ old('text_color', $tippingBay->text_color ?? '#ffffff') }}" 
                                           class="w-12 h-10 border border-gray-300 rounded cursor-pointer">
                                    <input type="text" name="text_color_hex" id="text_color_hex" 
                                           value="{{ old('text_color', $tippingBay->text_color ?? '#ffffff') }}" 
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-sm" 
                                           placeholder="#ffffff"
                                           maxlength="7"
                                           pattern="^#[0-9A-Fa-f]{6}$">
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Choose text color that contrasts well with your bay background</p>
                            </div>
                        </div>
                        
                        <div class="mt-4 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="text-blue-400">ℹ️</div>
                                </div>
                                <div class="ml-3">
                                    <h5 class="text-sm font-medium text-blue-800">Map Styling Tips:</h5>
                                    <ul class="mt-2 text-sm text-blue-700 space-y-1">
                                        <li>• Leave positions empty to position manually using the map editor</li>
                                        <li>• Use the <a href="{{ route('admin.depot-map.manage-positions') }}" class="underline hover:no-underline">Interactive Map Editor</a> for drag-and-drop positioning and real-time styling</li>
                                        <li>• Position values are percentages relative to the map image size</li>
                                        <li>• Rotation is useful for aligning bays with your depot layout</li>
                                        <li>• Choose text colors that contrast well with bay status colors</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
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
        
        // Sync color picker with hex input
        document.getElementById('text_color').addEventListener('input', function() {
            document.getElementById('text_color_hex').value = this.value;
        });
        
        document.getElementById('text_color_hex').addEventListener('input', function() {
            if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                document.getElementById('text_color').value = this.value;
            }
        });
    </script>
</x-app-layout>