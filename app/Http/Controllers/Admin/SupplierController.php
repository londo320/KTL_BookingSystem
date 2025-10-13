<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SupplierController extends Controller
{
    /**
     * Search suppliers for autocomplete (API endpoint)
     */
    public function search(Request $request)
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([
                'suppliers' => [],
                'total' => 0,
                'has_more' => false,
                'exact_match' => false
            ]);
        }

        $page = $request->get('page', 1);
        $perPage = 10;

        // Search active suppliers first, then inactive ones
        $activeSuppliers = Supplier::where('is_active', true)
            ->where('name', 'like', '%' . $query . '%')
            ->orderBy('name')
            ->get(['id', 'name', 'is_active']);

        $inactiveSuppliers = Supplier::where('is_active', false)
            ->where('name', 'like', '%' . $query . '%')
            ->orderBy('name')
            ->get(['id', 'name', 'is_active']);

        $allSuppliers = $activeSuppliers->concat($inactiveSuppliers);
        $total = $allSuppliers->count();

        // Paginate results
        $suppliers = $allSuppliers->skip(($page - 1) * $perPage)->take($perPage);

        // Check for exact match (case-insensitive)
        $exactMatch = $allSuppliers->first(function ($supplier) use ($query) {
            return strtolower($supplier->name) === strtolower($query);
        });

        return response()->json([
            'suppliers' => $suppliers->values(),
            'total' => $total,
            'has_more' => $total > ($page * $perPage),
            'exact_match' => (bool) $exactMatch
        ]);
    }

    /**
     * Quick create supplier (API endpoint)
     */
    public function quickCreate(Request $request)
    {
        $name = trim($request->name);
        $name = Str::title($name);

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Check if supplier already exists (case-insensitive, including soft-deleted)
        $existing = Supplier::withTrashed()->whereRaw('LOWER(name) = ?', [strtolower($name)])->first();

        if ($existing) {
            if ($existing->trashed()) {
                // Restore soft-deleted supplier
                $existing->restore();
                $existing->update(['is_active' => true, 'last_used_at' => now()]);

                return response()->json([
                    'success' => true,
                    'supplier' => $existing,
                    'message' => 'Deleted supplier restored and activated'
                ]);
            } else {
                // Reactivate if it exists but is inactive
                if (!$existing->is_active) {
                    $existing->update(['is_active' => true, 'last_used_at' => now()]);
                }

                return response()->json([
                    'success' => true,
                    'supplier' => $existing,
                    'message' => $existing->is_active ? 'Supplier already exists' : 'Inactive supplier reactivated'
                ]);
            }
        }

        // Create new supplier
        $supplier = Supplier::create([
            'name' => $name,
            'is_active' => true,
            'last_used_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'supplier' => $supplier,
            'message' => 'Supplier created successfully'
        ]);
    }

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
