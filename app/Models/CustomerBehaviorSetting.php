<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerBehaviorSetting extends Model
{
    protected $fillable = [
        'customer_id',
        'setting_key',
        'setting_value',
        'setting_type',
        'description',
        'updated_by',
    ];

    protected $casts = [
        'customer_id' => 'integer',
        'updated_by' => 'integer',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Get casted value based on setting_type
    public function getCastedValue()
    {
        return match ($this->setting_type) {
            'integer' => (int) $this->setting_value,
            'boolean' => filter_var($this->setting_value, FILTER_VALIDATE_BOOLEAN),
            'float' => (float) $this->setting_value,
            default => $this->setting_value
        };
    }

    // Get setting for customer with fallback to default
    public static function getCustomerSetting(int $customerId, string $key, $defaultValue = null)
    {
        $setting = static::where('customer_id', $customerId)
            ->where('setting_key', $key)
            ->first();

        if (! $setting) {
            return $defaultValue ?? static::getDefaultValue($key);
        }

        return $setting->getCastedValue();
    }

    // Set or update customer setting
    public static function setCustomerSetting(int $customerId, string $key, $value, string $type = 'integer', ?string $description = null): self
    {
        return static::updateOrCreate(
            ['customer_id' => $customerId, 'setting_key' => $key],
            [
                'setting_value' => (string) $value,
                'setting_type' => $type,
                'description' => $description,
                'updated_by' => auth()->id(),
            ]
        );
    }

    // Default values for behavior settings
    public static function getDefaultValue(string $key)
    {
        $defaults = [
            'max_rebooks_per_booking' => 3,
            'max_last_minute_rebooks_30days' => 5,
            'max_total_rebooks_30days' => 10,
            'max_cancellations_30days' => 15,
            'minimum_hours_notice' => 24,
            'allow_weekend_bookings' => true,
            'allow_holiday_bookings' => false,
            'priority_booking' => false,
            'auto_approve_bookings' => false,
        ];

        return $defaults[$key] ?? null;
    }

    // Get all available setting keys with descriptions
    public static function getAvailableSettings(): array
    {
        return [
            'max_rebooks_per_booking' => [
                'label' => 'Max Rebooks Per Booking',
                'description' => 'Maximum number of times a single booking can be rebooked',
                'type' => 'integer',
                'default' => 3,
                'min' => 0,
                'max' => 99999,
            ],
            'max_last_minute_rebooks_30days' => [
                'label' => 'Max Last-Minute Rebooks (30 days)',
                'description' => 'Maximum last-minute rebooks allowed in 30 days (< 24h notice)',
                'type' => 'integer',
                'default' => 5,
                'min' => 0,
                'max' => 99999,
            ],
            'max_total_rebooks_30days' => [
                'label' => 'Max Total Rebooks (30 days)',
                'description' => 'Maximum total rebooks allowed in 30 days',
                'type' => 'integer',
                'default' => 10,
                'min' => 0,
                'max' => 99999,
            ],
            'max_cancellations_30days' => [
                'label' => 'Max Cancellations (30 days)',
                'description' => 'Maximum cancellations allowed in 30 days',
                'type' => 'integer',
                'default' => 15,
                'min' => 0,
                'max' => 99999,
            ],
            'minimum_hours_notice' => [
                'label' => 'Minimum Hours Notice',
                'description' => 'Minimum hours notice required for changes/cancellations',
                'type' => 'integer',
                'default' => 24,
                'min' => 0,
                'max' => 99999,
            ],
            'allow_weekend_bookings' => [
                'label' => 'Allow Weekend Bookings',
                'description' => 'Allow customer to make bookings on weekends',
                'type' => 'boolean',
                'default' => true,
            ],
            'allow_holiday_bookings' => [
                'label' => 'Allow Holiday Bookings',
                'description' => 'Allow customer to make bookings on holidays',
                'type' => 'boolean',
                'default' => false,
            ],
            'priority_booking' => [
                'label' => 'Priority Booking',
                'description' => 'Give customer priority when booking slots',
                'type' => 'boolean',
                'default' => false,
            ],
            'auto_approve_bookings' => [
                'label' => 'Auto-Approve Bookings',
                'description' => 'Automatically approve bookings without manual review',
                'type' => 'boolean',
                'default' => false,
            ],
        ];
    }

    // Get customer's current settings with defaults
    public static function getCustomerSettings(int $customerId): array
    {
        $settings = [];
        $availableSettings = static::getAvailableSettings();

        $customerSettings = static::where('customer_id', $customerId)
            ->pluck('setting_value', 'setting_key')
            ->toArray();

        foreach ($availableSettings as $key => $config) {
            $value = $customerSettings[$key] ?? $config['default'];

            // Cast the value
            $settings[$key] = match ($config['type']) {
                'integer' => (int) $value,
                'boolean' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
                'float' => (float) $value,
                default => $value
            };
        }

        return $settings;
    }
}
