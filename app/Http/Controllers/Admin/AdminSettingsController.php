<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Depot;
use App\Models\PalletType;
use App\Models\Setting;
use Illuminate\Http\Request;

class AdminSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function dashboard()
    {
        $depots = Depot::orderBy('name')->get();
        $tippingWorkflowEnabled = Setting::isTippingWorkflowEnabled();

        return view('admin.settings.dashboard', compact('depots', 'tippingWorkflowEnabled'));
    }

    public function updateTippingWorkflow(Request $request)
    {
        $request->validate([
            'tipping_workflow_enabled' => 'required|boolean',
        ]);

        Setting::set('tipping_workflow_enabled', $request->tipping_workflow_enabled, 'boolean');

        $status = $request->tipping_workflow_enabled ? 'enabled' : 'disabled';

        return back()->with('success', "Tipping workflow has been {$status}.");
    }

    public function palletTypes()
    {
        $palletTypes = PalletType::orderBy('name')->get();
        return view('admin.settings.pallet-types', compact('palletTypes'));
    }

    public function storePalletType(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:pallet_types,code',
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        PalletType::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return back()->with('success', 'Pallet type created successfully.');
    }

    public function updatePalletType(Request $request, PalletType $palletType)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:pallet_types,code,' . $palletType->id,
            'description' => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $palletType->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'description' => $request->description,
            'is_active' => $request->boolean('is_active'),
        ]);

        return back()->with('success', 'Pallet type updated successfully.');
    }

    public function destroyPalletType(PalletType $palletType)
    {
        // Check if pallet type is in use
        if ($palletType->poLinesExpected()->exists() || $palletType->poLinesActual()->exists()) {
            return back()->withErrors(['error' => 'Cannot delete pallet type that is currently in use.']);
        }

        $palletType->delete();
        return back()->with('success', 'Pallet type deleted successfully.');
    }

    // DEPRECATED: Container sizes functionality has been replaced by Trailer Types management
    // public function containerSizes()
    // {
    //     // Get container sizes from existing bookings (since they're stored as strings)
    //     $containerSizes = \App\Models\Booking::whereNotNull('container_size')
    //         ->distinct()
    //         ->pluck('container_size')
    //         ->filter()
    //         ->sort()
    //         ->values();

    //     return view('admin.settings.container-sizes', compact('containerSizes'));
    // }
}
