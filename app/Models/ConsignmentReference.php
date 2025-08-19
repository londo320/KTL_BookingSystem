<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsignmentReference extends Model
{
    protected $fillable = [
        'consignment_id',
        'reference_type',
        'reference_value',
        'notes',
    ];

    // Relationships
    public function consignment(): BelongsTo
    {
        return $this->belongsTo(Consignment::class);
    }

    // Scopes
    public function scopeByType($query, $type)
    {
        return $query->where('reference_type', $type);
    }

    public function scopeByValue($query, $value)
    {
        return $query->where('reference_value', 'LIKE', "%{$value}%");
    }
}
