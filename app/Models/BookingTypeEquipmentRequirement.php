<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingTypeEquipmentRequirement extends Model
{
    protected $fillable = [
        'booking_type_id',
        'required_equipment',
        'is_active',
    ];

    protected $casts = [
        'required_equipment' => 'array',
        'is_active' => 'boolean',
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
        $requirement = static::where('booking_type_id', $bookingTypeId)
            ->where('is_active', true)
            ->first();

        return $requirement?->required_equipment ?? [];
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
