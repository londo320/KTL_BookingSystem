<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerDepotTimeWindow;
use App\Models\Depot;
use Illuminate\Http\Request;

class CustomerTimeWindowController extends Controller
{
    public function edit(Customer $customer)
    {
        $depots = Depot::orderBy('name')->get();
        $timeWindows = CustomerDepotTimeWindow::where('customer_id', $customer->id)
            ->get()
            ->keyBy('depot_id');

        return view('admin.customers.time-windows', compact('customer', 'depots', 'timeWindows'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'depots' => 'nullable|array',
            'depots.*.allowed_start_time' => 'nullable|date_format:H:i',
            'depots.*.allowed_end_time' => 'nullable|date_format:H:i',
            'depots.*.days_of_week' => 'nullable|array',
            'depots.*.days_of_week.*' => 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'depots.*.is_active' => 'nullable|boolean',
        ]);

        if (!empty($validated['depots'])) {
            foreach ($validated['depots'] as $depotId => $windowData) {
                // Only create/update if time window is set
                if (!empty($windowData['allowed_start_time']) && !empty($windowData['allowed_end_time'])) {
                    CustomerDepotTimeWindow::updateOrCreate(
                        [
                            'customer_id' => $customer->id,
                            'depot_id' => $depotId,
                        ],
                        [
                            'allowed_start_time' => $windowData['allowed_start_time'],
                            'allowed_end_time' => $windowData['allowed_end_time'],
                            'days_of_week' => $windowData['days_of_week'] ?? null,
                            'is_active' => $windowData['is_active'] ?? true,
                        ]
                    );
                } else {
                    // Delete if times are cleared
                    CustomerDepotTimeWindow::where('customer_id', $customer->id)
                        ->where('depot_id', $depotId)
                        ->delete();
                }
            }
        }

        return redirect()
            ->route('app.customers.time-windows.edit', $customer)
            ->with('success', 'Time windows updated successfully!');
    }
}
