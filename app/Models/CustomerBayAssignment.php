<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class CustomerBayAssignment extends Model
{
    protected $fillable = [
        'customer_id',
        'tipping_bay_id',
        'priority',
        'is_active',
    ];

    protected $casts = [
        'priority' => 'integer',
        'is_active' => 'boolean',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function tippingBay(): BelongsTo
    {
        return $this->belongsTo(TippingBay::class);
    }

    /**
     * Get allowed bay IDs for a customer at a depot
     */
    public static function getAllowedBayIds(int $customerId, int $depotId): ?array
    {
        $assignments = static::where('customer_id', $customerId)
            ->where('is_active', true)
            ->whereHas('tippingBay', function ($q) use ($depotId) {
                $q->where('depot_id', $depotId);
            })
            ->get();

        if ($assignments->isEmpty()) {
            return null; // No restrictions
        }

        return $assignments->pluck('tipping_bay_id')->toArray();
    }

    /**
     * Get available bays for customer, sorted by priority and equipment match
     */
    public static function getAvailableBaysForCustomer(
        int $customerId,
        int $depotId,
        ?array $requiredEquipment = null
    ): Collection {
        $allowedBayIds = static::getAllowedBayIds($customerId, $depotId);

        // Get available bays
        $query = TippingBay::available()
            ->forDepot($depotId);

        // Apply customer restrictions if they exist
        if ($allowedBayIds !== null) {
            $query->whereIn('id', $allowedBayIds);
        }

        $bays = $query->get();

        // Filter by equipment requirements if specified
        if ($requiredEquipment && count($requiredEquipment) > 0) {
            $bays = $bays->filter(function ($bay) use ($requiredEquipment) {
                $bayEquipment = $bay->equipment ?? [];
                foreach ($requiredEquipment as $equipment) {
                    if (!in_array($equipment, $bayEquipment)) {
                        return false;
                    }
                }
                return true;
            });
        }

        // Sort by priority if customer has assignments
        if ($allowedBayIds !== null) {
            $priorityMap = static::where('customer_id', $customerId)
                ->where('is_active', true)
                ->whereIn('tipping_bay_id', $bays->pluck('id')->toArray())
                ->pluck('priority', 'tipping_bay_id')
                ->toArray();

            $bays = $bays->sortByDesc(function ($bay) use ($priorityMap) {
                return $priorityMap[$bay->id] ?? 0;
            });
        }

        return $bays;
    }

    /**
     * Check if customer has restrictions at depot
     */
    public static function hasRestrictions(int $customerId, int $depotId): bool
    {
        return static::where('customer_id', $customerId)
            ->where('is_active', true)
            ->whereHas('tippingBay', function ($q) use ($depotId) {
                $q->where('depot_id', $depotId);
            })
            ->exists();
    }
}
