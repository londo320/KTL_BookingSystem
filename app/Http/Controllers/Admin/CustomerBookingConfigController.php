<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerBookingConfig;
use App\Models\Depot;
use Illuminate\Http\Request;

class CustomerBookingConfigController extends Controller
{
    // No constructor middleware needed - route already has 'function-access' middleware

    /**
     * Show the form for editing customer booking configurations
     */
    public function edit(Customer $customer)
    {
        $depots = Depot::orderBy('name')->get();

        // Get existing configs for this customer
        $configs = CustomerBookingConfig::where('customer_id', $customer->id)
            ->get()
            ->keyBy('depot_id');

        // Build config array with defaults
        $configData = [];

        // Global config (depot_id = null)
        $globalConfig = $configs->get(null);
        $configData['global'] = [
            'sku_fields_enabled' => $globalConfig?->sku_fields_enabled ?? true,
            'require_po_data' => $globalConfig?->require_po_data ?? true,
        ];

        // Per-depot configs
        foreach ($depots as $depot) {
            $depotConfig = $configs->get($depot->id);
            $configData['depots'][$depot->id] = [
                'sku_fields_enabled' => $depotConfig?->sku_fields_enabled ?? null,
                'require_po_data' => $depotConfig?->require_po_data ?? null,
            ];
        }

        return view('admin.customers.booking-config', compact('customer', 'depots', 'configData'));
    }

    /**
     * Update customer booking configurations
     */
    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'global.sku_fields_enabled' => 'required|boolean',
            'global.require_po_data' => 'required|boolean',
            'depots.*.sku_fields_enabled' => 'nullable|boolean',
            'depots.*.require_po_data' => 'nullable|boolean',
        ]);

        // Update global config
        CustomerBookingConfig::updateOrCreate(
            [
                'customer_id' => $customer->id,
                'depot_id' => null,
            ],
            [
                'sku_fields_enabled' => $validated['global']['sku_fields_enabled'],
                'require_po_data' => $validated['global']['require_po_data'],
            ]
        );

        // Update per-depot configs
        if (!empty($validated['depots'])) {
            foreach ($validated['depots'] as $depotId => $config) {
                // Convert empty strings to null
                $skuEnabled = $config['sku_fields_enabled'] ?? null;
                $requirePo = $config['require_po_data'] ?? null;

                // If both are empty strings or null, they'll be null here
                $skuEnabled = $skuEnabled === '' ? null : $skuEnabled;
                $requirePo = $requirePo === '' ? null : $requirePo;

                // Only create/update if values are set (not null)
                if ($skuEnabled !== null || $requirePo !== null) {
                    CustomerBookingConfig::updateOrCreate(
                        [
                            'customer_id' => $customer->id,
                            'depot_id' => $depotId,
                        ],
                        [
                            'sku_fields_enabled' => $skuEnabled ?? true,
                            'require_po_data' => $requirePo ?? true,
                        ]
                    );
                } else {
                    // If both are null, delete the depot-specific config (fall back to global)
                    CustomerBookingConfig::where('customer_id', $customer->id)
                        ->where('depot_id', $depotId)
                        ->delete();
                }
            }
        }

        return redirect()
            ->route('app.customers.booking-config.edit', $customer)
            ->with('success', 'Booking configuration updated successfully!');
    }
}
