<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PoLine extends Model
{
    protected $fillable = [
        'booking_po_number_id',
        'line_number',
        'expected_cases', // Keep database column name for now, but will rename to units later
        'expected_pallets',
        'expected_pallet_type_id',
        'actual_cases', // Keep database column name for now, but will rename to units later
        'actual_pallets',
        'actual_pallet_type_id',
        'sku',
        'description',
        'bbe',
        'qty',
        'batch',
        'scc_number',
    ];

    protected $casts = [
        'expected_cases' => 'integer',
        'expected_pallets' => 'integer',
        'actual_cases' => 'integer',
        'actual_pallets' => 'integer',
        'qty' => 'integer',
        'bbe' => 'date',
    ];

    public function bookingPoNumber(): BelongsTo
    {
        return $this->belongsTo(BookingPoNumber::class);
    }

    public function expectedPalletType(): BelongsTo
    {
        return $this->belongsTo(PalletType::class, 'expected_pallet_type_id');
    }

    public function actualPalletType(): BelongsTo
    {
        return $this->belongsTo(PalletType::class, 'actual_pallet_type_id');
    }

    public function actualPallets(): HasMany
    {
        return $this->hasMany(PoLineActualPallet::class);
    }

    // Accessor methods for units terminology
    public function getExpectedUnitsAttribute(): ?int
    {
        return $this->expected_cases;
    }

    public function getActualUnitsAttribute(): ?int
    {
        return $this->actual_cases;
    }
    
    public function getTotalActualPalletsAttribute(): int
    {
        return $this->actualPallets->sum('quantity');
    }
    
    public function getActualPalletTypesBreakdownAttribute(): array
    {
        return $this->actualPallets->map(function ($actualPallet) {
            return [
                'type' => $actualPallet->palletType->name,
                'quantity' => $actualPallet->quantity,
                'notes' => $actualPallet->notes,
            ];
        })->toArray();
    }
    
    public function hasMultiplePalletTypes(): bool
    {
        return $this->actualPallets->count() > 1;
    }

    public function getUnitVarianceAttribute(): ?int
    {
        if (is_null($this->expected_cases) || is_null($this->actual_cases)) {
            return null;
        }

        return $this->actual_cases - $this->expected_cases;
    }

    // Keep old method for backwards compatibility
    public function getCaseVarianceAttribute(): ?int
    {
        return $this->unit_variance;
    }

    public function getPalletVarianceAttribute(): ?int
    {
        if (is_null($this->expected_pallets)) {
            return null;
        }
        
        // Use total from actual pallets breakdown if available, otherwise fallback to old field
        $actualTotal = $this->actualPallets->count() > 0 
            ? $this->total_actual_pallets 
            : $this->actual_pallets;
            
        if (is_null($actualTotal)) {
            return null;
        }

        return $actualTotal - $this->expected_pallets;
    }

    public function getPalletTypeVarianceAttribute(): ?string
    {
        // If we have detailed actual pallets, use those for variance calculation
        if ($this->actualPallets->count() > 0 && $this->expected_pallet_type_id) {
            $expected = $this->expectedPalletType?->name ?? 'Unknown';
            $actualTypes = $this->actualPallets->pluck('palletType.name')->unique();
            
            // Check if actual types match expected
            if ($actualTypes->count() === 1 && $actualTypes->first() === $expected) {
                return null; // No variance
            }
            
            $actualString = $actualTypes->join(', ');
            return "Expected: {$expected}, Actual: {$actualString}";
        }
        
        // Fallback to old single pallet type logic
        if (! $this->expected_pallet_type_id || ! $this->actual_pallet_type_id) {
            return null;
        }

        if ($this->expected_pallet_type_id !== $this->actual_pallet_type_id) {
            $expected = $this->expectedPalletType?->name ?? 'Unknown';
            $actual = $this->actualPalletType?->name ?? 'Unknown';

            return "Expected: {$expected}, Actual: {$actual}";
        }

        return null;
    }

    public function hasVariance(): bool
    {
        return ($this->unit_variance ?? 0) !== 0 ||
               ($this->pallet_variance ?? 0) !== 0 ||
               ! empty($this->pallet_type_variance);
    }

    public function isComplete(): bool
    {
        $hasCases = ! is_null($this->actual_cases);
        $hasPallets = ! is_null($this->actual_pallets) || $this->actualPallets->count() > 0;
        
        return $hasCases && $hasPallets;
    }

    public function scopeWithVariance($query)
    {
        return $query->whereRaw('(actual_cases - expected_cases) != 0 OR (actual_pallets - expected_pallets) != 0 OR expected_pallet_type_id != actual_pallet_type_id');
    }

    public function scopeIncomplete($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('actual_cases')->orWhereNull('actual_pallets');
        });
    }

    public function getDisplayNameAttribute(): string
    {
        return "Line {$this->line_number}: ".
               ($this->expected_units ? "{$this->expected_units} units" : '').
               ($this->expected_units && $this->expected_pallets ? ', ' : '').
               ($this->expected_pallets ? "{$this->expected_pallets} ".($this->expectedPalletType?->name ?? 'pallets') : '');
    }
}
