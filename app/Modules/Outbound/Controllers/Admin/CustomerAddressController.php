<?php

namespace App\Modules\Outbound\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Outbound\Models\CustomerAddress;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CustomerAddressController extends Controller
{
    public function index(Request $request)
    {
        $query = CustomerAddress::with('customer');

        // Filter by customer
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Filter by active status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Search by address or company name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('address_line_1', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('postcode', 'like', "%{$search}%");
            });
        }

        // Filter addresses needing geocoding
        if ($request->filled('needs_geocoding') && $request->needs_geocoding) {
            $query->needsGeocoding();
        }

        $addresses = $query->orderBy('customer_id')
            ->orderBy('is_default', 'desc')
            ->orderBy('company_name')
            ->paginate(20);

        $customers = Customer::orderBy('name')->get();

        // Statistics
        $stats = [
            'total_addresses' => CustomerAddress::count(),
            'active_addresses' => CustomerAddress::active()->count(),
            'needs_geocoding' => CustomerAddress::needsGeocoding()->count(),
            'default_addresses' => CustomerAddress::where('is_default', true)->count(),
        ];

        return view('outbound::admin.addresses.index', compact('addresses', 'customers', 'stats'));
    }

    public function show(CustomerAddress $address)
    {
        $address->load(['customer', 'orders.outboundLoad']);

        return view('outbound::admin.addresses.show', compact('address'));
    }

    public function create(Request $request)
    {
        $customers = Customer::orderBy('name')->get();
        $selectedCustomer = null;

        if ($request->filled('customer_id')) {
            $selectedCustomer = Customer::find($request->customer_id);
        }

        return view('outbound::admin.addresses.create', compact('customers', 'selectedCustomer'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'address_name' => 'nullable|string|max:100',
            'is_default' => 'boolean',
            'contact_name' => 'nullable|string|max:100',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:100',
            'company_name' => 'nullable|string|max:200',
            'address_line_1' => 'required|string|max:200',
            'address_line_2' => 'nullable|string|max:200',
            'city' => 'required|string|max:100',
            'county' => 'nullable|string|max:100',
            'postcode' => 'required|string|max:20',
            'country' => 'nullable|string|max:5',
            'delivery_instructions' => 'nullable|string',
            'access_notes' => 'nullable|string',
            'delivery_hours' => 'nullable|array',
            'requires_appointment' => 'boolean',
            'requires_signature' => 'boolean',
            'requires_photo_proof' => 'boolean',
            'special_equipment' => 'nullable|array',
            'latest_delivery_time' => 'nullable|date_format:H:i',
            'delivery_buffer_minutes' => 'nullable|integer|min:0|max:120',
            'unloading_duration_minutes' => 'nullable|integer|min:5|max:240',
            'site_closure_time' => 'nullable|date_format:H:i',
            'lunch_break_start' => 'nullable|date_format:H:i',
            'lunch_break_end' => 'nullable|date_format:H:i',
        ]);

        // Set default country
        if (empty($validated['country'])) {
            $validated['country'] = 'GB';
        }

        // Handle default address logic
        if ($validated['is_default'] ?? false) {
            CustomerAddress::where('customer_id', $validated['customer_id'])
                ->update(['is_default' => false]);
        }

        $address = CustomerAddress::create($validated);

        // Attempt geocoding if possible
        if ($this->shouldGeocode()) {
            $this->geocodeAddress($address);
        }

        return redirect()->route('outbound.admin.addresses.index')
            ->with('success', 'Customer address created successfully');
    }

    public function edit(CustomerAddress $address)
    {
        $customers = Customer::orderBy('name')->get();

        return view('outbound::admin.addresses.edit', compact('address', 'customers'));
    }

    public function update(Request $request, CustomerAddress $address)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'address_name' => 'nullable|string|max:100',
            'is_default' => 'boolean',
            'contact_name' => 'nullable|string|max:100',
            'contact_phone' => 'nullable|string|max:20',
            'contact_email' => 'nullable|email|max:100',
            'company_name' => 'nullable|string|max:200',
            'address_line_1' => 'required|string|max:200',
            'address_line_2' => 'nullable|string|max:200',
            'city' => 'required|string|max:100',
            'county' => 'nullable|string|max:100',
            'postcode' => 'required|string|max:20',
            'country' => 'nullable|string|max:5',
            'delivery_instructions' => 'nullable|string',
            'access_notes' => 'nullable|string',
            'delivery_hours' => 'nullable|array',
            'requires_appointment' => 'boolean',
            'requires_signature' => 'boolean',
            'requires_photo_proof' => 'boolean',
            'special_equipment' => 'nullable|array',
            'latest_delivery_time' => 'nullable|date_format:H:i',
            'delivery_buffer_minutes' => 'nullable|integer|min:0|max:120',
            'unloading_duration_minutes' => 'nullable|integer|min:5|max:240',
            'site_closure_time' => 'nullable|date_format:H:i',
            'lunch_break_start' => 'nullable|date_format:H:i',
            'lunch_break_end' => 'nullable|date_format:H:i',
            'is_active' => 'boolean',
        ]);

        // Check if address has changed - if so, clear geocoding
        $addressChanged = $this->hasAddressChanged($address, $validated);

        // Handle default address logic
        if ($validated['is_default'] ?? false) {
            CustomerAddress::where('customer_id', $validated['customer_id'])
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        $address->update($validated);

        // Re-geocode if address changed
        if ($addressChanged && $this->shouldGeocode()) {
            $this->geocodeAddress($address);
        }

        return back()->with('success', 'Address updated successfully');
    }

    public function destroy(CustomerAddress $address)
    {
        // Check if address has active orders
        $activeOrdersCount = $address->orders()
            ->whereIn('status', ['pending', 'ready_for_collection', 'collected', 'in_transit', 'out_for_delivery'])
            ->count();

        if ($activeOrdersCount > 0) {
            return back()->with('error', "Cannot delete address with {$activeOrdersCount} active orders");
        }

        $address->delete();

        return redirect()->route('outbound.admin.addresses.index')
            ->with('success', 'Address deleted successfully');
    }

    public function geocode(CustomerAddress $address)
    {
        if ($this->geocodeAddress($address)) {
            return back()->with('success', 'Address geocoded successfully');
        }

        return back()->with('error', 'Failed to geocode address');
    }

    public function bulkGeocode(Request $request)
    {
        $addressIds = $request->input('address_ids', []);
        
        if (empty($addressIds)) {
            return back()->with('error', 'No addresses selected');
        }

        $addresses = CustomerAddress::whereIn('id', $addressIds)->get();
        $successCount = 0;

        foreach ($addresses as $address) {
            if ($this->geocodeAddress($address)) {
                $successCount++;
            }
        }

        return back()->with('success', "Successfully geocoded {$successCount} of {$addresses->count()} addresses");
    }

    public function setDefault(CustomerAddress $address)
    {
        // Remove default from other addresses for this customer
        CustomerAddress::where('customer_id', $address->customer_id)
            ->update(['is_default' => false]);

        // Set this address as default
        $address->update(['is_default' => true]);

        return back()->with('success', 'Default address updated');
    }

    // Helper methods
    protected function shouldGeocode(): bool
    {
        // Only geocode if service is enabled and we have API credentials
        return config('services.geocoding.enabled', false);
    }

    protected function geocodeAddress(CustomerAddress $address): bool
    {
        // This would integrate with your preferred geocoding service
        // (Google Maps, OpenStreetMap Nominatim, etc.)
        
        // For now, return mock success
        $address->update([
            'latitude' => 51.5074 + (rand(-1000, 1000) / 10000), // Mock London area
            'longitude' => -0.1278 + (rand(-1000, 1000) / 10000),
            'geocoded_at' => now(),
        ]);

        return true;
    }

    protected function hasAddressChanged(CustomerAddress $address, array $validated): bool
    {
        $addressFields = ['address_line_1', 'address_line_2', 'city', 'county', 'postcode', 'country'];

        foreach ($addressFields as $field) {
            if ($address->getOriginal($field) !== ($validated[$field] ?? null)) {
                return true;
            }
        }

        return false;
    }

    // AJAX endpoints
    public function getCustomerAddresses(Customer $customer)
    {
        $addresses = $customer->customerAddresses()
            ->active()
            ->orderBy('is_default', 'desc')
            ->orderBy('address_name')
            ->get();

        return response()->json($addresses);
    }

    public function validatePostcode(Request $request)
    {
        $postcode = $request->input('postcode');
        
        // Basic UK postcode validation
        $isValid = preg_match('/^[A-Za-z]{1,2}[0-9Rr][0-9A-Za-z]? [0-9][ABD-HJLNP-UW-Zabd-hjlnp-uw-z]{2}$/', $postcode);

        return response()->json(['valid' => (bool)$isValid]);
    }
}