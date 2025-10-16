<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EquipmentType;
use Illuminate\Http\Request;

class EquipmentTypeController extends Controller
{
    public function index()
    {
        $equipmentTypes = EquipmentType::ordered()->get();

        return view('admin.equipment-types.index', compact('equipmentTypes'));
    }

    public function create()
    {
        $equipmentType = new EquipmentType();
        $maxSortOrder = EquipmentType::max('sort_order') ?? 0;

        return view('admin.equipment-types.create', compact('equipmentType', 'maxSortOrder'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'key' => 'required|string|max:255|unique:equipment_types,key|regex:/^[a-z0-9_]+$/',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'required|integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');

        EquipmentType::create($validated);

        return redirect()->route('app.equipment-types.index')
            ->with('success', 'Equipment type created successfully');
    }

    public function edit(EquipmentType $equipmentType)
    {
        return view('admin.equipment-types.edit', compact('equipmentType'));
    }

    public function update(Request $request, EquipmentType $equipmentType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'key' => 'required|string|max:255|regex:/^[a-z0-9_]+$/|unique:equipment_types,key,' . $equipmentType->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'required|integer|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $equipmentType->update($validated);

        return redirect()->route('app.equipment-types.index')
            ->with('success', 'Equipment type updated successfully');
    }

    public function destroy(EquipmentType $equipmentType)
    {
        $equipmentType->delete();

        return redirect()->route('app.equipment-types.index')
            ->with('success', 'Equipment type deleted successfully');
    }
}
