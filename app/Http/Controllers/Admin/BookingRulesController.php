<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingType;
use App\Models\Depot;
use Illuminate\Http\Request;

class BookingRulesController extends Controller
{
    public function index()
    {
        $depots = Depot::with('bookingTypes')->get();
        $types = BookingType::all();

        return view('admin.booking-rules.index', compact('depots', 'types'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'rules' => 'array',
            'rules.*.duration' => 'nullable|integer|min:1',
        ]);

        foreach ($request->rules ?? [] as $key => $value) {
            [$depotId, $typeId] = explode('-', $key);

            if ($value['duration']) {
                // attach or update
                Depot::find($depotId)
                    ->bookingTypes()
                    ->syncWithoutDetaching([
                        $typeId => ['duration_minutes' => $value['duration']],
                    ]);
            } else {
                // detach if empty
                Depot::find($depotId)
                    ->bookingTypes()
                    ->detach($typeId);
            }
        }

        return redirect()->route('admin.booking-rules.index')->with('success', 'Rules updated.');
    }
}
