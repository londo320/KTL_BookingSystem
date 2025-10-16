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
        'booking_start_time',
        'booking_end_time',
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
            ->withPivot('duration_minutes', 'booking_start_time', 'booking_end_time')
            ->withTimestamps();
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'booking_type_customer')
            ->withPivot('depot_id', 'duration_minutes', 'booking_start_time', 'booking_end_time')
            ->withTimestamps();
    }

    public function slots()
    {
        return $this->hasMany(Slot::class);
    }

    public function equipmentRequirements()
    {
        return $this->hasMany(BookingTypeEquipmentRequirement::class);
    }

    public function durationRules()
    {
        return $this->hasMany(BookingTypeDurationRule::class);
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

    /**
     * Get duration with case count factored in
     * Checks case-based rules first, then falls back to standard duration
     */
    public function getDurationWithCaseCount(
        int $caseCount,
        ?int $depotId = null,
        ?int $customerId = null
    ): int {
        // Check case-based duration rules first
        $caseDuration = BookingTypeDurationRule::getDurationForCaseCount(
            $this->id,
            $caseCount,
            $depotId,
            $customerId
        );

        if ($caseDuration !== null) {
            return $caseDuration;
        }

        // Fall back to standard duration logic
        if ($customerId && $depotId) {
            return $this->getDurationForCustomer($depotId, $customerId);
        }

        if ($depotId) {
            return $this->getDurationForDepot($depotId);
        }

        return $this->duration_minutes ?? 60;
    }

    /**
     * Get time restrictions for a specific customer and depot
     * Returns array with 'start' and 'end' keys
     * Priority: Customer+Depot > Customer-only > Depot-only > Default
     */
    public function getTimeRestrictionsFor(?int $customerId = null, ?int $depotId = null): array
    {
        // Check customer + depot specific
        if ($customerId && $depotId) {
            $customerDepotPivot = $this->customers()
                ->where('customer_id', $customerId)
                ->where('depot_id', $depotId)
                ->first();

            if ($customerDepotPivot && ($customerDepotPivot->pivot->booking_start_time || $customerDepotPivot->pivot->booking_end_time)) {
                return [
                    'start' => $customerDepotPivot->pivot->booking_start_time,
                    'end' => $customerDepotPivot->pivot->booking_end_time,
                ];
            }
        }

        // Check customer-only (any depot)
        if ($customerId) {
            $customerPivot = $this->customers()
                ->where('customer_id', $customerId)
                ->whereNull('depot_id')
                ->first();

            if ($customerPivot && ($customerPivot->pivot->booking_start_time || $customerPivot->pivot->booking_end_time)) {
                return [
                    'start' => $customerPivot->pivot->booking_start_time,
                    'end' => $customerPivot->pivot->booking_end_time,
                ];
            }
        }

        // Check depot-only
        if ($depotId) {
            $depotPivot = $this->depots()->where('depot_id', $depotId)->first();

            if ($depotPivot && ($depotPivot->pivot->booking_start_time || $depotPivot->pivot->booking_end_time)) {
                return [
                    'start' => $depotPivot->pivot->booking_start_time,
                    'end' => $depotPivot->pivot->booking_end_time,
                ];
            }
        }

        // Fall back to default
        return [
            'start' => $this->booking_start_time,
            'end' => $this->booking_end_time,
        ];
    }

    /**
     * Check if this booking type is available at a specific time
     * @param \Carbon\Carbon|string $time The time to check (slot start time)
     * @param int|null $customerId Optional customer ID for customer-specific rules
     * @param int|null $depotId Optional depot ID for depot-specific rules
     * @return bool
     */
    public function isAvailableAtTime($time, ?int $customerId = null, ?int $depotId = null): bool
    {
        $restrictions = $this->getTimeRestrictionsFor($customerId, $depotId);
        $startTime = $restrictions['start'];
        $endTime = $restrictions['end'];

        // If no time restrictions set, available 24/7
        if (!$startTime && !$endTime) {
            return true;
        }

        $checkTime = is_string($time) ? \Carbon\Carbon::parse($time) : $time;
        $timeOnly = $checkTime->format('H:i:s');

        // If only start time is set
        if ($startTime && !$endTime) {
            return $timeOnly >= $startTime;
        }

        // If only end time is set
        if (!$startTime && $endTime) {
            return $timeOnly <= $endTime;
        }

        // Both start and end times are set
        return $timeOnly >= $startTime && $timeOnly <= $endTime;
    }

    /**
     * Get human-readable time availability description
     * @return string
     */
    public function getTimeAvailabilityAttribute(): string
    {
        if (!$this->booking_start_time && !$this->booking_end_time) {
            return '24/7';
        }

        if ($this->booking_start_time && $this->booking_end_time) {
            return sprintf('%s - %s',
                \Carbon\Carbon::parse($this->booking_start_time)->format('H:i'),
                \Carbon\Carbon::parse($this->booking_end_time)->format('H:i')
            );
        }

        if ($this->booking_start_time) {
            return sprintf('From %s', \Carbon\Carbon::parse($this->booking_start_time)->format('H:i'));
        }

        if ($this->booking_end_time) {
            return sprintf('Until %s', \Carbon\Carbon::parse($this->booking_end_time)->format('H:i'));
        }

        return '24/7';
    }
}
