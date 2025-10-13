@csrf

<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
  {{-- Customer --}}
  <div>
    <label for="customer_id" class="block text-sm font-medium">Customer <span class="text-red-500">*</span></label>
    <select name="customer_id" id="customer_id" required
            class="mt-1 block w-full border rounded p-2">
      <option value="">– Select Customer –</option>
      @foreach(\App\Models\Customer::orderBy('name')->get() as $customer)
        <option value="{{ $customer->id }}"
                @selected(old('customer_id', $product->customer_id ?? '') == $customer->id)>
          {{ $customer->name }}
        </option>
      @endforeach
    </select>
    @error('customer_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
  </div>

  {{-- SKU --}}
  <div>
    <label for="sku" class="block text-sm font-medium">SKU <span class="text-red-500">*</span></label>
    <input type="text" name="sku" id="sku" required
           value="{{ old('sku', $product->sku ?? '') }}"
           class="mt-1 block w-full border rounded p-2" />
    @error('sku') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
  </div>

  {{-- Description --}}
  <div class="md:col-span-2">
    <label for="description" class="block text-sm font-medium">Description</label>
    <textarea name="description" id="description" rows="2"
              class="mt-1 block w-full border rounded p-2">{{ old('description', $product->description ?? '') }}</textarea>
    @error('description') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
  </div>

  {{-- Product Type --}}
  <div>
    <label for="product_type" class="block text-sm font-medium">Product Type <span class="text-red-500">*</span></label>
    <select name="product_type" id="product_type" required
            class="mt-1 block w-full border rounded p-2">
      <option value="finished_product" @selected(old('product_type', $product->product_type ?? 'finished_product') == 'finished_product')>
        Finished Product
      </option>
      <option value="raw_material" @selected(old('product_type', $product->product_type ?? '') == 'raw_material')>
        Raw Material
      </option>
    </select>
    @error('product_type') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
  </div>

  {{-- Cases Per Pallet --}}
  <div>
    <label for="cases_per_pallet" class="block text-sm font-medium">Cases Per Pallet</label>
    <input type="number" name="cases_per_pallet" id="cases_per_pallet"
           value="{{ old('cases_per_pallet', $product->cases_per_pallet ?? '') }}"
           placeholder="e.g., 60"
           class="mt-1 block w-full border rounded p-2" />
    <p class="text-xs text-gray-500 mt-1">Auto-calculates total cases when entering pallets in bookings</p>
    @error('cases_per_pallet') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
  </div>
</div>

<div class="mt-6">
  <button type="submit"
          class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700">
    Save Product
  </button>
  <a href="{{ route('app.products.index') }}"
     class="ml-4 text-gray-600 hover:underline">Cancel</a>
</div>
