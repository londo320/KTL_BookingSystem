<?php

namespace App\Modules\Outbound\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Customer;
use App\Models\Depot;

class WmsStagingOrder extends Model
{
    protected $fillable = [
        'source_system',
        'source_file_name',
        'uploaded_at',
        'load_reference',
        'external_load_id',
        'order_reference',
        'po_number',
        'customer_code',
        'customer_name',
        'collection_depot_code',
        'collection_reference',
        'delivery_address_raw',
        'delivery_postcode',
        'planned_delivery_date',
        'delivery_time_start',
        'delivery_time_end',
        'pallets',
        'cases',
        'units',
        'weight_kg',
        'temperature_controlled',
        'fragile',
        'hazardous',
        'special_instructions',
        'processing_status',
        'processing_notes',
        'processed_at',
        'outbound_load_id',
        'outbound_order_id',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'planned_delivery_date' => 'date',
        'delivery_time_start' => 'datetime',
        'delivery_time_end' => 'datetime',
        'pallets' => 'integer',
        'cases' => 'integer',
        'units' => 'integer',
        'weight_kg' => 'decimal:2',
        'temperature_controlled' => 'boolean',
        'fragile' => 'boolean',
        'hazardous' => 'boolean',
        'processed_at' => 'datetime',
    ];

    // Relationships
    public function outboundLoad(): BelongsTo
    {
        return $this->belongsTo(OutboundLoad::class);
    }

    public function outboundOrder(): BelongsTo
    {
        return $this->belongsTo(OutboundOrder::class);
    }

    public function physicalLoad(): BelongsTo
    {
        return $this->belongsTo(PhysicalLoadRegistration::class, 'load_reference', 'load_reference');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('processing_status', 'pending');
    }

    public function scopeMatched($query)
    {
        return $query->where('processing_status', 'matched');
    }

    public function scopeFailed($query)
    {
        return $query->where('processing_status', 'failed');
    }

    public function scopeForLoad($query, string $loadReference)
    {
        return $query->where('load_reference', $loadReference);
    }

    public function scopeFromSystem($query, string $system)
    {
        return $query->where('source_system', $system);
    }

    // Helper methods
    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'pending' => 'bg-yellow-100 text-yellow-800',
            'matched' => 'bg-green-100 text-green-800',
            'failed' => 'bg-red-100 text-red-800',
            'ignored' => 'bg-gray-100 text-gray-800',
        ];
        
        return $badges[$this->processing_status] ?? 'bg-gray-100 text-gray-800';
    }

    public function isPending(): bool
    {
        return $this->processing_status === 'pending';
    }

    public function isMatched(): bool
    {
        return $this->processing_status === 'matched';
    }

    public function hasFailed(): bool
    {
        return $this->processing_status === 'failed';
    }

    // Matching methods
    public function findMatchingCustomer(): ?Customer
    {
        // Try exact match first
        $customer = Customer::where('customer_code', $this->customer_code)->first();
        
        if (!$customer) {
            // Try fuzzy match on name
            $customer = Customer::where('name', 'LIKE', '%' . $this->customer_name . '%')->first();
        }
        
        return $customer;
    }

    public function findMatchingDepot(): ?Depot
    {
        return Depot::where('code', $this->collection_depot_code)
            ->orWhere('name', 'LIKE', '%' . $this->collection_depot_code . '%')
            ->first();
    }

    public function markAsMatched(OutboundLoad $load, OutboundOrder $order): void
    {
        $this->update([
            'processing_status' => 'matched',
            'processed_at' => now(),
            'outbound_load_id' => $load->id,
            'outbound_order_id' => $order->id,
            'processing_notes' => 'Successfully matched to outbound system',
        ]);
    }

    public function markAsFailed(string $reason): void
    {
        $this->update([
            'processing_status' => 'failed',
            'processed_at' => now(),
            'processing_notes' => $reason,
        ]);
    }
}