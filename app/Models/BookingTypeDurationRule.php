<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingTypeDurationRule extends Model
{
    protected $fillable = [
        'booking_type_id',
        'depot_id',
        'customer_id',
        'min_cases',
        'max_cases',
        'duration_minutes',
        'priority',
    ];

    protected $casts = [
        'min_cases' => 'integer',
        'max_cases' => 'integer',
        'duration_minutes' => 'integer',
        'priority' => 'integer',
    ];

    public function bookingType(): BelongsTo
    {
        return $this->belongsTo(BookingType::class);
    }

    public function depot(): BelongsTo
    {
        return $this->belongsTo(Depot::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get duration for a booking based on case count
     * Checks rules in priority order:
     * 1. Customer + depot specific
     * 2. Depot specific
     * 3. Global (no depot/customer)
     */
    public static function getDurationForCaseCount(
        int $bookingTypeId,
        int $caseCount,
        ?int $depotId = null,
        ?int $customerId = null
    ): ?int {
        $query = static::where('booking_type_id', $bookingTypeId)
            ->where('min_cases', '<=', $caseCount)
            ->where(function ($q) use ($caseCount) {
                $q->whereNull('max_cases')
                    ->orWhere('max_cases', '>=', $caseCount);
            })
            ->orderByDesc('priority');

        // Try customer + depot specific first
        if ($customerId && $depotId) {
            $rule = (clone $query)
                ->where('customer_id', $customerId)
                ->where('depot_id', $depotId)
                ->first();
            if ($rule) {
                return $rule->duration_minutes;
            }
        }

        // Try depot specific
        if ($depotId) {
            $rule = (clone $query)
                ->where('depot_id', $depotId)
                ->whereNull('customer_id')
                ->first();
            if ($rule) {
                return $rule->duration_minutes;
            }
        }

        // Try global rule
        $rule = (clone $query)
            ->whereNull('depot_id')
            ->whereNull('customer_id')
            ->first();

        return $rule?->duration_minutes;
    }

    /**
     * Check if case count falls within this rule's range
     */
    public function appliesToCaseCount(int $caseCount): bool
    {
        if ($caseCount < $this->min_cases) {
            return false;
        }

        if ($this->max_cases !== null && $caseCount > $this->max_cases) {
            return false;
        }

        return true;
    }
}
