<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Movement;
use App\Models\TippingBay;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OperationalQueueController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin|depot-admin|site-admin|warehouse']);
    }

    /**
     * Get operational queue dashboard for managing 30+ containers efficiently
     */
    public function dashboard(Request $request)
    {
        $allowedDepotIds = $this->getAllowedDepotIds();
        
        // Get user's default depot for action restrictions
        $user = auth()->user();
        $defaultDepotId = $user->depot_id ?? $allowedDepotIds[0] ?? null;
        
        // Allow viewing all depots but note which is default for actions
        $selectedDepotId = $request->get('depot_id');

        // Show all allowed depots for viewing, but track default for actions
        if ($selectedDepotId && in_array($selectedDepotId, $allowedDepotIds)) {
            $currentDepotId = $selectedDepotId;
        } elseif ($selectedDepotId === "") {
            // Explicitly selected "All Depots"
            $currentDepotId = null;
        } else {
            // Default to user's depot if not specified
            $currentDepotId = $defaultDepotId;
        }
        
        // Filter movements by selected depot or show all allowed depots
        $depotIds = $currentDepotId ? [$currentDepotId] : $allowedDepotIds;
        
        // Get all active movements for queue management (including factory bookings)
        $allMovements = Movement::with([
            'booking.slot.depot', 
            'booking.customer', 
            'booking.poNumbers',
            'factoryBooking.depot',
            'factoryBooking.customer',
            'factoryBooking.poNumbers',
            'tippingBay', 
            'tippingLocation'
        ])
            ->where(function($query) use ($depotIds) {
                // Regular bookings
                $query->whereNotNull('booking_id')
                      ->whereHas('booking.slot', fn($q) => $q->whereIn('depot_id', $depotIds))
                      // Factory bookings
                      ->orWhere(function($subQuery) use ($depotIds) {
                          $subQuery->whereNotNull('factory_booking_id')
                                   ->whereHas('factoryBooking', fn($q) => $q->whereIn('depot_id', $depotIds));
                      });
            })
            ->whereIn('current_status', ['arrived', 'in_parking', 'at_bay', 'unloading', 'empty', 'back_to_parking', 'trailer_collected'])
            ->where(function($query) {
                $query->whereNull('actual_departure')
                      ->whereNull('collection_unit_departed_at');
            })
            ->get();

        // PRIORITY QUEUE: What should be tipped next for maximum efficiency
        $tippingQueue = $this->calculateTippingPriority($allMovements);
        
        // BAY MANAGEMENT: Current bay status and what's next
        $bayStatus = $this->getBayEfficiencyStatus($allMovements, $depotIds);
        
        // COLLECTION URGENCY: Overdue/urgent collections
        $collectionUrgency = $this->getCollectionUrgency($allMovements);
        
        // NEW ARRIVALS: Recently arrived, need assignment
        $newArrivals = $allMovements->whereIn('current_status', ['arrived'])->sortBy('created_at');
        
        // OPERATIONAL STATS
        $stats = $this->calculateOperationalStats($allMovements);

        // Get allowed depots for filter dropdown
        $allDepots = \App\Models\Depot::whereIn('id', $allowedDepotIds)->get();

        return view('warehouse.operations.queue-management', compact(
            'tippingQueue', 
            'bayStatus', 
            'collectionUrgency', 
            'newArrivals',
            'stats',
            'allDepots',
            'currentDepotId',
            'defaultDepotId'
        ));
    }

    /**
     * Calculate what should be tipped next based on efficiency rules
     */
    private function calculateTippingPriority($movements)
    {
        // Get loaded trailers ready for tipping (arrived or in parking but not yet unloaded)
        $readyToTip = $movements->whereIn('current_status', ['arrived', 'in_parking'])
            ->filter(function($movement) {
                // Include trailers that haven't started unloading yet
                if ($movement->current_status === 'in_parking') {
                    // Only include if not yet started unloading (still loaded)
                    return $movement->unloading_started_at === null;
                }
                // Include arrived trailers
                return $movement->current_status === 'arrived';
            });

        return $readyToTip->map(function($movement) {
            // Get the bookable model (regular booking or factory booking)
            $booking = $movement->booking ?? $movement->factoryBooking;
            $isFactoryBooking = $movement->factoryBooking !== null;
            
            $priority = 0;
            $reasons = [];

            // PRIORITY FACTORS:
            
            // 1. Customer Priority (high-value customers first)
            if ($booking->customer && $booking->customer->priority_level ?? 0 > 5) {
                $priority += 100;
                $reasons[] = 'Priority Customer';
            }

            // 2. Waiting Time (older bookings first)
            $waitingMinutes = 0;
            if ($movement->current_status === 'in_parking' && $movement->moved_to_location_at) {
                $waitingMinutes = round($movement->moved_to_location_at->diffInMinutes(now()));
            } else {
                // For regular bookings check arrived_at, for factory bookings use arrived_at directly
                $arrivalTime = $isFactoryBooking ? $booking->arrived_at : $movement->booking->arrived_at;
                if ($arrivalTime) {
                    $waitingMinutes = round($arrivalTime->diffInMinutes(now()));
                }
            }
            $priority += min($waitingMinutes, 480); // Max 8 hours boost
            if ($waitingMinutes > 120) {
                $reasons[] = 'Long Wait (' . round($waitingMinutes/60, 1) . 'h)';
            }

            // 3. Appointment Time (scheduled bookings priority - only for regular bookings)
            if (!$isFactoryBooking && $booking->slot && $booking->slot->start_at) {
                $scheduledTime = Carbon::parse($booking->slot->start_at);
                if ($scheduledTime->isPast() && $scheduledTime->diffInMinutes(now()) > 30) {
                    $priority += 50;
                    $reasons[] = 'Overdue Appointment';
                }
            }

            // 4. Trailer Type Efficiency (similar types together)
            // This would reduce changeover time between different product types
            if ($booking->trailer_type ?? null) {
                // Boost if same type as currently tipping
                $currentlyTipping = $this->getCurrentlyTippingTypes();
                if (in_array($booking->trailer_type, $currentlyTipping)) {
                    $priority += 25;
                    $reasons[] = 'Same Type Efficiency';
                }
            }

            // 5. Collection Time Pressure
            if ($booking->collection_scheduled_at && $booking->collection_scheduled_at->diffInHours(now()) < 2) {
                $priority += 75;
                $reasons[] = 'Urgent Collection';
            }

            // 6. Manual Priority Boost (can be positive or negative)
            if ($booking->manual_priority_boost && $booking->manual_priority_boost != 0) {
                $priority += $booking->manual_priority_boost;
                if ($booking->manual_priority_boost > 0) {
                    $reasons[] = 'Manual Boost (+' . $booking->manual_priority_boost . ')';
                } else {
                    $reasons[] = 'Manual Penalty (' . $booking->manual_priority_boost . ')';
                }
            }

            $movement->efficiency_priority = round($priority);
            $movement->priority_reasons = $reasons;
            return $movement;
        })->sortBy([
            // Primary sort: Live Tips first, then Drops (tipping_type priority)
            function($movement) {
                $booking = $movement->booking ?? $movement->factoryBooking;
                $tippingType = $booking->tipping_type ?? null;
                // Live tips get priority 1, drops get priority 2, null/unset gets priority 3
                if ($tippingType === 'live_tip') return 1;
                if ($tippingType === 'drop') return 2;
                return 3;
            },
            // Secondary sort: Within same tipping type, prioritize by slot date/time
            function($movement) {
                $booking = $movement->booking ?? $movement->factoryBooking;
                return $booking->slot->start_at ?? $booking->arrived_at ?? now()->addYears(1);
            },
            // Tertiary sort: Within same slot time, sort by priority score (descending)
            function($movement) {
                return -($movement->efficiency_priority ?? 0);
            }
        ]);
    }

    /**
     * Get bay efficiency status - which bays will be free soon
     */
    private function getBayEfficiencyStatus($movements, $depotIds = null)
    {
        $query = TippingBay::where('is_active', true);
        
        if ($depotIds) {
            $query->whereIn('depot_id', $depotIds);
        }
        
        $bays = $query->get();
        
        return $bays->map(function($bay) use ($movements) {
            $currentMovement = $movements->where('tipping_bay_id', $bay->id)->first();
            
            if (!$currentMovement) {
                return [
                    'bay' => $bay,
                    'status' => 'available',
                    'estimated_free_at' => now(),
                    'current_booking' => null,
                    'estimated_duration_remaining' => 0
                ];
            }

            // Estimate when bay will be free
            $estimatedDuration = 45; // Default 45 minutes tipping time
            $timeInBay = $currentMovement->moved_to_bay_at ? 
                $currentMovement->moved_to_bay_at->diffInMinutes(now()) : 0;
            $remainingTime = max(0, $estimatedDuration - $timeInBay);

            return [
                'bay' => $bay,
                'status' => $currentMovement->current_status,
                'estimated_free_at' => now()->addMinutes($remainingTime),
                'current_booking' => $currentMovement->booking,
                'estimated_duration_remaining' => $remainingTime
            ];
        })->sortBy('estimated_free_at');
    }

    /**
     * Get collection urgency - overdue collections
     */
    private function getCollectionUrgency($movements)
    {
        $emptyTrailers = $movements->whereIn('current_status', ['empty', 'back_to_parking'])
            ->filter(function($movement) {
                // Only show trailers that have been unloaded
                return $movement->unloading_completed_at !== null;
            });
        
        return $emptyTrailers->map(function($movement) {
            $booking = $movement->booking;
            $hoursWaiting = $movement->unloading_completed_at ? 
                $movement->unloading_completed_at->diffInHours(now()) : 0;

            $urgency = 'normal';
            if ($hoursWaiting > 8) $urgency = 'critical';
            elseif ($hoursWaiting > 4) $urgency = 'high';
            elseif ($hoursWaiting > 2) $urgency = 'medium';

            $movement->collection_urgency = $urgency;
            $movement->hours_waiting_collection = $hoursWaiting;
            return $movement;
        })->sortByDesc('hours_waiting_collection');
    }

    /**
     * Calculate operational statistics
     */
    private function calculateOperationalStats($movements)
    {
        return [
            'total_on_site' => $movements->count(),
            'ready_to_tip' => $movements->whereIn('current_status', ['in_parking'])
                ->filter(fn($m) => $m->unloading_started_at === null)->count(),
            'currently_tipping' => $movements->where('current_status', 'unloading')->count(),
            'awaiting_bays' => $movements->whereIn('current_status', ['in_parking'])
                ->filter(fn($m) => $m->unloading_started_at === null)->count(),
            'empty_waiting_collection' => $movements->whereIn('current_status', ['empty', 'back_to_parking'])
                ->filter(fn($m) => $m->unloading_completed_at !== null)->count(),
            'average_wait_time' => $this->calculateAverageWaitTime($movements),
            'efficiency_score' => $this->calculateEfficiencyScore($movements)
        ];
    }

    private function calculateAverageWaitTime($movements)
    {
        $waitingMovements = $movements->whereIn('current_status', ['in_parking']);
        if ($waitingMovements->isEmpty()) return 0;

        $totalWaitMinutes = $waitingMovements->sum(function($movement) {
            return $movement->moved_to_location_at ? 
                $movement->moved_to_location_at->diffInMinutes(now()) : 0;
        });

        return round($totalWaitMinutes / $waitingMovements->count(), 1);
    }

    private function calculateEfficiencyScore($movements)
    {
        // Simple efficiency score based on throughput vs capacity
        // Get depot IDs from movements to filter bays
        $depotIds = $movements->map(function($movement) {
            return $movement->booking?->slot?->depot_id;
        })->filter()->unique()->values()->toArray();
        
        $query = TippingBay::where('is_active', true);
        if (!empty($depotIds)) {
            $query->whereIn('depot_id', $depotIds);
        }
        $activeBays = $query->count();
        
        $currentlyTipping = $movements->where('current_status', 'unloading')->count();
        
        if ($activeBays == 0) return 0;
        return round(($currentlyTipping / $activeBays) * 100, 1);
    }

    private function getCurrentlyTippingTypes()
    {
        // Get trailer types currently being tipped for efficiency matching
        return Movement::where('current_status', 'unloading')
            ->join('bookings', 'movements.booking_id', '=', 'bookings.id')
            ->pluck('bookings.trailer_type')
            ->filter()
            ->unique()
            ->toArray();
    }

    /**
     * Start tipping process for Drop type bookings
     */
    public function startTipping(Request $request, Booking $booking)
    {
        // Verify this is a Drop type booking
        if ($booking->tipping_type !== 'drop') {
            return response()->json([
                'success' => false,
                'error' => 'This action is only available for Drop type bookings'
            ]);
        }

        // Get or create movement for this booking
        $movement = $booking->getOrCreateMovement();

        // Check if booking is ready to start tipping
        if (!in_array($movement->current_status, ['arrived', 'in_parking'])) {
            return response()->json([
                'success' => false,
                'error' => 'Booking is not ready to start tipping (current status: ' . $movement->current_status . ')'
            ]);
        }

        try {
            // Start the tipping process for Drop workflow
            $booking->update([
                'tipping_started_at' => now(),
                'tipping_status' => 'tipping_in_progress'
            ]);

            // Update movement status
            $movement->update([
                'current_status' => 'unloading',
                'unloading_started_at' => now()
            ]);

            // Record in booking history
            \App\Models\BookingHistory::recordAction(
                $booking,
                'tipping_started',
                'Drop workflow tipping started - trailer will be processed independently'
            );

            return response()->json([
                'success' => true,
                'message' => 'Tipping started successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Failed to start tipping for booking ' . $booking->id . ': ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to start tipping process'
            ]);
        }
    }

    private function getAllowedDepotIds(): array
    {
        $assignedDepotIds = auth()->user()->depots()->pluck('depots.id')->toArray();

        if (empty($assignedDepotIds) && auth()->user()->hasRole('admin|site-admin')) {
            return \App\Models\Depot::pluck('id')->toArray();
        }

        return $assignedDepotIds;
    }
}