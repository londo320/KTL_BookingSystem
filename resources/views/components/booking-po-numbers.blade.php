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

<div x-data="poNumbersManager({{ $customer_id ?? 'null' }})"
     x-init="init({{ $existingData->toJson() }}); window.poNumbersManagerInstance = this; watchCustomerField();"
     @if($readonly) data-readonly="true" @endif>
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

                        <div class="space-y-2">
                            <template x-for="(line, lineIndex) in po.lines" :key="lineIndex">
                                <div class="bg-white border border-gray-200 rounded p-1.5">
                                    <input type="hidden" :name="`po_numbers[${poIndex}][lines][${lineIndex}][line_number]`"
                                           :value="lineIndex + 1">

                                    @if($hide_actuals)
                                        <!-- Single Line Layout: Line# | SKU | Description | Pallets | Cases | Type | Remove -->
                                        <div class="flex gap-1.5 items-end">
                                            <!-- Line Number -->
                                            <div class="w-8 flex-shrink-0">
                                                <label class="block text-[10px] font-medium text-gray-600 mb-0.5">Line</label>
                                                <div class="h-7 flex items-center">
                                                    <span class="text-xs font-medium text-gray-700" x-text="lineIndex + 1"></span>
                                                </div>
                                            </div>

                                            <!-- SKU -->
                                            <div class="w-24 flex-shrink-0 relative">
                                                <label class="block text-[10px] font-medium text-gray-600 mb-0.5">SKU</label>
                                                <input type="text" x-model="line.sku"
                                                       :id="`sku-input-${poIndex}-${lineIndex}`"
                                                       :name="`po_numbers[${poIndex}][lines][${lineIndex}][sku]`"
                                                       @input="searchProducts(poIndex, lineIndex, $event.target.value); line.product_id = null;"
                                                       placeholder="SKU123"
                                                       autocomplete="off"
                                                       class="h-7 w-full border-gray-300 rounded text-xs px-2 {{ $readonly ? 'bg-gray-100' : '' }}"
                                                       {{ $readonly ? 'readonly' : '' }}>
                                                <div x-show="showSkuDropdown[`${poIndex}-${lineIndex}`]"
                                                     x-ref="skuDropdown"
                                                     @click.away="showSkuDropdown[`${poIndex}-${lineIndex}`] = false"
                                                     class="absolute z-10 w-96 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
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

                                            <!-- Description -->
                                            <div class="flex-1 min-w-[200px]">
                                                <label class="block text-[10px] font-medium text-gray-600 mb-0.5">Description</label>
                                                <input type="text" x-model="line.description"
                                                       :name="`po_numbers[${poIndex}][lines][${lineIndex}][description]`"
                                                       placeholder="Product description"
                                                       :readonly="line.product_id ? true : false"
                                                       :class="line.product_id ? 'h-7 w-full border-gray-300 rounded text-xs px-2 bg-gray-100' : 'h-7 w-full border-gray-300 rounded text-xs px-2 {{ $readonly ? 'bg-gray-100' : '' }}'"
                                                       {{ $readonly ? 'readonly' : '' }}>
                                            </div>

                                            <!-- Pallets -->
                                            <div class="w-14 flex-shrink-0">
                                                <label class="block text-[10px] font-medium text-gray-600 mb-0.5">
                                                    Plts <span class="text-red-500">*</span>
                                                </label>
                                                <input type="number"
                                                       x-model="(line.pallet_entries && line.pallet_entries[0]) ? line.pallet_entries[0].pallets : ''"
                                                       :name="`po_numbers[${poIndex}][lines][${lineIndex}][pallet_entries][0][pallets]`"
                                                       @input="if(line.cases_per_pallet && $event.target.value) { if(!line.pallet_entries) line.pallet_entries = [{}]; line.pallet_entries[0].cases = $event.target.value * line.cases_per_pallet; }"
                                                       placeholder="0"
                                                       maxlength="4"
                                                       required
                                                       class="h-7 w-full border-gray-300 rounded text-xs px-2 {{ $readonly ? 'bg-gray-100' : '' }}"
                                                       {{ $readonly ? 'readonly' : '' }}>
                                            </div>

                                            <!-- Cases -->
                                            <div class="w-20 flex-shrink-0">
                                                <label class="block text-[10px] font-medium text-gray-600 mb-0.5">
                                                    Cases <span class="text-red-500">*</span>
                                                    <span x-show="line.cases_per_pallet" class="text-[9px] text-green-600">(auto)</span>
                                                </label>
                                                <input type="number"
                                                       x-model="(line.pallet_entries && line.pallet_entries[0]) ? line.pallet_entries[0].cases : ''"
                                                       :name="`po_numbers[${poIndex}][lines][${lineIndex}][pallet_entries][0][cases]`"
                                                       placeholder="0"
                                                       maxlength="6"
                                                       required
                                                       class="h-7 w-full border-gray-300 rounded text-xs px-2 {{ $readonly ? 'bg-gray-100' : '' }}"
                                                       {{ $readonly ? 'readonly' : '' }}>
                                            </div>

                                            <!-- Pallet Type -->
                                            <div class="w-32 flex-shrink-0">
                                                <label class="block text-[10px] font-medium text-gray-600 mb-0.5">Pallet Type</label>
                                                <select x-model="(line.pallet_entries && line.pallet_entries[0]) ? line.pallet_entries[0].type_id : ''"
                                                        :name="`po_numbers[${poIndex}][lines][${lineIndex}][pallet_entries][0][type_id]`"
                                                        class="h-7 w-full border-gray-300 rounded text-xs px-2 {{ $readonly ? 'bg-gray-100' : '' }}"
                                                        {{ $readonly ? 'disabled' : '' }}>
                                                    <option value="">-</option>
                                                    @foreach($palletTypes as $type)
                                                        <option value="{{ $type->id }}">{{ $type->display_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <!-- Remove Button -->
                                            @unless($readonly)
                                                <div class="w-16 flex-shrink-0 flex items-end">
                                                    <button type="button" @click="removePoLine(poIndex, lineIndex)"
                                                            class="h-7 text-red-500 hover:text-red-700 text-[10px] whitespace-nowrap">
                                                        Remove
                                                    </button>
                                                </div>
                                            @endunless
                                        </div>
                                    @else
                                        <!-- SKU and Description on Same Line -->
                                        <div class="mb-3 flex gap-2 items-start">
                                            <!-- SKU (20% width) -->
                                            <div class="w-1/5 flex-shrink-0 relative">
                                                <label class="block text-xs font-medium text-gray-600 mb-1">SKU *</label>
                                                <input type="text" x-model="line.sku"
                                                       :id="`sku-input-${poIndex}-${lineIndex}`"
                                                       :name="`po_numbers[${poIndex}][lines][${lineIndex}][sku]`"
                                                       @input="searchProducts(poIndex, lineIndex, $event.target.value); line.product_id = null;"
                                                       placeholder="SKU123"
                                                       autocomplete="off"
                                                       class="block w-full border-gray-300 rounded text-xs {{ $readonly ? 'bg-gray-100' : '' }}"
                                                       {{ $readonly ? 'readonly' : '' }} required>
                                                <div x-show="showSkuDropdown[`${poIndex}-${lineIndex}`]"
                                                     x-ref="skuDropdown"
                                                     @click.away="showSkuDropdown[`${poIndex}-${lineIndex}`] = false"
                                                     class="absolute z-10 w-96 mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
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

                                            <!-- Description (80% width) -->
                                            <div class="flex-1 min-w-0">
                                                <label class="block text-xs font-medium text-gray-600 mb-1">Description</label>
                                                <input type="text" x-model="line.description"
                                                       :name="`po_numbers[${poIndex}][lines][${lineIndex}][description]`"
                                                       placeholder="Product description"
                                                       :readonly="line.product_id ? true : false"
                                                       :class="line.product_id ? 'block w-full border-gray-300 rounded text-xs bg-gray-100' : 'block w-full border-gray-300 rounded text-xs {{ $readonly ? 'bg-gray-100' : '' }}'"
                                                       {{ $readonly ? 'readonly' : '' }}>
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <!-- Expected Quantities -->
                                            <div>
                                                <h6 class="text-xs font-medium text-gray-600 mb-2">Expected</h6>
                                                <div class="grid grid-cols-3 gap-2">
                                                    <div>
                                                        <label class="block text-xs text-gray-500">Pallets</label>
                                                        <input type="number" x-model="line.expected_pallets"
                                                               :name="`po_numbers[${poIndex}][lines][${lineIndex}][expected_pallets]`"
                                                               @input="calculateExpectedCases(poIndex, lineIndex)"
                                                               @change="triggerSummaryUpdate()"
                                                               :disabled="!line.sku || line.sku.trim() === ''"
                                                               :class="(!line.sku || line.sku.trim() === '') ? 'mt-1 block w-full border-gray-300 rounded text-xs bg-gray-100 cursor-not-allowed' : 'mt-1 block w-full border-gray-300 rounded text-xs {{ $readonly ? 'bg-gray-100' : '' }}'"
                                                               {{ $readonly ? 'readonly' : '' }}>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs text-gray-500">Units</label>
                                                        <input type="number" x-model="line.expected_cases"
                                                               :name="`po_numbers[${poIndex}][lines][${lineIndex}][expected_cases]`"
                                                               @change="triggerSummaryUpdate()"
                                                               :disabled="!line.sku || line.sku.trim() === ''"
                                                               :class="(!line.sku || line.sku.trim() === '') ? 'mt-1 block w-full border-gray-300 rounded text-xs bg-gray-100 cursor-not-allowed' : 'mt-1 block w-full border-gray-300 rounded text-xs {{ $readonly ? 'bg-gray-100' : '' }}'"
                                                               {{ $readonly ? 'readonly' : '' }}>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs text-gray-500">Type</label>
                                                        <select x-model="line.expected_pallet_type_id"
                                                                :name="`po_numbers[${poIndex}][lines][${lineIndex}][expected_pallet_type_id]`"
                                                                @change="triggerSummaryUpdate()"
                                                                :disabled="!line.sku || line.sku.trim() === '' {{ $readonly ? '|| true' : '' }}"
                                                                :class="(!line.sku || line.sku.trim() === '') ? 'mt-1 block w-full border-gray-300 rounded text-xs bg-gray-100 cursor-not-allowed' : 'mt-1 block w-full border-gray-300 rounded text-xs {{ $readonly ? 'bg-gray-100' : '' }}'">
                                                            <option value="">Select</option>
                                                            @foreach($palletTypes as $type)
                                                                <option value="{{ $type->id }}">{{ $type->display_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div x-show="!line.sku || line.sku.trim() === ''" class="mt-2 text-xs text-amber-600 italic">
                                                    ⚠️ Please select a SKU first to enter expected quantities
                                                </div>
                                            </div>

                                            <!-- Actual Quantities -->
                                            <div>
                                                <h6 class="text-xs font-medium text-gray-600 mb-2">Actual</h6>
                                                <div class="grid grid-cols-3 gap-2">
                                                    <div>
                                                        <label class="block text-xs text-gray-500">Pallets</label>
                                                        <input type="number" x-model="line.actual_pallets"
                                                               :name="`po_numbers[${poIndex}][lines][${lineIndex}][actual_pallets]`"
                                                               @input="calculateActualCases(poIndex, lineIndex)"
                                                               @change="triggerSummaryUpdate()"
                                                               :disabled="!line.sku || line.sku.trim() === ''"
                                                               :class="(!line.sku || line.sku.trim() === '') ? 'mt-1 block w-full border-gray-300 rounded text-xs bg-gray-100 cursor-not-allowed' : 'mt-1 block w-full border-gray-300 rounded text-xs {{ $readonly ? 'bg-gray-100' : '' }}'"
                                                               {{ $readonly ? 'readonly' : '' }}>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs text-gray-500">Units</label>
                                                        <input type="number" x-model="line.actual_cases"
                                                               :name="`po_numbers[${poIndex}][lines][${lineIndex}][actual_cases]`"
                                                               @change="triggerSummaryUpdate()"
                                                               :disabled="!line.sku || line.sku.trim() === ''"
                                                               :class="(!line.sku || line.sku.trim() === '') ? 'mt-1 block w-full border-gray-300 rounded text-xs bg-gray-100 cursor-not-allowed' : 'mt-1 block w-full border-gray-300 rounded text-xs {{ $readonly ? 'bg-gray-100' : '' }}'"
                                                               {{ $readonly ? 'readonly' : '' }}>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs text-gray-500">Type</label>
                                                        <select x-model="line.actual_pallet_type_id"
                                                                :name="`po_numbers[${poIndex}][lines][${lineIndex}][actual_pallet_type_id]`"
                                                                @change="triggerSummaryUpdate()"
                                                                :disabled="!line.sku || line.sku.trim() === '' {{ $readonly ? '|| true' : '' }}"
                                                                :class="(!line.sku || line.sku.trim() === '') ? 'mt-1 block w-full border-gray-300 rounded text-xs bg-gray-100 cursor-not-allowed' : 'mt-1 block w-full border-gray-300 rounded text-xs {{ $readonly ? 'bg-gray-100' : '' }}'">
                                                            <option value="">Select</option>
                                                            @foreach($palletTypes as $type)
                                                                <option value="{{ $type->id }}">{{ $type->display_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div x-show="!line.sku || line.sku.trim() === ''" class="mt-2 text-xs text-amber-600 italic">
                                                    ⚠️ Please select a SKU first to enter actual quantities
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Remove Button for Edit Layout -->
                                        @unless($readonly)
                                            <div class="mt-2 flex justify-end">
                                                <button type="button" @click="removePoLine(poIndex, lineIndex)"
                                                        class="text-red-500 hover:text-red-700 text-xs">
                                                    Remove Line
                                                </button>
                                            </div>
                                        @endunless
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
                                    <div class="ml-4" x-html="(summaryUpdateCounter || true) && getPoSummaryText(po, 'expected')"></div>
                                </div>
                                @unless($hide_actuals)
                                    <div>
                                        <span class="text-gray-600 font-medium">Actual:</span>
                                        <div class="ml-4" x-html="(summaryUpdateCounter || true) && getPoSummaryText(po, 'actual')"></div>
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
        summaryUpdateCounter: 0,

        init(existingData = []) {
            this.poNumbers = existingData.length > 0 ? existingData : [];

            // If no existing PO data and not readonly, add a default empty PO for customer creation
            if (this.poNumbers.length === 0 && !document.querySelector('[data-readonly="true"]')) {
                this.addPoNumber();
                this.addPoLine(0);
            }
        },

        watchCustomerField() {
            // Watch for changes in the customer select field
            const customerSelect = document.querySelector('select[name="customer_id"]');
            if (customerSelect) {
                customerSelect.addEventListener('change', (e) => {
                    this.customerId = e.target.value || null;
                    // Clear all SKU search results when customer changes
                    this.skuSearchResults = {};
                    this.showSkuDropdown = {};
                });
            }
        },

        getCurrentCustomerId() {
            // Get customer ID from the form field (in case it changed)
            const customerSelect = document.querySelector('select[name="customer_id"]');
            if (customerSelect) {
                return customerSelect.value || null;
            }
            return this.customerId;
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

            // Get current customer ID
            const currentCustomerId = this.getCurrentCustomerId();

            // Check if customer is selected
            if (!currentCustomerId) {
                this.showSkuDropdown[key] = true;
                this.skuSearchResults[key] = [{
                    id: 'error',
                    sku: '',
                    description: '⚠️ Please select a customer first'
                }];
                this.skuSearchLoading[key] = false;
                return;
            }

            // Set loading state
            this.skuSearchLoading[key] = true;
            this.showSkuDropdown[key] = true;

            // Debounce the search
            this.skuSearchTimeout = setTimeout(() => {
                fetch(`/api/products/search?q=${encodeURIComponent(query)}&customer_id=${currentCustomerId}`)
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
            this.poNumbers[poIndex].lines[lineIndex].product_id = product.id; // Mark as existing product
            this.poNumbers[poIndex].lines[lineIndex].cases_per_pallet = product.cases_per_pallet; // Store for calculation

            // Hide dropdown
            this.showSkuDropdown[key] = false;
        },

        calculateExpectedCases(poIndex, lineIndex) {
            const line = this.poNumbers[poIndex].lines[lineIndex];
            const pallets = parseFloat(line.expected_pallets) || 0;
            const casesPerPallet = parseFloat(line.cases_per_pallet) || 0;

            if (pallets > 0 && casesPerPallet > 0) {
                line.expected_cases = pallets * casesPerPallet;
            }
            this.summaryUpdateCounter++;
        },

        calculateActualCases(poIndex, lineIndex) {
            const line = this.poNumbers[poIndex].lines[lineIndex];
            const pallets = parseFloat(line.actual_pallets) || 0;
            const casesPerPallet = parseFloat(line.cases_per_pallet) || 0;

            if (pallets > 0 && casesPerPallet > 0) {
                line.actual_cases = pallets * casesPerPallet;
            }
            this.summaryUpdateCounter++;
        },

        triggerSummaryUpdate() {
            this.summaryUpdateCounter++;
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
                product_id: null,
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
            const palletTypes = @json($palletTypes->keyBy('id'));

            // For expected quantities - use direct fields only (edit layout)
            if (type === 'expected') {
                po.lines.forEach(line => {
                    const cases = parseInt(line.expected_cases) || 0;
                    const pallets = parseInt(line.expected_pallets) || 0;
                    const typeId = line.expected_pallet_type_id;

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
            } else if (type === 'actual') {
                // For actual quantities - use direct fields only
                po.lines.forEach(line => {
                    const cases = parseInt(line.actual_cases) || 0;
                    const pallets = parseInt(line.actual_pallets) || 0;
                    const typeId = line.actual_pallet_type_id;

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

            const parts = [];

            if (totalPallets > 0) {
                parts.push(`${totalPallets} pallet${totalPallets !== 1 ? 's' : ''}`);
            }

            if (totalCases > 0) {
                parts.push(`${totalCases} unit${totalCases !== 1 ? 's' : ''}`);
            }

            // Show pallet type breakdown if types are specified
            if (totalPallets > 0 && Object.keys(palletBreakdown).length > 0 && palletBreakdown['Unspecified'] !== totalPallets) {
                const palletParts = Object.entries(palletBreakdown).map(([typeName, count]) => {
                    return `${count} ${typeName}`;
                });
                parts.push(`<span class="text-gray-500">(${palletParts.join(', ')})</span>`);
            }

            return parts.length > 0 ? parts.join(' ') : '<span class="text-gray-400 italic">No quantities specified</span>';
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