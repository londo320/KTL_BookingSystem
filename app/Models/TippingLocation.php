<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TippingLocation extends Model
{
    protected $fillable = [
        'depot_id',
        'name',
        'code',
        'description',
        'location_type',
        'capacity',
        'is_active',
        'coordinates',
        'map_x',
        'map_y',
        'show_on_map',
        'map_width',
        'map_height',
        'map_rotation',
        'text_size',
        'text_color',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'integer',
        'coordinates' => 'array',
        'show_on_map' => 'boolean',
        'map_x' => 'decimal:2',
        'map_y' => 'decimal:2',
        'map_width' => 'integer',
        'map_height' => 'integer',
        'map_rotation' => 'decimal:2',
    ];

    // Location type constants
    public const TYPE_DROP_ZONE = 'drop_zone';
    public const TYPE_COLLECTION_ZONE = 'collection_zone';
    public const TYPE_GENERAL = 'general';

    public function depot(): BelongsTo
    {
        return $this->belongsTo(Depot::class);
    }

    // Note: Bookings are related through the movements table
    // Use activeBookings() relationship instead

    public function activeBookings()
    {
        return $this->hasManyThrough(
            Booking::class,
            \App\Models\Movement::class,
            'tipping_location_id', // Foreign key on movements table
            'id', // Foreign key on bookings table
            'id', // Local key on tipping_locations table
            'booking_id' // Local key on movements table
        )->whereIn('movements.current_status', [
            'trailer_dropped',
            'at_bay',
            'unloading',
        ]);
    }

    public function getCurrentOccupancy(): int
    {
        return $this->activeBookings()->count();
    }

    public function getAvailableCapacity(): int
    {
        return max(0, $this->capacity - $this->getCurrentOccupancy());
    }

    public function isAvailable(): bool
    {
        return $this->is_active && $this->getAvailableCapacity() > 0;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForDepot($query, $depotId)
    {
        return $query->where('depot_id', $depotId);
    }

    public function scopeAvailable($query)
    {
        return $query->active()->whereRaw('
            (SELECT COUNT(*) FROM movements 
             WHERE movements.tipping_location_id = tipping_locations.id 
             AND movements.current_status IN ("trailer_dropped", "at_bay", "unloading")
            ) < tipping_locations.capacity
        ');
    }

    // Location type scopes
    public function scopeDropZones($query)
    {
        return $query->where('location_type', self::TYPE_DROP_ZONE);
    }

    public function scopeCollectionZones($query)
    {
        return $query->where('location_type', self::TYPE_COLLECTION_ZONE);
    }

    public function scopeGeneral($query)
    {
        return $query->where('location_type', self::TYPE_GENERAL);
    }

    public function markOccupied(?Booking $booking = null): void
    {
        if ($booking) {
            BookingHistory::recordAction(
                $booking,
                'modified',
                "Trailer dropped at location: {$this->name}",
                null,
                null,
                ['location_id' => $this->id, 'location_name' => $this->name, 'action_type' => 'trailer_dropped']
            );
        }
    }

    public function markAvailable(?Booking $booking = null): void
    {
        if ($booking) {
            BookingHistory::recordAction(
                $booking,
                'location_cleared',
                "Location cleared: {$this->name}",
                null,
                null,
                ['location_id' => $this->id, 'location_name' => $this->name]
            );
        }
    }
}
