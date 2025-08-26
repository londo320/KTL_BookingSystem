<?php

namespace App\Modules\Outbound\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Customer;
use App\Models\Depot;

class OutboundOrder extends Model
{
    protected $fillable = [
        'outbound_load_id',
        'customer_id',
        'customer_address_id',
        'order_reference',
        'internal_order_number',
        'po_number',
        'collection_depot_id',
        'collection_reference',
        'planned_delivery_date',
        'planned_delivery_time_start',
        'planned_delivery_time_end',
        'estimated_delivery_time',
        'actual_delivery_time',
        'expected_pallets',
        'expected_cases',
        'expected_units',
        'expected_weight_kg',
        'actual_pallets',
        'actual_cases',
        'actual_units',
        'actual_weight_kg',
        'temperature_controlled',
        'fragile',
        'hazardous',
        'status',
        'collection_notes',
        'delivery_notes',
        'handling_instructions',
        'latest_vehicle_arrival_time',
        'delivery_window_end',
        'travel_time_to_site_minutes',
        'site_processing_time_minutes',
        'delivery_priority',
        'must_deliver_by',
        'preferred_delivery_window_start',
        'preferred_delivery_window_end',
    ];

    protected $casts = [
        'planned_delivery_date' => 'date',
        'estimated_delivery_time' => 'datetime',
        'actual_delivery_time' => 'datetime',
        'latest_vehicle_arrival_time' => 'datetime',
        'delivery_window_end' => 'datetime',
        'must_deliver_by' => 'datetime',
        'expected_weight_kg' => 'decimal:2',
        'actual_weight_kg' => 'decimal:2',
        'temperature_controlled' => 'boolean',
        'fragile' => 'boolean',
        'hazardous' => 'boolean',
    ];

    // Relationships
    public function outboundLoad(): BelongsTo
    {
        return $this->belongsTo(OutboundLoad::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function customerAddress(): BelongsTo
    {
        return $this->belongsTo(CustomerAddress::class);
    }

    public function collectionDepot(): BelongsTo
    {
        return $this->belongsTo(Depot::class, 'collection_depot_id');
    }

    public function palletDetails(): HasMany
    {
        return $this->hasMany(OrderPalletDetail::class);
    }

    public function tracking(): HasMany
    {
        return $this->hasMany(DeliveryTracking::class)->orderBy('event_timestamp', 'desc');
    }

    // Helper methods
    public function getStatusBadgeAttribute(): string
    {
        $statusClasses = [
            'pending' => 'bg-gray-100 text-gray-800',
            'ready_for_collection' => 'bg-blue-100 text-blue-800',
            'collected' => 'bg-indigo-100 text-indigo-800',
            'in_transit' => 'bg-yellow-100 text-yellow-800',
            'out_for_delivery' => 'bg-orange-100 text-orange-800',
            'delivered' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
            'returned' => 'bg-purple-100 text-purple-800',
        ];

        $class = $statusClasses[$this->status] ?? 'bg-gray-100 text-gray-800';
        $label = ucfirst(str_replace('_', ' ', $this->status));

        return "<span class=\"px-2 py-1 text-xs font-semibold rounded-full {$class}\">{$label}</span>";
    }

    public function getPriorityBadgeAttribute(): string
    {
        $priorityClasses = [
            'standard' => 'bg-gray-100 text-gray-800',
            'priority' => 'bg-yellow-100 text-yellow-800',
            'urgent' => 'bg-red-100 text-red-800',
        ];

        $class = $priorityClasses[$this->delivery_priority] ?? 'bg-gray-100 text-gray-800';
        $label = ucfirst($this->delivery_priority);

        return "<span class=\"px-2 py-1 text-xs font-semibold rounded-full {$class}\">{$label}</span>";
    }

    public function getTotalPalletsAttribute(): int
    {
        return $this->actual_pallets ?? $this->expected_pallets ?? 0;
    }

    public function getTotalCasesAttribute(): int
    {
        return $this->actual_cases ?? $this->expected_cases ?? 0;
    }

    public function getTotalUnitsAttribute(): int
    {
        return $this->actual_units ?? $this->expected_units ?? 0;
    }

    public function getTotalWeightAttribute(): float
    {
        return $this->actual_weight_kg ?? $this->expected_weight_kg ?? 0.0;
    }

    public function hasVariance(): bool
    {
        return ($this->actual_pallets && $this->actual_pallets != $this->expected_pallets) ||
               ($this->actual_cases && $this->actual_cases != $this->expected_cases) ||
               ($this->actual_units && $this->actual_units != $this->expected_units);
    }

    public function getVarianceDetails(): array
    {
        $variances = [];

        if ($this->actual_pallets && $this->actual_pallets != $this->expected_pallets) {
            $variances[] = [
                'type' => 'pallets',
                'expected' => $this->expected_pallets,
                'actual' => $this->actual_pallets,
                'variance' => $this->actual_pallets - $this->expected_pallets
            ];
        }

        if ($this->actual_cases && $this->actual_cases != $this->expected_cases) {
            $variances[] = [
                'type' => 'cases',
                'expected' => $this->expected_cases,
                'actual' => $this->actual_cases,
                'variance' => $this->actual_cases - $this->expected_cases
            ];
        }

        if ($this->actual_units && $this->actual_units != $this->expected_units) {
            $variances[] = [
                'type' => 'units',
                'expected' => $this->expected_units,
                'actual' => $this->actual_units,
                'variance' => $this->actual_units - $this->expected_units
            ];
        }

        return $variances;
    }

    public function isOverdue(): bool
    {
        return $this->must_deliver_by && 
               $this->must_deliver_by->lt(now()) && 
               !in_array($this->status, ['delivered', 'cancelled']);
    }

    public function getSpecialRequirementsAttribute(): array
    {
        $requirements = [];

        if ($this->temperature_controlled) {
            $requirements[] = 'Temperature Controlled';
        }

        if ($this->fragile) {
            $requirements[] = 'Fragile';
        }

        if ($this->hazardous) {
            $requirements[] = 'Hazardous';
        }

        if ($this->delivery_priority === 'urgent') {
            $requirements[] = 'Urgent Delivery';
        }

        return $requirements;
    }

    // Scopes
    public function scopeForLoad($query, $loadId)
    {
        return $query->where('outbound_load_id', $loadId);
    }

    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('delivery_priority', $priority);
    }

    public function scopeOverdue($query)
    {
        return $query->where('must_deliver_by', '<', now())
                    ->whereNotIn('status', ['delivered', 'cancelled']);
    }

    public function scopeWithVariance($query)
    {
        return $query->where(function ($q) {
            $q->whereRaw('actual_pallets != expected_pallets')
              ->orWhereRaw('actual_cases != expected_cases')
              ->orWhereRaw('actual_units != expected_units');
        });
    }

    // Generate internal order number
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            if (empty($order->internal_order_number)) {
                $date = now()->format('ymd');
                $sequence = static::whereDate('created_at', today())->count() + 1;
                $order->internal_order_number = "ORD{$date}" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
            }
        });

        static::saved(function ($order) {
            // Update load totals when order is saved
            $order->outboundLoad->updateTotals();
        });
    }
}