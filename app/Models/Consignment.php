<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consignment extends Model
{
    protected $fillable = [
        'consignment_number',
        'origin_depot_id',
        'depot_route',
        'collection_time',
        'delivery_time',
        'delivery_address',
        'total_pallets',
        'total_cases',
        'total_weight_kg',
        'status',
        'additional_data',
        'notes',
    ];

    protected $casts = [
        'depot_route' => 'array',
        'additional_data' => 'array',
        'collection_time' => 'datetime',
        'delivery_time' => 'datetime',
        'total_weight_kg' => 'decimal:2',
    ];

    // Relationships
    public function origin_depot(): BelongsTo
    {
        return $this->belongsTo(Depot::class, 'origin_depot_id');
    }

    public function references(): HasMany
    {
        return $this->hasMany(ConsignmentReference::class);
    }

    public function loads(): HasMany
    {
        return $this->hasMany(ConsignmentLoad::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(Movement::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDepot($query, $depotId)
    {
        return $query->where('origin_depot_id', $depotId)
            ->orWhereJsonContains('depot_route', $depotId);
    }

    // Helper methods
    public function getCustomers()
    {
        return Customer::whereIn('id', $this->loads()->pluck('customer_id'))->get();
    }

    public function getTotalsByDepot($depotId)
    {
        return $this->loads()->where('depot_id', $depotId)->selectRaw('
            SUM(expected_cases) as expected_cases,
            SUM(expected_pallets) as expected_pallets,
            SUM(actual_cases) as actual_cases,
            SUM(actual_pallets) as actual_pallets
        ')->first();
    }

    public function updateTotals(): void
    {
        $totals = $this->loads()->selectRaw('
            SUM(COALESCE(actual_pallets, expected_pallets, 0)) as total_pallets,
            SUM(COALESCE(actual_cases, expected_cases, 0)) as total_cases,
            SUM(weight_kg) as total_weight_kg
        ')->first();

        $this->update([
            'total_pallets' => $totals->total_pallets ?? 0,
            'total_cases' => $totals->total_cases ?? 0,
            'total_weight_kg' => $totals->total_weight_kg,
        ]);
    }

    public function isMultiDepot(): bool
    {
        return ! empty($this->depot_route) && count($this->depot_route) > 1;
    }

    public function getRouteDepots()
    {
        if (empty($this->depot_route)) {
            return collect();
        }

        return Depot::whereIn('id', $this->depot_route)
            ->orderByRaw('FIELD(id, '.implode(',', $this->depot_route).')')
            ->get();
    }
}
