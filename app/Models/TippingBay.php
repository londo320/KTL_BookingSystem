<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TippingBay extends Model
{
    protected $fillable = [
        'depot_id',
        'name',
        'code',
        'description',
        'is_active',
        'is_occupied',
        'equipment',
        'map_x',
        'map_y',
        'show_on_map',
        'map_rotation',
        'map_width',
        'map_height',
        'text_size',
        'text_color',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_occupied' => 'boolean',
        'show_on_map' => 'boolean',
        'equipment' => 'array',
        'map_x' => 'decimal:2',
        'map_y' => 'decimal:2',
        'map_rotation' => 'decimal:2',
        'map_width' => 'integer',
        'map_height' => 'integer',
    ];

    public function depot(): BelongsTo
    {
        return $this->belongsTo(Depot::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function currentBooking()
    {
        // Check for regular bookings first
        $regularBooking = Booking::whereHas('movements', function ($query) {
                $query->where('tipping_bay_id', $this->id)
                      ->whereIn('current_status', ['at_bay', 'unloading', 'empty', 'in_location']);
            })
            ->whereNull('departed_at')
            ->latest()
            ->first();
            
        if ($regularBooking) {
            return $regularBooking;
        }
        
        // Check for factory bookings
        $factoryBooking = \App\Models\FactoryBooking::whereHas('movements', function ($query) {
                $query->where('tipping_bay_id', $this->id)
                      ->whereIn('current_status', ['at_bay', 'unloading', 'empty', 'in_location']);
            })
            ->whereNull('departed_at')
            ->latest()
            ->first();
            
        return $factoryBooking;
    }

    public function getCurrentBookingAttribute()
    {
        return $this->currentBooking();
    }

    public function isAvailable(): bool
    {
        return $this->is_active && ! $this->is_occupied;
    }

    public function markOccupied(?Booking $booking = null): void
    {
        $this->update(['is_occupied' => true]);

        if ($booking) {
            BookingHistory::recordAction(
                $booking,
                'modified',
                "Moved to tipping bay: {$this->name}",
                null,
                null,
                ['bay_id' => $this->id, 'bay_name' => $this->name, 'action_type' => 'moved_to_bay']
            );
        }
    }

    public function markAvailable(?Booking $booking = null): void
    {
        $this->update(['is_occupied' => false]);

        if ($booking) {
            BookingHistory::recordAction(
                $booking,
                'modified',
                "Bay cleared: {$this->name}",
                null,
                null,
                ['bay_id' => $this->id, 'bay_name' => $this->name, 'action_type' => 'bay_cleared']
            );
        }
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
        return $query->active()->where('is_occupied', false);
    }

    public function updateOccupancyStatus(): void
    {
        $hasActiveBookings = $this->bookings()
            ->whereNull('departed_at')
            ->whereIn('tipping_status', ['moved_to_bay', 'tipping_in_progress', 'tipping_completed'])
            ->exists();

        $this->update(['is_occupied' => $hasActiveBookings]);
    }

    public function syncOccupancyStatus(): bool
    {
        $hasActiveBookings = $this->bookings()
            ->whereNull('departed_at')
            ->whereIn('tipping_status', ['moved_to_bay', 'tipping_in_progress', 'tipping_completed'])
            ->exists();

        if ($this->is_occupied !== $hasActiveBookings) {
            $this->update(['is_occupied' => $hasActiveBookings]);
            return true; // Status was changed
        }

        return false; // No change needed
    }

    public function getStatusBadgeAttribute(): string
    {
        if (! $this->is_active) {
            return '<span class="px-2 py-1 text-xs bg-gray-200 text-gray-600 rounded">Inactive</span>';
        }

        if ($this->is_occupied) {
            return '<span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded">Occupied</span>';
        }

        return '<span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">Available</span>';
    }
}
