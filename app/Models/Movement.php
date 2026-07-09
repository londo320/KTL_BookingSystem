<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Movement extends Model
{
    protected $fillable = [
        'movement_type',
        'reference_number',
        'depot_id',
        'vehicle_id',
        'trailer_id',
        'carrier_company',
        'carrier_contact',
        'driver_name',
        'driver_phone',
        'estimated_arrival',
        'actual_arrival',
        'estimated_departure',
        'actual_departure',
        'current_status',
        'gate_number',
        'tipping_location_id',
        'tipping_bay_id',
        'current_location_notes',
        'load_type',
        'hazmat',
        'temperature_requirements',
        'special_instructions',
        'unloading_started_at',
        'unloading_completed_at',
        'moved_to_bay_at',
        'moved_to_location_at',
        'loading_started_at',
        'loading_completed_at',
        'operation_notes',
        'trailer_dropped_at',
        'trailer_collected_at',
        'unit_departed_at',
        'collection_unit_arrived_at',
        'collection_unit_departed_at',
        'collection_unit_registration',
        'collection_driver_name',
        'collection_driver_phone',
        'collection_notes',
        'collecting_vehicle_id',
        'swap_notes',
        'booking_id',
        'factory_booking_id',
        'consignment_id',
        'additional_data',
        'custom_fields',
    ];

    protected $casts = [
        'hazmat' => 'boolean',
        'additional_data' => 'array',
        'custom_fields' => 'array',
        'estimated_arrival' => 'datetime',
        'actual_arrival' => 'datetime',
        'estimated_departure' => 'datetime',
        'actual_departure' => 'datetime',
        'unloading_started_at' => 'datetime',
        'unloading_completed_at' => 'datetime',
        'moved_to_bay_at' => 'datetime',
        'moved_to_location_at' => 'datetime',
        'loading_started_at' => 'datetime',
        'loading_completed_at' => 'datetime',
        'trailer_dropped_at' => 'datetime',
        'trailer_collected_at' => 'datetime',
        'unit_departed_at' => 'datetime',
        'collection_unit_arrived_at' => 'datetime',
        'collection_unit_departed_at' => 'datetime',
    ];

    // Relationships
    public function depot(): BelongsTo
    {
        return $this->belongsTo(Depot::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function trailer(): BelongsTo
    {
        return $this->belongsTo(Trailer::class);
    }

    public function collecting_vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'collecting_vehicle_id');
    }

    public function tippingLocation(): BelongsTo
    {
        return $this->belongsTo(TippingLocation::class, 'tipping_location_id');
    }

    public function tippingBay(): BelongsTo
    {
        return $this->belongsTo(TippingBay::class, 'tipping_bay_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function factoryBooking(): BelongsTo
    {
        return $this->belongsTo(FactoryBooking::class);
    }

    public function consignment(): BelongsTo
    {
        return $this->belongsTo(Consignment::class);
    }

    // Get the bookable model (either Booking or FactoryBooking)
    public function getBookableAttribute()
    {
        return $this->booking_id ? $this->booking : $this->factoryBooking;
    }

    public function loads(): HasMany
    {
        return $this->hasMany(MovementLoad::class);
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('movement_type', $type);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('current_status', $status);
    }

    public function scopeOnSite($query)
    {
        return $query->whereIn('current_status', [
            'arrived', 'in_parking', 'at_bay', 'unloading', 'empty', 'back_to_parking',
        ]);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('actual_arrival', Carbon::today());
    }

    // Helper methods
    public function isOnSite(): bool
    {
        return in_array($this->current_status, [
            'arrived', 'in_parking', 'at_bay', 'unloading', 'empty', 'back_to_parking',
        ]);
    }

    public function canStartUnloading(): bool
    {
        return in_array($this->current_status, ['at_bay', 'arrived']) &&
               $this->tipping_bay_id &&
               ! $this->unloading_started_at;
    }

    public function canStartLoading(): bool
    {
        return in_array($this->current_status, ['empty', 'at_bay']) &&
               $this->tipping_bay_id &&
               ! $this->loading_started_at;
    }

    public function getTotalExpectedPallets(): int
    {
        return $this->loads()->sum('expected_pallets');
    }

    public function getTotalActualPallets(): int
    {
        return $this->loads()->sum('actual_pallets');
    }

    public function getTotalExpectedCases(): int
    {
        return $this->loads()->sum('expected_cases');
    }

    public function getTotalActualCases(): int
    {
        return $this->loads()->sum('actual_cases');
    }

    public function getTimeOnSite(): ?string
    {
        if (! $this->actual_arrival) {
            return null;
        }

        $endTime = $this->actual_departure ?? now();

        return $this->actual_arrival->diffForHumans($endTime, true);
    }

    public function getTimeInBay(): ?string
    {
        if (! $this->moved_to_bay_at) {
            return null;
        }

        // End time is when they left the bay or now if still there
        $endTime = $this->moved_to_location_at ?? $this->actual_departure ?? now();
        
        return $this->moved_to_bay_at->diffForHumans($endTime, true);
    }

    public function getTimeInCurrentStatus(): ?string
    {
        $statusStartTime = null;
        
        switch ($this->current_status) {
            case 'at_bay':
                $statusStartTime = $this->moved_to_bay_at;
                break;
            case 'unloading':
                $statusStartTime = $this->unloading_started_at;
                break;
            case 'in_parking':
            case 'back_to_parking':
                $statusStartTime = $this->moved_to_location_at;
                break;
            case 'trailer_dropped':
                $statusStartTime = $this->trailer_dropped_at;
                break;
        }
        
        if (!$statusStartTime) {
            return null;
        }
        
        return $statusStartTime->diffForHumans(now(), true);
    }

    // Status update methods
    public function updateStatus(string $status, ?string $notes = null): bool
    {
        $this->current_status = $status;
        if ($notes) {
            $this->operation_notes = ($this->operation_notes ? $this->operation_notes."\n" : '').
                                   '['.now()->format('Y-m-d H:i').'] '.$notes;
        }

        return $this->save();
    }

    public function startUnloading(?string $notes = null): bool
    {
        if (! $this->canStartUnloading()) {
            return false;
        }

        $this->unloading_started_at = now();
        $this->current_status = 'unloading';
        if ($notes) {
            $this->operation_notes = $notes;
        }

        return $this->save();
    }

    public function completeUnloading(?string $notes = null): bool
    {
        if ($this->current_status !== 'unloading') {
            return false;
        }

        $this->unloading_completed_at = now();
        $this->current_status = 'empty';
        if ($notes) {
            $this->operation_notes = ($this->operation_notes ? $this->operation_notes."\n" : '').
                                   '['.now()->format('Y-m-d H:i').'] '.$notes;
        }

        return $this->save();
    }

    public function startLoading(?string $notes = null): bool
    {
        if (! $this->canStartLoading()) {
            return false;
        }

        $this->loading_started_at = now();
        $this->current_status = 'loading';
        if ($notes) {
            $this->operation_notes = ($this->operation_notes ? $this->operation_notes."\n" : '').
                                   '['.now()->format('Y-m-d H:i').'] '.$notes;
        }

        return $this->save();
    }

    public function completeLoading(?string $notes = null): bool
    {
        if ($this->current_status !== 'loading') {
            return false;
        }

        $this->loading_completed_at = now();
        $this->current_status = 'loaded';
        if ($notes) {
            $this->operation_notes = ($this->operation_notes ? $this->operation_notes."\n" : '').
                                   '['.now()->format('Y-m-d H:i').'] '.$notes;
        }

        return $this->save();
    }
}
