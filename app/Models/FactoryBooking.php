<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class FactoryBooking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reference',
        'depot_id',
        'customer_id',
        'carrier_id',
        'trailer_type_id',
        'tipping_type',
        'arrived_at',
        'vehicle_registration',
        'trailer_registration',
        'delivery_notes',
        'vehicle_details',
        'priority',
        'status',
        'processing_started_at',
        'completed_at',
        'departed_at',
        'registered_by',
        'gate_notes',
    ];

    protected $casts = [
        'arrived_at' => 'datetime',
        'processing_started_at' => 'datetime',
        'completed_at' => 'datetime',
        'departed_at' => 'datetime',
        'vehicle_details' => 'array',
        'priority' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($factoryBooking) {
            if (empty($factoryBooking->reference)) {
                $factoryBooking->reference = static::generateReference();
            }
            
            if (empty($factoryBooking->arrived_at)) {
                $factoryBooking->arrived_at = now();
            }
        });
    }

    // Relationships
    public function depot(): BelongsTo
    {
        return $this->belongsTo(Depot::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function carrier(): BelongsTo
    {
        return $this->belongsTo(Carrier::class);
    }

    public function trailerType(): BelongsTo
    {
        return $this->belongsTo(TrailerType::class);
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(Movement::class);
    }

    public function poNumbers(): HasMany
    {
        return $this->hasMany(BookingPoNumber::class);
    }

    // Scopes
    public function scopeOnSite($query)
    {
        return $query->whereIn('status', ['arrived', 'processing']);
    }

    public function scopeByDepot($query, $depotId)
    {
        return $query->where('depot_id', $depotId);
    }

    public function scopeByPriority($query, $order = 'desc')
    {
        return $query->orderBy('priority', $order)->orderBy('arrived_at');
    }

    /**
     * Check if factory booking is overdue for tipping
     */
    public function isOverdueForTipping(): bool
    {
        if ($this->status !== 'arrived' || $this->completed_at || !$this->arrived_at) {
            return false;
        }

        $targetMinutes = $this->depot->getFactoryTippingTimeTarget($this->customer_id);
        $targetTime = $this->arrived_at->copy()->addMinutes($targetMinutes);
        
        return now()->greaterThan($targetTime);
    }

    /**
     * Get time remaining until tipping target is exceeded
     */
    public function timeUntilOverdue(): ?Carbon
    {
        if ($this->status !== 'arrived' || $this->completed_at || !$this->arrived_at) {
            return null;
        }

        $targetMinutes = $this->depot->getFactoryTippingTimeTarget($this->customer_id);
        $targetTime = $this->arrived_at->copy()->addMinutes($targetMinutes);
        
        return now()->lessThan($targetTime) ? $targetTime : null;
    }

    /**
     * Get minutes elapsed since arrival
     */
    public function minutesOnSite(): int
    {
        if (!$this->arrived_at) {
            return 0;
        }

        $endTime = $this->completed_at ?? now();
        return $this->arrived_at->diffInMinutes($endTime);
    }

    // Helper methods
    public static function generateReference(): string
    {
        $year = now()->year;
        $lastRecord = static::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastRecord ? (int)substr($lastRecord->reference, -3) + 1 : 1;
        
        return 'FAC-' . $year . '-' . str_pad($sequence, 3, '0', STR_PAD_LEFT);
    }

    public function getOrCreateMovement(): Movement
    {
        return $this->movements()->firstOrCreate(
            ['factory_booking_id' => $this->id],
            [
                'movement_type' => 'factory_delivery',
                'reference_number' => $this->reference,
                'depot_id' => $this->depot_id,
                'factory_booking_id' => $this->id,
                'current_status' => 'arrived',
                'actual_arrival' => $this->arrived_at,
                'carrier_company' => $this->carrier?->name,
                'load_type' => 'factory_delivery',
            ]
        );
    }

    public function getCurrentMovementStatus(): ?string
    {
        $movement = $this->movements()->first();
        return $movement?->current_status;
    }

    public function getTippingStatusAttribute(): ?string
    {
        return $this->getCurrentMovementStatus();
    }

    public function getTippingStatusBadgeAttribute(): string
    {
        $movement = $this->movements()->first();
        $movementStatus = $this->getCurrentMovementStatus();
        
        $statusConfig = [
            'arrived' => ['class' => 'bg-blue-100 text-blue-800', 'label' => '📋 Arrived'],
            'in_waiting' => ['class' => 'bg-yellow-100 text-yellow-800', 'label' => '⏳ Waiting'],
            'trailer_dropped' => ['class' => 'bg-orange-100 text-orange-800', 'label' => '📍 Trailer Dropped'],
            'at_bay' => ['class' => 'bg-purple-100 text-purple-800', 'label' => '🚛 At Bay'],
            'unloading' => ['class' => 'bg-red-100 text-red-800', 'label' => '⚡ Tipping'],
            'empty' => ['class' => 'bg-green-100 text-green-800', 'label' => '✅ Empty'],
            'departed' => ['class' => 'bg-gray-100 text-gray-800', 'label' => '🏁 Departed'],
        ];
        
        $config = $statusConfig[$movementStatus] ?? $statusConfig['arrived'];
        
        return '<span class="px-2 py-1 text-xs rounded '.$config['class'].'">'.$config['label'].'</span>';
    }

    public function isOnSite(): bool
    {
        return in_array($this->status, ['arrived', 'processing']);
    }

    public function getTimeOnSite(): ?string
    {
        if (!$this->arrived_at) {
            return null;
        }

        $endTime = $this->departed_at ?? now();
        return $this->arrived_at->diffForHumans($endTime, true);
    }
}
