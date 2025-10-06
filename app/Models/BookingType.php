<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'duration_minutes',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'start_time' => 'datetime',   // for bookings
        'end_time' => 'datetime',   // for bookings
        'cut_off_time' => 'string',     // or 'date:H:i' if you like
    ];

    public function depots()
    {
        return $this->belongsToMany(Depot::class, 'booking_type_depot')
            ->withPivot('duration_minutes')
            ->withTimestamps();
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'booking_type_customer')
            ->withPivot('depot_id', 'duration_minutes')
            ->withTimestamps();
    }

    public function slots()
    {
        return $this->hasMany(Slot::class);
    }

    /**
     * Get duration for a specific depot (in minutes)
     */
    public function getDurationForDepot($depotId)
    {
        $pivot = $this->depots()->where('depot_id', $depotId)->first();
        return $pivot ? $pivot->pivot->duration_minutes : ($this->duration_minutes ?? 60);
    }

    /**
     * Get duration for a specific customer at a specific depot (in minutes)
     * Checks in this order:
     * 1. Customer + depot specific
     * 2. Customer-only (depot_id null)
     * 3. Depot-only
     * 4. Default duration
     */
    public function getDurationForCustomer($depotId, $customerId)
    {
        // Check for customer + depot specific duration
        $customerDepotPivot = $this->customers()
            ->where('customer_id', $customerId)
            ->where('depot_id', $depotId)
            ->first();

        if ($customerDepotPivot) {
            return $customerDepotPivot->pivot->duration_minutes;
        }

        // Check for customer-only duration (any depot)
        $customerPivot = $this->customers()
            ->where('customer_id', $customerId)
            ->whereNull('depot_id')
            ->first();

        if ($customerPivot) {
            return $customerPivot->pivot->duration_minutes;
        }

        // Fall back to depot-only duration
        return $this->getDurationForDepot($depotId);
    }
}
