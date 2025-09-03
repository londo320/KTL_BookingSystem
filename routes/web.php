<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminSettingsController;
use App\Http\Controllers\TestEmailController;
use App\Http\Controllers\Warehouse\BookingController;
use App\Http\Controllers\Warehouse\BookingRebookController;
use App\Http\Controllers\Admin\BookingRulesController;
use App\Http\Controllers\Admin\BookingTypeController;
use App\Http\Controllers\Admin\CarrierController;
use App\Http\Controllers\Admin\CarrierMergeController;
use App\Http\Controllers\Admin\CustomerBehaviorController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\CustomerDepotProductController;
use App\Http\Controllers\Admin\DepotCaseRangeController;
use App\Http\Controllers\Admin\DepotController;
use App\Http\Controllers\Admin\DroppedTrailersController;
use App\Http\Controllers\Admin\FactoryBookingController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Warehouse\SettingsController as WarehouseSettingsController;
use App\Http\Controllers\Admin\SlotCapacityController;
use App\Http\Controllers\Admin\SlotController;
use App\Http\Controllers\Admin\SlotGeneratorController;
use App\Http\Controllers\Admin\SlotReleaseRuleController;
use App\Http\Controllers\Admin\SlotTemplateController;
use App\Http\Controllers\Admin\SlotUsageController;
use App\Http\Controllers\Admin\UserSwitchController;
use App\Http\Controllers\Customer\CustomerDashboardController;
use App\Http\Controllers\DepotAdmin\DepotAdminDashboardController;
use App\Http\Controllers\PendingAccessController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SiteAdmin\SiteAdminDashboardController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

// Pending access route (for users without roles/depots)
Route::middleware('auth')->get('/pending-access', [PendingAccessController::class, 'index'])->name('pending-access');

Route::get('/redirect-after-login', function () {
    $user = auth()->user();
    
    // Check if user has any roles or depot assignments
    if (!$user->roles()->exists() || !$user->depots()->exists()) {
        return redirect()->route('pending-access');
    }
    
    // Warehouse users (warehouse, depot-admin, site-admin, admin) go to app dashboard
    if ($user->hasWarehouseAccess()) {
        return redirect()->route('app.dashboard');
    }
    
    // Customer-only users go to customer dashboard
    if ($user->hasRole('customer')) {
        return redirect()->route('customer.dashboard');
    }
    
    // Fallback to pending access page
    return redirect()->route('pending-access');
})->name('redirect-after-login');

Route::get('/', function () {
    if (!auth()->check()) {
        return redirect()->route('login');
    }
    
    return redirect()->route('redirect-after-login');
});


Route::get('/depot-info-public', function () {
    $depots = \App\Models\Depot::select('id', 'name')->get();
    $users = \App\Models\User::select('id', 'name', 'email', 'depot_id')
        ->with('depot:id,name')
        ->get();
    
    return response()->json([
        'depots' => $depots,
        'users_with_depots' => $users->filter(fn($u) => $u->depot_id)->map(function($u) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'depot_id' => $u->depot_id,
                'depot_name' => $u->depot->name ?? null
            ];
        })->values()
    ]);
});

// Route::view('/', 'welcome');

Route::middleware('auth')->group(function () {

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::patch('/profile/default-depot', 'updateDefaultDepot')->name('profile.update-default-depot');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    Route::get('/dashboard', function () {
        return redirect()->route('redirect-after-login');
    })->name('dashboard');

    // Universal switch-back route (accessible to all authenticated users during testing)
    Route::post('switch-back', [UserSwitchController::class, 'switchBack'])->name('switch-back');

    // Emergency recovery route (GET request for direct URL access)
    Route::get('emergency-switch-back', [UserSwitchController::class, 'switchBack'])->name('emergency-switch-back');

    // Recovery page for locked-out users
    Route::get('recovery', function () {
        return view('recovery');
    })->name('recovery');

    // API routes accessible to all authenticated users
    Route::get('api/carriers/search', [CarrierController::class, 'search'])->name('api.carriers.search');
    Route::post('api/carriers/quick-create', [CarrierController::class, 'quickCreate'])->name('api.carriers.quick-create');

    /**
     * ───── Main Application Routes (Unified Function-Based Access) ─────
     * All users access the application through these routes with function-based permissions
     */
    Route::prefix('app')->as('app.')->middleware(['auth', 'function-access'])->group(function () {
        
        // ──── Core Application Routes (Always Available) ────
        Route::get('/dashboard', [WarehouseController::class, 'dashboard'])->name('dashboard');
        
        // ──── Inbound Module Routes ────
        Route::middleware(['inbound-access'])->group(function () {
            // ──── Bookings Management ────
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::get('/bookings-streamlined', [BookingController::class, 'indexStreamlined'])->name('bookings.streamlined');
        Route::get('/bookings/create', [BookingController::class, 'create'])->name('bookings.create');
        Route::post('/bookings', [BookingController::class, 'store'])->name('bookings.store');
        Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
        Route::get('/bookings/{booking}/edit', [BookingController::class, 'edit'])->name('bookings.edit');
        Route::put('/bookings/{booking}', [BookingController::class, 'update'])->name('bookings.update');
        Route::delete('/bookings/{booking}', [BookingController::class, 'destroy'])->name('bookings.destroy');
        
        // Booking specific actions
        Route::post('/bookings/{booking}/arrived', [BookingController::class, 'markArrived'])->name('bookings.arrived');
        Route::post('/bookings/{booking}/departed', [BookingController::class, 'markDeparted'])->name('bookings.departed');
        Route::patch('/bookings/{booking}/departure', [BookingController::class, 'markDeparted'])->name('bookings.departure');
        Route::get('/bookings/{booking}/arrival', [BookingController::class, 'arrivalForm'])->name('bookings.arrival.form');
        Route::post('/bookings/{booking}/arrival', [BookingController::class, 'arrival'])->name('bookings.arrival');
        Route::post('/bookings/{booking}/cancel', [BookingRebookController::class, 'cancel'])->name('bookings.cancel');
        Route::post('/bookings/{booking}/clear-bay', [BookingController::class, 'clearBay'])->name('bookings.clear-bay');
        Route::get('/bookings/{booking}/transfer-bay', [BookingController::class, 'transferBayForm'])->name('bookings.transfer-bay.form');
        Route::post('/bookings/{booking}/transfer-bay', [BookingController::class, 'transferBay'])->name('bookings.transfer-bay');
        
        // Booking exports
        Route::get('/bookings/export/pdf', [BookingController::class, 'exportPdf'])->name('bookings.export.pdf');
        Route::get('/bookings/export/csv', [BookingController::class, 'exportCsv'])->name('bookings.export.csv');
        Route::get('/bookings/export/excel', [BookingController::class, 'exportExcel'])->name('bookings.export.excel');
        
        // Additional booking actions
        Route::get('/bookings/{booking}/download-pdf', [BookingController::class, 'downloadPdf'])->name('bookings.download-pdf');
        Route::post('/bookings/{booking}/email-pdf', [BookingController::class, 'emailPdf'])->name('bookings.email-pdf');
        Route::post('/bookings/{booking}/move-to-waiting', [BookingController::class, 'moveToWaiting'])->name('bookings.move-to-waiting');
        Route::post('/bookings/{booking}/unbook', [BookingController::class, 'unbook'])->name('bookings.unbook');
        Route::get('/bookings/{booking}/rebook', [BookingRebookController::class, 'show'])->name('bookings.rebook.show');
        Route::post('/bookings/{booking}/rebook', [BookingRebookController::class, 'store'])->name('bookings.rebook.store');
        Route::get('/bookings/{booking}/history', [BookingRebookController::class, 'history'])->name('bookings.history');
        Route::get('bookings/fix-historical-departures', [BookingController::class, 'fixHistoricalDepartures'])->name('bookings.fix-historical-departures');
        Route::post('bookings/fix-historical-departures', [BookingController::class, 'fixHistoricalDepartures'])->name('bookings.fix-historical-departures.process');
        
        // ──── Factory Bookings ────
        Route::resource('factory-bookings', FactoryBookingController::class);
        Route::post('/factory-bookings/{factoryBooking}/start-processing', [FactoryBookingController::class, 'startProcessing'])->name('factory-bookings.start-processing');
        Route::post('/factory-bookings/{factoryBooking}/complete', [FactoryBookingController::class, 'complete'])->name('factory-bookings.complete');
        Route::post('/factory-bookings/{factoryBooking}/mark-departed', [FactoryBookingController::class, 'markDeparted'])->name('factory-bookings.mark-departed');
        Route::post('/factory-bookings/{factoryBooking}/add-po-numbers', [FactoryBookingController::class, 'addPoNumbers'])->name('factory-bookings.add-po-numbers');
        Route::get('/factory-bookings/{factoryBooking}/workflow', [\App\Http\Controllers\Admin\FactoryBookingWorkflowController::class, 'show'])->name('factory-bookings.workflow.show');
        Route::get('/factory-bookings/{factoryBooking}/history', [\App\Http\Controllers\Admin\FactoryBookingController::class, 'history'])->name('factory-bookings.history');
        
        // ──── Customers ────
        Route::resource('customers', CustomerController::class);
        Route::post('/customers/{customer}/toggle', [CustomerController::class, 'toggle'])->name('customers.toggle');
        Route::resource('customer-depot-products', CustomerDepotProductController::class);
        
        // ──── Carriers ────
        Route::resource('carriers', CarrierController::class);
        Route::post('/carriers/{carrier}/toggle', [CarrierController::class, 'toggle'])->name('carriers.toggle');
        Route::post('/carriers/bulk-action', [CarrierController::class, 'bulkAction'])->name('carriers.bulk-action');
        Route::get('/carriers/cleanup', [CarrierController::class, 'cleanup'])->name('carriers.cleanup');
        Route::post('/carriers/{id}/restore', [CarrierController::class, 'restore'])->name('carriers.restore');
        
        // Carrier merge routes
        Route::prefix('carriers/merge')->name('carriers.merge.')->group(function () {
            Route::get('/', [CarrierMergeController::class, 'index'])->name('index');
            Route::get('/preview', [CarrierMergeController::class, 'preview'])->name('preview');
            Route::post('/execute', [CarrierMergeController::class, 'merge'])->name('execute');
            Route::get('/history', [CarrierMergeController::class, 'history'])->name('history');
        });
        
        // ──── Depots ────
        Route::resource('depots', DepotController::class);
        
        // Depot case ranges management
        Route::prefix('depots/{depot}/case-ranges')->name('depots.case-ranges.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\DepotCaseRangeController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\Admin\DepotCaseRangeController::class, 'create'])->name('create');
            Route::get('/{caseRange}/edit', [\App\Http\Controllers\Admin\DepotCaseRangeController::class, 'edit'])->name('edit');
            Route::delete('/{caseRange}', [\App\Http\Controllers\Admin\DepotCaseRangeController::class, 'destroy'])->name('destroy');
        });
        
        // Depot products management
        Route::prefix('depots/{depot}/products')->name('depots.products.')->group(function () {
            Route::post('/', [\App\Http\Controllers\Admin\DepotProductController::class, 'store'])->name('store');
            Route::put('/{product}', [\App\Http\Controllers\Admin\DepotProductController::class, 'update'])->name('update');
            Route::delete('/{product}', [\App\Http\Controllers\Admin\DepotProductController::class, 'destroy'])->name('destroy');
        });
        
        // ──── Users Management ────
        Route::resource('users', AdminController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        Route::post('/users/{user}/switch-to', [UserSwitchController::class, 'switchTo'])->name('users.switch-to');
        
        // ──── Custom Roles ────
        Route::resource('custom-roles', \App\Http\Controllers\Admin\CustomRoleController::class);
        Route::post('/custom-roles/{customRole}/toggle', [\App\Http\Controllers\Admin\CustomRoleController::class, 'toggle'])->name('custom-roles.toggle');
        Route::post('/custom-roles/create-predefined', [\App\Http\Controllers\Admin\CustomRoleController::class, 'createPredefined'])->name('custom-roles.create-predefined');
        
        // ──── Reports & Analytics ────
        Route::get('/trailer-report', [WarehouseController::class, 'trailerReport'])->name('trailer-report');
        Route::get('/tipping-workflow', [WarehouseController::class, 'tippingWorkflow'])->name('tipping-workflow');
        
        // ──── Tipping Workflow Management ────
        Route::get('/tipping-workflow/dashboard', [\App\Http\Controllers\Admin\TippingWorkflowController::class, 'dashboard'])->name('tipping-workflow.dashboard');
        Route::get('/tipping-workflow/{booking}', [\App\Http\Controllers\Admin\TippingWorkflowController::class, 'show'])->name('tipping-workflow.show');
        Route::post('/tipping-workflow/{booking}/drop-trailer', [\App\Http\Controllers\Admin\TippingWorkflowController::class, 'dropTrailer'])->name('tipping-workflow.drop-trailer');
        Route::post('/tipping-workflow/{booking}/drop-trailer-detached', [\App\Http\Controllers\Admin\TippingWorkflowController::class, 'dropTrailerDetached'])->name('tipping-workflow.drop-trailer-detached');
        Route::post('/tipping-workflow/{booking}/move-to-location', [\App\Http\Controllers\Admin\TippingWorkflowController::class, 'moveToLocation'])->name('tipping-workflow.move-to-location');
        Route::post('/tipping-workflow/{booking}/move-to-bay', [\App\Http\Controllers\Admin\TippingWorkflowController::class, 'moveToBay'])->name('tipping-workflow.move-to-bay');
        Route::post('/tipping-workflow/{booking}/start-tipping', [\App\Http\Controllers\Admin\TippingWorkflowController::class, 'startTipping'])->name('tipping-workflow.start-tipping');
        Route::post('/tipping-workflow/{booking}/complete-tipping', [\App\Http\Controllers\Admin\TippingWorkflowController::class, 'completeTipping'])->name('tipping-workflow.complete-tipping');
        Route::post('/tipping-workflow/{booking}/unit-depart', [\App\Http\Controllers\Admin\TippingWorkflowController::class, 'unitDepart'])->name('tipping-workflow.unit-depart');
        Route::post('/tipping-workflow/{booking}/collection-arrival', [\App\Http\Controllers\Admin\TippingWorkflowController::class, 'collectionArrival'])->name('tipping-workflow.collection-arrival');
        Route::post('/tipping-workflow/{booking}/collection-depart', [\App\Http\Controllers\Admin\TippingWorkflowController::class, 'collectionDepart'])->name('tipping-workflow.collection-depart');
        Route::post('/tipping-workflow/{booking}/trailer-depart', [\App\Http\Controllers\Admin\TippingWorkflowController::class, 'trailerDepart'])->name('tipping-workflow.trailer-depart');
        Route::post('/tipping-workflow/{booking}/move-trailer', [\App\Http\Controllers\Admin\TippingWorkflowController::class, 'moveTrailer'])->name('tipping-workflow.move-trailer');
        
        // ──── System Management ────
        Route::resource('booking-types', BookingTypeController::class);
        Route::resource('slot-templates', SlotTemplateController::class);
        Route::post('slot-templates/{slotTemplate}/duplicate', [SlotTemplateController::class, 'duplicate'])->name('slot-templates.duplicate');
        Route::post('slot-templates/bulk-duplicate', [SlotTemplateController::class, 'bulkDuplicate'])->name('slot-templates.bulk-duplicate');
        Route::resource('booking-rules', BookingRulesController::class)->only(['index', 'store']);
        
        // Settings management
        Route::get('/settings/dashboard', [AdminSettingsController::class, 'dashboard'])->name('settings.dashboard');
        Route::post('/settings/tipping-workflow', [AdminSettingsController::class, 'updateTippingWorkflow'])->name('settings.tipping-workflow');
        Route::post('/settings/outbound-module', [AdminSettingsController::class, 'updateOutboundModule'])->name('settings.outbound-module');
        Route::post('/settings/inbound-module', [AdminSettingsController::class, 'updateInboundModule'])->name('settings.inbound-module');
        Route::post('/settings/admin-approval-emails', [AdminSettingsController::class, 'updateAdminApprovalEmails'])->name('settings.admin-approval-emails');
        Route::get('/settings/pallet-types', [\App\Http\Controllers\Admin\PalletTypeController::class, 'index'])->name('settings.pallet-types');
        Route::get('/settings/pallet-types/create', [\App\Http\Controllers\Admin\PalletTypeController::class, 'create'])->name('settings.pallet-types.create');
        Route::post('/settings/pallet-types', [\App\Http\Controllers\Admin\PalletTypeController::class, 'store'])->name('settings.pallet-types.store');
        Route::get('/settings/pallet-types/{palletType}', [\App\Http\Controllers\Admin\PalletTypeController::class, 'show'])->name('settings.pallet-types.show');
        Route::get('/settings/pallet-types/{palletType}/edit', [\App\Http\Controllers\Admin\PalletTypeController::class, 'edit'])->name('settings.pallet-types.edit');
        Route::put('/settings/pallet-types/{palletType}', [\App\Http\Controllers\Admin\PalletTypeController::class, 'update'])->name('settings.pallet-types.update');
        Route::patch('/settings/pallet-types/{palletType}/toggle-active', [\App\Http\Controllers\Admin\PalletTypeController::class, 'toggleActive'])->name('settings.pallet-types.toggle-active');
        Route::delete('/settings/pallet-types/{palletType}', [\App\Http\Controllers\Admin\PalletTypeController::class, 'destroy'])->name('settings.pallet-types.destroy');
        Route::get('/settings/container-sizes', [AdminSettingsController::class, 'containerSizes'])->name('settings.container-sizes');
        
        // ──── Inbound Module Routes (Slot Management) ────
        Route::middleware(['inbound-access'])->group(function () {
            // Slot management
            Route::resource('slots', SlotController::class)->except(['show']);
            Route::post('/slots/generate', [SlotGeneratorController::class, 'store'])->name('slots.generate');
            Route::get('/slot-usage', [SlotUsageController::class, 'index'])->name('slot-usage.index');
            Route::get('/slot-capacity', [SlotCapacityController::class, 'index'])->name('slot-capacity.index');
            Route::post('/slot-capacity', [SlotCapacityController::class, 'update'])->name('slot-capacity.update');
            Route::resource('slot-release-rules', SlotReleaseRuleController::class)
                ->names('slotReleaseRules')
                ->parameters(['slot-release-rules' => 'rule']);
            
            // Arrival time settings
            Route::resource('arrival-time-settings', \App\Http\Controllers\Admin\ArrivalTimeSettingController::class);
            Route::get('arrival-time-settings-preview', [\App\Http\Controllers\Admin\ArrivalTimeSettingController::class, 'preview'])->name('arrival-time-settings.preview');
        }); // End Inbound Module Routes (Slot Management)
        
        // Operations routes
        Route::post('/operations/move-to-collection-zone/{booking}', [\App\Http\Controllers\Admin\OperationsController::class, 'moveToCollectionZone'])->name('operations.move-to-collection-zone');
        
        // Factory booking workflow  
        Route::get('/factory-booking-workflow/{factoryBooking}', [\App\Http\Controllers\Admin\FactoryBookingWorkflowController::class, 'show'])->name('factory-booking-workflow.show');
        Route::post('/factory-booking-workflow/{factoryBooking}/drop-trailer', [\App\Http\Controllers\Admin\FactoryBookingWorkflowController::class, 'dropTrailer'])->name('factory-booking-workflow.drop-trailer');
        Route::post('/factory-booking-workflow/{factoryBooking}/move-to-bay', [\App\Http\Controllers\Admin\FactoryBookingWorkflowController::class, 'moveToBay'])->name('factory-booking-workflow.move-to-bay');
        Route::post('/factory-booking-workflow/{factoryBooking}/complete-tipping', [\App\Http\Controllers\Admin\FactoryBookingWorkflowController::class, 'completeTipping'])->name('factory-booking-workflow.complete-tipping');
        Route::post('/factory-booking-workflow/{factoryBooking}/trailer-depart', [\App\Http\Controllers\Admin\FactoryBookingWorkflowController::class, 'trailerDepart'])->name('factory-booking-workflow.trailer-depart');
        Route::post('/factory-booking-workflow/{factoryBooking}/add-po-line', [\App\Http\Controllers\Admin\FactoryBookingWorkflowController::class, 'addPoLine'])->name('factory-booking-workflow.add-po-line');
        Route::post('/factory-booking-workflow/{factoryBooking}/update-po-line', [\App\Http\Controllers\Admin\FactoryBookingWorkflowController::class, 'updatePoLine'])->name('factory-booking-workflow.update-po-line');
        Route::post('/factory-booking-workflow/{factoryBooking}/move-trailer', [\App\Http\Controllers\Admin\FactoryBookingWorkflowController::class, 'moveTrailer'])->name('factory-booking-workflow.move-trailer');
        }); // End Inbound Module Routes
        
        // ──── General Management Routes (Always Available) ────
        
        // Email testing routes
        Route::prefix('test-email')->name('test-email.')->group(function () {
            Route::get('/', [TestEmailController::class, 'index'])->name('index');
            Route::get('/preview', [TestEmailController::class, 'preview'])->name('preview');
            Route::post('/send', [TestEmailController::class, 'send'])->name('send');
        });
        Route::resource('products', ProductController::class);
        Route::resource('trailer-types', \App\Http\Controllers\Admin\TrailerTypeController::class);
        Route::post('trailer-types/{id}/restore', [\App\Http\Controllers\Admin\TrailerTypeController::class, 'restore'])->name('trailer-types.restore');
        Route::post('trailer-types/{id}/toggle', [\App\Http\Controllers\Admin\TrailerTypeController::class, 'toggle'])->name('trailer-types.toggle');
        
        // ──── Inbound Module Routes (Tipping Operations) ────
        Route::middleware(['inbound-access'])->group(function () {
            Route::resource('tipping-locations', \App\Http\Controllers\Admin\TippingLocationController::class);
            Route::resource('tipping-bays', \App\Http\Controllers\Admin\TippingBayController::class);
        
        // Tipping location management
        Route::patch('/tipping-locations/{tippingLocation}/toggle-active', [\App\Http\Controllers\Admin\TippingLocationController::class, 'toggleActive'])->name('tipping-locations.toggle-active');
        
            // Tipping bay management  
            Route::post('/tipping-bays/{tippingBay}/mark-available', [\App\Http\Controllers\Admin\TippingBayController::class, 'markAvailable'])->name('tipping-bays.mark-available');
            
            // Customer behavior analysis routes
            Route::prefix('customer-behavior')->name('customer-behavior.')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\CustomerBehaviorController::class, 'index'])->name('index');
                Route::get('/flagged', [\App\Http\Controllers\Admin\CustomerBehaviorController::class, 'flagged'])->name('flagged');
                Route::get('/export', [\App\Http\Controllers\Admin\CustomerBehaviorController::class, 'export'])->name('export');
                Route::get('/{customer}', [\App\Http\Controllers\Admin\CustomerBehaviorController::class, 'show'])->name('show');
                Route::get('/{customer}/settings', [\App\Http\Controllers\Admin\CustomerBehaviorController::class, 'settings'])->name('settings');
                Route::put('/{customer}/settings', [\App\Http\Controllers\Admin\CustomerBehaviorController::class, 'updateSettings'])->name('update-settings');
                Route::get('/{customer}/reset-settings', [\App\Http\Controllers\Admin\CustomerBehaviorController::class, 'resetSettings'])->name('reset-settings');
            });
            
            // Tipping guide
            Route::get('tipping-guide', function () {
                return view('admin.tipping-guide');
            })->name('tipping-guide');
        }); // End Inbound Module Routes (Tipping Operations)
        
        // Factory Tipping Time Targets Settings (Warehouse Management)
        Route::get('settings/factory-tipping-targets', [WarehouseSettingsController::class, 'index'])->name('settings.factory-tipping-targets');
        Route::post('settings/factory-tipping-targets', [WarehouseSettingsController::class, 'store'])->name('settings.factory-tipping-targets.store');
        
        // Debug route to check user functions and navigation
        Route::get('debug/user-functions', function () {
            $user = auth()->user();
            if (!$user) {
                return 'Not logged in';
            }
            
            try {
                $debug = [
                    'user' => [
                        'name' => $user->name,
                        'email' => $user->email,
                        'roles' => $user->getRoleNames()->toArray(),
                    ],
                    'functions' => [
                        'total_assigned' => $user->functions()->count(),
                        'has_settings_manage' => $user->hasFunction('settings.manage'),
                        'has_settings_global' => $user->hasFunction('settings.manage.global'),
                        'assigned_functions' => $user->functions()->pluck('function_key')->toArray(),
                    ],
                    'navigation' => \App\Helpers\NavigationHelper::getNavigationItems(),
                ];
                
                return response()->json($debug, JSON_PRETTY_PRINT);
            } catch (\Exception $e) {
                return response()->json([
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);
            }
        })->name('debug.user-functions');
        
        // Operational management routes
        Route::get('queue-management', [\App\Http\Controllers\Admin\OperationalQueueController::class, 'dashboard'])->name('queue-management');
        Route::get('operations-control', [BookingController::class, 'operationsControl'])->name('operations-control');
        Route::get('trailer-operations-dashboard', [BookingController::class, 'trailerOperationsDashboard'])->name('trailer-operations-dashboard');
        Route::get('trailer-location-report', [BookingController::class, 'trailerLocationReport'])->name('trailer-location-report');
        Route::get('empty-unit-collection', [BookingController::class, 'emptyUnitCollection'])->name('empty-unit-collection');
        Route::post('empty-unit-collection', [BookingController::class, 'emptyUnitCollection'])->name('empty-unit-collection.process');
        
        // Depot map routes
        Route::prefix('depot-map')->name('depot-map.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\DepotMapController::class, 'index'])->name('index');
            Route::get('/manage-positions/{depot}', [\App\Http\Controllers\Admin\DepotMapController::class, 'managePositions'])->name('manage-positions');
            Route::get('/select-map-file/{depot?}', [\App\Http\Controllers\Admin\DepotMapController::class, 'selectMapFile'])->name('select-map-file');
            Route::post('/refresh', [\App\Http\Controllers\Admin\DepotMapController::class, 'refreshStatus'])->name('refresh');
            Route::post('/change-bay', [\App\Http\Controllers\Admin\DepotMapController::class, 'changeBay'])->name('change-bay');
            Route::get('/location/{location}', [\App\Http\Controllers\Admin\DepotMapController::class, 'getLocationStatus'])->name('location-status');
            Route::post('/update-position', [\App\Http\Controllers\Admin\DepotMapController::class, 'updatePosition'])->name('update-position');
            Route::post('/update-location-position', [\App\Http\Controllers\Admin\DepotMapController::class, 'updateLocationPosition'])->name('update-location-position');
            Route::post('/delete-map-file', [\App\Http\Controllers\Admin\DepotMapController::class, 'deleteMapFile'])->name('delete-map-file');
            Route::post('/update-map-file', [\App\Http\Controllers\Admin\DepotMapController::class, 'updateMapFile'])->name('update-map-file');
            Route::post('/upload-map-file', [\App\Http\Controllers\Admin\DepotMapController::class, 'uploadMapFile'])->name('upload-map-file');
        });
        
        // Arrivals management
        Route::get('/arrivals', [BookingController::class, 'arrivals'])->name('arrivals.index');
        
        // Slot generation
        Route::get('/slots/generate', [\App\Http\Controllers\Admin\SlotGeneratorController::class, 'index'])->name('slots.generate.form');
        Route::post('/slots/generate', [\App\Http\Controllers\Admin\SlotGeneratorController::class, 'store'])->name('slots.generate');
    });
    
    /**
     * ───── Legacy Warehouse Routes (Redirect to App Routes) ─────
     */
    Route::prefix('warehouse')->as('warehouse.')->middleware(['role:warehouse|depot-admin|site-admin|admin'])->group(function () {
        Route::get('/dashboard', function() { return redirect()->route('app.dashboard'); })->name('dashboard');
        Route::get('/bookings', function() { return redirect()->route('app.bookings.index'); })->name('bookings');
        Route::get('/factory-bookings', function() { return redirect()->route('app.factory-bookings.index'); })->name('factory-bookings');
        Route::get('/trailer-report', function() { return redirect()->route('app.trailer-report'); })->name('trailer-report');
        Route::get('/tipping-workflow', function() { return redirect()->route('app.tipping-workflow'); })->name('tipping-workflow');
    });

    /**
     * ───── Admin Routes ─────
     */
    Route::prefix('admin')->as('admin.')->middleware(['role:admin|depot-admin|site-admin|warehouse'])->group(function () {
        Route::get('/dashboard', function () {
            return redirect()->route('warehouse.dashboard');
        })->name('dashboard');

        // Carrier merge routes (MUST come before resource routes to avoid conflicts)
        Route::prefix('carriers/merge')->name('carriers.merge.')->group(function () {
            Route::get('/', [CarrierMergeController::class, 'index'])->name('index');
            Route::get('/preview', [CarrierMergeController::class, 'preview'])->name('preview');
            Route::post('/execute', [CarrierMergeController::class, 'merge'])->name('execute');
            Route::get('/history', [CarrierMergeController::class, 'history'])->name('history');
            Route::post('/{merge}/undo', [CarrierMergeController::class, 'undoMerge'])->name('undo');
        });

        // Carrier management routes (specific routes before resource)
        Route::post('carriers/{carrier}/toggle', [CarrierController::class, 'toggle'])->name('carriers.toggle');
        Route::post('carriers/{id}/restore', [CarrierController::class, 'restore'])->name('carriers.restore');
        Route::post('carriers/bulk-action', [CarrierController::class, 'bulkAction'])->name('carriers.bulk-action');
        Route::get('carriers/cleanup', [CarrierController::class, 'cleanup'])->name('carriers.cleanup');

        // Factory booking specific routes (before resource routes)
        Route::post('factory-bookings/{factoryBooking}/start-processing', [FactoryBookingController::class, 'startProcessing'])->name('factory-bookings.start-processing');
        Route::post('factory-bookings/{factoryBooking}/complete', [FactoryBookingController::class, 'complete'])->name('factory-bookings.complete');
        Route::post('factory-bookings/{factoryBooking}/mark-departed', [FactoryBookingController::class, 'markDeparted'])->name('factory-bookings.mark-departed');
        
        // Factory booking tipping workflow
        Route::prefix('factory-booking-workflow')->name('factory-booking-workflow.')->group(function () {
            Route::get('/{factoryBooking}', [\App\Http\Controllers\Admin\FactoryBookingWorkflowController::class, 'show'])->name('show');
            Route::post('/{factoryBooking}/drop-trailer', [\App\Http\Controllers\Admin\FactoryBookingWorkflowController::class, 'dropTrailer'])->name('drop-trailer');
            Route::post('/{factoryBooking}/move-to-location', [\App\Http\Controllers\Admin\FactoryBookingWorkflowController::class, 'moveToLocation'])->name('move-to-location');
            Route::post('/{factoryBooking}/drop-trailer-detached', [\App\Http\Controllers\Admin\FactoryBookingWorkflowController::class, 'dropTrailerDetached'])->name('drop-trailer-detached');
            Route::post('/{factoryBooking}/move-to-bay', [\App\Http\Controllers\Admin\FactoryBookingWorkflowController::class, 'moveToBay'])->name('move-to-bay');
            Route::post('/{factoryBooking}/complete-tipping', [\App\Http\Controllers\Admin\FactoryBookingWorkflowController::class, 'completeTipping'])->name('complete-tipping');
            Route::post('/{factoryBooking}/trailer-depart', [\App\Http\Controllers\Admin\FactoryBookingWorkflowController::class, 'trailerDepart'])->name('trailer-depart');
            Route::post('/{factoryBooking}/move-trailer', [\App\Http\Controllers\Admin\FactoryBookingWorkflowController::class, 'moveTrailer'])->name('move-trailer');
        });

        Route::resources([
            'depots' => DepotController::class,
            'booking-types' => BookingTypeController::class,
            'slot-templates' => SlotTemplateController::class,
            'products' => ProductController::class,
            'customers' => CustomerController::class,
            'customer-depot-products' => CustomerDepotProductController::class,
            'carriers' => CarrierController::class,
            'factory-bookings' => FactoryBookingController::class,
            'trailer-types' => \App\Http\Controllers\Admin\TrailerTypeController::class,
            'tipping-locations' => \App\Http\Controllers\Admin\TippingLocationController::class,
            'tipping-bays' => \App\Http\Controllers\Admin\TippingBayController::class,
            'custom-roles' => \App\Http\Controllers\Admin\CustomRoleController::class,
        ]);

        // Additional trailer type actions
        Route::post('trailer-types/{id}/restore', [\App\Http\Controllers\Admin\TrailerTypeController::class, 'restore'])->name('trailer-types.restore');
        Route::post('trailer-types/{id}/toggle', [\App\Http\Controllers\Admin\TrailerTypeController::class, 'toggle'])->name('trailer-types.toggle');

        // Custom role management actions
        Route::post('custom-roles/{customRole}/toggle', [\App\Http\Controllers\Admin\CustomRoleController::class, 'toggle'])->name('custom-roles.toggle');
        Route::post('custom-roles/create-predefined', [\App\Http\Controllers\Admin\CustomRoleController::class, 'createPredefined'])->name('custom-roles.create-predefined');

        // Additional tipping location actions  
        Route::patch('tipping-locations/{tippingLocation}/toggle-active', [\App\Http\Controllers\Admin\TippingLocationController::class, 'toggleActive'])->name('tipping-locations.toggle-active');
        
        // Additional tipping bay actions
        Route::post('tipping-bays/{tippingBay}/mark-available', [\App\Http\Controllers\Admin\TippingBayController::class, 'markAvailable'])->name('tipping-bays.mark-available');

        // Dropped trailers management
        Route::get('dropped-trailers', [DroppedTrailersController::class, 'index'])->name('dropped-trailers.index');
        Route::get('dropped-trailers/{booking}/reconnect', [DroppedTrailersController::class, 'reconnect'])->name('dropped-trailers.reconnect.form');
        Route::post('dropped-trailers/{booking}/reconnect', [DroppedTrailersController::class, 'reconnect'])->name('dropped-trailers.reconnect');
        Route::get('tipping-guide', function () {
            return view('admin.tipping-guide');
        })->name('tipping-guide');

        Route::post('slot-templates/{slotTemplate}/duplicate', [SlotTemplateController::class, 'duplicate'])->name('slot-templates.duplicate');
        Route::post('slot-templates/bulk-duplicate', [SlotTemplateController::class, 'bulkDuplicate'])->name('slot-templates.bulk-duplicate');

        // ─── Slot Generation ──────────────────────────────────────────
        Route::get('slots/generate', [SlotGeneratorController::class, 'index'])->name('slots.generate.form');
        Route::post('slots/generate', [SlotGeneratorController::class, 'store'])->name('slots.generate');
        Route::resource('slots', SlotController::class)->except(['show']);


        // User switching routes (testing only)
        Route::post('switch-user/{user}', [UserSwitchController::class, 'switchTo'])->name('switch-user');

        Route::get('booking-rules', [BookingRulesController::class, 'index'])->name('booking-rules.index');
        Route::post('booking-rules', [BookingRulesController::class, 'store'])->name('booking-rules.store');


        Route::get('slot-usage', [SlotUsageController::class, 'index'])->name('slot-usage.index');

        Route::get('settings/dashboard', [AdminSettingsController::class, 'dashboard'])->name('settings.dashboard');
        Route::post('settings/tipping-workflow', [AdminSettingsController::class, 'updateTippingWorkflow'])->name('settings.tipping-workflow');
        Route::post('settings/outbound-module', [AdminSettingsController::class, 'updateOutboundModule'])->name('settings.outbound-module');
        Route::post('settings/inbound-module', [AdminSettingsController::class, 'updateInboundModule'])->name('settings.inbound-module');
        
        // Pallet Types Management
        Route::get('settings/pallet-types', [AdminSettingsController::class, 'palletTypes'])->name('settings.pallet-types');
        Route::post('settings/pallet-types', [AdminSettingsController::class, 'storePalletType'])->name('settings.pallet-types.store');
        Route::put('settings/pallet-types/{palletType}', [AdminSettingsController::class, 'updatePalletType'])->name('settings.pallet-types.update');
        Route::delete('settings/pallet-types/{palletType}', [AdminSettingsController::class, 'destroyPalletType'])->name('settings.pallet-types.destroy');
        
        // Container Sizes Management - DEPRECATED (replaced by Trailer Types)
        // Route::get('settings/container-sizes', [AdminSettingsController::class, 'containerSizes'])->name('settings.container-sizes');
        Route::resource('depot-case-ranges', DepotCaseRangeController::class);

        Route::resource('users', AdminController::class)->names([
            'index' => 'users.index',
            'create' => 'users.create',
            'store' => 'users.store',
            'edit' => 'users.edit',
            'update' => 'users.update',
        ]);
    });

    /**
     * ───── Booking Routes (Available to Admin, Depot-Admin, Site-Admin) ─────
     */
    Route::prefix('admin')->as('admin.')->middleware(['role:admin|depot-admin|site-admin'])->group(function () {
        
        // ──── Inbound Module Routes ────
        Route::middleware(['inbound-access'])->group(function () {
            // IMPORTANT: Specific routes MUST come before resource routes to avoid conflicts
            // Historical data fixes
            Route::get('bookings/fix-historical-departures', [BookingController::class, 'fixHistoricalDepartures'])->name('bookings.fix-historical-departures');
            Route::post('bookings/fix-historical-departures', [BookingController::class, 'fixHistoricalDepartures'])->name('bookings.fix-historical-departures.process');
            
            Route::resource('bookings', BookingController::class);
            Route::get('bookings-streamlined', [BookingController::class, 'indexStreamlined'])->name('bookings.streamlined');
        }); // End Inbound Module Routes
        
        // Depot Map Routes
        Route::prefix('depot-map')->name('depot-map.')->group(function () {
            Route::get('/debug', function () {
                $depot = \App\Models\Depot::first();
                $user = auth()->user();
                return response()->json([
                    'depot' => $depot ? $depot->name : 'No depot',
                    'user_depot_id' => $user ? $user->depot_id : 'No user',
                    'total_depots' => \App\Models\Depot::count(),
                    'locations_count' => $depot ? \App\Models\TippingLocation::where('depot_id', $depot->id)->active()->count() : 0
                ]);
            })->name('debug');
            Route::get('/test-full', function () {
                try {
                    $depot = \App\Models\Depot::first();
                    $bays = \App\Models\TippingBay::where('depot_id', $depot->id)->active()->get();
                    
                    return response()->json([
                        'success' => true,
                        'depot' => $depot->name,
                        'total_bays' => $bays->count(),
                        'bay_names' => $bays->pluck('name')->take(5)->toArray(),
                        'message' => 'Controller logic working'
                    ]);
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            })->name('test-full');
            Route::get('/view-test', function () {
                try {
                    $depot = \App\Models\Depot::first();
                    $bays = collect(); // Empty collection
                    $bayStatuses = [];
                    $activitySummary = [
                        'total_locations' => 0,
                        'available_locations' => 0,
                        'active_bookings' => 0,
                        'awaiting_collection' => 0,
                        'todays_arrivals' => 0,
                        'pending_arrivals' => 0,
                    ];
                    $recentActivity = collect();

                    return view('admin.depot-map.index', compact(
                        'depot',
                        'bays',
                        'bayStatuses',
                        'activitySummary',
                        'recentActivity'
                    ));
                } catch (\Exception $e) {
                    return response()->json([
                        'error' => 'View rendering failed: ' . $e->getMessage(),
                        'line' => $e->getLine(),
                        'file' => $e->getFile()
                    ]);
                }
            })->name('view-test');
            Route::get('/position-test', function () {
                try {
                    $depot = \App\Models\Depot::first();
                    $bays = \App\Models\TippingBay::where('depot_id', $depot->id)->active()->get();
                    
                    return response()->json([
                        'success' => true,
                        'depot' => $depot->name,
                        'total_bays' => $bays->count(),
                        'view_exists' => view()->exists('admin.depot-map.manage-positions'),
                        'message' => 'Position controller logic working'
                    ]);
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'error' => $e->getMessage(),
                        'line' => $e->getLine(),
                        'file' => $e->getFile()
                    ]);
                }
            })->name('position-test');
            Route::get('/position-view-test', function () {
                try {
                    $depot = \App\Models\Depot::first();
                    $bays = \App\Models\TippingBay::where('depot_id', $depot->id)->active()->get();
                    
                    return view('admin.depot-map.manage-positions', compact('depot', 'bays'));
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'error' => 'View rendering failed: ' . $e->getMessage(),
                        'line' => $e->getLine(),
                        'file' => $e->getFile(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            })->name('position-view-test');
            Route::get('/direct-depot-map', function () {
                $depot = \App\Models\Depot::first();
                $bays = \App\Models\TippingBay::where('depot_id', $depot->id)
                    ->active()
                    ->where('show_on_map', true)
                    ->whereNotNull('map_x')
                    ->whereNotNull('map_y')
                    ->orderBy('name')
                    ->get();
                
                return response()->json([
                    'depot' => $depot->name,
                    'bays_count' => $bays->count(),
                    'bays' => $bays->map(function($bay) {
                        return [
                            'name' => $bay->name,
                            'x' => $bay->map_x,
                            'y' => $bay->map_y,
                            'show_on_map' => $bay->show_on_map
                        ];
                    })
                ]);
            })->name('direct-depot-map');
            Route::get('/controller-debug', function () {
                try {
                    $controller = new \App\Http\Controllers\Admin\DepotMapController();
                    $request = request();
                    $result = $controller->index($request);
                    
                    if ($result instanceof \Illuminate\Http\RedirectResponse) {
                        return response()->json([
                            'type' => 'redirect',
                            'url' => $result->getTargetUrl(),
                            'session_errors' => session()->all()
                        ]);
                    }
                    
                    return response()->json([
                        'type' => 'success',
                        'message' => 'Controller returned view successfully'
                    ]);
                } catch (\Exception $e) {
                    return response()->json([
                        'type' => 'exception',
                        'error' => $e->getMessage(),
                        'line' => $e->getLine(),
                        'file' => $e->getFile()
                    ]);
                }
            })->name('controller-debug');
            Route::get('/auth-debug', function () {
                return response()->json([
                    'authenticated' => auth()->check(),
                    'user' => auth()->user() ? [
                        'id' => auth()->user()->id,
                        'name' => auth()->user()->name,
                        'depot_id' => auth()->user()->depot_id,
                        'roles' => auth()->user()->getRoleNames()
                    ] : null,
                    'session_id' => session()->getId(),
                    'middleware_applied' => 'auth middleware working'
                ]);
            })->name('auth-debug');
            Route::get('/simple', function () {
                return '<h1>Depot Map Simple Test</h1><p>This route is working!</p>';
            })->name('simple');
            Route::get('/minimal', function () {
                $depot = \App\Models\Depot::first();
                $locations = \App\Models\TippingLocation::where('depot_id', $depot->id)->active()->get();
                return view('admin.depot-map.index', [
                    'depot' => $depot,
                    'locations' => $locations,
                    'locationStatuses' => [],
                    'activitySummary' => [
                        'total_locations' => 0,
                        'available_locations' => 0,
                        'active_bookings' => 0,
                        'awaiting_collection' => 0,
                        'todays_arrivals' => 0,
                        'pending_arrivals' => 0,
                    ],
                    'recentActivity' => collect(),
                ]);
            })->name('minimal');
            Route::get('/', [\App\Http\Controllers\Admin\DepotMapController::class, 'index'])->name('index');
            Route::get('/test', function () {
                return view('admin.depot-map.test');
            })->name('test');
            Route::get('/manage-positions', [\App\Http\Controllers\Admin\DepotMapController::class, 'manageBayPositions'])->name('manage-positions');
            Route::post('/update-position', [\App\Http\Controllers\Admin\DepotMapController::class, 'updateBayPosition'])->name('update-position');
            Route::post('/update-location-position', [\App\Http\Controllers\Admin\DepotMapController::class, 'updateLocationPosition'])->name('update-location-position');
            Route::get('/bay/{bay}', [\App\Http\Controllers\Admin\DepotMapController::class, 'getBayStatus'])->name('bay-status');
            Route::post('/change-bay', [\App\Http\Controllers\Admin\DepotMapController::class, 'changeBay'])->name('change-bay');
            Route::get('/location/{location}', [\App\Http\Controllers\Admin\DepotMapController::class, 'getLocationStatus'])->name('location-status');
            Route::post('/refresh', [\App\Http\Controllers\Admin\DepotMapController::class, 'refreshStatus'])->name('refresh');
            Route::get('/select-map-file/{depot?}', [\App\Http\Controllers\Admin\DepotMapController::class, 'selectMapFile'])->name('select-map-file');
            Route::post('/update-map-file', [\App\Http\Controllers\Admin\DepotMapController::class, 'updateMapFile'])->name('update-map-file');
            Route::post('/upload-map-file', [\App\Http\Controllers\Admin\DepotMapController::class, 'uploadMapFile'])->name('upload-map-file');
            Route::delete('/delete-map-file', [\App\Http\Controllers\Admin\DepotMapController::class, 'deleteMapFile'])->name('delete-map-file');
            Route::get('/depot-debug', function () {
                $user = Auth::user();
                $depots = \App\Models\Depot::all();
                return response()->json([
                    'user_id' => $user->id ?? null,
                    'user_depot_id' => $user->depot_id ?? null,
                    'user_depot_name' => $user->depot->name ?? null,
                    'all_depots' => $depots->map(fn($d) => ['id' => $d->id, 'name' => $d->name])
                ]);
            })->name('depot-debug');
            Route::get('/depot-info', function () {
                $depots = \App\Models\Depot::select('id', 'name')->get();
                $users = \App\Models\User::select('id', 'name', 'email', 'depot_id')
                    ->with('depot:id,name')
                    ->where('role', 'admin')
                    ->get();
                
                return response()->json([
                    'depots' => $depots,
                    'admin_users' => $users->map(function($u) {
                        return [
                            'id' => $u->id,
                            'name' => $u->name,
                            'email' => $u->email,
                            'depot_id' => $u->depot_id,
                            'depot_name' => $u->depot->name ?? null
                        ];
                    })
                ]);
            })->name('depot-info');
            Route::get('/user-depot-check', function () {
                $user = Auth::user();
                $depot = null;
                if ($user && $user->depot_id) {
                    $depot = $user->depot;
                }
                $firstDepot = \App\Models\Depot::first();
                
                return response()->json([
                    'authenticated' => Auth::check(),
                    'user_id' => $user->id ?? null,
                    'user_name' => $user->name ?? null,
                    'user_depot_id' => $user->depot_id ?? null,
                    'user_depot_name' => $depot->name ?? null,
                    'first_depot_id' => $firstDepot->id ?? null,
                    'first_depot_name' => $firstDepot->name ?? null,
                    'will_load_depot' => $depot ? $depot->name : ($firstDepot ? $firstDepot->name : 'none')
                ]);
            })->name('user-depot-check');
        });

        // Rebooking and cancellation routes
        Route::prefix('bookings/{booking}')->group(function () {
            Route::get('/rebook', [BookingRebookController::class, 'show'])->name('bookings.rebook.show');
            Route::post('/rebook', [BookingRebookController::class, 'store'])->name('bookings.rebook.store');
            Route::post('/cancel', [BookingRebookController::class, 'cancel'])->name('bookings.cancel');
            Route::get('/history', [BookingRebookController::class, 'history'])->name('bookings.history');
        });

        // Customer behavior analysis routes
        Route::prefix('customer-behavior')->name('customer-behavior.')->group(function () {
            Route::get('/', [CustomerBehaviorController::class, 'index'])->name('index');
            Route::get('/flagged', [CustomerBehaviorController::class, 'flagged'])->name('flagged');
            Route::get('/export', [CustomerBehaviorController::class, 'export'])->name('export');
            Route::get('/{customer}', [CustomerBehaviorController::class, 'show'])->name('show');
            Route::get('/{customer}/settings', [CustomerBehaviorController::class, 'settings'])->name('settings');
            Route::put('/{customer}/settings', [CustomerBehaviorController::class, 'updateSettings'])->name('update-settings');
            Route::get('/{customer}/reset-settings', [CustomerBehaviorController::class, 'resetSettings'])->name('reset-settings');
        });

        // Arrival time settings routes  
        Route::resource('arrival-time-settings', \App\Http\Controllers\Admin\ArrivalTimeSettingController::class);
        Route::get('arrival-time-settings-preview', [\App\Http\Controllers\Admin\ArrivalTimeSettingController::class, 'preview'])->name('arrival-time-settings.preview');

        // Tipping workflow routes
        Route::prefix('tipping-workflow')->name('tipping-workflow.')->group(function () {
            Route::get('/dashboard', [\App\Http\Controllers\Admin\TippingWorkflowController::class, 'dashboard'])->name('dashboard');
            Route::get('/{booking}', [\App\Http\Controllers\Admin\TippingWorkflowController::class, 'show'])->name('show');
            Route::post('/{booking}/drop-trailer', [\App\Http\Controllers\Admin\TippingWorkflowController::class, 'dropTrailer'])->name('drop-trailer');
            Route::post('/{booking}/move-to-location', [\App\Http\Controllers\Admin\TippingWorkflowController::class, 'moveToLocation'])->name('move-to-location');
            Route::post('/{booking}/drop-trailer-detached', [\App\Http\Controllers\Admin\TippingWorkflowController::class, 'dropTrailerDetached'])->name('drop-trailer-detached');
            Route::post('/{booking}/move-to-bay', [\App\Http\Controllers\Admin\TippingWorkflowController::class, 'moveToBay'])->name('move-to-bay');
            Route::post('/{booking}/start-tipping', [\App\Http\Controllers\Admin\TippingWorkflowController::class, 'startTipping'])->name('start-tipping');
            Route::post('/{booking}/complete-tipping', [\App\Http\Controllers\Admin\TippingWorkflowController::class, 'completeTipping'])->name('complete-tipping');
            Route::post('/{booking}/unit-depart', [\App\Http\Controllers\Admin\TippingWorkflowController::class, 'unitDepart'])->name('unit-depart');
            Route::post('/{booking}/collection-arrival', [\App\Http\Controllers\Admin\TippingWorkflowController::class, 'collectionArrival'])->name('collection-arrival');
            Route::post('/{booking}/collection-depart', [\App\Http\Controllers\Admin\TippingWorkflowController::class, 'collectionDepart'])->name('collection-depart');
            Route::post('/{booking}/trailer-depart', [\App\Http\Controllers\Admin\TippingWorkflowController::class, 'trailerDepart'])->name('trailer-depart');
            Route::post('/{booking}/move-trailer', [\App\Http\Controllers\Admin\TippingWorkflowController::class, 'moveTrailer'])->name('move-trailer');
        });

        // Arrival/Departure routes
        Route::get('bookings/{booking}/arrival', [BookingController::class, 'markArrived'])->name('bookings.arrival.form');
        Route::post('bookings/{booking}/arrival', [BookingController::class, 'markArrived'])->name('bookings.arrival');
        Route::post('bookings/{booking}/unbook', [BookingController::class, 'unbook'])->name('bookings.unbook');
        Route::post('bookings/{booking}/assign-bay', [BookingController::class, 'assignBayFromWaiting'])->name('bookings.assign-bay');
        Route::patch('bookings/{booking}/departure', [BookingController::class, 'markDeparted'])->name('bookings.departure');
        Route::get('empty-unit-collection', [BookingController::class, 'emptyUnitCollection'])->name('empty-unit-collection');
        Route::post('empty-unit-collection', [BookingController::class, 'emptyUnitCollection'])->name('empty-unit-collection.process');
        Route::get('trailer-location-report', [BookingController::class, 'trailerLocationReport'])->name('trailer-location-report');
        Route::get('trailer-operations-dashboard', [BookingController::class, 'trailerOperationsDashboard'])->name('trailer-operations-dashboard');
        Route::get('operations-control', [BookingController::class, 'operationsControl'])->name('operations-control');
        Route::get('queue-management', [\App\Http\Controllers\Admin\OperationalQueueController::class, 'dashboard'])->name('queue-management');
        
        // Streamlined Operations Routes
        Route::prefix('operations')->name('operations.')->group(function () {
            Route::post('{booking}/assign-drop-zone', [\App\Http\Controllers\Admin\OperationsController::class, 'assignDropZone'])->name('assign-drop-zone');
            Route::post('{booking}/unit-depart', [\App\Http\Controllers\Admin\OperationsController::class, 'unitDepart'])->name('unit-depart');
            Route::post('{booking}/shunt-to-bay', [\App\Http\Controllers\Admin\OperationsController::class, 'shuntToBay'])->name('shunt-to-bay');
            Route::post('{booking}/start-tipping', [\App\Http\Controllers\Admin\OperationsController::class, 'startTipping'])->name('start-tipping');
            Route::post('{booking}/complete-tipping', [\App\Http\Controllers\Admin\OperationsController::class, 'completeTipping'])->name('complete-tipping');
            Route::post('{booking}/move-to-collection-zone', [\App\Http\Controllers\Admin\OperationsController::class, 'moveToCollectionZone'])->name('move-to-collection-zone');
            Route::post('{booking}/record-collection', [\App\Http\Controllers\Admin\OperationsController::class, 'recordCollection'])->name('record-collection');
            Route::get('available-locations', [\App\Http\Controllers\Admin\OperationsController::class, 'getAvailableLocations'])->name('available-locations');
            Route::get('available-bays', [\App\Http\Controllers\Admin\OperationsController::class, 'getAvailableBays'])->name('available-bays');
        });
        Route::get('api/available-trailers', [BookingController::class, 'getAvailableTrailers'])->name('api.available-trailers');

        // Bay transfer routes
        Route::get('bookings/{booking}/transfer-bay', [BookingController::class, 'transferBay'])->name('bookings.transfer-bay.form');
        Route::post('bookings/{booking}/transfer-bay', [BookingController::class, 'transferBay'])->name('bookings.transfer-bay');

        // Quick bay management actions
        Route::post('bookings/{booking}/move-to-waiting', [BookingController::class, 'moveToWaitingArea'])->name('bookings.move-to-waiting');
        Route::post('bookings/{booking}/clear-bay', [BookingController::class, 'clearBay'])->name('bookings.clear-bay');

        // Priority Settings routes
        Route::get('operations/priority-settings', [\App\Http\Controllers\Admin\PrioritySettingsController::class, 'index'])->name('operations.priority-settings');
        Route::put('operations/customers/{customer}/priority', [\App\Http\Controllers\Admin\PrioritySettingsController::class, 'updateCustomerPriority'])->name('operations.update-customer-priority');
        Route::put('operations/bookings/{booking}/priority', [\App\Http\Controllers\Admin\PrioritySettingsController::class, 'updateBookingPriority'])->name('operations.update-booking-priority');
        Route::put('operations/bookings/{booking}/tipping-type', [\App\Http\Controllers\Admin\PrioritySettingsController::class, 'setTippingType'])->name('operations.set-tipping-type');
        Route::post('operations/reset-priorities', [\App\Http\Controllers\Admin\PrioritySettingsController::class, 'resetAllPriorities'])->name('operations.reset-priorities');

        // Simple tipping control routes
        Route::post('bookings/{booking}/start-tipping', [BookingController::class, 'startTipping'])->name('bookings.start-tipping');
        Route::post('bookings/{booking}/complete-tipping', [BookingController::class, 'completeTipping'])->name('bookings.complete-tipping');

        // PDF Email and Download routes
        Route::post('bookings/{booking}/email-pdf', [BookingController::class, 'emailPDF'])->name('bookings.email-pdf');
        Route::get('bookings/{booking}/download-pdf', [BookingController::class, 'downloadPDF'])->name('bookings.download-pdf');

        // Export routes
        Route::get('bookings/export/pdf', [BookingController::class, 'exportPDF'])->name('bookings.export.pdf');
        Route::get('bookings/export/csv', [BookingController::class, 'exportCSV'])->name('bookings.export.csv');
        Route::get('bookings/export/excel', [BookingController::class, 'exportExcel'])->name('bookings.export.excel');
    });

    /**
     * ───── Depot Admin Routes ─────
     */
    Route::prefix('depot-admin')->as('depot.')->middleware(['role:admin|depot-admin|site-admin|warehouse'])->group(function () {
        Route::get('/dashboard', [DepotAdminDashboardController::class, 'index'])->name('dashboard');

        // IMPORTANT: Specific routes MUST come before resource routes to avoid conflicts
        // Historical data fixes for depot-admin
        Route::get('bookings/fix-historical-departures', [BookingController::class, 'fixHistoricalDepartures'])->name('bookings.fix-historical-departures');
        Route::post('bookings/fix-historical-departures', [BookingController::class, 'fixHistoricalDepartures'])->name('bookings.fix-historical-departures.process');

        // Booking management for depot-admin
        Route::resource('bookings', BookingController::class);
        Route::get('bookings-streamlined', [BookingController::class, 'indexStreamlined'])->name('bookings.streamlined');
        Route::get('/arrivals', [BookingController::class, 'arrivals'])->name('arrivals.index');
        
        // Depot Map Routes for depot-admin
        Route::prefix('depot-map')->name('depot-map.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\DepotMapController::class, 'index'])->name('index');
            Route::get('/manage-positions', [\App\Http\Controllers\Admin\DepotMapController::class, 'manageBayPositions'])->name('manage-positions');
            Route::post('/update-position', [\App\Http\Controllers\Admin\DepotMapController::class, 'updateBayPosition'])->name('update-position');
            Route::post('/update-location-position', [\App\Http\Controllers\Admin\DepotMapController::class, 'updateLocationPosition'])->name('update-location-position');
            Route::get('/bay/{bay}', [\App\Http\Controllers\Admin\DepotMapController::class, 'getBayStatus'])->name('bay-status');
            Route::post('/change-bay', [\App\Http\Controllers\Admin\DepotMapController::class, 'changeBay'])->name('change-bay');
            Route::get('/location/{location}', [\App\Http\Controllers\Admin\DepotMapController::class, 'getLocationStatus'])->name('location-status');
            Route::post('/refresh', [\App\Http\Controllers\Admin\DepotMapController::class, 'refreshStatus'])->name('refresh');
            Route::get('/select-map-file/{depot?}', [\App\Http\Controllers\Admin\DepotMapController::class, 'selectMapFile'])->name('select-map-file');
            Route::post('/update-map-file', [\App\Http\Controllers\Admin\DepotMapController::class, 'updateMapFile'])->name('update-map-file');
            Route::post('/upload-map-file', [\App\Http\Controllers\Admin\DepotMapController::class, 'uploadMapFile'])->name('upload-map-file');
            Route::delete('/delete-map-file', [\App\Http\Controllers\Admin\DepotMapController::class, 'deleteMapFile'])->name('delete-map-file');
            Route::get('/depot-debug', function () {
                $user = Auth::user();
                $depots = \App\Models\Depot::all();
                return response()->json([
                    'user_id' => $user->id ?? null,
                    'user_depot_id' => $user->depot_id ?? null,
                    'user_depot_name' => $user->depot->name ?? null,
                    'all_depots' => $depots->map(fn($d) => ['id' => $d->id, 'name' => $d->name])
                ]);
            })->name('depot-debug');
            Route::get('/depot-info', function () {
                $depots = \App\Models\Depot::select('id', 'name')->get();
                $users = \App\Models\User::select('id', 'name', 'email', 'depot_id')
                    ->with('depot:id,name')
                    ->where('role', 'admin')
                    ->get();
                
                return response()->json([
                    'depots' => $depots,
                    'admin_users' => $users->map(function($u) {
                        return [
                            'id' => $u->id,
                            'name' => $u->name,
                            'email' => $u->email,
                            'depot_id' => $u->depot_id,
                            'depot_name' => $u->depot->name ?? null
                        ];
                    })
                ]);
            })->name('depot-info');
            Route::get('/user-depot-check', function () {
                $user = Auth::user();
                $depot = null;
                if ($user && $user->depot_id) {
                    $depot = $user->depot;
                }
                $firstDepot = \App\Models\Depot::first();
                
                return response()->json([
                    'authenticated' => Auth::check(),
                    'user_id' => $user->id ?? null,
                    'user_name' => $user->name ?? null,
                    'user_depot_id' => $user->depot_id ?? null,
                    'user_depot_name' => $depot->name ?? null,
                    'first_depot_id' => $firstDepot->id ?? null,
                    'first_depot_name' => $firstDepot->name ?? null,
                    'will_load_depot' => $depot ? $depot->name : ($firstDepot ? $firstDepot->name : 'none')
                ]);
            })->name('user-depot-check');
        });

        // Arrival/Departure routes for depot-admin
        Route::get('bookings/{booking}/arrival', [BookingController::class, 'markArrived'])->name('bookings.arrival.form');
        Route::post('bookings/{booking}/arrival', [BookingController::class, 'markArrived'])->name('bookings.arrival');
        Route::post('bookings/{booking}/unbook', [BookingController::class, 'unbook'])->name('bookings.unbook');
        Route::post('bookings/{booking}/assign-bay', [BookingController::class, 'assignBayFromWaiting'])->name('bookings.assign-bay');
        Route::patch('bookings/{booking}/departure', [BookingController::class, 'markDeparted'])->name('bookings.departure');
        Route::get('empty-unit-collection', [BookingController::class, 'emptyUnitCollection'])->name('empty-unit-collection');
        Route::post('empty-unit-collection', [BookingController::class, 'emptyUnitCollection'])->name('empty-unit-collection.process');
        Route::get('trailer-location-report', [BookingController::class, 'trailerLocationReport'])->name('trailer-location-report');
        Route::get('trailer-operations-dashboard', [BookingController::class, 'trailerOperationsDashboard'])->name('trailer-operations-dashboard');
        Route::get('operations-control', [BookingController::class, 'operationsControl'])->name('operations-control');
        Route::get('queue-management', [\App\Http\Controllers\Admin\OperationalQueueController::class, 'dashboard'])->name('queue-management');
        
        // Streamlined Operations Routes
        Route::prefix('operations')->name('operations.')->group(function () {
            Route::post('{booking}/assign-drop-zone', [\App\Http\Controllers\Admin\OperationsController::class, 'assignDropZone'])->name('assign-drop-zone');
            Route::post('{booking}/unit-depart', [\App\Http\Controllers\Admin\OperationsController::class, 'unitDepart'])->name('unit-depart');
            Route::post('{booking}/shunt-to-bay', [\App\Http\Controllers\Admin\OperationsController::class, 'shuntToBay'])->name('shunt-to-bay');
            Route::post('{booking}/start-tipping', [\App\Http\Controllers\Admin\OperationsController::class, 'startTipping'])->name('start-tipping');
            Route::post('{booking}/complete-tipping', [\App\Http\Controllers\Admin\OperationsController::class, 'completeTipping'])->name('complete-tipping');
            Route::post('{booking}/move-to-collection-zone', [\App\Http\Controllers\Admin\OperationsController::class, 'moveToCollectionZone'])->name('move-to-collection-zone');
            Route::post('{booking}/record-collection', [\App\Http\Controllers\Admin\OperationsController::class, 'recordCollection'])->name('record-collection');
            Route::get('available-locations', [\App\Http\Controllers\Admin\OperationsController::class, 'getAvailableLocations'])->name('available-locations');
            Route::get('available-bays', [\App\Http\Controllers\Admin\OperationsController::class, 'getAvailableBays'])->name('available-bays');
        });
        Route::get('api/available-trailers', [BookingController::class, 'getAvailableTrailers'])->name('api.available-trailers');

        // Bay transfer routes for depot-admin
        Route::get('bookings/{booking}/transfer-bay', [BookingController::class, 'transferBay'])->name('bookings.transfer-bay.form');
        Route::post('bookings/{booking}/transfer-bay', [BookingController::class, 'transferBay'])->name('bookings.transfer-bay');

        // Quick bay management actions for depot-admin
        Route::post('bookings/{booking}/move-to-waiting', [BookingController::class, 'moveToWaitingArea'])->name('bookings.move-to-waiting');
        Route::post('bookings/{booking}/clear-bay', [BookingController::class, 'clearBay'])->name('bookings.clear-bay');

        // Simple tipping control routes for depot-admin
        Route::post('bookings/{booking}/start-tipping', [BookingController::class, 'startTipping'])->name('bookings.start-tipping');
        Route::post('bookings/{booking}/complete-tipping', [BookingController::class, 'completeTipping'])->name('bookings.complete-tipping');

        // PDF Email and Download routes for depot-admin
        Route::post('bookings/{booking}/email-pdf', [BookingController::class, 'emailPDF'])->name('bookings.email-pdf');
        Route::get('bookings/{booking}/download-pdf', [BookingController::class, 'downloadPDF'])->name('bookings.download-pdf');

        // Export routes for depot-admin
        Route::get('bookings/export/pdf', [BookingController::class, 'exportPDF'])->name('bookings.export.pdf');
        Route::get('bookings/export/csv', [BookingController::class, 'exportCSV'])->name('bookings.export.csv');
        Route::get('bookings/export/excel', [BookingController::class, 'exportExcel'])->name('bookings.export.excel');

        // Dropped trailers management for depot-admin
        Route::get('dropped-trailers', [DroppedTrailersController::class, 'index'])->name('dropped-trailers.index');
        Route::get('dropped-trailers/{booking}/reconnect', [DroppedTrailersController::class, 'reconnect'])->name('dropped-trailers.reconnect.form');
        Route::post('dropped-trailers/{booking}/reconnect', [DroppedTrailersController::class, 'reconnect'])->name('dropped-trailers.reconnect');

        Route::resource('slots', SlotController::class)->except(['show']);
    });

    /**
     * ───── Site Admin Routes ─────
     */
    Route::prefix('site-admin')->as('site.')->middleware(['role:admin|site-admin'])->group(function () {
        Route::get('/dashboard', function () {
            return redirect()->route('warehouse.dashboard');
        })->name('dashboard');
        Route::get('/search', [SiteAdminDashboardController::class, 'search'])->name('search');
        Route::get('/arrivals', [SiteAdminDashboardController::class, 'arrivals'])->name('arrivals.index');
        Route::get('/departures', [SiteAdminDashboardController::class, 'departures'])->name('departures.index');

        // Booking arrival/departure processing routes
        Route::post('bookings/{booking}/arrival', [\App\Http\Controllers\Admin\BookingController::class, 'markArrived'])->name('bookings.arrival');
        Route::patch('bookings/{booking}/departure', [\App\Http\Controllers\Admin\BookingController::class, 'markDeparted'])->name('bookings.departure');
    });

    /**
     * ───── Customer Routes ─────
     */
    Route::prefix('customer')->middleware(['role:customer'])->as('customer.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Customer\CustomerDashboardController::class, 'index'])->name('dashboard');

        // Booking management
        Route::resource('bookings', \App\Http\Controllers\Customer\CustomerBookingController::class)->except(['destroy']);

        // API endpoints for booking creation
        Route::get('/availability', [\App\Http\Controllers\Customer\CustomerBookingController::class, 'availability'])->name('availability');
        Route::get('/slots', [\App\Http\Controllers\Customer\CustomerBookingController::class, 'slots'])->name('slots');

        // Rebooking and cancellation routes for customers
        Route::prefix('bookings/{booking}')->group(function () {
            Route::get('/rebook', [\App\Http\Controllers\Customer\CustomerBookingController::class, 'showRebook'])->name('bookings.rebook.show');
            Route::post('/rebook', [\App\Http\Controllers\Customer\CustomerBookingController::class, 'rebook'])->name('bookings.rebook.store');
            Route::post('/cancel', [\App\Http\Controllers\Customer\CustomerBookingController::class, 'cancel'])->name('bookings.cancel');
            Route::get('/history', [\App\Http\Controllers\Customer\CustomerBookingController::class, 'history'])->name('bookings.history');
        });

        // PDF Email and Download routes
        Route::post('/bookings/{booking}/email-pdf', [\App\Http\Controllers\Customer\CustomerBookingController::class, 'emailPDF'])->name('bookings.email-pdf');
        Route::get('/bookings/{booking}/download-pdf', [\App\Http\Controllers\Customer\CustomerBookingController::class, 'downloadPDF'])->name('bookings.download-pdf');
    });

    // Debug route to test role routing (remove in production)
    Route::get('/test-roles', function () {
        $user = auth()->user();
        if (! $user) {
            return response()->json(['error' => 'Not authenticated']);
        }

        $roles = $user->getRoleNames();
        $routes = [];

        try {
            if ($user->hasRole('admin')) {
                $routes['admin'] = route('admin.dashboard');
            }
            if ($user->hasRole('depot-admin')) {
                $routes['depot-admin'] = route('depot.dashboard');
            }
            if ($user->hasRole('site-admin')) {
                $routes['site-admin'] = route('site.dashboard');
            }
            if ($user->hasRole('customer')) {
                $routes['customer'] = route('customer.dashboard');
            }
        } catch (\Exception $e) {
            $routes['error'] = $e->getMessage();
        }

        return response()->json([
            'user' => $user->name,
            'email' => $user->email,
            'roles' => $roles,
            'available_routes' => $routes,
            'depots' => $user->depots->pluck('name', 'id'),
            'customers' => $user->customers->pluck('name', 'id'),
        ]);
    })->name('test-roles');

    // Outbound Module Routes (Beta Testing)
    Route::prefix('outbound')->as('outbound.')->middleware(['role:admin|depot-admin|site-admin|warehouse', 'outbound-access'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [\App\Modules\Outbound\Controllers\Admin\OutboundDashboardController::class, 'index'])->name('dashboard');
        Route::get('/load-status-counts', [\App\Modules\Outbound\Controllers\Admin\OutboundDashboardController::class, 'getLoadStatusCounts'])->name('dashboard.load-status-counts');
        Route::get('/order-status-counts', [\App\Modules\Outbound\Controllers\Admin\OutboundDashboardController::class, 'getOrderStatusCounts'])->name('dashboard.order-status-counts');
        Route::get('/upcoming-collections', [\App\Modules\Outbound\Controllers\Admin\OutboundDashboardController::class, 'getUpcomingCollections'])->name('dashboard.upcoming-collections');
        
        // Load Management
        Route::resource('loads', \App\Modules\Outbound\Controllers\Admin\OutboundLoadController::class);
        Route::get('/loads/{load}/timing-analysis', [\App\Modules\Outbound\Controllers\Admin\OutboundLoadController::class, 'timingAnalysis'])->name('loads.timing-analysis');
        Route::post('/loads/{load}/update-status', [\App\Modules\Outbound\Controllers\Admin\OutboundLoadController::class, 'updateStatus'])->name('loads.update-status');
        Route::get('/loads/{load}/add-order', [\App\Modules\Outbound\Controllers\Admin\OutboundLoadController::class, 'addOrderForm'])->name('loads.add-order');
        Route::post('/loads/{load}/add-order', [\App\Modules\Outbound\Controllers\Admin\OutboundLoadController::class, 'addOrder'])->name('loads.store-order');
        Route::get('/loads/{load}/add-collection', [\App\Modules\Outbound\Controllers\Admin\OutboundLoadController::class, 'addCollectionForm'])->name('loads.add-collection');
        Route::post('/loads/{load}/add-collection', [\App\Modules\Outbound\Controllers\Admin\OutboundLoadController::class, 'addCollection'])->name('loads.store-collection');
        
        // Customer Address Management
        Route::resource('addresses', \App\Modules\Outbound\Controllers\Admin\CustomerAddressController::class);
        Route::post('/addresses/{address}/set-default', [\App\Modules\Outbound\Controllers\Admin\CustomerAddressController::class, 'setDefault'])->name('addresses.set-default');
        Route::post('/addresses/{address}/toggle-active', [\App\Modules\Outbound\Controllers\Admin\CustomerAddressController::class, 'toggleActive'])->name('addresses.toggle-active');
        
        // Physical Load Registration (Driver Arrivals)
        Route::get('/arrivals', [\App\Modules\Outbound\Controllers\Admin\PhysicalLoadController::class, 'dashboard'])->name('arrivals.dashboard');
        Route::get('/arrivals/register', [\App\Modules\Outbound\Controllers\Admin\PhysicalLoadController::class, 'create'])->name('arrivals.create');
        Route::post('/arrivals/register', [\App\Modules\Outbound\Controllers\Admin\PhysicalLoadController::class, 'store'])->name('arrivals.store');
        Route::get('/arrivals/{physicalLoad}', [\App\Modules\Outbound\Controllers\Admin\PhysicalLoadController::class, 'show'])->name('physical-loads.show');
        Route::post('/arrivals/{physicalLoad}/match-orders', [\App\Modules\Outbound\Controllers\Admin\PhysicalLoadController::class, 'triggerMatching'])->name('physical-loads.trigger-matching');
        Route::post('/arrivals/{physicalLoad}/update-status', [\App\Modules\Outbound\Controllers\Admin\PhysicalLoadController::class, 'updateStatus'])->name('physical-loads.update-status');
        Route::post('/arrivals/process-all', [\App\Modules\Outbound\Controllers\Admin\PhysicalLoadController::class, 'processAllPending'])->name('arrivals.process-all');
        
        // File Import System
        Route::prefix('imports')->name('imports.')->group(function () {
            Route::get('/dashboard', [\App\Modules\Outbound\Controllers\Admin\ImportController::class, 'dashboard'])->name('dashboard');
            Route::get('/upload', [\App\Modules\Outbound\Controllers\Admin\ImportController::class, 'create'])->name('create');
            Route::post('/upload', [\App\Modules\Outbound\Controllers\Admin\ImportController::class, 'store'])->name('store');
            Route::get('/{fileUpload}', [\App\Modules\Outbound\Controllers\Admin\ImportController::class, 'show'])->name('show');
            Route::get('/{fileUpload}/review', [\App\Modules\Outbound\Controllers\Admin\ImportController::class, 'review'])->name('review');
            Route::post('/{fileUpload}/approve', [\App\Modules\Outbound\Controllers\Admin\ImportController::class, 'approve'])->name('approve');
            Route::post('/{fileUpload}/reject', [\App\Modules\Outbound\Controllers\Admin\ImportController::class, 'reject'])->name('reject');
            Route::post('/{fileUpload}/reprocess', [\App\Modules\Outbound\Controllers\Admin\ImportController::class, 'reprocess'])->name('reprocess');
            Route::get('/{fileUpload}/download', [\App\Modules\Outbound\Controllers\Admin\ImportController::class, 'download'])->name('download');
            Route::delete('/{fileUpload}', [\App\Modules\Outbound\Controllers\Admin\ImportController::class, 'destroy'])->name('destroy');
            
            // Template management
            Route::get('/templates/list', [\App\Modules\Outbound\Controllers\Admin\ImportController::class, 'templates'])->name('templates');
            Route::get('/templates/create', [\App\Modules\Outbound\Controllers\Admin\ImportController::class, 'createTemplate'])->name('templates.create');
            Route::post('/templates/create', [\App\Modules\Outbound\Controllers\Admin\ImportController::class, 'storeTemplate'])->name('templates.store');
            Route::get('/templates/{template}/edit', [\App\Modules\Outbound\Controllers\Admin\ImportController::class, 'editTemplate'])->name('templates.edit');
            Route::put('/templates/{template}', [\App\Modules\Outbound\Controllers\Admin\ImportController::class, 'updateTemplate'])->name('templates.update');
            Route::patch('/templates/{template}/toggle', [\App\Modules\Outbound\Controllers\Admin\ImportController::class, 'toggleTemplate'])->name('templates.toggle');
        });
        
        // Order Management (placeholder routes)
        Route::get('/orders', function() { 
            return redirect()->route('outbound.loads.index'); 
        })->name('orders.index');
        Route::get('/orders/{order}', function() { 
            return redirect()->back(); 
        })->name('orders.show');
        
        // Collection Management (placeholder routes)
        Route::get('/collections/{collection}', function() { 
            return redirect()->back(); 
        })->name('collections.show');
    });

    Route::fallback(function () {
        return auth()->check()
            ? redirect()->route('depot.dashboard')
            : redirect()->route('login');
    });

});
