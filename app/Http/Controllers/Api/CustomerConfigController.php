<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerBookingConfig;
use App\Models\Slot;
use Illuminate\Http\Request;

class CustomerConfigController extends Controller
{
    /**
     * Get customer booking configuration
     */
    public function getConfig(Request $request)
    {
        $customerId = $request->input('customer_id');
        $slotId = $request->input('slot_id');
        $depotId = $request->input('depot_id');

        // Fall back to deriving depot from slot if depot_id wasn't given directly
        if (! $depotId && $slotId) {
            $slot = Slot::find($slotId);
            $depotId = $slot?->depot_id;
        }

        // Get configuration
        $config = CustomerBookingConfig::getConfig($customerId, $depotId);

        return response()->json([
            'config' => $config,
            'depot_id' => $depotId,
        ]);
    }
}
