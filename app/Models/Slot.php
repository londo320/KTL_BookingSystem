<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Slot extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'depot_id',
        'tipping_bay_id',
        'start_at',
        'end_at',
        'capacity',
        'is_blocked',
        'released_at',
        'locked_at',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'locked_at' => 'datetime',
        'released_at' => 'datetime',
        'cut_off_time' => 'string', // or 'datetime:H:i' if needed
    ];

    protected $dates = ['released_at', 'locked_at'];

    /**
     * A slot belongs to a depot
     */
    public function depot(): BelongsTo
    {
        return $this->belongsTo(Depot::class);
    }

    /**
     * A slot belongs to a tipping bay
     */
    public function tippingBay(): BelongsTo
    {
        return $this->belongsTo(TippingBay::class);
    }

    // Alias for convenience
    public function bay(): BelongsTo
    {
        return $this->tippingBay();
    }

    /**
     * A slot can have many bookings
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function allowed_customers()
    {
        return $this->belongsToMany(
            Customer::class,
            'slot_customer',       // Pivot table
            'slot_id',             // Foreign key on pivot table for this model
            'customer_id'          // Foreign key on pivot table for related model
        );
    }

    /**
     * Get all bookings occupying this slot (including extended bookings from earlier slots)
     */
    public function occupyingBookings()
    {
        return $this->belongsToMany(Booking::class, 'slot_bookings')
            ->withPivot('is_primary')
            ->withTimestamps();
    }

    /**
     * Check remaining capacity for this slot
     */
    public function remainingCapacity()
    {
        $occupied = $this->occupyingBookings()->count();
        return max(0, ($this->capacity ?? 1) - $occupied);
    }

    /**
     * Check if slot has available capacity
     */
    public function hasCapacity()
    {
        return $this->remainingCapacity() > 0;
    }
}
