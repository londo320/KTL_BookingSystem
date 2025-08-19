<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingHistory extends Model
{
    protected $table = 'booking_history';

    protected $fillable = [
        'booking_id',
        'customer_id',
        'user_id',
        'original_slot_id',
        'original_start_time',
        'original_end_time',
        'new_slot_id',
        'new_start_time',
        'new_end_time',
        'action',
        'reason',
        'changes',
        'hours_before_slot',
        'is_last_minute',
        'customer_rebook_count_30days',
        'customer_cancel_count_30days',
    ];

    protected $casts = [
        'original_start_time' => 'datetime',
        'original_end_time' => 'datetime',
        'new_start_time' => 'datetime',
        'new_end_time' => 'datetime',
        'changes' => 'array',
        'is_last_minute' => 'boolean',
        'customer_rebook_count_30days' => 'integer',
        'customer_cancel_count_30days' => 'integer',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function originalSlot(): BelongsTo
    {
        return $this->belongsTo(Slot::class, 'original_slot_id');
    }

    public function newSlot(): BelongsTo
    {
        return $this->belongsTo(Slot::class, 'new_slot_id');
    }

    public static function recordAction(
        Booking $booking,
        string $action,
        ?string $reason = null,
        ?Slot $originalSlot = null,
        ?Slot $newSlot = null,
        ?array $changes = null
    ) {
        $slotToCheck = $newSlot ?? $originalSlot ?? $booking->slot;
        $hoursBeforeSlot = $slotToCheck ? now()->diffInHours($slotToCheck->start_at, false) : null;

        // Get recent customer activity counts
        $recentRebooks = static::where('customer_id', $booking->customer_id)
            ->where('action', 'rebooked')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        $recentCancels = static::where('customer_id', $booking->customer_id)
            ->where('action', 'cancelled')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        return static::create([
            'booking_id' => $booking->id,
            'customer_id' => $booking->customer_id,
            'user_id' => auth()->id() ?? $booking->user_id,
            'original_slot_id' => $originalSlot?->id,
            'original_start_time' => $originalSlot?->start_at,
            'original_end_time' => $originalSlot?->end_at,
            'new_slot_id' => $newSlot?->id,
            'new_start_time' => $newSlot?->start_at,
            'new_end_time' => $newSlot?->end_at,
            'action' => $action,
            'reason' => $reason,
            'changes' => $changes,
            'hours_before_slot' => $hoursBeforeSlot,
            'is_last_minute' => $hoursBeforeSlot !== null && abs($hoursBeforeSlot) < 24,
            'customer_rebook_count_30days' => $recentRebooks,
            'customer_cancel_count_30days' => $recentCancels,
        ]);
    }

    public function scopeLastMinute($query)
    {
        return $query->where('is_last_minute', true);
    }

    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeRecentActivity($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Record arrival status based on configurable arrival time rules
     */
    public static function recordArrival(
        Booking $booking,
        \Carbon\Carbon $actualArrivalTime,
        ?string $reason = null
    ) {
        $scheduledTime = $booking->slot->start_at;
        $depotId = $booking->slot->depot_id ?? null;
        $customerId = $booking->customer_id;

        // Get arrival status using configurable rules
        $arrivalStatus = \App\Models\ArrivalTimeSetting::determineArrivalStatus(
            $scheduledTime,
            $actualArrivalTime,
            $customerId,
            $depotId
        );

        // Determine the action based on arrival status
        $action = match ($arrivalStatus) {
            \App\Models\ArrivalTimeSetting::STATUS_EARLY => 'early_arrival',
            \App\Models\ArrivalTimeSetting::STATUS_LATE => 'late_arrival',
            \App\Models\ArrivalTimeSetting::STATUS_ON_TIME => 'on_time_arrival',
            default => 'arrival'
        };

        // Record the arrival history
        return static::recordAction(
            $booking,
            $action,
            $reason,
            $booking->slot,
            null,
            [
                'actual_arrival_time' => $actualArrivalTime->toISOString(),
                'scheduled_time' => $scheduledTime->toISOString(),
                'arrival_status' => $arrivalStatus,
                'difference_minutes' => $actualArrivalTime->diffInMinutes($scheduledTime, false),
            ]
        );
    }
}
