<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrailerType extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Get bookings using this trailer type
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class, 'trailer_type_id');
    }

    /**
     * Scope for active trailer types
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if this trailer type can be deleted
     */
    public function canBeDeleted()
    {
        return $this->bookings()->count() === 0;
    }
}
