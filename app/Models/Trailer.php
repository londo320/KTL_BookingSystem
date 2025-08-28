<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trailer extends Model
{
    protected $fillable = [
        'trailer_number',
        'trailer_type',
        'size',
        'capacity_pallets',
        'capacity_weight_kg',
        'temperature_controlled',
        'owner',
        'additional_data',
        'is_active',
    ];

    protected $casts = [
        'additional_data' => 'array',
        'temperature_controlled' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function movements(): HasMany
    {
        return $this->hasMany(Movement::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('trailer_type', $type);
    }

    public function scopeTemperatureControlled($query)
    {
        return $query->where('temperature_controlled', true);
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
            ->whereIn('current_status', ['arrived', 'in_parking', 'at_bay', 'unloading', 'loading', 'loaded', 'in_parking'])
            ->exists();
    }

    public function getCapacityText(): string
    {
        $parts = [];
        if ($this->capacity_pallets) {
            $parts[] = $this->capacity_pallets.' pallets';
        }
        if ($this->capacity_weight_kg) {
            $parts[] = number_format($this->capacity_weight_kg).' kg';
        }

        return implode(', ', $parts) ?: 'Not specified';
    }
}
