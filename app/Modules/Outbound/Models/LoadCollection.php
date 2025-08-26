<?php

namespace App\Modules\Outbound\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Depot;

class LoadCollection extends Model
{
    protected $fillable = [
        'outbound_load_id',
        'depot_id',
        'planned_collection_time',
        'actual_collection_time',
        'estimated_duration_minutes',
        'actual_duration_minutes',
        'collection_sequence',
        'collection_notes',
        'depot_pallets',
        'depot_cases',
        'depot_units',
        'depot_weight_kg',
        'status',
    ];

    protected $casts = [
        'planned_collection_time' => 'datetime',
        'actual_collection_time' => 'datetime',
        'depot_weight_kg' => 'decimal:2',
    ];

    // Relationships
    public function outboundLoad(): BelongsTo
    {
        return $this->belongsTo(OutboundLoad::class);
    }

    public function depot(): BelongsTo
    {
        return $this->belongsTo(Depot::class);
    }

    // Helper methods
    public function getStatusBadgeAttribute(): string
    {
        $statusClasses = [
            'pending' => 'bg-gray-100 text-gray-800',
            'ready' => 'bg-blue-100 text-blue-800',
            'collecting' => 'bg-yellow-100 text-yellow-800',
            'collected' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
        ];

        $class = $statusClasses[$this->status] ?? 'bg-gray-100 text-gray-800';
        $label = ucfirst($this->status);

        return "<span class=\"px-2 py-1 text-xs font-semibold rounded-full {$class}\">{$label}</span>";
    }

    public function isOverdue(): bool
    {
        return $this->planned_collection_time && 
               $this->planned_collection_time->lt(now()) && 
               !in_array($this->status, ['collected', 'failed']);
    }

    public function getDurationVariance(): ?int
    {
        if (!$this->estimated_duration_minutes || !$this->actual_duration_minutes) {
            return null;
        }

        return $this->actual_duration_minutes - $this->estimated_duration_minutes;
    }

    public function updateDepotTotals(): void
    {
        $orderTotals = $this->outboundLoad->orders()
            ->where('collection_depot_id', $this->depot_id)
            ->selectRaw('
                SUM(COALESCE(actual_pallets, expected_pallets, 0)) as total_pallets,
                SUM(COALESCE(actual_cases, expected_cases, 0)) as total_cases,
                SUM(COALESCE(actual_units, expected_units, 0)) as total_units,
                SUM(COALESCE(actual_weight_kg, expected_weight_kg, 0)) as total_weight_kg
            ')->first();

        $this->update([
            'depot_pallets' => $orderTotals->total_pallets ?? 0,
            'depot_cases' => $orderTotals->total_cases ?? 0,
            'depot_units' => $orderTotals->total_units ?? 0,
            'depot_weight_kg' => $orderTotals->total_weight_kg ?? 0,
        ]);
    }

    // Scopes
    public function scopeForLoad($query, $loadId)
    {
        return $query->where('outbound_load_id', $loadId);
    }

    public function scopeForDepot($query, $depotId)
    {
        return $query->where('depot_id', $depotId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOverdue($query)
    {
        return $query->where('planned_collection_time', '<', now())
                    ->whereNotIn('status', ['collected', 'failed']);
    }

    public function scopeOrderedBySequence($query)
    {
        return $query->orderBy('collection_sequence');
    }
}