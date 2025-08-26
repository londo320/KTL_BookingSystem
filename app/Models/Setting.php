<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'description',
    ];

    /**
     * Get a setting value by key
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();

        if (! $setting) {
            return $default;
        }

        return match ($setting->type) {
            'boolean' => (bool) $setting->value,
            'integer' => (int) $setting->value,
            'json' => json_decode($setting->value, true),
            default => $setting->value
        };
    }

    /**
     * Set a setting value by key
     */
    public static function set(string $key, mixed $value, string $type = 'string'): void
    {
        $processedValue = match ($type) {
            'boolean' => $value ? '1' : '0',
            'json' => json_encode($value),
            default => (string) $value
        };

        static::updateOrCreate(
            ['key' => $key],
            [
                'value' => $processedValue,
                'type' => $type,
            ]
        );
    }

    /**
     * Check if tipping workflow is enabled
     */
    public static function isTippingWorkflowEnabled(): bool
    {
        return static::get('tipping_workflow_enabled', true);
    }

    /**
     * Get factory vehicle tipping time target in minutes for a specific depot and customer
     */
    public static function getFactoryTippingTimeTarget(int $depotId, int $customerId = null): int
    {
        // Try to get customer-specific setting first if customer provided
        if ($customerId) {
            $customerSetting = static::get("factory_tipping_target_depot_{$depotId}_customer_{$customerId}");
            if ($customerSetting !== null) {
                return (int) $customerSetting;
            }
        }

        // Try depot-specific setting
        $depotSetting = static::get("factory_tipping_target_depot_{$depotId}");
        if ($depotSetting !== null) {
            return (int) $depotSetting;
        }

        // Return default of 60 minutes
        return static::get('factory_tipping_target_default', 60);
    }

    /**
     * Set factory vehicle tipping time target in minutes
     */
    public static function setFactoryTippingTimeTarget(int $depotId, int $minutes, int $customerId = null): void
    {
        if ($customerId) {
            static::set("factory_tipping_target_depot_{$depotId}_customer_{$customerId}", $minutes, 'integer');
        } else {
            static::set("factory_tipping_target_depot_{$depotId}", $minutes, 'integer');
        }
    }

    /**
     * Set default factory vehicle tipping time target in minutes
     */
    public static function setDefaultFactoryTippingTimeTarget(int $minutes): void
    {
        static::set('factory_tipping_target_default', $minutes, 'integer');
    }
}
