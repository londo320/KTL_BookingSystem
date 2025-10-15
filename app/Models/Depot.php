<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Depot extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slot_system_mode',
        'location',
        'cut_off_time',
        'map_file',
        'map_notes',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'start_time' => 'datetime',   // for bookings
        'end_time' => 'datetime',   // for bookings
        'cut_off_time' => 'string',     // or 'date:H:i' if you like
    ];

    public function slots()
    {
        return $this->hasMany(Slot::class);
    }

    public function bookingTypes()
    {
        return $this->belongsToMany(BookingType::class)
            ->withPivot('duration_minutes')
            ->withTimestamps();
    }

    public function slotGenerationSetting()
    {
        return $this->hasOne(SlotGenerationSetting::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot(['expected_case_count', 'override_duration_minutes'])
            ->withTimestamps();
    }

    public function customerProducts()
    {
        return $this->hasMany(CustomerDepotProduct::class);
    }

    public function caseRanges()
    {
        return $this->hasMany(DepotCaseRange::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'depot_user');
    }

    public function tippingBays()
    {
        return $this->hasMany(TippingBay::class);
    }

    // Alias for convenience
    public function bays()
    {
        return $this->tippingBays();
    }

    /**
     * Get factory vehicle tipping time target in minutes for this depot and optional customer
     */
    public function getFactoryTippingTimeTarget(int $customerId = null): int
    {
        return \App\Models\Setting::getFactoryTippingTimeTarget($this->id, $customerId);
    }

    /**
     * Set factory vehicle tipping time target in minutes for this depot
     */
    public function setFactoryTippingTimeTarget(int $minutes, int $customerId = null): void
    {
        \App\Models\Setting::setFactoryTippingTimeTarget($this->id, $minutes, $customerId);
    }

    /**
     * Check if this depot has custom factory tipping time targets
     */
    public function hasCustomFactoryTippingTargets(): bool
    {
        $depotSetting = \App\Models\Setting::get("factory_tipping_target_depot_{$this->id}");
        return $depotSetting !== null;
    }

    /**
     * Get all customer-specific factory tipping time targets for this depot
     */
    public function getCustomerFactoryTippingTargets(): array
    {
        $targets = [];
        $customers = \App\Models\Customer::all();
        
        foreach ($customers as $customer) {
            $target = \App\Models\Setting::get("factory_tipping_target_depot_{$this->id}_customer_{$customer->id}");
            if ($target !== null) {
                $targets[$customer->id] = [
                    'customer' => $customer,
                    'target_minutes' => $target,
                ];
            }
        }
        
        return $targets;
    }
}
