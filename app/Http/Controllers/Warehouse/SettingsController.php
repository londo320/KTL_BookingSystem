<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Depot;
use App\Models\Customer;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'function:settings.manage']);
    }

    /**
     * Show the warehouse settings form.
     */
    public function index()
    {
        $user = auth()->user();
        
        try {
            // Get depots accessible to this user (simplified approach)
            if ($user->hasRole('admin') || $user->hasRole('site-admin')) {
                $depots = Depot::all();
                $customers = Customer::all();
            } else {
                // For other roles, try to get accessible items
                $accessibleDepotIds = $user->getAccessibleDepotIds();
                $depots = Depot::whereIn('id', $accessibleDepotIds)->get();
                
                $accessibleCustomerIds = $user->getAccessibleCustomerIds();
                $customers = Customer::whereIn('id', $accessibleCustomerIds)->get();
            }
            
            // Get current factory tipping targets
            $defaultTarget = Setting::get('factory_tipping_target_default', 60);
            
            // Get depot-specific targets using assignable functions
            $depotTargets = [];
            foreach ($depots as $depot) {
                if ($depot->hasCustomFactoryTippingTargets()) {
                    $depotTargets[$depot->id] = $depot->getFactoryTippingTimeTarget();
                }
            }
            
            // Get customer-specific targets using assignable functions
            $customerTargets = [];
            foreach ($depots as $depot) {
                $customerTargets[$depot->id] = $depot->getCustomerFactoryTippingTargets();
            }

            return view('warehouse.settings.factory-tipping-targets', compact('depots', 'customers', 'defaultTarget', 'depotTargets', 'customerTargets'));
        } catch (\Exception $e) {
            // Fallback for debugging
            return response()->json([
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => $user->id,
                'user_email' => $user->email,
            ]);
        }
    }

    /**
     * Handle form submission.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        
        $data = $request->validate([
            'default_target' => 'required|integer|min:1|max:1440',
            'depot_targets' => 'nullable|array',
            'depot_targets.*' => 'nullable|integer|min:1|max:1440',
            'customer_targets' => 'nullable|array',
            'customer_targets.*' => 'nullable|array',
            'customer_targets.*.*' => 'nullable|integer|min:1|max:1440',
        ]);

        // Check user has permission to manage settings for these depots
        $accessibleDepotIds = $user->getAccessibleDepotIds();

        // Save default target (only if user has global settings permission)
        if ($user->hasFunction('settings.manage.global')) {
            Setting::setDefaultFactoryTippingTimeTarget($data['default_target']);
        }

        // Save depot-specific targets using assignable functions
        if (!empty($data['depot_targets'])) {
            foreach ($data['depot_targets'] as $depotId => $minutes) {
                if ($minutes && in_array($depotId, $accessibleDepotIds)) {
                    $depot = Depot::findOrFail((int)$depotId);
                    $depot->setFactoryTippingTimeTarget((int)$minutes);
                }
            }
        }

        // Save customer-specific targets using assignable functions
        if (!empty($data['customer_targets'])) {
            foreach ($data['customer_targets'] as $depotId => $customerTargets) {
                if (is_array($customerTargets) && in_array($depotId, $accessibleDepotIds)) {
                    $depot = Depot::findOrFail((int)$depotId);
                    foreach ($customerTargets as $customerId => $minutes) {
                        if ($minutes) {
                            $depot->setFactoryTippingTimeTarget((int)$minutes, (int)$customerId);
                        }
                    }
                }
            }
        }

        session()->flash('success', 'Factory tipping time targets saved successfully.');

        return redirect()->route('app.settings.factory-tipping-targets');
    }
}