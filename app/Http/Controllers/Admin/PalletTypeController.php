<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PalletType;
use Illuminate\Http\Request;

class PalletTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = PalletType::query();

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->active();
            } elseif ($request->status === 'inactive') {
                $query->inactive();
            }
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('code', 'like', '%'.$request->search.'%')
                    ->orWhere('description', 'like', '%'.$request->search.'%');
            });
        }

        $palletTypes = $query->orderBy('name')->paginate(20);

        return view('admin.pallet-types.index', compact('palletTypes'));
    }

    public function create()
    {
        return view('admin.pallet-types.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:pallet_types,name',
            'code' => 'required|string|max:10|unique:pallet_types,code',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        PalletType::create($validated);

        return redirect()->route('admin.pallet-types.index')
            ->with('success', 'Pallet type created successfully.');
    }

    public function show(PalletType $palletType)
    {
        $palletType->load(['poLinesExpected', 'poLinesActual']);

        return view('admin.pallet-types.show', compact('palletType'));
    }

    public function edit(PalletType $palletType)
    {
        return view('admin.pallet-types.edit', compact('palletType'));
    }

    public function update(Request $request, PalletType $palletType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:pallet_types,name,'.$palletType->id,
            'code' => 'required|string|max:10|unique:pallet_types,code,'.$palletType->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $palletType->update($validated);

        return redirect()->route('admin.pallet-types.index')
            ->with('success', 'Pallet type updated successfully.');
    }

    public function destroy(PalletType $palletType)
    {
        // Check if pallet type is in use
        $inUse = $palletType->poLinesExpected()->exists() || $palletType->poLinesActual()->exists();

        if ($inUse) {
            return redirect()->route('app.settings.pallet-types')
                ->with('error', 'Cannot delete pallet type as it is currently in use.');
        }

        $palletType->delete();

        return redirect()->route('app.settings.pallet-types')
            ->with('success', 'Pallet type deleted successfully.');
    }

    public function toggleActive(PalletType $palletType)
    {
        $palletType->update([
            'is_active' => ! $palletType->is_active,
        ]);

        $status = $palletType->is_active ? 'activated' : 'deactivated';

        return redirect()->route('app.settings.pallet-types')
            ->with('success', "Pallet type {$status} successfully.");
    }
}
