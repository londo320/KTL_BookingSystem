<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class Carrier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'contact_email',
        'contact_phone',
        'is_active',
        'requires_approval',
        'last_used_at'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'requires_approval' => 'boolean',
        'last_used_at' => 'datetime'
    ];

    /**
     * Get all depots this carrier is configured for
     */
    public function depots()
    {
        return $this->belongsToMany(Depot::class, 'depot_carrier')
            ->withPivot([
                'is_enabled',
                'auto_disable_unused',
                'auto_disable_months',
                'allowed_customer_ids'
            ])
            ->withTimestamps();
    }

    /**
     * Get all bookings using this carrier
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get merge records where this carrier was the source
     */
    public function mergesAsSource()
    {
        return $this->hasMany(CarrierMerge::class, 'source_carrier_id');
    }

    /**
     * Get merge records where this carrier was the target
     */
    public function mergesAsTarget()
    {
        return $this->hasMany(CarrierMerge::class, 'target_carrier_id');
    }

    /**
     * Mark carrier as used (update last_used_at)
     */
    public function markAsUsed()
    {
        $this->update(['last_used_at' => now()]);
        return $this;
    }

    /**
     * Check if carrier should be auto-disabled based on inactivity
     */
    public function shouldAutoDisable($months = 6)
    {
        if (!$this->last_used_at) {
            return false;
        }
        
        return $this->last_used_at->lt(now()->subMonths($months));
    }

    /**
     * Find existing carrier or create/reactivate one
     */
    public static function findOrReactivate($name)
    {
        $carrier = static::withTrashed()->where('name', $name)->first();
        
        if ($carrier) {
            if ($carrier->trashed()) {
                $carrier->restore();
            }
            if (!$carrier->is_active) {
                $carrier->update(['is_active' => true]);
            }
            return $carrier;
        }
        
        return static::create(['name' => $name, 'is_active' => true]);
    }

    /**
     * Check if this carrier can be merged into another
     */
    public function canBeMergedInto(Carrier $targetCarrier)
    {
        // Prevent merging into inactive carrier
        if (!$targetCarrier->is_active) {
            return false;
        }
        
        // Prevent self-merge
        if ($this->id === $targetCarrier->id) {
            return false;
        }
        
        // Prevent merging into soft-deleted carrier
        if ($targetCarrier->trashed()) {
            return false;
        }
        
        return true;
    }

    /**
     * Merge this carrier into another carrier
     */
    public function mergeInto(Carrier $targetCarrier, $deleteAfterMerge = false)
    {
        if (!$this->canBeMergedInto($targetCarrier)) {
            throw new InvalidArgumentException('Cannot merge into the specified carrier');
        }

        return DB::transaction(function() use ($targetCarrier, $deleteAfterMerge) {
            $bookingsCount = $this->bookings()->count();
            
            // 1. Update all bookings to use target carrier
            $this->bookings()->update(['carrier_id' => $targetCarrier->id]);
            
            // 2. Merge depot relationships
            $depotRelationships = $this->mergeDepotRelationships($targetCarrier);
            
            // 3. Update last_used_at to most recent
            if ($this->last_used_at && (!$targetCarrier->last_used_at || $this->last_used_at->gt($targetCarrier->last_used_at))) {
                $targetCarrier->update(['last_used_at' => $this->last_used_at]);
            }
            
            // 4. Create merge audit record
            CarrierMerge::create([
                'source_carrier_id' => $this->id,
                'source_carrier_name' => $this->getOriginal('name'),
                'target_carrier_id' => $targetCarrier->id,
                'target_carrier_name' => $targetCarrier->name,
                'bookings_moved' => $bookingsCount,
                'depot_relationships_merged' => $depotRelationships,
                'merged_by' => auth()->id(),
                'source_deleted' => $deleteAfterMerge
            ]);
            
            // 5. Handle source carrier
            if ($deleteAfterMerge) {
                $this->delete(); // Soft delete
            } else {
                $this->update([
                    'is_active' => false,
                    'name' => $this->name . ' (MERGED INTO: ' . $targetCarrier->name . ')'
                ]);
            }
            
            return $targetCarrier;
        });
    }

    /**
     * Merge depot relationships into target carrier
     */
    protected function mergeDepotRelationships(Carrier $targetCarrier)
    {
        $sourceDepots = DB::table('depot_carrier')->where('carrier_id', $this->id)->get();
        $mergedRelationships = [];
        
        foreach ($sourceDepots as $sourceDepot) {
            $existing = DB::table('depot_carrier')
                ->where('depot_id', $sourceDepot->depot_id)
                ->where('carrier_id', $targetCarrier->id)
                ->first();
            
            $relationshipData = [
                'depot_id' => $sourceDepot->depot_id,
                'carrier_id' => $targetCarrier->id,
                'is_enabled' => true,
                'auto_disable_unused' => true,
                'auto_disable_months' => 6,
                'allowed_customer_ids' => null,
                'updated_at' => now(),
            ];
            
            if ($existing) {
                // Merge settings - keep most permissive
                $relationshipData = [
                    'is_enabled' => $existing->is_enabled || $sourceDepot->is_enabled,
                    'auto_disable_unused' => $existing->auto_disable_unused && $sourceDepot->auto_disable_unused,
                    'auto_disable_months' => min($existing->auto_disable_months, $sourceDepot->auto_disable_months),
                    'allowed_customer_ids' => $this->mergeCustomerIds($existing->allowed_customer_ids, $sourceDepot->allowed_customer_ids),
                    'updated_at' => now(),
                ];
                
                DB::table('depot_carrier')
                    ->where('depot_id', $sourceDepot->depot_id)
                    ->where('carrier_id', $targetCarrier->id)
                    ->update($relationshipData);
            } else {
                // Create new relationship with source settings
                $relationshipData = array_merge($relationshipData, [
                    'is_enabled' => $sourceDepot->is_enabled,
                    'auto_disable_unused' => $sourceDepot->auto_disable_unused,
                    'auto_disable_months' => $sourceDepot->auto_disable_months,
                    'allowed_customer_ids' => $sourceDepot->allowed_customer_ids,
                    'created_at' => now(),
                ]);
                
                DB::table('depot_carrier')->insert($relationshipData);
            }
            
            $mergedRelationships[] = $relationshipData;
        }
        
        // Remove old depot relationships
        DB::table('depot_carrier')->where('carrier_id', $this->id)->delete();
        
        return $mergedRelationships;
    }

    /**
     * Merge customer IDs from two JSON arrays
     */
    protected function mergeCustomerIds($existing, $source)
    {
        $existingIds = $existing ? json_decode($existing, true) : [];
        $sourceIds = $source ? json_decode($source, true) : [];
        
        // If either is null/empty, result should be null (no restrictions)
        if (empty($existingIds) || empty($sourceIds)) {
            return null;
        }
        
        // Merge unique customer IDs
        $merged = array_unique(array_merge($existingIds, $sourceIds));
        return json_encode(array_values($merged));
    }

    /**
     * Get carriers available for a specific depot and customer
     */
    public static function availableForDepotAndCustomer($depotId, $customerId = null)
    {
        return static::where('carriers.is_active', true)
            ->join('depot_carrier', 'carriers.id', '=', 'depot_carrier.carrier_id')
            ->where('depot_carrier.depot_id', $depotId)
            ->where('depot_carrier.is_enabled', true)
            ->where(function($query) use ($customerId) {
                $query->whereNull('depot_carrier.allowed_customer_ids');
                if ($customerId) {
                    $query->orWhereRaw('JSON_CONTAINS(depot_carrier.allowed_customer_ids, ?)', [$customerId]);
                }
            })
            ->orderBy('carriers.name')
            ->select('carriers.*')
            ->get();
    }

    /**
     * Find potential duplicate carriers
     */
    public static function findPotentialDuplicates()
    {
        return static::with('bookings')
            ->get()
            ->groupBy(function($carrier) {
                // Group by normalized name
                return strtolower(preg_replace('/[^a-z0-9]/', '', $carrier->name));
            })
            ->filter(fn($group) => $group->count() > 1);
    }

    /**
     * Scope for active carriers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for carriers with bookings
     */
    public function scopeWithBookings($query)
    {
        return $query->has('bookings');
    }

    /**
     * Get display name with status indicators
     */
    public function getDisplayNameAttribute()
    {
        $name = $this->name;
        
        if (!$this->is_active) {
            $name .= ' (Inactive)';
        }
        
        if ($this->requires_approval) {
            $name .= ' (Pending)';
        }
        
        return $name;
    }

    /**
     * Get carriers available for a specific depot and customer
     */
    public static function getAvailableForDepotAndCustomer($depotId, $customerId)
    {
        return static::query()
            ->where('is_active', true)
            ->whereHas('depots', function ($query) use ($depotId, $customerId) {
                $query->where('depot_id', $depotId)
                    ->where('depot_carrier.is_enabled', true)
                    ->where(function ($q) use ($customerId) {
                        $q->whereNull('depot_carrier.allowed_customer_ids')
                          ->orWhereRaw('JSON_CONTAINS(depot_carrier.allowed_customer_ids, ?)', [json_encode((string)$customerId)]);
                    });
            })
            ->orderBy('name')
            ->get();
    }
}