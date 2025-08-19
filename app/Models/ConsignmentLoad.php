<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsignmentLoad extends Model
{
    protected $fillable = [
        'consignment_id',
        'customer_id',
        'depot_id',
        'expected_cases',
        'expected_pallets',
        'expected_pallet_type_id',
        'actual_cases',
        'actual_pallets',
        'actual_pallet_type_id',
        'weight_kg',
        'customer_reference',
        'load_notes',
        'load_status',
        'loaded_at',
        'delivered_at',
    ];

    protected $casts = [
        'weight_kg' => 'decimal:2',
        'loaded_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    // Relationships
    public function consignment(): BelongsTo
    {
        return $this->belongsTo(Consignment::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function depot(): BelongsTo
    {
        return $this->belongsTo(Depot::class);
    }

    public function expected_pallet_type(): BelongsTo
    {
        return $this->belongsTo(PalletType::class, 'expected_pallet_type_id');
    }

    public function actual_pallet_type(): BelongsTo
    {
        return $this->belongsTo(PalletType::class, 'actual_pallet_type_id');
    }

    // Helper methods
    public function getCaseVariance(): int
    {
        if (! $this->expected_cases || ! $this->actual_cases) {
            return 0;
        }

        return $this->actual_cases - $this->expected_cases;
    }

    public function getPalletVariance(): int
    {
        if (! $this->expected_pallets || ! $this->actual_pallets) {
            return 0;
        }

        return $this->actual_pallets - $this->expected_pallets;
    }

    public function hasVariance(): bool
    {
        return $this->getCaseVariance() !== 0 || $this->getPalletVariance() !== 0;
    }

    public function getPalletTypeVariance(): ?string
    {
        if (! $this->expected_pallet_type_id || ! $this->actual_pallet_type_id) {
            return null;
        }
        if ($this->expected_pallet_type_id === $this->actual_pallet_type_id) {
            return null;
        }

        return "Expected: {$this->expected_pallet_type->name}, Actual: {$this->actual_pallet_type->name}";
    }
}
