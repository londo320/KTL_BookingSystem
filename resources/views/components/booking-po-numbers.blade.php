@props(['booking', 'readonly' => false, 'customer_view' => false, 'hide_actuals' => false, 'customer_id' => null])

@php
    $palletTypes = \App\Models\PalletType::active()->orderBy('name')->get();
    
    // Check if we have old input data (from validation errors)
    $oldPoNumbers = old('po_numbers', []);
    
    if (!empty($oldPoNumbers)) {
        // Use old input data if available (validation failed)
        $existingData = collect($oldPoNumbers)->map(function($po) {
            return [
                'po_number' => $po['po_number'] ?? '',
                'lines' => collect($po['lines'] ?? [])->map(function($line) {
                    return [
                        'line_number' => $line['line_number'] ?? 1,
                        'sku' => $line['sku'] ?? '',
                        'description' => $line['description'] ?? '',
                        'expected_cases' => $line['expected_cases'] ?? null,
                        'expected_pallets' => $line['expected_pallets'] ?? null,
                        'expected_pallet_type_id' => $line['expected_pallet_type_id'] ?? '',
                        'actual_cases' => $line['actual_cases'] ?? null,
                        'actual_pallets' => $line['actual_pallets'] ?? null,
                        'actual_pallet_type_id' => $line['actual_pallet_type_id'] ?? '',
                        'pallet_entries' => $line['pallet_entries'] ?? [['cases' => '', 'pallets' => '', 'type_id' => '']],
                    ];
                })->toArray()
            ];
        });
    } else {
        // Use existing booking data - transform to new pallet_entries structure
        $existingData = $booking->poNumbers()->with('lines.expectedPalletType', 'lines.actualPalletType')->get()->map(function($po) {
            return [
                'po_number' => $po->po_number,
                'lines' => $po->lines->map(function($line) {
                    // Convert existing expected_cases/pallets to pallet_entries format
                    $palletEntries = [];
                    if ($line->expected_cases > 0 || $line->expected_pallets > 0) {
                        $palletEntries[] = [
                            'cases' => $line->expected_cases ?: '',
                            'pallets' => $line->expected_pallets ?: '',
                            'type_id' => $line->expected_pallet_type_id ?: '',
                        ];
                    }
                    
                    // If no entries, add empty one for form
                    if (empty($palletEntries)) {
                        $palletEntries[] = ['cases' => '', 'pallets' => '', 'type_id' => ''];
                    }
                    
                    return [
                        'line_number' => $line->line_number,
                        'sku' => $line->sku ?? '',
                        'description' => $line->description ?? '',
                        'expected_cases' => $line->expected_cases,
                        'expected_pallets' => $line->expected_pallets,
                        'expected_pallet_type_id' => $line->expected_pallet_type_id,
                        'actual_cases' => $line->actual_cases,
                        'actual_pallets' => $line->actual_pallets,
                        'actual_pallet_type_id' => $line->actual_pallet_type_id,
                        'pallet_entries' => $palletEntries, // Add the new format for editing
                    ];
                })->toArray()
            ];
        });
    }
@endphp

<div x-data="poNumbersManager({{ $customer_id ?? 'null' }})" x-init="init({{ $existingData->toJson() }}); window.poNumbersManagerInstance = this;" @if($readonly) data-readonly="true" @endif>
    <div class="bg-white border rounded-lg p-4">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-medium text-gray-900">PO Numbers & Lines</h3>
            @unless($readonly)
                <button type="button" @click="addPoNumber()" 
                        class="btn-sm bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-sm">
                    + Add PO Number
                </button>
            @endunless
        </div>

        <!-- PO Numbers List -->
        <div class="space-y-6">
            <template x-for="(po, poIndex) in poNumbers" :key="poIndex">
                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                    <div class="flex justify-between items-start mb-3">
                        <h4 class="font-medium text-gray-800">PO Number <span x-text="poIndex + 1"></span></h4>
                        @unless($readonly)
                            <button type="button" @click="removePoNumber(poIndex)" 
                                    class="text-red-500 hover:text-red-700 text-sm">
                                Remove PO
                            </button>
                        @endunless
                    </div>

                    <!-- PO Number -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">PO Number *</label>
                        <input type="text" x-model="po.po_number" 
                               :name="`po_numbers[${poIndex}][po_number]`"
                               class="mt-1 block w-full border-gray-300 rounded {{ $readonly ? 'bg-gray-100' : '' }}"
                               {{ $readonly ? 'readonly' : '' }} required>
                    </div>

                    <!-- PO Lines -->
                    <div class="border-t pt-4">
                        <div class="flex justify-between items-center mb-3">
                            <h5 class="text-sm font-medium text-gray-700">PO Lines</h5>
                            @unless($readonly)
                                <button type="button" @click="addPoLine(poIndex)" 
                                        class="text-blue-600 hover:text-blue-800 text-sm">
                                    + Add Line
                                </button>
                            @endunless
                        </div>

                        <div class="space-y-4">
                            <template x-for="(line, lineIndex) in po.lines" :key="lineIndex">
                                <div class="border border-gray-300 rounded p-3 bg-white">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm font-medium text-gray-600">Line <span x-text="lineIndex + 1"></span></span>
                                        @unless($readonly)
                                            <button type="button" @click="removePoLine(poIndex, lineIndex)" 
                                                    class="text-red-500 hover:text-red-700 text-xs">
                                                Remove
                                            </button>
                                        @endunless
                                    </div>

                                    <input type="hidden" :name="`po_numbers[${poIndex}][lines][${lineIndex}][line_number]`"
                                           :value="lineIndex + 1">

                                    @if($hide_actuals)
                                        <!-- SKU and Description Fields -->
                                        <div class="mb-3 grid grid-cols-2 gap-3">
                                            <div class="relative">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">SKU / Product Number</label>
                                                <input type="text" x-model="line.sku"
                                                       :id="`sku-input-${poIndex}-${lineIndex}`"
                                                       :name="`po_numbers[${poIndex}][lines][${lineIndex}][sku]`"
                                                       @input="searchProducts(poIndex, lineIndex, $event.target.value)"
                                                       @focus="showSkuDropdown[`${poIndex}-${lineIndex}`] = false"
                                                       placeholder="e.g., SKU123"
                                                       autocomplete="off"
                                                       class="w-full border-gray-300 rounded {{ $readonly ? 'bg-gray-100' : '' }}"
                                                       {{ $readonly ? 'readonly' : '' }}>
                                                <div x-show="showSkuDropdown[`${poIndex}-${lineIndex}`]"
                                                     x-ref="skuDropdown"
                                                     @click.away="showSkuDropdown[`${poIndex}-${lineIndex}`] = false"
                                                     class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                                    <template x-for="product in skuSearchResults[`${poIndex}-${lineIndex}`] || []" :key="product.id">
                                                        <button type="button"
                                                                @click="selectProduct(poIndex, lineIndex, product)"
                                                                class="w-full text-left px-3 py-2 hover:bg-gray-100 border-b last:border-b-0">
                                                            <div class="font-medium text-sm" x-text="product.sku"></div>
                                                            <div class="text-xs text-gray-600" x-text="product.description"></div>
                                                        </button>
                                                    </template>
                                                    <div x-show="(skuSearchResults[`${poIndex}-${lineIndex}`] || []).length === 0 && skuSearchLoading[`${poIndex}-${lineIndex}`]"
                                                         class="px-3 py-2 text-sm text-gray-500">
                                                        Searching...
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Product Description</label>
                                                <input type="text" x-model="line.description"
                                                       :name="`po_numbers[${poIndex}][lines][${lineIndex}][description]`"
                                                       placeholder="Product description"
                                                       class="w-full border-gray-300 rounded {{ $readonly ? 'bg-gray-100' : '' }}"
                                                       {{ $readonly ? 'readonly' : '' }}>
                                            </div>
                                        </div>

                                        <!-- Simplified Three-Box Layout -->
                                        <div>
                                            <h6 class="text-sm font-medium text-gray-700 mb-3">Pallet & Case Details</h6>
                                            
                                            <!-- Multiple pallet type entries for the same line -->
                                            <div class="space-y-3">
                                                <template x-for="(palletEntry, palletIndex) in (line.pallet_entries || [{cases: '', pallets: '', type_id: ''}])" :key="palletIndex">
                                                    <div class="border border-gray-200 rounded-lg p-3 bg-gray-50">
                                                        <div class="flex justify-between items-center mb-2">
                                                            <span class="text-sm font-medium text-gray-600">
                                                                <span x-show="(line.pallet_entries || []).length > 1">Entry <span x-text="palletIndex + 1"></span></span>
                                                                <span x-show="(line.pallet_entries || []).length <= 1">Expected Quantities</span>
                                                            </span>
                                                            @unless($readonly)
                                                                <button x-show="palletIndex > 0 || (line.pallet_entries || []).length > 1" type="button" @click="removePalletEntry(poIndex, lineIndex, palletIndex)" 
                                                                        class="text-red-500 hover:text-red-700 text-xs">
                                                                    Remove
                                                                </button>
                                                            @endunless
                                                        </div>
                                                        
                                                        <div class="grid grid-cols-3 gap-3">
                                                            <!-- Cases (Required) -->
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                                    Cases <span class="text-red-500">*</span>
                                                                </label>
                                                                <input type="number" x-model="palletEntry.cases" 
                                                                       :name="`po_numbers[${poIndex}][lines][${lineIndex}][pallet_entries][${palletIndex}][cases]`"
                                                                       placeholder="0"
                                                                       required
                                                                       class="w-full border-gray-300 rounded {{ $readonly ? 'bg-gray-100' : '' }}"
                                                                       {{ $readonly ? 'readonly' : '' }}>
                                                            </div>
                                                            
                                                            <!-- Pallets (Required) -->
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                                                    Pallets <span class="text-red-500">*</span>
                                                                </label>
                                                                <input type="number" x-model="palletEntry.pallets" 
                                                                       :name="`po_numbers[${poIndex}][lines][${lineIndex}][pallet_entries][${palletIndex}][pallets]`"
                                                                       placeholder="0"
                                                                       required
                                                                       class="w-full border-gray-300 rounded {{ $readonly ? 'bg-gray-100' : '' }}"
                                                                       {{ $readonly ? 'readonly' : '' }}>
                                                            </div>
                                                            
                                                            <!-- Pallet Type (Optional) -->
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 mb-1">Pallet Type</label>
                                                                <select x-model="palletEntry.type_id" 
                                                                        :name="`po_numbers[${poIndex}][lines][${lineIndex}][pallet_entries][${palletIndex}][type_id]`"
                                                                        class="w-full border-gray-300 rounded {{ $readonly ? 'bg-gray-100' : '' }}"
                                                                        {{ $readonly ? 'disabled' : '' }}>
                                                                    <option value="">Optional</option>
                                                                    @foreach($palletTypes as $type)
                                                                        <option value="{{ $type->id }}">{{ $type->display_name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>
                                                
                                                <!-- Add Another Pallet Type Button -->
                                                @unless($readonly)
                                                    <div class="text-center">
                                                        <button type="button" @click="addPalletEntry(poIndex, lineIndex)" 
                                                                class="text-blue-600 hover:text-blue-800 text-sm border border-blue-300 hover:border-blue-500 rounded px-3 py-2">
                                                            + Add Another Pallet Type
                                                        </button>
                                                    </div>
                                                @endunless
                                                
                                                <!-- Line Summary -->
                                                <div x-show="(line.pallet_entries || []).length > 1" class="bg-blue-50 border border-blue-200 rounded p-2">
                                                    <div class="text-sm font-medium text-blue-800">Line Total:</div>
                                                    <div class="text-sm text-blue-700">
                                                        Cases: <span x-text="getLineTotalCases(line)"></span> | 
                                                        Pallets: <span x-text="getLineTotalPallets(line)"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        <div class="grid grid-cols-2 gap-4">
                                            <!-- Expected Quantities -->
                                            <div>
                                                <h6 class="text-xs font-medium text-gray-600 mb-2">Expected</h6>
                                                <div class="grid grid-cols-3 gap-2">
                                                    <div>
                                                        <label class="block text-xs text-gray-500">Units</label>
                                                        <input type="number" x-model="line.expected_cases" 
                                                               :name="`po_numbers[${poIndex}][lines][${lineIndex}][expected_cases]`"
                                                               class="mt-1 block w-full border-gray-300 rounded text-xs {{ $readonly ? 'bg-gray-100' : '' }}"
                                                               {{ $readonly ? 'readonly' : '' }}>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs text-gray-500">Pallets</label>
                                                        <input type="number" x-model="line.expected_pallets" 
                                                               :name="`po_numbers[${poIndex}][lines][${lineIndex}][expected_pallets]`"
                                                               class="mt-1 block w-full border-gray-300 rounded text-xs {{ $readonly ? 'bg-gray-100' : '' }}"
                                                               {{ $readonly ? 'readonly' : '' }}>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs text-gray-500">Type</label>
                                                        <select x-model="line.expected_pallet_type_id" 
                                                                :name="`po_numbers[${poIndex}][lines][${lineIndex}][expected_pallet_type_id]`"
                                                                class="mt-1 block w-full border-gray-300 rounded text-xs {{ $readonly ? 'bg-gray-100' : '' }}"
                                                                {{ $readonly ? 'disabled' : '' }}>
                                                            <option value="">Select</option>
                                                            @foreach($palletTypes as $type)
                                                                <option value="{{ $type->id }}">{{ $type->display_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Actual Quantities -->
                                            <div>
                                                <h6 class="text-xs font-medium text-gray-600 mb-2">Actual</h6>
                                                <div class="grid grid-cols-3 gap-2">
                                                    <div>
                                                        <label class="block text-xs text-gray-500">Units</label>
                                                        <input type="number" x-model="line.actual_cases" 
                                                               :name="`po_numbers[${poIndex}][lines][${lineIndex}][actual_cases]`"
                                                               class="mt-1 block w-full border-gray-300 rounded text-xs {{ $readonly ? 'bg-gray-100' : '' }}"
                                                               {{ $readonly ? 'readonly' : '' }}>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs text-gray-500">Pallets</label>
                                                        <input type="number" x-model="line.actual_pallets" 
                                                               :name="`po_numbers[${poIndex}][lines][${lineIndex}][actual_pallets]`"
                                                               class="mt-1 block w-full border-gray-300 rounded text-xs {{ $readonly ? 'bg-gray-100' : '' }}"
                                                               {{ $readonly ? 'readonly' : '' }}>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs text-gray-500">Type</label>
                                                        <select x-model="line.actual_pallet_type_id" 
                                                                :name="`po_numbers[${poIndex}][lines][${lineIndex}][actual_pallet_type_id]`"
                                                                class="mt-1 block w-full border-gray-300 rounded text-xs {{ $readonly ? 'bg-gray-100' : '' }}"
                                                                {{ $readonly ? 'disabled' : '' }}>
                                                            <option value="">Select</option>
                                                            @foreach($palletTypes as $type)
                                                                <option value="{{ $type->id }}">{{ $type->display_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Variance Display -->
                                    @unless($hide_actuals)
                                        <div x-show="line.expected_cases || line.expected_pallets" class="mt-2 pt-2 border-t border-gray-200">
                                            <div class="bg-blue-50 rounded p-2">
                                                <div class="text-xs font-medium text-blue-800 mb-1">Variance</div>
                                                <div class="grid grid-cols-2 gap-4 text-xs">
                                                    <div>
                                                        <span class="text-gray-600">Units:</span>
                                                        <span :class="getLineVarianceClass(line, 'units')" x-text="getLineVarianceText(line, 'units')"></span>
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-600">Pallets:</span>
                                                        <span :class="getLineVarianceClass(line, 'pallets')" x-text="getLineVarianceText(line, 'pallets')"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endunless
                                </div>
                            </template>

                            <!-- Empty lines state -->
                            <div x-show="po.lines.length === 0" class="text-center py-4 border-2 border-dashed border-gray-200 rounded">
                                <p class="text-gray-500 text-sm mb-2">No lines added to this PO yet</p>
                                @unless($readonly)
                                    <button type="button" @click="addPoLine(poIndex)" 
                                            class="text-blue-600 hover:text-blue-800 text-sm">
                                        Add First Line
                                    </button>
                                @endunless
                            </div>
                        </div>
                    </div>

                    <!-- PO Summary -->
                    <div x-show="po.lines.length > 0" class="mt-4 pt-4 border-t border-gray-300">
                        <div class="bg-gray-100 rounded p-3">
                            <div class="text-sm font-medium text-gray-800 mb-2">PO Summary</div>
                            <div class="space-y-2 text-sm">
                                <div>
                                    <span class="text-gray-600 font-medium">Expected:</span>
                                    <div class="ml-4" x-html="getPoSummaryText(po, 'expected')"></div>
                                </div>
                                @unless($hide_actuals)
                                    <div>
                                        <span class="text-gray-600 font-medium">Actual:</span>
                                        <div class="ml-4" x-html="getPoSummaryText(po, 'actual')"></div>
                                    </div>
                                @endunless
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Empty state -->
        <div x-show="poNumbers.length === 0" class="text-center py-8">
            <p class="text-gray-500 mb-3">No PO numbers added yet</p>
            @unless($readonly)
                <button type="button" @click="addPoNumber()" 
                        class="btn bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                    Add First PO Number
                </button>
            @endunless
        </div>
    </div>
</div>

<script>
function poNumbersManager(customerId = null) {
    return {
        poNumbers: [],
        customerId: customerId,
        skuSearchResults: {},
        showSkuDropdown: {},
        skuSearchLoading: {},
        skuSearchTimeout: null,

        init(existingData = []) {
            this.poNumbers = existingData.length > 0 ? existingData : [];

            // If no existing PO data and not readonly, add a default empty PO for customer creation
            if (this.poNumbers.length === 0 && !document.querySelector('[data-readonly="true"]')) {
                this.addPoNumber();
                this.addPoLine(0);
            }
        },

        searchProducts(poIndex, lineIndex, query) {
            const key = `${poIndex}-${lineIndex}`;

            // Clear previous timeout
            if (this.skuSearchTimeout) {
                clearTimeout(this.skuSearchTimeout);
            }

            // If query is too short, hide dropdown
            if (query.length < 1) {
                this.showSkuDropdown[key] = false;
                this.skuSearchResults[key] = [];
                return;
            }

            // Set loading state
            this.skuSearchLoading[key] = true;
            this.showSkuDropdown[key] = true;

            if (!this.customerId) {
                console.error('Customer ID not found');
                this.skuSearchLoading[key] = false;
                return;
            }

            // Debounce the search
            this.skuSearchTimeout = setTimeout(() => {
                fetch(`/api/products/search?q=${encodeURIComponent(query)}&customer_id=${this.customerId}`)
                    .then(response => response.json())
                    .then(data => {
                        this.skuSearchResults[key] = data.products || [];
                        this.skuSearchLoading[key] = false;
                        this.showSkuDropdown[key] = this.skuSearchResults[key].length > 0;
                    })
                    .catch(error => {
                        console.error('Error searching products:', error);
                        this.skuSearchLoading[key] = false;
                        this.showSkuDropdown[key] = false;
                    });
            }, 300);
        },

        selectProduct(poIndex, lineIndex, product) {
            const key = `${poIndex}-${lineIndex}`;

            // Update the line with selected product
            this.poNumbers[poIndex].lines[lineIndex].sku = product.sku;
            this.poNumbers[poIndex].lines[lineIndex].description = product.description;

            // Hide dropdown
            this.showSkuDropdown[key] = false;
        },
        
        addPoNumber() {
            this.poNumbers.push({
                po_number: '',
                lines: []
            });
        },
        
        removePoNumber(poIndex) {
            this.poNumbers.splice(poIndex, 1);
        },
        
        addPoLine(poIndex) {
            this.poNumbers[poIndex].lines.push({
                line_number: this.poNumbers[poIndex].lines.length + 1,
                sku: '',
                description: '',
                expected_cases: null,
                expected_pallets: null,
                expected_pallet_type_id: '',
                actual_cases: null,
                actual_pallets: null,
                actual_pallet_type_id: '',
                pallet_types: [],
                pallet_entries: [{cases: '', pallets: '', type_id: ''}]
            });
        },
        
        removePoLine(poIndex, lineIndex) {
            this.poNumbers[poIndex].lines.splice(lineIndex, 1);
            // Renumber remaining lines
            this.poNumbers[poIndex].lines.forEach((line, index) => {
                line.line_number = index + 1;
            });
        },
        
        addPalletEntry(poIndex, lineIndex) {
            if (!this.poNumbers[poIndex].lines[lineIndex].pallet_entries) {
                this.poNumbers[poIndex].lines[lineIndex].pallet_entries = [];
            }
            this.poNumbers[poIndex].lines[lineIndex].pallet_entries.push({
                cases: '',
                pallets: '',
                type_id: ''
            });
        },
        
        removePalletEntry(poIndex, lineIndex, palletIndex) {
            if (this.poNumbers[poIndex].lines[lineIndex].pallet_entries.length > 1) {
                this.poNumbers[poIndex].lines[lineIndex].pallet_entries.splice(palletIndex, 1);
            }
        },
        
        getLineTotalCases(line) {
            if (!line.pallet_entries || line.pallet_entries.length === 0) return 0;
            return line.pallet_entries.reduce((total, entry) => {
                return total + (parseInt(entry.cases) || 0);
            }, 0);
        },
        
        getLineTotalPallets(line) {
            if (!line.pallet_entries || line.pallet_entries.length === 0) return 0;
            return line.pallet_entries.reduce((total, entry) => {
                return total + (parseInt(entry.pallets) || 0);
            }, 0);
        },
        
        // Legacy functions for backward compatibility
        addPalletType(poIndex, lineIndex) {
            if (!this.poNumbers[poIndex].lines[lineIndex].pallet_types) {
                this.poNumbers[poIndex].lines[lineIndex].pallet_types = [];
            }
            this.poNumbers[poIndex].lines[lineIndex].pallet_types.push({
                type_id: '',
                quantity: null
            });
        },
        
        removePalletType(poIndex, lineIndex, palletIndex) {
            this.poNumbers[poIndex].lines[lineIndex].pallet_types.splice(palletIndex, 1);
        },
        
        getTotalPallets(line) {
            if (!line.pallet_types || line.pallet_types.length === 0) return 0;
            return line.pallet_types.reduce((total, entry) => {
                return total + (parseInt(entry.quantity) || 0);
            }, 0);
        },
        
        getLineVarianceText(line, type) {
            const expected = type === 'units' ? line.expected_cases : line.expected_pallets;
            const actual = type === 'units' ? line.actual_cases : line.actual_pallets;
            
            if (!expected || !actual) return 'N/A';
            const variance = actual - expected;
            return variance === 0 ? 'No variance' : `${variance > 0 ? '+' : ''}${variance}`;
        },
        
        getLineVarianceClass(line, type) {
            const expected = type === 'units' ? line.expected_cases : line.expected_pallets;
            const actual = type === 'units' ? line.actual_cases : line.actual_pallets;
            
            if (!expected || !actual) return 'text-gray-500';
            const variance = actual - expected;
            return variance === 0 ? 'text-green-600' : 'text-red-600 font-medium';
        },
        
        getPalletBreakdown(po, type) {
            const palletsField = type === 'expected' ? 'expected_pallets' : 'actual_pallets';
            const typeField = type === 'expected' ? 'expected_pallet_type_id' : 'actual_pallet_type_id';
            
            const palletTypes = @json($palletTypes->keyBy('id'));
            const breakdown = {};
            
            po.lines.forEach(line => {
                const palletCount = parseInt(line[palletsField]) || 0;
                const typeId = line[typeField];
                
                if (palletCount > 0 && typeId && palletTypes[typeId]) {
                    const typeName = palletTypes[typeId].name;
                    breakdown[typeName] = (breakdown[typeName] || 0) + palletCount;
                }
            });
            
            return breakdown;
        },
        
        getPoSummaryText(po, type) {
            let totalCases = 0;
            let palletBreakdown = {};
            let totalPallets = 0;
            
            // For expected quantities with new pallet_entries structure
            if (type === 'expected') {
                const palletTypes = @json($palletTypes->keyBy('id'));
                
                po.lines.forEach(line => {
                    if (line.pallet_entries && line.pallet_entries.length > 0) {
                        line.pallet_entries.forEach(entry => {
                            const cases = parseInt(entry.cases) || 0;
                            const pallets = parseInt(entry.pallets) || 0;
                            const typeId = entry.type_id;
                            
                            totalCases += cases;
                            totalPallets += pallets;
                            
                            if (pallets > 0) {
                                if (typeId && palletTypes[typeId]) {
                                    const typeName = palletTypes[typeId].name;
                                    palletBreakdown[typeName] = (palletBreakdown[typeName] || 0) + pallets;
                                } else {
                                    palletBreakdown['Unspecified'] = (palletBreakdown['Unspecified'] || 0) + pallets;
                                }
                            }
                        });
                    }
                    
                    // Fallback to old structure if no pallet_entries
                    if ((!line.pallet_entries || line.pallet_entries.length === 0) && line.expected_cases) {
                        totalCases += parseInt(line.expected_cases) || 0;
                    }
                });
            } else {
                // Fall back to old structure for actual quantities
                const unitsField = type === 'actual' ? 'actual_cases' : 'expected_cases';
                totalCases = po.lines.reduce((sum, line) => sum + (parseInt(line[unitsField]) || 0), 0);
                palletBreakdown = this.getPalletBreakdown(po, type);
                totalPallets = Object.values(palletBreakdown).reduce((sum, count) => sum + count, 0);
            }
            
            const parts = [];
            
            if (totalCases > 0) {
                parts.push(`<strong>${totalCases} cases</strong>`);
            }
            
            if (totalPallets > 0) {
                const palletParts = Object.entries(palletBreakdown).map(([typeName, count]) => {
                    return `${count} ${typeName}`;
                });
                
                parts.push(`${palletParts.join(', ')} <em>(total: ${totalPallets} pallets)</em>`);
            }
            
            return parts.length > 0 ? parts.join('<br>') : '<em>No quantities specified</em>';
        },
        
        validatePoNumbers() {
            // Check if at least one PO has at least one line with cases > 0
            let hasCases = false;
            
            for (let po of this.poNumbers) {
                if (po.lines && po.lines.length > 0) {
                    for (let line of po.lines) {
                        // Check new pallet_entries structure
                        if (line.pallet_entries && line.pallet_entries.length > 0) {
                            for (let entry of line.pallet_entries) {
                                if (entry.cases && parseInt(entry.cases) > 0) {
                                    hasCases = true;
                                    break;
                                }
                            }
                        }
                        // Fallback to old structure
                        else if (line.expected_cases && parseInt(line.expected_cases) > 0) {
                            hasCases = true;
                            break;
                        }
                        
                        if (hasCases) break;
                    }
                    if (hasCases) break;
                }
            }
            
            return hasCases;
        }
    }
}

// Add form validation on submit
document.addEventListener('DOMContentLoaded', function() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            // Check if this form contains PO numbers component
            const poComponent = form.querySelector('[x-data*="poNumbersManager"]');
            if (poComponent && window.poNumbersManagerInstance) {
                if (!window.poNumbersManagerInstance.validatePoNumbers()) {
                    e.preventDefault();
                    alert('At least one PO line must have cases greater than 0.');
                    return false;
                }
            }
        });
    });
});
</script>