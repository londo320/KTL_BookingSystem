<?php

namespace App\Modules\Outbound\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Customer;

class CustomerAddress extends Model
{
    protected $fillable = [
        'customer_id',
        'address_name',
        'is_default',
        'contact_name',
        'contact_phone',
        'contact_email',
        'company_name',
        'address_line_1',
        'address_line_2',
        'city',
        'county',
        'postcode',
        'country',
        'latitude',
        'longitude',
        'geocoded_at',
        'delivery_instructions',
        'access_notes',
        'delivery_hours',
        'requires_appointment',
        'requires_signature',
        'requires_photo_proof',
        'special_equipment',
        'is_active',
        'latest_delivery_time',
        'delivery_buffer_minutes',
        'unloading_duration_minutes',
        'site_closure_time',
        'lunch_break_start',
        'lunch_break_end',
        'no_delivery_periods',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'requires_appointment' => 'boolean',
        'requires_signature' => 'boolean',
        'requires_photo_proof' => 'boolean',
        'is_active' => 'boolean',
        'delivery_hours' => 'array',
        'special_equipment' => 'array',
        'no_delivery_periods' => 'array',
        'geocoded_at' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
    ];

    // Relationships
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(OutboundOrder::class);
    }

    // Helper methods
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address_line_1,
            $this->address_line_2,
            $this->city,
            $this->county,
            $this->postcode,
        ]);

        return implode(', ', $parts);
    }

    public function getFormattedDeliveryHoursAttribute(): string
    {
        if (empty($this->delivery_hours)) {
            return 'No restrictions';
        }

        $formatted = [];
        foreach ($this->delivery_hours as $day => $hours) {
            if ($hours) {
                $formatted[] = ucfirst($day) . ': ' . $hours;
            }
        }

        return implode(', ', $formatted) ?: 'No restrictions';
    }

    public function hasCoordinates(): bool
    {
        return !empty($this->latitude) && !empty($this->longitude);
    }

    public function needsGeocoding(): bool
    {
        return !$this->hasCoordinates() || 
               ($this->geocoded_at && $this->geocoded_at->lt(now()->subMonths(6)));
    }

    public function getDeliveryConstraintsAttribute(): array
    {
        $constraints = [];

        if ($this->requires_appointment) {
            $constraints[] = 'Appointment required';
        }

        if ($this->requires_signature) {
            $constraints[] = 'Signature required';
        }

        if ($this->requires_photo_proof) {
            $constraints[] = 'Photo proof required';
        }

        if ($this->special_equipment) {
            $constraints[] = 'Special equipment: ' . implode(', ', $this->special_equipment);
        }

        if ($this->latest_delivery_time) {
            $constraints[] = 'Must deliver by ' . $this->latest_delivery_time;
        }

        return $constraints;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeNeedsGeocoding($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('latitude')
              ->orWhereNull('longitude')
              ->orWhere('geocoded_at', '<', now()->subMonths(6))
              ->orWhereNull('geocoded_at');
        });
    }

    // Boot method for ensuring only one default per customer
    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($address) {
            if ($address->is_default) {
                // Remove default flag from other addresses for this customer
                static::where('customer_id', $address->customer_id)
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }
        });
    }
}