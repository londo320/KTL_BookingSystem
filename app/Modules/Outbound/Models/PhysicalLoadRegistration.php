<?php

namespace App\Modules\Outbound\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Depot;
use App\Models\User;

class PhysicalLoadRegistration extends Model
{
    protected $fillable = [
        'load_reference',
        'driver_paperwork_ref',
        'vehicle_registration',
        'trailer_registration', 
        'carrier_company',
        'driver_name',
        'driver_phone',
        'driver_license',
        'arrival_time',
        'arrival_depot_id',
        'arrival_notes',
        'status',
        'outbound_load_id',
        'expected_orders',
        'matched_orders',
        'registered_by',
    ];

    protected $casts = [
        'arrival_time' => 'datetime',
        'expected_orders' => 'integer',
        'matched_orders' => 'integer',
    ];

    // Relationships
    public function arrivalDepot(): BelongsTo
    {
        return $this->belongsTo(Depot::class, 'arrival_depot_id');
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function outboundLoad(): BelongsTo
    {
        return $this->belongsTo(OutboundLoad::class);
    }

    public function stagingOrders(): HasMany
    {
        return $this->hasMany(WmsStagingOrder::class, 'load_reference', 'load_reference');
    }

    // Scopes
    public function scopeAwaiting($query)
    {
        return $query->where('status', 'arrived');
    }

    public function scopeMatched($query)
    {
        return $query->where('status', 'orders_matched');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('arrival_time', today());
    }

    // Helper methods
    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'arrived' => 'bg-yellow-100 text-yellow-800',
            'orders_matched' => 'bg-blue-100 text-blue-800',
            'ready_for_collection' => 'bg-green-100 text-green-800',
            'collecting' => 'bg-indigo-100 text-indigo-800',
            'departed' => 'bg-gray-100 text-gray-800',
            'cancelled' => 'bg-red-100 text-red-800',
        ];
        
        return $badges[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getMatchingProgressAttribute(): float
    {
        if ($this->expected_orders === 0) {
            return 0;
        }
        
        return ($this->matched_orders / $this->expected_orders) * 100;
    }

    public function isFullyMatched(): bool
    {
        return $this->expected_orders > 0 && $this->matched_orders >= $this->expected_orders;
    }

    public function canStartCollection(): bool
    {
        return $this->status === 'orders_matched' && $this->isFullyMatched();
    }

    // Actions
    public function updateMatchedCount(): void
    {
        $this->matched_orders = $this->stagingOrders()
            ->where('processing_status', 'matched')
            ->count();
        
        $this->save();
        
        // Auto-update status if fully matched
        if ($this->isFullyMatched() && $this->status === 'arrived') {
            $this->update(['status' => 'orders_matched']);
        }
    }
}