<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookingPoNumber extends Model
{
    protected $fillable = [
        'booking_id',
        'factory_booking_id',
        'po_number',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function factoryBooking(): BelongsTo
    {
        return $this->belongsTo(FactoryBooking::class);
    }

    // Get the parent bookable model (either Booking or FactoryBooking)
    public function getBookableAttribute()
    {
        return $this->booking_id ? $this->booking : $this->factoryBooking;
    }

    public function lines(): HasMany
    {
        return $this->hasMany(PoLine::class)->orderBy('line_number');
    }

    // Calculated attributes based on PO lines
    public function getTotalExpectedUnitsAttribute(): int
    {
        return $this->lines->sum('expected_cases') ?? 0;
    }

    public function getTotalActualUnitsAttribute(): int
    {
        return $this->lines->sum('actual_cases') ?? 0;
    }

    // Keep old methods for backwards compatibility
    public function getTotalExpectedCasesAttribute(): int
    {
        return $this->total_expected_units;
    }

    public function getTotalActualCasesAttribute(): int
    {
        return $this->total_actual_units;
    }

    public function getTotalExpectedPalletsAttribute(): int
    {
        return $this->lines->sum('expected_pallets') ?? 0;
    }

    public function getTotalActualPalletsAttribute(): int
    {
        return $this->lines->sum(function ($line) {
            return $line->total_actual_pallets;
        }) ?? 0;
    }

    public function getTotalUnitVarianceAttribute(): int
    {
        return $this->total_actual_units - $this->total_expected_units;
    }

    public function getTotalCaseVarianceAttribute(): int
    {
        return $this->total_unit_variance;
    }

    public function getTotalPalletVarianceAttribute(): int
    {
        return $this->total_actual_pallets - $this->total_expected_pallets;
    }

    public function getExpectedPalletBreakdownAttribute(): array
    {
        return $this->lines
            ->filter(function ($line) {
                return $line->expected_pallets > 0 && $line->expectedPalletType;
            })
            ->groupBy(function ($line) {
                return $line->expectedPalletType->name;
            })
            ->map(function ($group, $typeName) {
                return [
                    'type' => $typeName,
                    'count' => $group->sum('expected_pallets'),
                ];
            })
            ->values()
            ->toArray();
    }

    public function getActualPalletBreakdownAttribute(): array
    {
        $breakdown = [];
        
        foreach ($this->lines as $line) {
            // Use new multiple pallet types if available
            if ($line->actualPallets->count() > 0) {
                foreach ($line->actualPallets as $actualPallet) {
                    $typeName = $actualPallet->palletType->name;
                    if (!isset($breakdown[$typeName])) {
                        $breakdown[$typeName] = 0;
                    }
                    $breakdown[$typeName] += $actualPallet->quantity;
                }
            }
            // Fallback to old single pallet type system
            elseif ($line->actual_pallets > 0 && $line->actualPalletType) {
                $typeName = $line->actualPalletType->name;
                if (!isset($breakdown[$typeName])) {
                    $breakdown[$typeName] = 0;
                }
                $breakdown[$typeName] += $line->actual_pallets;
            }
        }
        
        return collect($breakdown)->map(function ($count, $typeName) {
            return [
                'type' => $typeName,
                'count' => $count,
            ];
        })->values()->toArray();
    }

    public function getExpectedSummaryTextAttribute(): string
    {
        $parts = [];

        if ($this->total_expected_units > 0) {
            $parts[] = number_format($this->total_expected_units).' units';
        }

        $palletBreakdown = $this->expected_pallet_breakdown;
        if (! empty($palletBreakdown)) {
            $palletParts = array_map(function ($item) {
                return $item['count'].' '.$item['type'];
            }, $palletBreakdown);

            if (count($palletParts) > 0) {
                $totalPallets = array_sum(array_column($palletBreakdown, 'count'));
                $parts[] = implode(', ', $palletParts).' (total: '.$totalPallets.' pallets)';
            }
        }

        return ! empty($parts) ? implode(', ', $parts) : 'No quantities specified';
    }

    public function getActualSummaryTextAttribute(): string
    {
        $parts = [];

        if ($this->total_actual_units > 0) {
            $parts[] = number_format($this->total_actual_units).' units';
        }

        $palletBreakdown = $this->actual_pallet_breakdown;
        if (! empty($palletBreakdown)) {
            $palletParts = array_map(function ($item) {
                return $item['count'].' '.$item['type'];
            }, $palletBreakdown);

            if (count($palletParts) > 0) {
                $totalPallets = array_sum(array_column($palletBreakdown, 'count'));
                $parts[] = implode(', ', $palletParts).' (total: '.$totalPallets.' pallets)';
            }
        }

        return ! empty($parts) ? implode(', ', $parts) : 'No quantities recorded';
    }

    public function hasVariance(): bool
    {
        return $this->lines->some(function ($line) {
            return $line->hasVariance();
        });
    }

    public function isComplete(): bool
    {
        return $this->lines->count() > 0 && $this->lines->every(function ($line) {
            return $line->isComplete();
        });
    }

    public function hasTypeVariances(): bool
    {
        return $this->lines->some(function ($line) {
            return ! empty($line->pallet_type_variance);
        });
    }

    public function getTypeVariancesAttribute(): array
    {
        return $this->lines
            ->filter(function ($line) {
                return ! empty($line->pallet_type_variance);
            })
            ->map(function ($line) {
                return "Line {$line->line_number}: {$line->pallet_type_variance}";
            })
            ->values()
            ->toArray();
    }

    public function scopeWithVariance($query)
    {
        return $query->whereHas('lines', function ($q) {
            $q->withVariance();
        });
    }

    public function scopeIncomplete($query)
    {
        return $query->whereHas('lines', function ($q) {
            $q->incomplete();
        });
    }

    public function getDisplaySummaryAttribute(): string
    {
        $summary = [];

        if ($this->total_expected_cases > 0) {
            $summary[] = "{$this->total_expected_cases} cases";
        }

        if ($this->total_expected_pallets > 0) {
            $summary[] = "{$this->total_expected_pallets} pallets";
        }

        if (empty($summary)) {
            return 'No quantities specified';
        }

        return implode(', ', $summary)." ({$this->lines->count()} line".($this->lines->count() > 1 ? 's' : '').')';
    }
}
