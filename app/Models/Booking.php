<?php

namespace App\Models;

use App\Models\ArrivalTimeSetting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Booking extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'slot_id',
        'booking_type_id',
        'user_id',
        'customer_id',
        'carrier_id',
        'trailer_type_id',
        'booking_reference',
        'original_booking_id',
        'rebook_reason',
        'cancelled_at',
        'cancellation_reason',
        'cancelled_by',
        'is_rebooked',
        'rebook_count',
        'notes',
        'container_size',
        'status',
        'end_time',
        'arrived_at',
        'departed_at',
        'bay_transferred_at',
        'bay_transferred_by',
        'bay_transfer_reason',
        'departure_vehicle_registration',
        'vehicle_details', // JSON storage for vehicle details
        // Tipping workflow fields
        'tipping_location_id',
        'tipping_bay_id',
        'tipping_status',
        'in_parking_at',
        'moved_to_bay_at',
        'tipping_started_at',
        'tipping_completed_at',
        'trailer_departed_at',
        'tipping_notes',
        'actual_tipping_duration',
        'tipping_issues',
        // Priority and collection fields
        'collection_scheduled_at',
        'manual_priority_boost',
        'priority_notes',
        // Tipping workflow type
        'tipping_type',
        'swap_trailer_id',
        'tipping_operator_id',
        'bay_assigned_by',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'start_time' => 'datetime',   // for bookings
        'end_time' => 'datetime',   // for bookings
        'cut_off_time' => 'string',     // or 'date:H:i' if you like
        'arrived_at' => 'datetime',
        'departed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'is_rebooked' => 'boolean',
        'rebook_count' => 'integer',
        'bay_transferred_at' => 'datetime',
        'vehicle_details' => 'array', // JSON casting
        // Tipping workflow casts
        'in_parking_at' => 'datetime',
        'moved_to_bay_at' => 'datetime',
        'tipping_started_at' => 'datetime',
        'tipping_completed_at' => 'datetime',
        'trailer_departed_at' => 'datetime',
        'collection_scheduled_at' => 'datetime',
        'tipping_issues' => 'array',
    ];

    protected static function updateBayOccupancy($booking)
    {
        // Update occupancy for old bay if tipping_bay_id changed
        if ($booking->isDirty('tipping_bay_id') && $booking->getOriginal('tipping_bay_id')) {
            $oldBay = \App\Models\TippingBay::find($booking->getOriginal('tipping_bay_id'));
            $oldBay?->syncOccupancyStatus();
        }

        // Update occupancy for current bay
        if ($booking->tipping_bay_id) {
            $currentBay = \App\Models\TippingBay::find($booking->tipping_bay_id);
            $currentBay?->syncOccupancyStatus();
        }
    }

    public function slot()
    {
        return $this->belongsTo(Slot::class);
    }

    public function bookingType()
    {
        return $this->belongsTo(BookingType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }

    public function trailerType()
    {
        return $this->belongsTo(TrailerType::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot(['po_reference', 'cases', 'pallets'])
            ->withTimestamps();
    }

    public function poNumbers()
    {
        return $this->hasMany(BookingPoNumber::class);
    }

    public function customer()
    {
        return $this->belongsTo(\App\Models\Customer::class);
    }

    public function movements()
    {
        return $this->hasMany(Movement::class);
    }


    /**
     * Get or create the primary movement for this booking
     */
    public function getOrCreateMovement(): Movement
    {
        $movement = $this->movements()->first();

        if (! $movement) {
            $movement = Movement::create([
                'movement_type' => 'inbound_booked',
                'reference_number' => $this->booking_reference,
                'depot_id' => $this->slot->depot_id,
                'booking_id' => $this->id,
                'current_status' => 'scheduled',
            ]);
        }

        return $movement;
    }

    /**
     * Get the current movement status through the movement system
     */
    public function getCurrentMovementStatus(): ?string
    {
        $movement = $this->movements()->first();

        return $movement?->current_status;
    }

    public function originalBooking()
    {
        return $this->belongsTo(Booking::class, 'original_booking_id');
    }

    public function rebookedBookings()
    {
        return $this->hasMany(Booking::class, 'original_booking_id');
    }

    public function swapTrailer()
    {
        return $this->belongsTo(Trailer::class, 'swap_trailer_id');
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function history()
    {
        return $this->hasMany(BookingHistory::class);
    }

    public function tippingLocation()
    {
        return $this->hasOneThrough(
            TippingLocation::class,
            Movement::class,
            'booking_id',
            'id',
            'id',
            'tipping_location_id'
        );
    }

    public function tippingBay()
    {
        return $this->belongsTo(TippingBay::class, 'tipping_bay_id');
    }

    public function tippingOperator()
    {
        return $this->belongsTo(User::class, 'tipping_operator_id');
    }

    public function bayAssignedBy()
    {
        return $this->belongsTo(User::class, 'bay_assigned_by');
    }

    public function scopeForUserDepots($query, $user = null)
    {
        $user = $user ?: auth()->user();

        if (! $user) {
            return $query; // Optionally throw or fail if unauthenticated
        }

        $depotIds = $user->depots()->pluck('depots.id')->toArray();

        return $query->whereHas('slot', fn ($q) => $q->whereIn('depot_id', $depotIds));
    }

    /**
     * Generate a unique booking reference
     */
    public static function generateBookingReference(): string
    {
        do {
            // Format: WM-YYYYMMDD-XXXX (e.g., WM-20250802-A1B2)
            $reference = 'WM-'.now()->format('Ymd').'-'.strtoupper(substr(uniqid(), -4));
        } while (self::where('booking_reference', $reference)->exists());

        return $reference;
    }

    public function rebook(Slot $newSlot, string $reason): Booking
    {
        $newBooking = null;
        DB::transaction(function () use ($newSlot, $reason, &$newBooking) {
            // Load PO numbers and lines before creating history
            $this->load(['poNumbers.lines.expectedPalletType', 'poNumbers.lines.actualPalletType']);

            // Capture original booking details for history
            $originalBookingData = [
                'po_numbers' => [],
                'total_expected_units' => $this->total_expected_cases,
                'total_expected_pallets' => $this->total_expected_pallets,
                'total_actual_units' => $this->total_actual_cases,
                'total_actual_pallets' => $this->total_actual_pallets,
            ];

            // Capture detailed PO information
            if ($this->poNumbers && $this->poNumbers->count() > 0) {
                foreach ($this->poNumbers as $po) {
                    $poData = [
                        'po_number' => $po->po_number,
                        'expected_units' => $po->total_expected_units,
                        'expected_pallets' => $po->total_expected_pallets,
                        'actual_units' => $po->total_actual_units,
                        'actual_pallets' => $po->total_actual_pallets,
                        'lines' => [],
                    ];

                    foreach ($po->lines as $line) {
                        $poData['lines'][] = [
                            'line_number' => $line->line_number,
                            'expected_cases' => $line->expected_cases,
                            'actual_cases' => $line->actual_cases,
                            'expected_pallets' => $line->expected_pallets,
                            'actual_pallets' => $line->actual_pallets,
                            'expected_pallet_type' => $line->expectedPalletType?->name,
                            'actual_pallet_type' => $line->actualPalletType?->name,
                            'pallet_type_variance' => $line->pallet_type_variance,
                        ];
                    }
                    $originalBookingData['po_numbers'][] = $poData;
                }
            }

            // Record history for original booking with PO details
            \App\Models\BookingHistory::recordAction(
                $this,
                'rebooked',
                $reason,
                $this->slot,
                $newSlot,
                [
                    'original_booking_data' => $originalBookingData,
                    'new_slot_details' => [
                        'slot_id' => $newSlot->id,
                        'start_time' => $newSlot->start_at->toISOString(),
                        'end_time' => $newSlot->end_at->toISOString(),
                        'depot' => $newSlot->depot->name,
                    ],
                ]
            );

            // Create new booking
            $newBooking = $this->replicate();
            $newBooking->slot_id = $newSlot->id;
            $newBooking->original_booking_id = $this->original_booking_id ?: $this->id;
            $newBooking->rebook_reason = $reason;
            $newBooking->is_rebooked = true;
            $newBooking->rebook_count = $this->rebook_count + 1;
            $newBooking->booking_reference = self::generateBookingReference();

            // Clear arrival/departure data for new booking
            $newBooking->arrived_at = null;
            $newBooking->departed_at = null;
            $newBooking->departure_vehicle_registration = null;

            $newBooking->save();

            // Copy PO numbers and their lines to the new booking
            if ($this->poNumbers && $this->poNumbers->count() > 0) {
                foreach ($this->poNumbers as $originalPo) {
                    // Create new PO number record
                    $newPo = \App\Models\BookingPoNumber::create([
                        'booking_id' => $newBooking->id,
                        'po_number' => $originalPo->po_number,
                    ]);

                    // Copy all PO lines
                    foreach ($originalPo->lines as $originalLine) {
                        \App\Models\PoLine::create([
                            'booking_po_number_id' => $newPo->id,
                            'line_number' => $originalLine->line_number,
                            'expected_cases' => $originalLine->expected_cases,
                            'expected_pallets' => $originalLine->expected_pallets,
                            'expected_pallet_type_id' => $originalLine->expected_pallet_type_id,
                            'actual_cases' => null, // Reset actual values for new booking
                            'actual_pallets' => null,
                            'actual_pallet_type_id' => null,
                        ]);
                    }
                }
            }

            // Mark original as cancelled
            $this->update([
                'cancelled_at' => now(),
                'cancellation_reason' => "Rebooked to {$newSlot->start_at->format('Y-m-d H:i')}",
                'cancelled_by' => auth()->id(),
            ]);

            // Record history for new booking
            \App\Models\BookingHistory::recordAction(
                $newBooking,
                'created',
                "Rebooked from slot {$this->slot->start_at->format('Y-m-d H:i')}",
                $this->slot,
                $newSlot,
                [
                    'rebooked_from_booking_id' => $this->id,
                    'copied_po_data' => $originalBookingData,
                    'rebook_reason' => $reason,
                ]
            );
        });

        return $newBooking;
    }

    public function cancel(string $reason): bool
    {
        \App\Models\BookingHistory::recordAction($this, 'cancelled', $reason, $this->slot);

        return $this->update([
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
            'cancelled_by' => auth()->id(),
        ]);
    }

    public function isCancelled(): bool
    {
        // Check if cancelled_at column exists (for when migrations haven't run yet)
        if (! Schema::hasColumn('bookings', 'cancelled_at')) {
            return false;
        }

        return ! is_null($this->cancelled_at);
    }

    public function isLastMinuteAction(): bool
    {
        $hoursBeforeSlot = now()->diffInHours($this->slot->start_at, false);

        return abs($hoursBeforeSlot) < 24;
    }

    public function getCustomerRebookPattern(): array
    {
        return \App\Models\BookingHistory::where('customer_id', $this->customer_id)
            ->recentActivity(30)
            ->where('action', 'rebooked')
            ->selectRaw('COUNT(*) as total_rebooks')
            ->selectRaw('SUM(CASE WHEN is_last_minute = 1 THEN 1 ELSE 0 END) as last_minute_rebooks')
            ->selectRaw('AVG(hours_before_slot) as avg_hours_notice')
            ->first()
            ->toArray();
    }

    public function scopeActive($query)
    {
        return $query->whereNull('cancelled_at');
    }

    public function scopeCancelled($query)
    {
        return $query->whereNotNull('cancelled_at');
    }

    public function scopeRebooked($query)
    {
        return $query->where('is_rebooked', true);
    }

    // Movement-based workflow methods
    public function dropTrailer(TippingLocation $location, ?string $notes = null): bool
    {
        $movement = $this->getOrCreateMovement();

        if (! in_array($movement->current_status, ['scheduled', 'arrived', 'in_parking'])) {
            return false;
        }

        $movement->update([
            'tipping_location_id' => $location->id,
            'current_status' => 'in_parking',
            'in_parking_at' => now(),
            'operation_notes' => $notes ? ($movement->operation_notes ? $movement->operation_notes."\n".$notes : $notes) : $movement->operation_notes,
        ]);

        BookingHistory::recordAction(
            $this,
            'modified',
            "Trailer dropped at location: {$location->name}",
            null,
            null,
            ['location_id' => $location->id, 'location_name' => $location->name, 'action_type' => 'in_parking']
        );

        return true;
    }

    public function moveToBay(TippingBay $bay, ?string $notes = null): bool
    {
        $movement = $this->getOrCreateMovement();

        if (! in_array($movement->current_status, ['scheduled', 'in_parking', 'in_parking', 'arrived'])) {
            return false;
        }

        if (! $bay->isAvailable()) {
            return false;
        }

        \DB::transaction(function () use ($bay, $notes, $movement) {
            $movement->update([
                'tipping_bay_id' => $bay->id,
                'current_status' => 'unloading', // Start tipping immediately
                'moved_to_bay_at' => now(),
                'unloading_started_at' => now(), // Start tipping timer
                'custom_fields' => array_merge(
                    $movement->custom_fields ?? [],
                    ['moved_to_bay_at' => now()->toISOString()]
                ),
                'operation_notes' => $notes ? ($movement->operation_notes ? $movement->operation_notes."\n".$notes : $notes) : $movement->operation_notes,
            ]);

            // Also update booking status
            $this->update([
                'tipping_started_at' => now(),
                'tipping_status' => 'tipping_in_progress'
            ]);

            $bay->markOccupied($this);
        });

        BookingHistory::recordAction(
            $this,
            'modified',
            "Trailer moved to bay: {$bay->name}",
            null,
            null,
            ['bay_id' => $bay->id, 'bay_name' => $bay->name, 'action_type' => 'moved_to_bay']
        );

        return true;
    }

    /**
     * Move trailer directly to bay without drop location (skip in_parking stage)
     */
    public function moveDirectlyToBay(TippingBay $bay, ?string $notes = null): bool
    {
        $movement = $this->getOrCreateMovement();

        if (! in_array($movement->current_status, ['scheduled', 'arrived', 'in_parking'])) {
            return false;
        }

        if (! $bay->isAvailable()) {
            return false;
        }

        \DB::transaction(function () use ($bay, $notes, $movement) {
            $movement->update([
                'tipping_bay_id' => $bay->id,
                'current_status' => 'at_bay',
                'moved_to_bay_at' => now(),
                'operation_notes' => $notes ? ($movement->operation_notes ? $movement->operation_notes."\n".$notes : $notes) : $movement->operation_notes,
            ]);

            // Mark bay as occupied - this will record the history action
            $bay->markOccupied($this);
        });

        return true;
    }

    /**
     * Transfer booking from current bay to another bay
     */
    public function transferToBay(TippingBay $newBay, ?string $reason = null): bool
    {
        $movement = $this->getOrCreateMovement();

        // Can only transfer if currently assigned to a bay
        if (! $movement->tipping_bay_id || ! in_array($movement->current_status, ['at_bay', 'unloading'])) {
            return false;
        }

        if (! $newBay->isAvailable()) {
            return false;
        }

        \DB::transaction(function () use ($newBay, $reason, $movement) {
            // Mark current bay as available
            if ($this->tippingBay) {
                $this->tippingBay->markAvailable();
            }

            // Move to new bay
            $movement->update([
                'tipping_bay_id' => $newBay->id,
                'bay_transferred_at' => now(),
                'bay_transfer_reason' => $reason,
                'operation_notes' => ($movement->operation_notes ?? '')."\n[Bay Transfer] ".now()->format('M j H:i')." - Transferred to {$newBay->name}".($reason ? ": {$reason}" : ''),
            ]);

            $newBay->markOccupied($this);
        });

        return true;
    }

    public function startTipping(?int $operatorId = null, ?string $notes = null): bool
    {
        $movement = $this->getOrCreateMovement();

        if ($movement->current_status !== 'at_bay') {
            return false;
        }

        $movement->update([
            'current_status' => 'unloading',
            'unloading_started_at' => now(),
            'operation_notes' => $notes ? ($movement->operation_notes ? $movement->operation_notes."\n".$notes : $notes) : $movement->operation_notes,
        ]);

        $bayName = $movement->tippingBay?->name ?? 'Unknown Bay';

        BookingHistory::recordAction(
            $this,
            'modified',
            "Tipping started in bay: {$bayName}",
            null,
            null,
            ['bay_id' => $movement->tipping_bay_id, 'operator_id' => $operatorId, 'action_type' => 'tipping_started']
        );

        return true;
    }

    public function completeTipping(?string $notes = null, ?array $issues = null, bool $immediateDepart = false): bool
    {
        $movement = $this->getOrCreateMovement();

        if ($movement->current_status !== 'unloading') {
            return false;
        }

        $duration = null;
        if ($movement->unloading_started_at) {
            $duration = now()->diffInMinutes($movement->unloading_started_at);
        }

        // Free up current bay if any
        if ($movement->tippingBay) {
            $movement->tippingBay->markAvailable($this);
        }
        
        $movement->update([
            'current_status' => 'empty',
            'tipping_bay_id' => null, // Clear bay when tipping is completed
            'unloading_completed_at' => now(),
            'operation_notes' => $notes ? ($movement->operation_notes ? $movement->operation_notes."\n".$notes : $notes) : $movement->operation_notes,
        ]);

        BookingHistory::recordAction(
            $this,
            'modified',
            "Tipping completed in {$duration} minutes",
            null,
            null,
            ['duration_minutes' => $duration, 'issues' => $issues, 'action_type' => 'tipping_completed']
        );

        // If immediate departure is requested, mark as departed at the same time
        if ($immediateDepart) {
            $movement->update([
                'current_status' => 'departed',
                'departed_at' => $movement->unloading_completed_at,
            ]);

            $this->update([
                'status' => 'completed',
            ]);

            BookingHistory::recordAction(
                $this,
                'modified',
                'Vehicle departed immediately after tipping completion',
                null,
                null,
                ['departure_time' => $movement->unloading_completed_at, 'action_type' => 'immediate_departure']
            );
        }

        return true;
    }

    public function trailerDepart(?string $notes = null): bool
    {
        $movement = $this->getOrCreateMovement();

        if (! in_array($movement->current_status, ['empty', 'at_bay', 'in_parking'])) {
            return false;
        }

        // Check if trailer is being left on site
        $trailerLeftOnSite = $movement->custom_fields['trailer_left_on_site'] ?? false;
        $departureTime = now();
        
        \DB::transaction(function () use ($notes, $movement, $trailerLeftOnSite, $departureTime) {
            // Always record unit departure time
            $this->update([
                'status' => 'completed',
                'departed_at' => $departureTime, // Always set - this is when the UNIT left
            ]);
            
            if ($trailerLeftOnSite) {
                // Unit departed, trailer dropped on site
                $newStatus = $movement->custom_fields['trailer_status'] === 'empty_available' ? 'empty' : 'in_parking';
                
                $movement->update([
                    'current_status' => $newStatus,
                    'actual_departure' => $departureTime,
                    // trailer_collected_at stays NULL - trailer still on site
                    'operation_notes' => $notes ? ($movement->operation_notes ? $movement->operation_notes."\n".$notes : $notes) : $movement->operation_notes,
                ]);
                
                // Note: trailer_collected_at is tracked in movements table
                
                // Free up bay if occupied (trailer moved to drop zone)
                if ($movement->tippingBay && in_array($movement->current_status, ['at_bay', 'empty'])) {
                    $movement->tippingBay->markAvailable($this);
                    $movement->update(['tipping_bay_id' => null]);
                }
            } else {
                // Unit and trailer departed together
                $movement->update([
                    'current_status' => 'departed',
                    'actual_departure' => $departureTime,
                    'trailer_collected_at' => $departureTime, // Collected at same time as departure
                    'operation_notes' => $notes ? ($movement->operation_notes ? $movement->operation_notes."\n".$notes : $notes) : $movement->operation_notes,
                ]);
                
                // Note: trailer_collected_at is tracked in movements table
                
                // Free up bay if occupied
                if ($movement->tippingBay) {
                    $movement->tippingBay->markAvailable($this);
                }
            }
        });

        BookingHistory::recordAction(
            $this,
            'completed',
            $trailerLeftOnSite ? 'Unit departed - trailer dropped on site awaiting collection' : 'Unit and trailer departed together',
            null,
            null,
            ['final_status' => $movement->current_status, 'action_type' => 'trailer_departed', 'trailer_left_on_site' => $trailerLeftOnSite]
        );

        return true;
    }

    /**
     * Record trailer collection (when vehicle arrives to collect empty trailer)
     */
    public function collectTrailer(?string $notes = null, ?string $collectionVehicle = null): bool
    {
        $movement = $this->getOrCreateMovement();

        // Can only collect if trailer is empty or ready
        if (!in_array($movement->current_status, ['empty', 'in_parking'])) {
            return false;
        }

        \DB::transaction(function () use ($notes, $collectionVehicle, $movement) {
            // Update movement to collected status
            $movement->update([
                'current_status' => 'trailer_collected',
                'trailer_collected_at' => now(),
                'actual_departure' => now(),
                'operation_notes' => $notes ? ($movement->operation_notes ? $movement->operation_notes . "\n" . $notes : $notes) : $movement->operation_notes,
            ]);

            // Free up the bay if occupied
            if ($movement->tippingBay) {
                $movement->tippingBay->markAvailable($this);
                $movement->update(['tipping_bay_id' => null]);
            }

            // Note: trailer_collected_at is tracked in movements table, just update booking status
            $this->update([
                'status' => 'completed',
                'departure_vehicle_registration' => $collectionVehicle,
            ]);
        });

        BookingHistory::recordAction(
            $this,
            'completed',
            'Trailer collected and departed from site',
            null,
            null,
            ['collection_time' => now(), 'action_type' => 'trailer_collected', 'collection_vehicle' => $collectionVehicle]
        );

        return true;
    }

    public function getTippingStatusBadgeAttribute(): string
    {
        $movement = $this->movements()->first();
        $movementStatus = $this->getCurrentMovementStatus();
        $isEmptyTrailer = $movement && in_array($movement->current_status, ['empty', 'departed']) && $movement->unloading_completed_at;
        
        // Calculate timing information
        $timeInfo = $this->getTimingInfo();
        $timingColor = $this->getTimingColorClass($timeInfo);
        
        // Location is displayed separately in the view
        
        $statusConfig = [
            'scheduled' => ['class' => 'bg-gray-100 text-gray-800', 'label' => '⏳ Scheduled'],
            'en_route' => ['class' => 'bg-blue-100 text-blue-800', 'label' => '🚛 En Route'],
            'arrived' => ['class' => 'bg-indigo-100 text-indigo-800', 'label' => '📍 Arrived'],
            'in_parking' => ['class' => 'bg-yellow-100 text-yellow-800', 'label' => '⏸️ Waiting'],
            'in_parking' => [
                'class' => $isEmptyTrailer ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800',
                'label' => $isEmptyTrailer ? '✅ Empty Unit in Location' : '🚛 Unit & Trailer in Location'
            ],
            'in_parking' => [
                'class' => $isEmptyTrailer ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800',
                'label' => $isEmptyTrailer ? '✅ Empty Trailer Dropped' : '📍 Trailer Dropped (Loaded)'
            ],
            'at_bay' => [
                'class' => $isEmptyTrailer ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800',
                'label' => $isEmptyTrailer ? '✅ Empty Unit at Bay' : '🚛 Unit at Bay'
            ],
            'unloading' => ['class' => 'bg-orange-100 text-orange-800', 'label' => '⚡ Unloading'],
            'empty' => ['class' => 'bg-green-100 text-green-800', 'label' => '✅ Empty'],
            'loading' => ['class' => 'bg-orange-100 text-orange-800', 'label' => '⚡ Loading'],
            'loaded' => ['class' => 'bg-teal-100 text-teal-800', 'label' => '📦 Loaded'],
            'ready_to_depart' => ['class' => 'bg-emerald-100 text-emerald-800', 'label' => '🚀 Ready'],
            'departed' => ['class' => 'bg-purple-100 text-purple-800', 'label' => '🏁 Departed'],
            'trailer_collected' => ['class' => 'bg-purple-100 text-purple-800', 'label' => '🔄 Collected'],
        ];

        $config = $statusConfig[$movementStatus] ?? $statusConfig['scheduled'];
        
        // Override with timing-based color if applicable
        if ($timeInfo && in_array($movementStatus, ['arrived', 'in_parking', 'in_parking', 'in_parking', 'at_bay'])) {
            $config['class'] = $timingColor;
        }

        $badge = '<span class="px-2 py-1 text-xs rounded '.$config['class'].'">'.$config['label'];
        
        // Add timing information
        if ($timeInfo) {
            $badge .= '<br><small class="opacity-75">' . $timeInfo . '</small>';
        }
        
        // Location information is displayed separately in the view
        // so we don't include it in the badge to avoid duplication
        
        $badge .= '</span>';

        return $badge;
    }

    /**
     * Get timing information for the booking relative to scheduled time
     */
    public function getTimingInfo(): ?string
    {
        if (!$this->slot || !$this->arrived_at) {
            return null;
        }

        $scheduledStart = $this->slot->start_at;
        $actualArrival = $this->arrived_at;
        
        if (!$scheduledStart) {
            return null;
        }

        // Use ArrivalTimeSetting rules to determine status
        $status = ArrivalTimeSetting::determineArrivalStatus(
            $scheduledStart,
            $actualArrival,
            $this->customer_id,
            $this->slot->depot_id
        );
        
        $diffMinutes = round(abs($actualArrival->diffInMinutes($scheduledStart)));
        
        switch ($status) {
            case ArrivalTimeSetting::STATUS_ON_TIME:
                return 'On Time';
            case ArrivalTimeSetting::STATUS_EARLY:
                if ($diffMinutes < 60) {
                    return $diffMinutes . 'm Early';
                } else {
                    $hours = floor($diffMinutes / 60);
                    $mins = $diffMinutes % 60;
                    return $hours . 'h ' . ($mins > 0 ? $mins . 'm' : '') . ' Early';
                }
            case ArrivalTimeSetting::STATUS_LATE:
                if ($diffMinutes < 60) {
                    return $diffMinutes . 'm Late';
                } else {
                    $hours = floor($diffMinutes / 60);
                    $mins = $diffMinutes % 60;
                    return $hours . 'h ' . ($mins > 0 ? $mins . 'm' : '') . ' Late';
                }
            default:
                return null;
        }
    }

    /**
     * Get color class based on timing
     */
    public function getTimingColorClass(?string $timeInfo): string
    {
        if (!$timeInfo || !$this->slot || !$this->arrived_at) {
            return 'bg-gray-100 text-gray-800';
        }

        // Use ArrivalTimeSetting to get proper status and colors
        $statusDetails = ArrivalTimeSetting::getArrivalStatusDetails(
            $this->slot->start_at,
            $this->arrived_at,
            $this->customer_id,
            $this->slot->depot_id
        );
        
        // Map ArrivalTimeSetting CSS classes to our badge format
        switch ($statusDetails['status']) {
            case ArrivalTimeSetting::STATUS_ON_TIME:
                return 'bg-green-100 text-green-800';
            case ArrivalTimeSetting::STATUS_EARLY:
                return 'bg-yellow-100 text-yellow-800';
            case ArrivalTimeSetting::STATUS_LATE:
                return 'bg-red-100 text-red-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }


    public function canProgressToNextTippingStage(): bool
    {
        $movementStatus = $this->getCurrentMovementStatus();

        return match ($movementStatus) {
            'scheduled', 'en_route', 'arrived', 'in_parking' => true, // Can drop trailer or move to bay
            'in_parking' => true, // Can move to bay
            'at_bay' => true, // Can start unloading
            'unloading' => true, // Can complete unloading
            'empty', 'loaded', 'ready_to_depart' => true, // Can depart
            default => false
        };
    }

    public function scopeByTippingStatus($query, string $status)
    {
        return $query->whereHas('movements', function ($q) use ($status) {
            $q->where('current_status', $status);
        });
    }

    public function scopeInProgress($query)
    {
        return $query->whereHas('movements', function ($q) {
            $q->whereIn('current_status', [
                'in_parking',
                'at_bay',
                'unloading',
            ]);
        });
    }

    public function getTotalExpectedCasesAttribute(): int
    {
        return $this->poNumbers->sum(function ($po) {
            return $po->total_expected_cases;
        }) ?? 0;
    }

    public function getTotalActualCasesAttribute(): int
    {
        return $this->poNumbers->sum(function ($po) {
            return $po->total_actual_cases;
        }) ?? 0;
    }

    public function getTotalExpectedPalletsAttribute(): int
    {
        return $this->poNumbers->sum(function ($po) {
            return $po->total_expected_pallets;
        }) ?? 0;
    }

    public function getTotalActualPalletsAttribute(): int
    {
        return $this->poNumbers->sum(function ($po) {
            return $po->total_actual_pallets;
        }) ?? 0;
    }

    public function getTotalCaseVarianceAttribute(): int
    {
        return $this->total_actual_cases - $this->total_expected_cases;
    }

    public function getTotalPalletVarianceAttribute(): int
    {
        return $this->total_actual_pallets - $this->total_expected_pallets;
    }

    public function hasPoVariances(): bool
    {
        return $this->poNumbers->some(function ($po) {
            return $po->hasVariance();
        });
    }

    public function hasTypeVariances(): bool
    {
        return $this->poNumbers->some(function ($po) {
            return $po->hasTypeVariances();
        });
    }

    public function getTotalLinesCountAttribute(): int
    {
        return $this->poNumbers->sum(function ($po) {
            return $po->lines->count();
        });
    }

    // Vehicle detail accessors - retrieve from movement additional_data, then vehicle_details JSON
    public function getVehicleRegistrationAttribute(): ?string
    {
        return $this->movements->first()?->additional_data['vehicle_registration'] 
            ?? ($this->vehicle_details['vehicle_registration'] ?? null);
    }

    public function getContainerNumberAttribute(): ?string
    {
        return $this->movements->first()?->additional_data['container_number'] 
            ?? ($this->vehicle_details['container_number'] ?? null);
    }

    public function getCarrierCompanyAttribute(): ?string
    {
        return $this->movements->first()?->additional_data['carrier_company'] 
            ?? ($this->vehicle_details['carrier_company'] ?? null)
            ?? $this->carrier?->name;
    }

    public function getGateNumberAttribute(): ?string
    {
        return $this->movements->first()?->additional_data['gate_number'] 
            ?? ($this->vehicle_details['gate_number'] ?? null);
    }

    public function getTrailerSizeAttribute(): ?string
    {
        return $this->movements->first()?->additional_data['trailer_size'] 
            ?? ($this->vehicle_details['trailer_size'] ?? null);
    }
    
    public function getEstimatedArrivalAttribute()
    {
        if (!$this->vehicle_details || empty($this->vehicle_details['estimated_arrival'])) {
            return null;
        }
        
        return \Carbon\Carbon::parse($this->vehicle_details['estimated_arrival']);
    }
    
    public function getSpecialInstructionsAttribute(): ?string
    {
        return $this->vehicle_details['special_instructions'] ?? null;
    }

    public function getWaitingAreaLocationAttribute(): ?string
    {
        return $this->movements->first()?->custom_fields['waiting_area_location'] 
            ?? ($this->vehicle_details['waiting_area_location'] ?? null);
    }

    // Backwards compatibility accessors for views
    public function getTippingStatusAttribute(): ?string
    {
        return $this->getCurrentMovementStatus();
    }

    public function getTrailerDroppedAtAttribute()
    {
        $movement = $this->movements()->first();
        return $movement?->in_parking_at;
    }

    public function getMovedToBayAtAttribute()
    {
        $movement = $this->movements()->first();
        if ($movement && isset($movement->custom_fields['moved_to_bay_at'])) {
            return \Carbon\Carbon::parse($movement->custom_fields['moved_to_bay_at']);
        }
        // Fallback for legacy data or if we're currently at bay
        if ($movement && $movement->current_status === 'at_bay' && $movement->tipping_bay_id) {
            return $movement->updated_at;
        }
        return null;
    }

    public function getTippingStartedAtAttribute()
    {
        $movement = $this->movements()->first();
        return $movement?->unloading_started_at;
    }

    public function getTippingCompletedAtAttribute()
    {
        $movement = $this->movements()->first();
        return $movement?->unloading_completed_at;
    }

    public function getTrailerDepartedAtAttribute()
    {
        $movement = $this->movements()->first();
        return $movement?->actual_departure;
    }

    public function getActualTippingDurationAttribute(): ?int
    {
        $movement = $this->movements()->first();
        if ($movement && $movement->unloading_started_at && $movement->unloading_completed_at) {
            return $movement->unloading_started_at->diffInMinutes($movement->unloading_completed_at);
        }
        return null;
    }

    public function getCurrentLocationAttribute(): ?string
    {
        $movement = $this->movements()->first();
        if (! $movement) {
            return null;
        }

        return match ($movement->current_status) {
            'scheduled', 'en_route', 'arrived' => 'on_site',
            'in_parking' => 'waiting_area',
            'at_bay', 'unloading' => 'tipping_bay',
            'empty', 'loaded', 'ready_to_depart' => 'ready',
            'back_to_parking' => 'parking_area',
            'trailer_dropped' => 'dropped',
            'departed', 'trailer_collected' => 'departed',
            default => 'unknown'
        };
    }


    public function getTrailerLeftOnSiteAttribute(): bool
    {
        $movement = $this->movements()->first();

        return $movement?->custom_fields['trailer_left_on_site'] ?? false;
    }

    public function getTrailerCollectedAtAttribute()
    {
        $movement = $this->movements()->first();

        return $movement?->trailer_collected_at; // Returns Carbon instance from movement
    }

    public function getDroppedTrailerStatusAttribute(): ?string
    {
        $movement = $this->movements()->first();

        return $movement?->custom_fields['trailer_status'] ?? null;
    }

    public function getTippingBayIdAttribute(): ?int
    {
        $movement = $this->movements()->first();

        return $movement?->tipping_bay_id;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_reference)) {
                $booking->booking_reference = self::generateBookingReference();
            }
        });

        static::created(function ($booking) {
            if (! $booking->is_rebooked) {
                \App\Models\BookingHistory::recordAction($booking, 'created', null, null, $booking->slot);
            }
        });

        static::updated(function ($booking) {
            if ($booking->wasChanged('status') && $booking->status === 'completed') {
                \App\Models\BookingHistory::recordAction($booking, 'completed', null, $booking->slot);
            }

            // Update bay occupancy when booking changes
            static::updateBayOccupancy($booking);
        });

        static::saved(function ($booking) {
            // Update bay occupancy when booking is saved
            static::updateBayOccupancy($booking);
        });

        static::deleted(function ($booking) {
            // Update bay occupancy when booking is deleted
            static::updateBayOccupancy($booking);
        });
    }
}
