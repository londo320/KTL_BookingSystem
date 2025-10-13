<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $products = Product::orderBy('sku')->paginate(25);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.create', ['product' => new Product]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'sku' => 'required|string|max:255',
            'description' => 'nullable|string',
            'product_type' => 'required|in:raw_material,finished_product',
            'cases_per_pallet' => 'nullable|integer|min:1',
            'default_case_count' => 'nullable|integer|min:0',
            'default_pallets' => 'nullable|integer|min:0',
        ]);

        // Check for duplicate SKU for this customer
        $existing = Product::where('customer_id', $data['customer_id'])
            ->where('sku', $data['sku'])
            ->first();

        if ($existing) {
            return back()->withErrors(['sku' => 'This SKU already exists for the selected customer.'])->withInput();
        }

        Product::create($data);

        return redirect()->route('app.products.index')
            ->with('success', 'Product created successfully');
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'sku' => 'required|string|max:255|unique:products,sku,{$product->id}',
            'description' => 'nullable|string',
            'default_case_count' => 'nullable|integer|min:0',
            'default_pallets' => 'nullable|integer|min:0',
        ]);

        $product->update($data);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully');
    }

    public function search(Request $request)
    {
        $query = $request->get('q', '');
        $customerId = $request->get('customer_id');

        if (strlen($query) < 1) {
            return response()->json([
                'products' => [],
                'total' => 0,
                'has_more' => false,
                'exact_match' => null
            ]);
        }

        if (!$customerId) {
            return response()->json([
                'products' => [],
                'total' => 0,
                'has_more' => false,
                'exact_match' => null,
                'error' => 'Customer ID required'
            ]);
        }

        $page = $request->get('page', 1);
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        // Search products for this customer
        $productsQuery = Product::where('customer_id', $customerId)
            ->where(function ($q) use ($query) {
                $q->where('sku', 'like', '%' . $query . '%')
                  ->orWhere('description', 'like', '%' . $query . '%');
            })
            ->orderBy('sku');

        $total = $productsQuery->count();
        $products = $productsQuery->skip($offset)->take($perPage)->get(['id', 'sku', 'description', 'customer_id', 'cases_per_pallet', 'product_type']);

        // Check for exact SKU match
        $exactMatch = Product::where('customer_id', $customerId)
            ->where('sku', $query)
            ->first(['id', 'sku', 'description', 'customer_id']);

        return response()->json([
            'products' => $products,
            'total' => $total,
            'has_more' => $total > ($offset + $perPage),
            'exact_match' => $exactMatch
        ]);
    }
}
