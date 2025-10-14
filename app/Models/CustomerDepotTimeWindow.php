<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerDepotTimeWindow extends Model
{
    protected $fillable = [
        'customer_id',
        'depot_id',
        'allowed_start_time',
        'allowed_end_time',
        'days_of_week',
        'is_active',
    ];

    protected $casts = [
        'allowed_start_time' => 'string',
        'allowed_end_time' => 'string',
        'days_of_week' => 'array',
        'is_active' => 'boolean',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function depot(): BelongsTo
    {
        return $this->belongsTo(Depot::class);
    }

    /**
     * Check if a customer can book at a specific depot and time
     */
    public static function isTimeAllowed(int $customerId, int $depotId, Carbon $slotTime): bool
    {
        $window = static::where('customer_id', $customerId)
            ->where('depot_id', $depotId)
            ->where('is_active', true)
            ->first();

        if (!$window) {
            return true; // No restrictions = allowed
        }

        // Check day of week if specified
        if ($window->days_of_week !== null && !in_array($slotTime->dayOfWeek, $window->days_of_week)) {
            return false;
        }

        // Check time window
        $slotTimeOnly = $slotTime->format('H:i:s');

        return $slotTimeOnly >= $window->allowed_start_time
            && $slotTimeOnly <= $window->allowed_end_time;
    }

    /**
     * Get the time window for a customer at a depot
     */
    public static function getTimeWindow(int $customerId, int $depotId): ?self
    {
        return static::where('customer_id', $customerId)
            ->where('depot_id', $depotId)
            ->where('is_active', true)
            ->first();
    }
}
