<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    protected $fillable = [
        'registration',
        'vehicle_type',
        'carrier_company',
        'default_driver_name',
        'default_driver_phone',
        'additional_data',
        'is_active',
    ];

    protected $casts = [
        'additional_data' => 'array',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function movements(): HasMany
    {
        return $this->hasMany(Movement::class);
    }

    public function collecting_movements(): HasMany
    {
        return $this->hasMany(Movement::class, 'collecting_vehicle_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCarrier($query, $carrier)
    {
        return $query->where('carrier_company', $carrier);
    }

    // Helper methods
    public function getCurrentMovement()
    {
        return $this->movements()
            ->whereNotIn('current_status', ['departed', 'trailer_collected'])
            ->latest()
            ->first();
    }

    public function isOnSite(): bool
    {
        return $this->movements()
            ->whereIn('current_status', ['arrived', 'in_parking', 'at_bay', 'unloading', 'loading', 'loaded'])
            ->exists();
    }
}
