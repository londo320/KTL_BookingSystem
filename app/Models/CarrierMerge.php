<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarrierMerge extends Model
{
    use HasFactory;

    protected $fillable = [
        'source_carrier_id',
        'source_carrier_name',
        'target_carrier_id',
        'target_carrier_name',
        'bookings_moved',
        'depot_relationships_merged',
        'merged_by',
        'source_deleted'
    ];

    protected $casts = [
        'depot_relationships_merged' => 'array',
        'source_deleted' => 'boolean'
    ];

    /**
     * Get the source carrier (may be soft deleted)
     */
    public function sourceCarrier()
    {
        return $this->belongsTo(Carrier::class, 'source_carrier_id')->withTrashed();
    }

    /**
     * Get the target carrier
     */
    public function targetCarrier()
    {
        return $this->belongsTo(Carrier::class, 'target_carrier_id');
    }

    /**
     * Get the user who performed the merge
     */
    public function mergedBy()
    {
        return $this->belongsTo(User::class, 'merged_by');
    }
}