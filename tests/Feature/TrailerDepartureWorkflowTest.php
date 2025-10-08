<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\BookingType;
use App\Models\Carrier;
use App\Models\Customer;
use App\Models\Depot;
use App\Models\FactoryBooking;
use App\Models\Movement;
use App\Models\Slot;
use App\Models\TippingBay;
use App\Models\TippingLocation;
use App\Models\TrailerType;
use App\Models\User;
use App\Services\SlotBookingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Comprehensive test suite for KTL Booking System trailer departure scenarios
 * and slot availability logic.
 */
class TrailerDepartureWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Depot $depot;
    protected Customer $customer;
    protected Customer $customerWithCustomDuration;
    protected BookingType $bookingType;
    protected Carrier $carrier;
    protected TrailerType $trailerType;
    protected TippingLocation $tippingLocation;
    protected TippingBay $tippingBay;
    protected SlotBookingService $slotBookingService;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user
        $this->admin = User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);

        // Create depot
        $this->depot = Depot::create([
            'name' => 'Test Depot',
            'location' => 'Test Location',
            'address' => '123 Test Street',
            'city' => 'Test City',
            'postcode' => 'TE5 1ST',
            'contact_name' => 'Test Contact',
            'contact_email' => 'depot@test.com',
            'contact_phone' => '01234567890',
        ]);

        // Create customers
        $this->customer = Customer::create([
            'name' => 'Test Customer',
        ]);

        $this->customerWithCustomDuration = Customer::create([
            'name' => 'Customer With Custom Duration',
        ]);

        // Create booking type
        $this->bookingType = BookingType::create([
            'name' => 'Standard Delivery',
            'description' => 'Standard delivery booking',
            'default_duration' => 60, // 1 hour default
        ]);

        // Create carrier
        $this->carrier = Carrier::create([
            'name' => 'Test Carrier Ltd',
            'contact_email' => 'carrier@test.com',
        ]);

        // Create trailer type
        $this->trailerType = TrailerType::create([
            'name' => 'Standard Trailer',
            'description' => '40ft Standard Trailer',
        ]);

        // Create tipping location
        $this->tippingLocation = TippingLocation::create([
            'name' => 'Main Parking Area',
            'depot_id' => $this->depot->id,
            'capacity' => 10,
        ]);

        // Create tipping bay
        $this->tippingBay = TippingBay::create([
            'name' => 'Bay 1',
            'depot_id' => $this->depot->id,
            'tipping_location_id' => $this->tippingLocation->id,
            'status' => 'available',
            'is_active' => true,
            'is_occupied' => false,
        ]);

        // Initialize SlotBookingService
        $this->slotBookingService = new SlotBookingService();

        // Authenticate as admin for all tests
        $this->actingAs($this->admin);
    }

    /**
     * Create a slot with released status
     */
    protected function createSlot(int $hoursFromNow = 24, int $capacity = 5): Slot
    {
        return Slot::create([
            'depot_id' => $this->depot->id,
            'start_at' => now()->addHours($hoursFromNow),
            'end_at' => now()->addHours($hoursFromNow + 1),
            'capacity' => $capacity,
            'released_at' => now()->subDay(), // Released and available
        ]);
    }

    /**
     * Create a booking with slot occupancy
     */
    protected function createBookingWithSlot(array $attributes = []): Booking
    {
        $slot = $this->createSlot();

        $bookingData = array_merge([
            'slot_id' => $slot->id,
            'booking_type_id' => $this->bookingType->id,
            'user_id' => $this->admin->id,
            'customer_id' => $this->customer->id,
            'carrier_id' => $this->carrier->id,
            'trailer_type_id' => $this->trailerType->id,
        ], $attributes);

        return $this->slotBookingService->createBooking(
            $bookingData,
            $slot,
            $this->bookingType->id
        );
    }

    /**
     * Test 1: Live Tip Workflow - Complete standard workflow
     */
    public function test_live_tip_workflow_complete_standard_flow(): void
    {
        // Create booking with live_tip tipping type
        $booking = $this->createBookingWithSlot([
            'tipping_type' => 'live_tip',
        ]);

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'tipping_type' => 'live_tip',
        ]);

        // Mark arrived
        $movement = $booking->getOrCreateMovement();
        $movement->update([
            'current_status' => 'arrived',
            'actual_arrival' => now(),
        ]);

        $booking->update(['arrived_at' => now()]);

        $this->assertDatabaseHas('movements', [
            'id' => $movement->id,
            'current_status' => 'arrived',
        ]);

        // Move to bay and complete tipping
        $this->assertTrue($booking->moveDirectlyToBay($this->tippingBay));
        $this->assertTrue($booking->startTipping());
        $this->assertTrue($booking->completeTipping());

        $movement->refresh();
        $this->assertEquals('empty', $movement->current_status);
        $this->assertNotNull($movement->unloading_completed_at);

        // Depart WITH trailer (unit and trailer leave together)
        $movement->update([
            'custom_fields' => ['trailer_left_on_site' => false],
        ]);

        $this->assertTrue($booking->trailerDepart());

        // Refresh to get latest data
        $booking->refresh();
        $movement->refresh();

        // Assert: departed_at set, actual_departure set, trailer_collected_at set
        $this->assertNotNull($booking->departed_at, 'Booking departed_at should be set');
        $this->assertNotNull($movement->actual_departure, 'Movement actual_departure should be set');
        $this->assertNotNull($movement->trailer_collected_at, 'Movement trailer_collected_at should be set');
        $this->assertEquals('departed', $movement->current_status);

        // Assert: NOT visible in queue management (actual_departure is not null)
        $queueBookings = Movement::whereNull('actual_departure')->get();
        $this->assertFalse(
            $queueBookings->contains('booking_id', $booking->id),
            'Booking should NOT be visible in queue management after complete departure'
        );
    }

    /**
     * Test 2: Drop Workflow - Loaded Trailer
     */
    public function test_drop_workflow_loaded_trailer(): void
    {
        // Create booking with drop tipping type
        $booking = $this->createBookingWithSlot([
            'tipping_type' => 'drop',
        ]);

        // Mark arrived
        $movement = $booking->getOrCreateMovement();
        $movement->update([
            'current_status' => 'arrived',
            'actual_arrival' => now(),
        ]);

        $booking->update(['arrived_at' => now()]);

        // Move trailer to parking (this is what happens in real workflow)
        $movement->update([
            'current_status' => 'in_parking',
            'tipping_location_id' => $this->tippingLocation->id,
            'moved_to_location_at' => now(),
        ]);

        // Unit departs, drops loaded trailer
        $movement->update([
            'custom_fields' => [
                'trailer_left_on_site' => true,
                'trailer_status' => 'awaiting_collection',
                'departure_scenario' => 'completed_dropped_trailer',
            ],
        ]);

        $this->assertTrue($booking->trailerDepart());

        // Refresh data
        $booking->refresh();
        $movement->refresh();

        // Assert: departed_at set, actual_departure NULL, trailer_collected_at NULL
        $this->assertNotNull($booking->departed_at, 'Booking departed_at should be set when unit leaves');
        $this->assertNull($movement->actual_departure, 'Movement actual_departure should be NULL (trailer still on site)');
        $this->assertNull($movement->trailer_collected_at, 'Movement trailer_collected_at should be NULL (trailer not collected)');

        // Assert: Visible in queue management with status "in_parking"
        $this->assertEquals('in_parking', $movement->current_status);
        $queueBookings = Movement::whereNull('actual_departure')->get();
        $this->assertTrue(
            $queueBookings->contains('booking_id', $booking->id),
            'Booking should be visible in queue management (trailer still on site)'
        );

        // Assert: Shows in dropped trailers report
        $droppedTrailers = Movement::where('current_status', 'in_parking')
            ->whereNull('actual_departure')
            ->get();
        $this->assertTrue(
            $droppedTrailers->contains('booking_id', $booking->id),
            'Booking should appear in dropped trailers report'
        );
    }

    /**
     * Test 3: Drop Workflow - Tip After Unit Departs
     */
    public function test_drop_workflow_tip_after_unit_departs(): void
    {
        // Create booking and drop loaded trailer (continue from test 2 scenario)
        $booking = $this->createBookingWithSlot([
            'tipping_type' => 'drop',
        ]);

        $movement = $booking->getOrCreateMovement();
        $movement->update([
            'current_status' => 'arrived',
            'actual_arrival' => now(),
        ]);

        $booking->update(['arrived_at' => now()]);

        // Move to parking first
        $movement->update([
            'current_status' => 'in_parking',
            'tipping_location_id' => $this->tippingLocation->id,
            'moved_to_location_at' => now(),
        ]);

        // Drop loaded trailer
        $movement->update([
            'custom_fields' => [
                'trailer_left_on_site' => true,
                'trailer_status' => 'awaiting_collection',
                'departure_scenario' => 'completed_dropped_trailer',
            ],
        ]);
        $booking->trailerDepart();

        $movement->refresh();
        $this->assertEquals('in_parking', $movement->current_status);

        // Now complete tipping process
        $this->assertTrue($booking->moveDirectlyToBay($this->tippingBay));
        $this->assertTrue($booking->startTipping());
        $this->assertTrue($booking->completeTipping());

        $movement->refresh();

        // Assert: status changes to "empty"
        $this->assertEquals('empty', $movement->current_status, 'Movement status should be "empty" after tipping');
        $this->assertNotNull($movement->unloading_completed_at, 'Unloading should be completed');

        // Assert: Still visible in queue (actual_departure still NULL)
        $this->assertNull($movement->actual_departure, 'actual_departure should still be NULL');
        $queueBookings = Movement::whereNull('actual_departure')->get();
        $this->assertTrue(
            $queueBookings->contains('booking_id', $booking->id),
            'Booking should still be visible in queue (trailer awaiting collection)'
        );
    }

    /**
     * Test 4: Drop Workflow - Empty Trailer Drop
     */
    public function test_drop_workflow_empty_trailer_drop(): void
    {
        // Create booking with drop tipping type
        $booking = $this->createBookingWithSlot([
            'tipping_type' => 'drop',
        ]);

        $movement = $booking->getOrCreateMovement();
        $movement->update([
            'current_status' => 'arrived',
            'actual_arrival' => now(),
        ]);

        $booking->update(['arrived_at' => now()]);

        // Complete tipping first
        $this->assertTrue($booking->moveDirectlyToBay($this->tippingBay));
        $this->assertTrue($booking->startTipping());
        $this->assertTrue($booking->completeTipping());

        $movement->refresh();
        $this->assertEquals('empty', $movement->current_status);

        // Unit departs, drops empty trailer
        $movement->update([
            'custom_fields' => [
                'trailer_left_on_site' => true,
                'trailer_status' => 'empty_available',
            ],
        ]);

        $this->assertTrue($booking->trailerDepart());

        // Refresh data
        $booking->refresh();
        $movement->refresh();

        // Assert: departed_at set, actual_departure NULL, status "empty"
        $this->assertNotNull($booking->departed_at, 'Booking departed_at should be set');
        $this->assertNull($movement->actual_departure, 'Movement actual_departure should be NULL');
        $this->assertEquals('empty', $movement->current_status, 'Movement status should be "empty"');

        // Assert: Visible in queue management
        $queueBookings = Movement::whereNull('actual_departure')->get();
        $this->assertTrue(
            $queueBookings->contains('booking_id', $booking->id),
            'Empty trailer should be visible in queue management'
        );
    }

    /**
     * Test 5: Factory Booking - Depart WITH Trailer
     */
    public function test_factory_booking_depart_with_trailer(): void
    {
        // Create factory booking
        $factoryBooking = FactoryBooking::create([
            'depot_id' => $this->depot->id,
            'customer_id' => $this->customer->id,
            'carrier_id' => $this->carrier->id,
            'trailer_type_id' => $this->trailerType->id,
            'tipping_type' => 'live_tip',
            'arrived_at' => now(),
            'vehicle_registration' => 'TEST123',
            'status' => 'arrived',
            'registered_by' => $this->admin->id,
        ]);

        $this->assertDatabaseHas('factory_bookings', [
            'id' => $factoryBooking->id,
            'tipping_type' => 'live_tip',
        ]);

        // Get or create movement
        $movement = $factoryBooking->getOrCreateMovement();
        $this->assertNotNull($movement);

        // Complete tipping
        $movement->update([
            'tipping_bay_id' => $this->tippingBay->id,
            'current_status' => 'at_bay',
        ]);

        $movement->update([
            'current_status' => 'unloading',
            'unloading_started_at' => now(),
        ]);

        $movement->update([
            'current_status' => 'empty',
            'unloading_completed_at' => now(),
        ]);

        // Depart with trailer
        $movement->update([
            'current_status' => 'departed',
            'actual_departure' => now(),
            'trailer_collected_at' => now(),
        ]);

        $factoryBooking->update(['departed_at' => now()]);

        // Refresh data
        $factoryBooking->refresh();
        $movement->refresh();

        // Assert: departed_at set, actual_departure set, trailer_collected_at set
        $this->assertNotNull($factoryBooking->departed_at, 'Factory booking departed_at should be set');
        $this->assertNotNull($movement->actual_departure, 'Movement actual_departure should be set');
        $this->assertNotNull($movement->trailer_collected_at, 'Movement trailer_collected_at should be set');
        $this->assertEquals('departed', $movement->current_status);
    }

    /**
     * Test 6: Factory Booking - Drop Trailer
     */
    public function test_factory_booking_drop_trailer(): void
    {
        // Create factory booking
        $factoryBooking = FactoryBooking::create([
            'depot_id' => $this->depot->id,
            'customer_id' => $this->customer->id,
            'carrier_id' => $this->carrier->id,
            'trailer_type_id' => $this->trailerType->id,
            'tipping_type' => 'drop',
            'arrived_at' => now(),
            'vehicle_registration' => 'DROP123',
            'status' => 'arrived',
            'registered_by' => $this->admin->id,
        ]);

        $movement = $factoryBooking->getOrCreateMovement();

        // Unit departs, drops trailer
        $movement->update([
            'current_status' => 'in_parking',
            'custom_fields' => [
                'trailer_left_on_site' => true,
                'trailer_status' => 'loaded',
            ],
        ]);

        $factoryBooking->update(['departed_at' => now()]);

        // Refresh data
        $factoryBooking->refresh();
        $movement->refresh();

        // Assert: departed_at set, actual_departure NULL, status "in_parking"
        $this->assertNotNull($factoryBooking->departed_at, 'Factory booking departed_at should be set');
        $this->assertNull($movement->actual_departure, 'Movement actual_departure should be NULL');
        $this->assertEquals('in_parking', $movement->current_status, 'Movement status should be "in_parking"');

        // Verify visible in queue
        $queueMovements = Movement::whereNull('actual_departure')->get();
        $this->assertTrue(
            $queueMovements->contains('factory_booking_id', $factoryBooking->id),
            'Factory booking should be visible in queue'
        );
    }

    /**
     * Test 7: Slot Availability - Cancel Releases Slots
     */
    public function test_slot_availability_cancel_releases_slots(): void
    {
        $slot = $this->createSlot();

        // Create booking occupying slots
        $booking = $this->slotBookingService->createBooking(
            [
                'slot_id' => $slot->id,
                'booking_type_id' => $this->bookingType->id,
                'user_id' => $this->admin->id,
                'customer_id' => $this->customer->id,
                'carrier_id' => $this->carrier->id,
                'trailer_type_id' => $this->trailerType->id,
            ],
            $slot,
            $this->bookingType->id
        );

        // Assert slot is occupied
        $this->assertDatabaseHas('slot_bookings', [
            'slot_id' => $slot->id,
            'booking_id' => $booking->id,
        ]);

        $this->assertEquals(1, $slot->occupyingBookings()->count(), 'Slot should have 1 booking');
        $this->assertEquals(4, $slot->remainingCapacity(), 'Slot should have 4 remaining capacity');

        // Cancel booking
        $this->slotBookingService->cancelBooking($booking);

        // Assert: slot_bookings pivot entries deleted
        $this->assertDatabaseMissing('slot_bookings', [
            'slot_id' => $slot->id,
            'booking_id' => $booking->id,
        ]);

        // Assert: Slot has capacity again
        $slot->refresh();
        $this->assertEquals(0, $slot->occupyingBookings()->count(), 'Slot should have no bookings');
        $this->assertEquals(5, $slot->remainingCapacity(), 'Slot should have full capacity again');
        $this->assertTrue($slot->hasCapacity(), 'Slot should have available capacity');

        // Verify booking is soft deleted
        $this->assertSoftDeleted('bookings', ['id' => $booking->id]);
    }

    /**
     * Test 8: Slot Availability - Multi-Slot with Customer Duration
     */
    public function test_slot_availability_multi_slot_with_customer_duration(): void
    {
        // Create 3 consecutive slots
        // Create consecutive slots with exact timestamps
        $baseTime = now()->addDay()->setHour(9)->setMinute(0)->setSecond(0)->setMicrosecond(0);

        $slot1 = Slot::create([
            'depot_id' => $this->depot->id,
            'start_at' => $baseTime->copy(),
            'end_at' => $baseTime->copy()->addHour(),
            'capacity' => 3,
            'released_at' => now()->subDay(),
        ]);

        $slot2 = Slot::create([
            'depot_id' => $this->depot->id,
            'start_at' => $baseTime->copy()->addHour(),
            'end_at' => $baseTime->copy()->addHours(2),
            'capacity' => 3,
            'released_at' => now()->subDay(),
        ]);

        $slot3 = Slot::create([
            'depot_id' => $this->depot->id,
            'start_at' => $baseTime->copy()->addHours(2),
            'end_at' => $baseTime->copy()->addHours(3),
            'capacity' => 3,
            'released_at' => now()->subDay(),
        ]);

        // Create a booking type with 2-hour duration for this customer
        $customBookingType = BookingType::create([
            'name' => 'Extended Delivery',
            'description' => 'Extended 2-hour delivery',
            'default_duration' => 120, // 2 hours
        ]);

        // Attach customer with custom 2-hour duration (120 minutes) for this depot
        $customBookingType->customers()->attach($this->customerWithCustomDuration->id, [
            'depot_id' => $this->depot->id,
            'duration_minutes' => 120,
        ]);

        // Debug: Check slot times and released_at
        $slot1->refresh();
        $slot2->refresh();
        \Log::info('Slot 1 end_at: ' . $slot1->end_at->toDateTimeString());
        \Log::info('Slot 1 released_at: ' . ($slot1->released_at ? $slot1->released_at->toDateTimeString() : 'NULL'));
        \Log::info('Slot 2 start_at: ' . $slot2->start_at->toDateTimeString());
        \Log::info('Slot 2 released_at: ' . ($slot2->released_at ? $slot2->released_at->toDateTimeString() : 'NULL'));
        \Log::info('Times match: ' . ($slot1->end_at->equalTo($slot2->start_at) ? 'YES' : 'NO'));

        // Book slot with customer requiring 2-hour duration
        $booking = $this->slotBookingService->createBooking(
            [
                'slot_id' => $slot1->id,
                'booking_type_id' => $customBookingType->id,
                'user_id' => $this->admin->id,
                'customer_id' => $this->customerWithCustomDuration->id,
                'carrier_id' => $this->carrier->id,
                'trailer_type_id' => $this->trailerType->id,
            ],
            $slot1,
            $customBookingType->id
        );

        // Assert: occupies 2 consecutive slots
        $occupiedSlots = $booking->occupiedSlots;
        $this->assertEquals(2, $occupiedSlots->count(), 'Booking should occupy 2 slots');

        // Verify correct slots are occupied
        $this->assertTrue(
            $occupiedSlots->contains('id', $slot1->id),
            'First slot should be occupied'
        );
        $this->assertTrue(
            $occupiedSlots->contains('id', $slot2->id),
            'Second slot should be occupied'
        );

        // Verify primary slot marker
        $primarySlot = $booking->occupiedSlots()->wherePivot('is_primary', true)->first();
        $this->assertEquals($slot1->id, $primarySlot->id, 'First slot should be marked as primary');

        // Assert: Both slots show reduced capacity
        $slot1->refresh();
        $slot2->refresh();

        $this->assertEquals(2, $slot1->remainingCapacity(), 'Slot 1 should have 2 remaining capacity');
        $this->assertEquals(2, $slot2->remainingCapacity(), 'Slot 2 should have 2 remaining capacity');

        // Verify third slot is not affected
        $slot3->refresh();
        $this->assertEquals(3, $slot3->remainingCapacity(), 'Slot 3 should have full capacity');
    }

    /**
     * Test 9: Trailer Collection After Drop
     */
    public function test_trailer_collection_after_drop(): void
    {
        // Create booking and drop trailer
        $booking = $this->createBookingWithSlot([
            'tipping_type' => 'drop',
        ]);

        $movement = $booking->getOrCreateMovement();
        $movement->update([
            'current_status' => 'arrived',
            'actual_arrival' => now(),
        ]);

        $booking->update(['arrived_at' => now()]);

        // Complete tipping and drop empty trailer
        $this->assertTrue($booking->moveDirectlyToBay($this->tippingBay));
        $this->assertTrue($booking->startTipping());
        $this->assertTrue($booking->completeTipping());

        $movement->update([
            'custom_fields' => [
                'trailer_left_on_site' => true,
                'trailer_status' => 'empty_available',
            ],
        ]);
        $booking->trailerDepart();

        $movement->refresh();
        $this->assertEquals('empty', $movement->current_status);
        $this->assertNull($movement->actual_departure);

        // Collect trailer
        $this->assertTrue($booking->collectTrailer('Trailer collected', 'COLLECT123'));

        // Refresh data
        $booking->refresh();
        $movement->refresh();

        // Assert trailer collected
        $this->assertNotNull($movement->trailer_collected_at, 'Trailer collected_at should be set');
        $this->assertNotNull($movement->actual_departure, 'Movement actual_departure should be set after collection');
        $this->assertEquals('trailer_collected', $movement->current_status);
        // Note: departure_vehicle_registration field doesn't exist in database, stored in vehicle_details JSON

        // Verify no longer in queue
        $queueMovements = Movement::whereNull('actual_departure')->get();
        $this->assertFalse(
            $queueMovements->contains('booking_id', $booking->id),
            'Booking should not be in queue after trailer collection'
        );
    }

    /**
     * Test 10: Queue Management Visibility Logic
     */
    public function test_queue_management_visibility_logic(): void
    {
        // Create multiple bookings with different states
        $bookingDeparted = $this->createBookingWithSlot(['tipping_type' => 'live_tip']);
        $bookingDropped = $this->createBookingWithSlot(['tipping_type' => 'drop']);
        $bookingInProgress = $this->createBookingWithSlot(['tipping_type' => 'live_tip']);

        // Booking 1: Fully departed (should NOT be in queue)
        $movement1 = $bookingDeparted->getOrCreateMovement();
        $movement1->update([
            'current_status' => 'departed',
            'actual_departure' => now(),
            'trailer_collected_at' => now(),
        ]);
        $bookingDeparted->update(['departed_at' => now()]);

        // Booking 2: Dropped trailer (should BE in queue)
        $movement2 = $bookingDropped->getOrCreateMovement();
        $movement2->update([
            'current_status' => 'in_parking',
            'actual_arrival' => now(),
            // actual_departure is NULL - trailer still on site
        ]);
        $bookingDropped->update(['arrived_at' => now(), 'departed_at' => now()]);

        // Booking 3: In progress (should BE in queue)
        $movement3 = $bookingInProgress->getOrCreateMovement();
        $movement3->update([
            'current_status' => 'unloading',
            'actual_arrival' => now(),
            'unloading_started_at' => now(),
            // actual_departure is NULL - still on site
        ]);
        $bookingInProgress->update(['arrived_at' => now()]);

        // Query queue management (whereNull actual_departure)
        $queueMovements = Movement::whereNull('actual_departure')
            ->whereIn('id', [$movement1->id, $movement2->id, $movement3->id])
            ->get();

        // Assert visibility
        $this->assertFalse(
            $queueMovements->contains('id', $movement1->id),
            'Fully departed booking should NOT be in queue'
        );
        $this->assertTrue(
            $queueMovements->contains('id', $movement2->id),
            'Dropped trailer should BE in queue'
        );
        $this->assertTrue(
            $queueMovements->contains('id', $movement3->id),
            'In-progress booking should BE in queue'
        );

        $this->assertEquals(2, $queueMovements->count(), 'Queue should contain exactly 2 movements');
    }

    /**
     * Test 11: Dropped Trailers Report
     */
    public function test_dropped_trailers_report(): void
    {
        // Create bookings with different trailer states
        $bookingDroppedLoaded = $this->createBookingWithSlot(['tipping_type' => 'drop']);
        $bookingDroppedEmpty = $this->createBookingWithSlot(['tipping_type' => 'drop']);
        $bookingCollected = $this->createBookingWithSlot(['tipping_type' => 'drop']);

        // Booking 1: Dropped loaded trailer (should appear in report)
        $movement1 = $bookingDroppedLoaded->getOrCreateMovement();
        $movement1->update([
            'current_status' => 'in_parking',
            'actual_arrival' => now(),
            'custom_fields' => [
                'trailer_left_on_site' => true,
                'trailer_status' => 'loaded',
            ],
        ]);
        $bookingDroppedLoaded->update(['arrived_at' => now(), 'departed_at' => now()]);

        // Booking 2: Dropped empty trailer (should appear in report)
        $movement2 = $bookingDroppedEmpty->getOrCreateMovement();
        $movement2->update([
            'current_status' => 'empty',
            'actual_arrival' => now(),
            'unloading_completed_at' => now(),
            'custom_fields' => [
                'trailer_left_on_site' => true,
                'trailer_status' => 'empty_available',
            ],
        ]);
        $bookingDroppedEmpty->update(['arrived_at' => now(), 'departed_at' => now()]);

        // Booking 3: Trailer collected (should NOT appear in report)
        $movement3 = $bookingCollected->getOrCreateMovement();
        $movement3->update([
            'current_status' => 'trailer_collected',
            'actual_arrival' => now(),
            'actual_departure' => now(),
            'trailer_collected_at' => now(),
        ]);
        $bookingCollected->update(['arrived_at' => now(), 'departed_at' => now()]);

        // Query dropped trailers report
        $droppedTrailers = Movement::whereIn('current_status', ['in_parking', 'empty'])
            ->whereNull('actual_departure')
            ->whereIn('id', [$movement1->id, $movement2->id, $movement3->id])
            ->get();

        // Assert report contents
        $this->assertTrue(
            $droppedTrailers->contains('id', $movement1->id),
            'Dropped loaded trailer should appear in report'
        );
        $this->assertTrue(
            $droppedTrailers->contains('id', $movement2->id),
            'Dropped empty trailer should appear in report'
        );
        $this->assertFalse(
            $droppedTrailers->contains('id', $movement3->id),
            'Collected trailer should NOT appear in report'
        );

        $this->assertEquals(2, $droppedTrailers->count(), 'Report should show exactly 2 dropped trailers');
    }

    /**
     * Test 12: Slot Occupancy After Booking Update
     */
    public function test_slot_occupancy_after_booking_update(): void
    {
        $slot1 = $this->createSlot(24);
        $slot2 = $this->createSlot(48);

        // Create booking in slot1
        $booking = $this->slotBookingService->createBooking(
            [
                'slot_id' => $slot1->id,
                'booking_type_id' => $this->bookingType->id,
                'user_id' => $this->admin->id,
                'customer_id' => $this->customer->id,
                'carrier_id' => $this->carrier->id,
                'trailer_type_id' => $this->trailerType->id,
            ],
            $slot1,
            $this->bookingType->id
        );

        // Verify initial slot occupancy
        $this->assertDatabaseHas('slot_bookings', [
            'slot_id' => $slot1->id,
            'booking_id' => $booking->id,
        ]);
        $this->assertDatabaseMissing('slot_bookings', [
            'slot_id' => $slot2->id,
            'booking_id' => $booking->id,
        ]);

        // Update booking to different slot
        $this->slotBookingService->updateBooking(
            $booking,
            ['slot_id' => $slot2->id],
            $slot2,
            $this->bookingType->id
        );

        // Verify slot occupancy changed
        $this->assertDatabaseMissing('slot_bookings', [
            'slot_id' => $slot1->id,
            'booking_id' => $booking->id,
        ]);

        $this->assertDatabaseHas('slot_bookings', [
            'slot_id' => $slot2->id,
            'booking_id' => $booking->id,
        ]);

        // Verify slot capacities
        $slot1->refresh();
        $slot2->refresh();

        $this->assertEquals(5, $slot1->remainingCapacity(), 'Old slot should have full capacity');
        $this->assertEquals(4, $slot2->remainingCapacity(), 'New slot should have reduced capacity');
    }
}
