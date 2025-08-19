<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovementLoad extends Model
{
    protected $fillable = [
        'movement_id',
        'customer_id',
        'operation_type',
        'sequence',
        'expected_cases',
        'expected_pallets',
        'expected_pallet_type_id',
        'actual_cases',
        'actual_pallets',
        'actual_pallet_type_id',
        'customer_reference',
        'po_number',
        'operation_started_at',
        'operation_completed_at',
        'operation_notes',
        'booking_po_line_id',
    ];

    protected $casts = [
        'operation_started_at' => 'datetime',
        'operation_completed_at' => 'datetime',
    ];

    // Relationships
    public function movement(): BelongsTo
    {
        return $this->belongsTo(Movement::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function expected_pallet_type(): BelongsTo
    {
        return $this->belongsTo(PalletType::class, 'expected_pallet_type_id');
    }

    public function actual_pallet_type(): BelongsTo
    {
        return $this->belongsTo(PalletType::class, 'actual_pallet_type_id');
    }

    public function booking_po_line(): BelongsTo
    {
        return $this->belongsTo(BookingPoLine::class, 'booking_po_line_id');
    }

    // Scopes
    public function scopeInbound($query)
    {
        return $query->where('operation_type', 'inbound');
    }

    public function scopeOutbound($query)
    {
        return $query->where('operation_type', 'outbound');
    }

    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
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

    public function isCompleted(): bool
    {
        return ! is_null($this->operation_completed_at);
    }
}
