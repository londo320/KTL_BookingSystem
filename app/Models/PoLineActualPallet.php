<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PoLineActualPallet extends Model
{
    protected $fillable = [
        'po_line_id',
        'pallet_type_id',
        'quantity',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function poLine(): BelongsTo
    {
        return $this->belongsTo(PoLine::class);
    }

    public function palletType(): BelongsTo
    {
        return $this->belongsTo(PalletType::class);
    }
}
