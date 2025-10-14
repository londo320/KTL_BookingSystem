<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerBookingConfig extends Model
{
    protected $fillable = [
        'customer_id',
        'depot_id',
        'sku_fields_enabled',
        'require_po_data',
    ];

    protected $casts = [
        'sku_fields_enabled' => 'boolean',
        'require_po_data' => 'boolean',
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
     * Get configuration for a customer at a specific depot
     * Checks in order:
     * 1. Customer + depot specific config
     * 2. Customer global config (depot_id = null)
     * 3. Default config (all enabled)
     */
    public static function getConfig(?int $customerId, ?int $depotId): array
    {
        if (!$customerId) {
            return [
                'sku_fields_enabled' => true,
                'require_po_data' => true,
            ];
        }

        // Check for customer + depot specific config
        if ($depotId) {
            $config = static::where('customer_id', $customerId)
                ->where('depot_id', $depotId)
                ->first();

            if ($config) {
                return [
                    'sku_fields_enabled' => $config->sku_fields_enabled,
                    'require_po_data' => $config->require_po_data,
                ];
            }
        }

        // Check for customer global config
        $config = static::where('customer_id', $customerId)
            ->whereNull('depot_id')
            ->first();

        if ($config) {
            return [
                'sku_fields_enabled' => $config->sku_fields_enabled,
                'require_po_data' => $config->require_po_data,
            ];
        }

        // Default: all enabled
        return [
            'sku_fields_enabled' => true,
            'require_po_data' => true,
        ];
    }

    /**
     * Check if SKU fields should be shown for this customer/depot combo
     */
    public static function skuFieldsEnabled(?int $customerId, ?int $depotId): bool
    {
        return static::getConfig($customerId, $depotId)['sku_fields_enabled'];
    }

    /**
     * Check if PO data is required for this customer/depot combo
     */
    public static function poDataRequired(?int $customerId, ?int $depotId): bool
    {
        return static::getConfig($customerId, $depotId)['require_po_data'];
    }
}
