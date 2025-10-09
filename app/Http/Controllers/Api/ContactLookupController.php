<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

class ContactLookupController extends Controller
{
    /**
     * Search for contacts based on name, filtering by depot, supplier, haulier
     */
    public function search(Request $request)
    {
        $query = $request->input('query');
        $depotId = $request->input('depot_id');
        $supplier = $request->input('supplier');
        $haulier = $request->input('haulier');

        if (empty($query)) {
            return response()->json([]);
        }

        // Build query to find unique contacts
        $bookingsQuery = Booking::select('contact_name', 'contact_phone', 'supplier', 'haulier')
            ->whereNotNull('contact_name')
            ->whereNotNull('contact_phone')
            ->where('contact_name', 'LIKE', '%' . $query . '%');

        // Filter by depot if provided
        if ($depotId) {
            $bookingsQuery->whereHas('slot', function ($q) use ($depotId) {
                $q->where('depot_id', $depotId);
            });
        }

        // Filter by supplier if provided
        if ($supplier) {
            $bookingsQuery->where('supplier', $supplier);
        }

        // Filter by haulier if provided
        if ($haulier) {
            $bookingsQuery->where('haulier', $haulier);
        }

        // Get unique contacts (group by name and phone)
        $contacts = $bookingsQuery
            ->groupBy('contact_name', 'contact_phone', 'supplier', 'haulier')
            ->limit(10)
            ->get()
            ->map(function ($booking) {
                return [
                    'name' => $booking->contact_name,
                    'phone' => $booking->contact_phone,
                    'supplier' => $booking->supplier,
                    'haulier' => $booking->haulier,
                    'display' => $booking->contact_name . ' (' . $booking->contact_phone . ')' .
                        ($booking->supplier ? ' - ' . $booking->supplier : '') .
                        ($booking->haulier ? ' - ' . $booking->haulier : ''),
                ];
            });

        return response()->json($contacts);
    }

    /**
     * Get contact phone number by name (with optional filters)
     */
    public function getPhone(Request $request)
    {
        $contactName = $request->input('name');
        $depotId = $request->input('depot_id');
        $supplier = $request->input('supplier');
        $haulier = $request->input('haulier');

        if (empty($contactName)) {
            return response()->json(['phone' => null]);
        }

        $query = Booking::whereNotNull('contact_phone')
            ->where('contact_name', $contactName);

        // Apply filters in order of specificity
        if ($supplier) {
            $query->where('supplier', $supplier);
        }

        if ($haulier) {
            $query->where('haulier', $haulier);
        }

        if ($depotId) {
            $query->whereHas('slot', function ($q) use ($depotId) {
                $q->where('depot_id', $depotId);
            });
        }

        // Get the most recent booking with this contact
        $booking = $query->orderBy('created_at', 'desc')->first();

        return response()->json([
            'phone' => $booking ? $booking->contact_phone : null,
            'supplier' => $booking ? $booking->supplier : null,
            'haulier' => $booking ? $booking->haulier : null,
        ]);
    }
}
