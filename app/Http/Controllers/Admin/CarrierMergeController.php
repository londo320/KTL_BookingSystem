<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Carrier;
use App\Models\CarrierMerge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CarrierMergeController extends Controller
{
    public function index()
    {
        $carriers = Carrier::withTrashed()
            ->withCount('bookings')
            ->orderBy('name')
            ->get();

        // Find potential duplicates
        $suggestedMerges = Carrier::findPotentialDuplicates();

        // Get recent merges
        $recentMerges = CarrierMerge::with(['sourceCarrier', 'targetCarrier', 'mergedBy'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.carriers.merge.index', compact('carriers', 'suggestedMerges', 'recentMerges'));
    }

    public function preview(Request $request)
    {
        $request->validate([
            'source_carrier_id' => 'required|exists:carriers,id',
            'target_carrier_id' => 'required|exists:carriers,id|different:source_carrier_id',
        ]);

        $sourceCarrier = Carrier::with(['bookings.slot.depot', 'depots'])->findOrFail($request->source_carrier_id);
        $targetCarrier = Carrier::with(['depots'])->findOrFail($request->target_carrier_id);

        $impact = [
            'bookings_to_update' => $sourceCarrier->bookings()->count(),
            'depots_affected' => $sourceCarrier->bookings()
                ->join('slots', 'bookings.slot_id', '=', 'slots.id')
                ->distinct('depot_id')
                ->count('depot_id'),
            'customers_affected' => $sourceCarrier->bookings()
                ->whereNotNull('customer_id')
                ->distinct('customer_id')
                ->count('customer_id'),
            'date_range' => [
                'from' => $sourceCarrier->bookings()->min('created_at'),
                'to' => $sourceCarrier->bookings()->max('created_at'),
            ],
            'depot_conflicts' => $this->getDepotConflicts($sourceCarrier, $targetCarrier),
        ];

        return response()->json([
            'source' => $sourceCarrier,
            'target' => $targetCarrier,
            'impact' => $impact,
            'can_merge' => $sourceCarrier->canBeMergedInto($targetCarrier),
            'warnings' => $this->getMergeWarnings($sourceCarrier, $targetCarrier),
        ]);
    }

    public function merge(Request $request)
    {
        $request->validate([
            'source_carrier_id' => 'required|exists:carriers,id',
            'target_carrier_id' => 'required|exists:carriers,id|different:source_carrier_id',
            'delete_source' => 'boolean',
            'reason' => 'nullable|string|max:500',
        ]);

        $sourceCarrier = Carrier::findOrFail($request->source_carrier_id);
        $targetCarrier = Carrier::findOrFail($request->target_carrier_id);

        if (!$sourceCarrier->canBeMergedInto($targetCarrier)) {
            return back()->withErrors(['error' => 'Cannot merge these carriers. Check that target carrier is active and not deleted.']);
        }

        try {
            $bookingsCount = $sourceCarrier->bookings()->count();
            $sourceCarrierName = $sourceCarrier->name;
            
            $result = $sourceCarrier->mergeInto($targetCarrier, $request->boolean('delete_source'));
            
            // Log the action
            \Log::info('Carrier merge completed', [
                'source_carrier' => $sourceCarrierName,
                'target_carrier' => $targetCarrier->name,
                'bookings_moved' => $bookingsCount,
                'source_deleted' => $request->boolean('delete_source'),
                'reason' => $request->reason,
                'merged_by' => auth()->user()->name,
            ]);
            
            return redirect()->route('admin.carriers.index')->with('success', 
                "Successfully merged '{$sourceCarrierName}' into '{$targetCarrier->name}'. " .
                "Updated {$bookingsCount} booking records."
            );
        } catch (\Exception $e) {
            \Log::error('Carrier merge failed', [
                'source' => $sourceCarrier->id,
                'target' => $targetCarrier->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return back()->withErrors(['error' => 'Merge failed: ' . $e->getMessage()]);
        }
    }

    public function history()
    {
        $merges = CarrierMerge::with(['sourceCarrier', 'targetCarrier', 'mergedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.carriers.merge.history', compact('merges'));
    }

    public function undoMerge(CarrierMerge $merge)
    {
        // This is a complex operation that would require careful handling
        // For now, we'll just show an error - implement if needed
        return back()->withErrors(['error' => 'Merge undo functionality not yet implemented. Please contact technical support if you need to reverse a merge.']);
    }

    protected function getDepotConflicts(Carrier $sourceCarrier, Carrier $targetCarrier)
    {
        $conflicts = [];
        
        foreach ($sourceCarrier->depots as $sourceDepot) {
            $targetDepot = $targetCarrier->depots->firstWhere('id', $sourceDepot->id);
            
            if ($targetDepot) {
                $sourceConfig = $sourceDepot->pivot;
                $targetConfig = $targetDepot->pivot;
                
                $conflict = [];
                
                if ($sourceConfig->is_enabled !== $targetConfig->is_enabled) {
                    $conflict[] = 'Enabled status differs';
                }
                
                if ($sourceConfig->auto_disable_unused !== $targetConfig->auto_disable_unused) {
                    $conflict[] = 'Auto-disable setting differs';
                }
                
                if ($sourceConfig->auto_disable_months !== $targetConfig->auto_disable_months) {
                    $conflict[] = 'Auto-disable timeframe differs';
                }
                
                $sourceCustomers = json_decode($sourceConfig->allowed_customer_ids, true) ?: [];
                $targetCustomers = json_decode($targetConfig->allowed_customer_ids, true) ?: [];
                
                if ($sourceCustomers !== $targetCustomers) {
                    $conflict[] = 'Customer restrictions differ';
                }
                
                if (!empty($conflict)) {
                    $conflicts[$sourceDepot->name] = $conflict;
                }
            }
        }
        
        return $conflicts;
    }

    protected function getMergeWarnings(Carrier $sourceCarrier, Carrier $targetCarrier)
    {
        $warnings = [];
        
        if ($sourceCarrier->bookings()->count() > 1000) {
            $warnings[] = 'Large number of bookings to update - merge may take some time';
        }
        
        if (!$targetCarrier->last_used_at || $targetCarrier->last_used_at->lt(now()->subMonths(6))) {
            $warnings[] = 'Target carrier has not been used recently';
        }
        
        if ($sourceCarrier->bookings()->count() > $targetCarrier->bookings()->count()) {
            $warnings[] = 'Source carrier has more bookings than target - consider reversing the merge direction';
        }
        
        $conflicts = $this->getDepotConflicts($sourceCarrier, $targetCarrier);
        if (!empty($conflicts)) {
            $warnings[] = 'Depot configuration conflicts detected - most permissive settings will be kept';
        }
        
        return $warnings;
    }
}