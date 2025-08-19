<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PalletType extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function getDisplayNameAttribute(): string
    {
        return "{$this->name} ({$this->code})";
    }

    // Relationship with PO lines
    public function poLinesExpected()
    {
        return $this->hasMany(PoLine::class, 'expected_pallet_type_id');
    }

    public function poLinesActual()
    {
        return $this->hasMany(PoLine::class, 'actual_pallet_type_id');
    }
}
