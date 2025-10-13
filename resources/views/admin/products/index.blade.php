@extends('layouts.admin')

@section('content')
<div class="p-6 bg-white rounded-lg shadow">
  <div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-semibold">Products</h1>
    <a href="{{ route('app.products.create') }}"
       class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
      + New Product
    </a>
  </div>

  {{-- Customer Filter --}}
  <div class="mb-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
    <form method="GET" action="{{ route('app.products.index') }}" class="flex gap-3 items-end">
      <div class="flex-1">
        <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">Filter by Customer</label>
        <select name="customer_id" id="customer_id" class="block w-full border-gray-300 rounded text-sm py-2" onchange="this.form.submit()">
          <option value="">All Customers</option>
          @foreach(\App\Models\Customer::orderBy('name')->get() as $customer)
            <option value="{{ $customer->id }}" @selected(request('customer_id') == $customer->id)>
              {{ $customer->name }}
            </option>
          @endforeach
        </select>
      </div>
      <div>
        <button type="submit" class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">
          Filter
        </button>
      </div>
      @if(request('customer_id'))
        <div>
          <a href="{{ route('app.products.index') }}" class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
            Clear Filter
          </a>
        </div>
      @endif
    </form>
  </div>

  @if(session('success'))
    <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
      {{ session('success') }}
    </div>
  @endif

  <table class="w-full table-auto border-collapse">
    <thead>
      <tr class="bg-gray-100">
        <th class="px-4 py-2 text-left">Customer</th>
        <th class="px-4 py-2 text-left">SKU</th>
        <th class="px-4 py-2 text-left">Description</th>
        <th class="px-4 py-2 text-left">Type</th>
        <th class="px-4 py-2 text-center">Cases/Pallet</th>
        <th class="px-4 py-2 text-right">Actions</th>
      </tr>
    </thead>
    <tbody>
      @foreach($products as $product)
      <tr class="border-t hover:bg-gray-50">
        <td class="px-4 py-2">
          <span class="font-medium text-gray-900">{{ $product->customer->name ?? 'N/A' }}</span>
        </td>
        <td class="px-4 py-2">
          <span class="font-mono text-sm">{{ $product->sku }}</span>
        </td>
        <td class="px-4 py-2 text-gray-700">{{ $product->description }}</td>
        <td class="px-4 py-2">
          <span class="px-2 py-1 text-xs rounded {{ $product->product_type === 'finished_product' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
            {{ ucfirst(str_replace('_', ' ', $product->product_type)) }}
          </span>
        </td>
        <td class="px-4 py-2 text-center">{{ $product->cases_per_pallet ?? '-' }}</td>
        <td class="px-4 py-2 text-right">
          <a href="{{ route('app.products.edit', $product) }}"
             class="mr-2 text-blue-600 hover:underline">Edit</a>
          <form action="{{ route('app.products.destroy', $product) }}"
                method="POST" class="inline">
            @csrf @method('DELETE')
            <button type="submit"
                    class="text-red-600 hover:underline"
                    onclick="return confirm('Delete this product?')">
              Delete
            </button>
          </form>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <div class="mt-4">
    {{ $products->links() }}
  </div>
</div>
@endsection
