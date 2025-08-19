<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class ArrivalTimeSetting extends Model
{
    protected $fillable = [
        'level',
        'depot_id',
        'customer_id',
        'early_threshold_minutes',
        'late_threshold_minutes',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'early_threshold_minutes' => 'integer',
        'late_threshold_minutes' => 'integer',
    ];

    const LEVEL_GLOBAL = 'global';
    const LEVEL_DEPOT = 'depot';
    const LEVEL_CUSTOMER = 'customer';

    const STATUS_EARLY = 'early';
    const STATUS_ON_TIME = 'on_time';
    const STATUS_LATE = 'late';

    /**
     * Relationships
     */
    public function depot(): BelongsTo
    {
        return $this->belongsTo(Depot::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the effective arrival time settings for a specific booking
     * Uses hierarchical inheritance: customer > depot > global
     */
    public static function getEffectiveSettings(?int $customerId = null, ?int $depotId = null): array
    {
        $cacheKey = "arrival_settings_{$customerId}_{$depotId}";
        
        return Cache::remember($cacheKey, 3600, function () use ($customerId, $depotId) {
            // Try customer-specific settings first
            if ($customerId) {
                $customerSetting = static::where('level', self::LEVEL_CUSTOMER)
                    ->where('customer_id', $customerId)
                    ->where('is_active', true)
                    ->first();
                
                if ($customerSetting) {
                    return [
                        'early_threshold_minutes' => $customerSetting->early_threshold_minutes,
                        'late_threshold_minutes' => $customerSetting->late_threshold_minutes,
                        'source' => 'customer',
                        'source_id' => $customerId,
                        'description' => $customerSetting->description,
                    ];
                }
            }

            // Try depot-specific settings
            if ($depotId) {
                $depotSetting = static::where('level', self::LEVEL_DEPOT)
                    ->where('depot_id', $depotId)
                    ->where('is_active', true)
                    ->first();
                
                if ($depotSetting) {
                    return [
                        'early_threshold_minutes' => $depotSetting->early_threshold_minutes,
                        'late_threshold_minutes' => $depotSetting->late_threshold_minutes,
                        'source' => 'depot',
                        'source_id' => $depotId,
                        'description' => $depotSetting->description,
                    ];
                }
            }

            // Fall back to global settings
            $globalSetting = static::where('level', self::LEVEL_GLOBAL)
                ->where('is_active', true)
                ->first();

            return [
                'early_threshold_minutes' => $globalSetting?->early_threshold_minutes ?? 0,
                'late_threshold_minutes' => $globalSetting?->late_threshold_minutes ?? 0,
                'source' => 'global',
                'source_id' => null,
                'description' => $globalSetting?->description ?? 'Default global settings (no tolerance)',
            ];
        });
    }

    /**
     * Determine arrival status based on scheduled vs actual arrival times
     */
    public static function determineArrivalStatus(
        Carbon $scheduledTime,
        Carbon $actualTime,
        ?int $customerId = null,
        ?int $depotId = null
    ): string {
        $settings = self::getEffectiveSettings($customerId, $depotId);
        
        $differenceMinutes = $scheduledTime->diffInMinutes($actualTime, false);
        
        // Negative means early (actual before scheduled), positive means late (actual after scheduled)
        if ($differenceMinutes < -$settings['early_threshold_minutes']) {
            return self::STATUS_EARLY;
        } elseif ($differenceMinutes > $settings['late_threshold_minutes']) {
            return self::STATUS_LATE;
        } else {
            return self::STATUS_ON_TIME;
        }
    }

    /**
     * Get human-readable arrival status with details
     */
    public static function getArrivalStatusDetails(
        Carbon $scheduledTime,
        Carbon $actualTime,
        ?int $customerId = null,
        ?int $depotId = null
    ): array {
        $status = self::determineArrivalStatus($scheduledTime, $actualTime, $customerId, $depotId);
        $differenceMinutes = abs($actualTime->diffInMinutes($scheduledTime, false));
        $settings = self::getEffectiveSettings($customerId, $depotId);
        
        $details = [
            'status' => $status,
            'difference_minutes' => $differenceMinutes,
            'scheduled_time' => $scheduledTime,
            'actual_time' => $actualTime,
            'settings_source' => $settings['source'],
            'early_threshold' => $settings['early_threshold_minutes'],
            'late_threshold' => $settings['late_threshold_minutes'],
        ];

        switch ($status) {
            case self::STATUS_EARLY:
                $details['message'] = "Arrived {$differenceMinutes} minutes early";
                $details['css_class'] = 'text-blue-600 bg-blue-50';
                $details['emoji'] = '⏪';
                break;
            case self::STATUS_LATE:
                $details['message'] = "Arrived {$differenceMinutes} minutes late";
                $details['css_class'] = 'text-red-600 bg-red-50';
                $details['emoji'] = '⏰';
                break;
            case self::STATUS_ON_TIME:
                $details['message'] = $differenceMinutes > 0 
                    ? "Arrived {$differenceMinutes} minutes after scheduled time (within tolerance)"
                    : "Arrived on time";
                $details['css_class'] = 'text-green-600 bg-green-50';
                $details['emoji'] = '✅';
                break;
        }

        return $details;
    }

    /**
     * Scope for active settings
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for specific level
     */
    public function scopeForLevel($query, string $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Clear cache when settings are updated
     */
    protected static function booted()
    {
        static::saved(function () {
            Cache::flush(); // Simple approach - clear all cache
            // In production, you might want to be more selective
        });

        static::deleted(function () {
            Cache::flush();
        });
    }

    /**
     * Create or update settings for a specific level
     */
    public static function updateSettings(
        string $level,
        int $earlyThreshold,
        int $lateThreshold,
        ?string $description = null,
        ?int $depotId = null,
        ?int $customerId = null
    ): self {
        $attributes = [
            'level' => $level,
            'depot_id' => $depotId,
            'customer_id' => $customerId,
        ];

        return static::updateOrCreate($attributes, [
            'early_threshold_minutes' => $earlyThreshold,
            'late_threshold_minutes' => $lateThreshold,
            'description' => $description,
            'is_active' => true,
        ]);
    }

    /**
     * Get all available levels with their descriptions
     */
    public static function getAvailableLevels(): array
    {
        return [
            self::LEVEL_GLOBAL => 'Global (applies to all)',
            self::LEVEL_DEPOT => 'Depot-specific',
            self::LEVEL_CUSTOMER => 'Customer-specific',
        ];
    }
}