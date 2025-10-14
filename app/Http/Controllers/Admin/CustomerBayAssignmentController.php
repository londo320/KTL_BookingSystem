<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomerBayAssignment;
use App\Models\Depot;
use App\Models\Bay;
use Illuminate\Http\Request;

class CustomerBayAssignmentController extends Controller
{
    public function edit(Customer $customer)
    {
        $depots = Depot::with('bays')->orderBy('name')->get();
        $assignments = CustomerBayAssignment::where('customer_id', $customer->id)
            ->get()
            ->keyBy('tipping_bay_id');

        return view('admin.customers.bay-assignments', compact('customer', 'depots', 'assignments'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'bays' => 'nullable|array',
            'bays.*.is_allowed' => 'nullable|boolean',
            'bays.*.priority' => 'nullable|integer|min:0|max:100',
        ]);

        if (!empty($validated['bays'])) {
            foreach ($validated['bays'] as $bayId => $assignmentData) {
                // Only create/update if is_allowed is explicitly set
                if (isset($assignmentData['is_allowed'])) {
                    CustomerBayAssignment::updateOrCreate(
                        [
                            'customer_id' => $customer->id,
                            'tipping_bay_id' => $bayId,
                        ],
                        [
                            'is_active' => $assignmentData['is_allowed'],
                            'priority' => $assignmentData['priority'] ?? 50,
                        ]
                    );
                } else {
                    // Delete if is_allowed is not set (no restriction)
                    CustomerBayAssignment::where('customer_id', $customer->id)
                        ->where('tipping_bay_id', $bayId)
                        ->delete();
                }
            }
        }

        return redirect()
            ->route('app.customers.bay-assignments.edit', $customer)
            ->with('success', 'Bay assignments updated successfully!');
    }
}
