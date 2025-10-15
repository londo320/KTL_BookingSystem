<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BayCapacityRule extends Model
{
    protected $fillable = [
        'depot_id',
        'booking_type_id',
        'time_start',
        'time_end',
        'days_of_week',
        'max_concurrent_bookings',
        'applicable_bay_ids',
        'capacity_weight',
        'is_active',
    ];

    protected $casts = [
        'days_of_week' => 'array',
        'applicable_bay_ids' => 'array',
        'max_concurrent_bookings' => 'integer',
        'capacity_weight' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function depot(): BelongsTo
    {
        return $this->belongsTo(Depot::class);
    }

    public function bookingType(): BelongsTo
    {
        return $this->belongsTo(BookingType::class);
    }

    /**
     * Check if this rule applies to a specific time
     */
    public function appliesToTime(Carbon $time): bool
    {
        if (!$this->is_active) {
            return false;
        }

        // Check day of week
        if ($this->days_of_week !== null && !empty($this->days_of_week)) {
            $dayName = $time->format('l'); // Monday, Tuesday, etc.
            if (!in_array($dayName, $this->days_of_week)) {
                return false;
            }
        }

        // Check time range
        $timeOnly = $time->format('H:i:s');
        return $timeOnly >= $this->time_start && $timeOnly < $this->time_end;
    }

    /**
     * Check if this rule applies to a specific bay
     */
    public function appliesToBay(int $bayId): bool
    {
        if ($this->applicable_bay_ids === null || empty($this->applicable_bay_ids)) {
            return true; // Applies to all bays
        }

        return in_array($bayId, $this->applicable_bay_ids);
    }

    /**
     * Get applicable capacity rules for a depot/booking type/time
     */
    public static function getApplicableRules(
        int $depotId,
        ?int $bookingTypeId,
        Carbon $time,
        ?int $bayId = null
    ): \Illuminate\Support\Collection {
        $query = static::where('depot_id', $depotId)
            ->where('is_active', true)
            ->where(function ($q) use ($bookingTypeId) {
                $q->whereNull('booking_type_id')
                    ->orWhere('booking_type_id', $bookingTypeId);
            });

        $rules = $query->get()->filter(function ($rule) use ($time, $bayId) {
            if (!$rule->appliesToTime($time)) {
                return false;
            }

            if ($bayId !== null && !$rule->appliesToBay($bayId)) {
                return false;
            }

            return true;
        });

        return $rules;
    }

    /**
     * Get current concurrent booking count for this rule
     */
    public function getCurrentConcurrentCount(Carbon $time): int
    {
        $applicableBayIds = $this->applicable_bay_ids ?? TippingBay::where('depot_id', $this->depot_id)
            ->pluck('id')
            ->toArray();

        // Count bookings that:
        // 1. Are in applicable bays
        // 2. Have the matching booking type (if specified)
        // 3. Overlap with the given time
        $query = Booking::whereHas('slot', function ($q) use ($applicableBayIds, $time) {
            $q->whereIn('tipping_bay_id', $applicableBayIds)
                ->where('start_at', '<=', $time)
                ->where('end_at', '>', $time);
        });

        if ($this->booking_type_id) {
            $query->where('booking_type_id', $this->booking_type_id);
        }

        return $query->count();
    }
}
