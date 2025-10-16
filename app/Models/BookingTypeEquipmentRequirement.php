<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingTypeEquipmentRequirement extends Model
{
    protected $fillable = [
        'booking_type_id',
        'equipment_type',
        'is_required',
        'priority_boost',
        'is_active',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'priority_boost' => 'integer',
    ];

    public function bookingType(): BelongsTo
    {
        return $this->belongsTo(BookingType::class);
    }

    /**
     * Get required equipment for a booking type
     */
    public static function getRequiredEquipment(int $bookingTypeId): array
    {
        return static::where('booking_type_id', $bookingTypeId)
            ->where('is_required', true)
            ->where('is_active', true)
            ->pluck('equipment_type')
            ->toArray();
    }

    /**
     * Check if booking type has equipment requirements
     */
    public static function hasRequirements(int $bookingTypeId): bool
    {
        return static::where('booking_type_id', $bookingTypeId)
            ->where('is_active', true)
            ->exists();
    }
}
