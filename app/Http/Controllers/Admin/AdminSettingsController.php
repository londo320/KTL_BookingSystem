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
        $this->middleware(['auth', 'function:settings.manage']);
    }

    public function dashboard()
    {
        $depots = Depot::orderBy('name')->get();
        $tippingWorkflowEnabled = Setting::isTippingWorkflowEnabled();
        
        // Module toggles
        $outboundModuleEnabled = Setting::get('outbound_module_enabled', false);
        $inboundModuleEnabled = Setting::get('inbound_module_enabled', true); // Default true for existing functionality

        // User approval settings
        $adminApprovalEmails = Setting::get('admin_approval_emails', '');
        
        return view('admin.settings.dashboard', compact(
            'depots', 
            'tippingWorkflowEnabled',
            'outboundModuleEnabled',
            'inboundModuleEnabled',
            'adminApprovalEmails'
        ));
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

    public function updateOutboundModule(Request $request)
    {
        $request->validate([
            'outbound_module_enabled' => 'required|boolean',
        ]);

        Setting::set('outbound_module_enabled', $request->outbound_module_enabled, 'boolean');

        $status = $request->outbound_module_enabled ? 'enabled' : 'disabled';

        return back()->with('success', "Outbound module has been {$status}.");
    }

    public function updateInboundModule(Request $request)
    {
        $request->validate([
            'inbound_module_enabled' => 'required|boolean',
        ]);

        Setting::set('inbound_module_enabled', $request->inbound_module_enabled, 'boolean');

        $status = $request->inbound_module_enabled ? 'enabled' : 'disabled';

        return back()->with('success', "Inbound module has been {$status}.");
    }
    
    public function updateAdminApprovalEmails(Request $request)
    {
        $request->validate([
            'admin_approval_emails' => 'nullable|string|max:1000',
        ]);
        
        // Clean up email list - remove spaces, validate format
        $emails = $request->admin_approval_emails;
        if ($emails) {
            $emailArray = array_map('trim', explode(',', $emails));
            $validEmails = array_filter($emailArray, function($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            });
            $emails = implode(', ', $validEmails);
        }
        
        Setting::set('admin_approval_emails', $emails ?: '');
        
        return back()->with('success', 'Admin approval email addresses updated successfully.');
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
