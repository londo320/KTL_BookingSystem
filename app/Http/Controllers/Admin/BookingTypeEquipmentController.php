<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BookingType;
use App\Models\BookingTypeEquipmentRequirement;
use Illuminate\Http\Request;

class BookingTypeEquipmentController extends Controller
{
    public function edit(BookingType $bookingType)
    {
        $requirements = BookingTypeEquipmentRequirement::where('booking_type_id', $bookingType->id)
            ->get()
            ->keyBy('equipment_type');

        return view('admin.booking_types.equipment-requirements', compact('bookingType', 'requirements'));
    }

    public function update(Request $request, BookingType $bookingType)
    {
        $validated = $request->validate([
            'equipment' => 'nullable|array',
            'equipment.*.required' => 'nullable|boolean',
            'equipment.*.priority_boost' => 'nullable|integer|min:0|max:100',
        ]);

        if (!empty($validated['equipment'])) {
            foreach ($validated['equipment'] as $equipmentType => $data) {
                // Only create/update if required is set
                if (isset($data['required']) && $data['required']) {
                    BookingTypeEquipmentRequirement::updateOrCreate(
                        [
                            'booking_type_id' => $bookingType->id,
                            'equipment_type' => $equipmentType,
                        ],
                        [
                            'is_required' => $data['required'],
                            'priority_boost' => $data['priority_boost'] ?? 10,
                        ]
                    );
                } else {
                    // Delete if not required
                    BookingTypeEquipmentRequirement::where('booking_type_id', $bookingType->id)
                        ->where('equipment_type', $equipmentType)
                        ->delete();
                }
            }
        }

        return redirect()
            ->route('app.booking-types.equipment.edit', $bookingType)
            ->with('success', 'Equipment requirements updated successfully!');
    }
}
