<?php

namespace App\Modules\Outbound\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Vehicle;
use App\Models\User;

class OutboundLoad extends Model
{
    protected $fillable = [
        'load_reference',
        'load_name',
        'created_from',
        'planned_vehicle_id',
        'assigned_driver_id',
        'total_orders',
        'total_customers',
        'total_collection_points',
        'total_delivery_points',
        'total_pallets',
        'total_cases',
        'total_units',
        'total_weight_kg',
        'status',
        'optimized_distance_km',
        'estimated_duration_minutes',
        'optimization_score',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'total_weight_kg' => 'decimal:2',
        'optimized_distance_km' => 'decimal:2',
        'optimization_score' => 'decimal:2',
    ];

    // Relationships
    public function collections(): HasMany
    {
        return $this->hasMany(LoadCollection::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(OutboundOrder::class);
    }

    public function plannedVehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'planned_vehicle_id');
    }

    public function assignedDriver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_driver_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function route(): HasMany
    {
        return $this->hasMany(LoadRoute::class);
    }

    // Helper methods
    public function generateLoadReference(): string
    {
        $date = now()->format('ymd');
        $sequence = static::whereDate('created_at', today())->count() + 1;
        
        return "LD{$date}" . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }

    public function updateTotals(): void
    {
        $orderTotals = $this->orders()->selectRaw('
            COUNT(*) as total_orders,
            COUNT(DISTINCT customer_id) as total_customers,
            COUNT(DISTINCT collection_depot_id) as total_collection_points,
            COUNT(DISTINCT customer_address_id) as total_delivery_points,
            SUM(COALESCE(actual_pallets, expected_pallets, 0)) as total_pallets,
            SUM(COALESCE(actual_cases, expected_cases, 0)) as total_cases,
            SUM(COALESCE(actual_units, expected_units, 0)) as total_units,
            SUM(COALESCE(actual_weight_kg, expected_weight_kg, 0)) as total_weight_kg
        ')->first();

        $this->update([
            'total_orders' => $orderTotals->total_orders ?? 0,
            'total_customers' => $orderTotals->total_customers ?? 0,
            'total_collection_points' => $orderTotals->total_collection_points ?? 0,
            'total_delivery_points' => $orderTotals->total_delivery_points ?? 0,
            'total_pallets' => $orderTotals->total_pallets ?? 0,
            'total_cases' => $orderTotals->total_cases ?? 0,
            'total_units' => $orderTotals->total_units ?? 0,
            'total_weight_kg' => $orderTotals->total_weight_kg ?? 0,
        ]);
    }

    public function getStatusBadgeAttribute(): string
    {
        $statusClasses = [
            'planning' => 'bg-gray-100 text-gray-800',
            'ready_for_collection' => 'bg-blue-100 text-blue-800',
            'collecting' => 'bg-yellow-100 text-yellow-800',
            'in_transit' => 'bg-orange-100 text-orange-800',
            'delivering' => 'bg-purple-100 text-purple-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
        ];

        $class = $statusClasses[$this->status] ?? 'bg-gray-100 text-gray-800';
        $label = ucfirst(str_replace('_', ' ', $this->status));

        return "<span class=\"px-2 py-1 text-xs font-semibold rounded-full {$class}\">{$label}</span>";
    }

    public function canBeOptimized(): bool
    {
        return in_array($this->status, ['planning', 'ready_for_collection']) && $this->orders()->count() > 0;
    }

    public function isActive(): bool
    {
        return !in_array($this->status, ['completed', 'cancelled']);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['completed', 'cancelled']);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Boot method for auto-generating reference
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($load) {
            if (empty($load->load_reference)) {
                $load->load_reference = $load->generateLoadReference();
            }
        });
    }
}