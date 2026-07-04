<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BaySchedule extends Model
{
    protected $fillable = [
        'tipping_bay_id',
        'day_of_week',
        'operational_start',
        'operational_end',
        'is_closed',
    ];

    protected $casts = [
        'is_closed' => 'boolean',
        'day_of_week' => 'integer',
    ];

    /**
     * Get the bay that owns this schedule
     */
    public function bay(): BelongsTo
    {
        return $this->belongsTo(TippingBay::class, 'tipping_bay_id');
    }

    /**
     * Get the day name for this schedule
     */
    public function getDayNameAttribute(): string
    {
        return match($this->day_of_week) {
            0 => 'Sunday',
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            default => 'Unknown',
        };
    }

    /**
     * Check if this day is operational (not closed and has times)
     */
    public function isOperational(): bool
    {
        return !$this->is_closed
            && $this->operational_start !== null
            && $this->operational_end !== null;
    }

    /**
     * Get formatted time range
     */
    public function getTimeRangeAttribute(): string
    {
        if ($this->is_closed) {
            return 'Closed';
        }

        if (!$this->operational_start || !$this->operational_end) {
            return '24/7';
        }

        return "{$this->operational_start} - {$this->operational_end}";
    }
}
