<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserFunction extends Model
{
    protected $fillable = [
        'user_id',
        'function_key',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Static method to get all available functions
    public static function getAllFunctions(): array
    {
        return [
            'Dashboard & Navigation' => [
                'dashboard.view' => 'Access Main Dashboard',
                'dashboard.warehouse' => 'Access Warehouse Dashboard',
                'warehouse.dashboard' => 'View Warehouse Dashboard',
            ],
            
            'Bookings - Core Operations' => [
                'bookings.view' => 'View Bookings List',
                'bookings.view-streamlined' => 'View Streamlined Interface',
                'bookings.view-all-depots' => 'View Bookings Across All Depots',
                'bookings.create' => 'Create New Booking',
                'bookings.store' => 'Save New Booking',
                'bookings.show' => 'View Booking Details',
                'bookings.edit' => 'Edit Existing Booking',
                'bookings.update' => 'Save Booking Changes',
                'bookings.delete' => 'Delete Booking',
                'bookings.search' => 'Search Bookings',
            ],
            
            'Bookings - Status & Operations' => [
                'bookings.arrival' => 'Mark Booking as Arrived',
                'bookings.arrival.form' => 'Access Arrival Form',
                'bookings.departure' => 'Mark Booking as Departed',
                'bookings.unbook' => 'Remove Booking from Slot',
                'bookings.assign-bay' => 'Assign Booking to Tipping Bay',
                'bookings.transfer-bay' => 'Transfer Booking Between Bays',
                'bookings.move-to-waiting' => 'Move Booking to Waiting Area',
                'bookings.clear-bay' => 'Clear Booking from Bay',
            ],
            
            'Bookings - Actions & Workflow' => [
                'bookings.rebook' => 'Rebook Existing Booking',
                'bookings.rebook.show' => 'View Rebook Form',
                'bookings.cancel' => 'Cancel Booking',
                'bookings.history' => 'View Booking History',
                'bookings.start-tipping' => 'Start Tipping Process',
                'bookings.complete-tipping' => 'Complete Tipping Process',
            ],
            
            'Bookings - Export & Documents' => [
                'bookings.export.pdf' => 'Export Bookings to PDF',
                'bookings.export.csv' => 'Export Bookings to CSV',
                'bookings.export.excel' => 'Export Bookings to Excel',
                'bookings.download-pdf' => 'Download Booking PDF',
                'bookings.email-pdf' => 'Email Booking PDF',
            ],
            
            'Factory Operations' => [
                'factory-bookings.view' => 'View Factory Bookings List',
                'factory-bookings.create' => 'Create Factory Booking',
                'factory-bookings.show' => 'View Factory Booking Details',
                'factory-bookings.edit' => 'Edit Factory Booking',
                'factory-bookings.start-processing' => 'Start Processing Factory Booking',
                'factory-bookings.complete' => 'Complete Factory Booking',
                'factory-bookings.mark-departed' => 'Mark Factory Booking as Departed',
            ],
            
            'Factory Workflow' => [
                'factory-booking-workflow.show' => 'View Factory Workflow',
                'factory-booking-workflow.drop-trailer' => 'Drop Trailer',
                'factory-booking-workflow.move-to-location' => 'Move to Location',
                'factory-booking-workflow.drop-trailer-detached' => 'Drop Detached Trailer',
                'factory-booking-workflow.move-to-bay' => 'Move to Tipping Bay',
                'factory-booking-workflow.start-tipping' => 'Start Factory Tipping',
                'factory-booking-workflow.complete-tipping' => 'Complete Factory Tipping',
                'factory-booking-workflow.trailer-depart' => 'Factory Trailer Departure',
            ],
            
            'Tipping Operations' => [
                'tipping-workflow.dashboard' => 'View Tipping Dashboard',
                'tipping-workflow.show' => 'View Tipping Workflow',
                'tipping-workflow.drop-trailer' => 'Drop Trailer at Location',
                'tipping-workflow.move-to-location' => 'Move Trailer to Location',
                'tipping-workflow.drop-trailer-detached' => 'Drop Detached Trailer',
                'tipping-workflow.move-to-bay' => 'Move Trailer to Bay',
                'tipping-workflow.start-tipping' => 'Start Tipping Operation',
                'tipping-workflow.complete-tipping' => 'Complete Tipping Operation',
                'tipping-workflow.unit-depart' => 'Process Unit Departure',
                'tipping-workflow.collection-arrival' => 'Process Collection Arrival',
                'tipping-workflow.collection-depart' => 'Process Collection Departure',
                'tipping-workflow.trailer-depart' => 'Process Trailer Departure',
            ],
            
            'Operations Control' => [
                'operations.assign-drop-zone' => 'Assign Trailer to Drop Zone',
                'operations.unit-depart' => 'Unit Departure Processing',
                'operations.shunt-to-bay' => 'Shunt Trailer to Bay',
                'operations.start-tipping' => 'Start Tipping Operation',
                'operations.complete-tipping' => 'Complete Tipping Operation',
                'operations.move-to-collection-zone' => 'Move to Collection Zone',
                'operations.record-collection' => 'Record Trailer Collection',
                'operations.available-locations' => 'View Available Locations',
                'operations.available-bays' => 'View Available Bays',
                'operations-control.view' => 'Access Operations Control',
                'queue-management.view' => 'View Queue Management',
                'trailer-operations-dashboard.view' => 'View Trailer Operations',
                'trailer-location-report.view' => 'View Trailer Location Report',
            ],
            
            'Tipping Infrastructure' => [
                'tipping-locations.view' => 'View Tipping Locations',
                'tipping-locations.create' => 'Create Tipping Location',
                'tipping-locations.show' => 'View Location Details',
                'tipping-locations.edit' => 'Edit Tipping Location',
                'tipping-locations.update' => 'Save Location Changes',
                'tipping-locations.delete' => 'Delete Tipping Location',
                'tipping-locations.toggle-active' => 'Toggle Location Status',
                'tipping-bays.view' => 'View Tipping Bays',
                'tipping-bays.create' => 'Create Tipping Bay',
                'tipping-bays.show' => 'View Bay Details',
                'tipping-bays.edit' => 'Edit Tipping Bay',
                'tipping-bays.update' => 'Save Bay Changes',
                'tipping-bays.delete' => 'Delete Tipping Bay',
                'tipping-bays.mark-available' => 'Mark Bay Available',
            ],
            
            'Depot Map & Visualization' => [
                'depot-map.view' => 'View Depot Map',
                'depot-map.manage-positions' => 'Manage Positions',
                'depot-map.update-position' => 'Update Bay Position',
                'depot-map.update-location-position' => 'Update Location Position',
                'depot-map.bay-status' => 'View Bay Status',
                'depot-map.change-bay' => 'Change Bay Assignment',
                'depot-map.location-status' => 'View Location Status',
                'depot-map.refresh' => 'Refresh Map Status',
                'depot-map.select-map-file' => 'Select Map File',
                'depot-map.update-map-file' => 'Update Map File',
                'depot-map.upload-map-file' => 'Upload Map File',
                'depot-map.delete-map-file' => 'Delete Map File',
            ],
            
            'Priority & Settings' => [
                'priority-settings.view' => 'View Priority Settings',
                'priority-settings.update-customer-priority' => 'Update Customer Priority',
                'priority-settings.update-booking-priority' => 'Update Booking Priority',
                'priority-settings.set-tipping-type' => 'Set Tipping Type',
                'priority-settings.reset-priorities' => 'Reset Priorities',
            ],
            
            'Factory Tipping Settings' => [
                'settings.manage' => 'Manage Factory Tipping Time Targets',
                'settings.manage.global' => 'Manage Global Factory Tipping Defaults',
            ],
            
            'Slots Management' => [
                'slots.view' => 'View Slots List',
                'slots.create' => 'Create New Slot',
                'slots.edit' => 'Edit Existing Slot',
                'slots.update' => 'Save Slot Changes',
                'slots.delete' => 'Delete Slot',
                'slots.generate' => 'Generate Slots Automatically',
                'slots.generate.form' => 'Access Slot Generation',
                'slot-templates.view' => 'View Slot Templates',
                'slot-templates.create' => 'Create Slot Template',
                'slot-templates.edit' => 'Edit Slot Template',
                'slot-templates.update' => 'Save Template Changes',
                'slot-templates.delete' => 'Delete Slot Template',
                'slot-templates.duplicate' => 'Duplicate Template',
                'slot-templates.bulk-duplicate' => 'Bulk Duplicate Templates',
                'slot-capacity.view' => 'View Slot Capacity',
                'slot-capacity.update' => 'Update Slot Capacity',
                'slot-usage.view' => 'View Slot Usage Statistics',
                'slot-release-rules.view' => 'View Release Rules',
                'slot-release-rules.create' => 'Create Release Rule',
                'slot-release-rules.edit' => 'Edit Release Rule',
                'slot-release-rules.update' => 'Save Release Rule',
                'slot-release-rules.delete' => 'Delete Release Rule',
            ],
            
            'Booking Configuration' => [
                'booking-types.view' => 'View Booking Types',
                'booking-types.create' => 'Create Booking Type',
                'booking-types.edit' => 'Edit Booking Type',
                'booking-types.update' => 'Save Booking Type',
                'booking-types.delete' => 'Delete Booking Type',
                'booking-rules.view' => 'View Booking Rules',
                'booking-rules.store' => 'Save Booking Rules',
            ],
            
            'Customer Management' => [
                'customers.view' => 'View Customers List',
                'customers.create' => 'Create New Customer',
                'customers.show' => 'View Customer Details',
                'customers.edit' => 'Edit Customer Information',
                'customers.update' => 'Save Customer Changes',
                'customers.delete' => 'Delete Customer',
                'customer-behavior.view' => 'View Customer Behavior',
                'customer-behavior.flagged' => 'View Flagged Customers',
                'customer-behavior.export' => 'Export Behavior Data',
                'customer-behavior.show' => 'View Specific Behavior',
                'customer-behavior.settings' => 'View Behavior Settings',
                'customer-behavior.update-settings' => 'Update Behavior Settings',
                'customer-behavior.reset-settings' => 'Reset Behavior Settings',
                'customer-depot-products.view' => 'View Customer Depot Products',
                'customer-depot-products.create' => 'Create Customer Product Link',
                'customer-depot-products.edit' => 'Edit Customer Product',
                'customer-depot-products.update' => 'Save Customer Product',
                'customer-depot-products.delete' => 'Delete Customer Product',
            ],
            
            'Carrier Management' => [
                'carriers.view' => 'View Carriers List',
                'carriers.create' => 'Create New Carrier',
                'carriers.show' => 'View Carrier Details',
                'carriers.edit' => 'Edit Carrier Information',
                'carriers.update' => 'Save Carrier Changes',
                'carriers.delete' => 'Delete Carrier',
                'carriers.restore' => 'Restore Deleted Carrier',
                'carriers.toggle' => 'Toggle Carrier Status',
                'carriers.bulk-action' => 'Bulk Actions on Carriers',
                'carriers.cleanup' => 'Clean Up Carrier Data',
                'carriers.search' => 'Search Carriers',
                'carriers.quick-create' => 'Quick Create Carrier',
                'carriers.merge.view' => 'View Carrier Merge',
                'carriers.merge.preview' => 'Preview Carrier Merge',
                'carriers.merge.execute' => 'Execute Carrier Merge',
                'carriers.merge.history' => 'View Merge History',
                'carriers.merge.undo' => 'Undo Carrier Merge',
            ],
            
            'Depot & Location Management' => [
                'depots.view' => 'View Depots List',
                'depots.create' => 'Create New Depot',
                'depots.show' => 'View Depot Details',
                'depots.edit' => 'Edit Depot Information',
                'depots.update' => 'Save Depot Changes',
                'depots.delete' => 'Delete Depot',
                'depots.products' => 'View Depot Products',
                'depot-products.view' => 'View Depot Products',
                'depot-products.create' => 'Create Depot Product',
                'depot-case-ranges.view' => 'View Case Ranges',
                'depot-case-ranges.create' => 'Create Case Range',
                'depot-case-ranges.edit' => 'Edit Case Range',
                'depot-case-ranges.update' => 'Save Case Range',
                'depot-case-ranges.delete' => 'Delete Case Range',
            ],
            
            'Product Management' => [
                'products.view' => 'View Products List',
                'products.create' => 'Create New Product',
                'products.show' => 'View Product Details',
                'products.edit' => 'Edit Product Information',
                'products.update' => 'Save Product Changes',
                'products.delete' => 'Delete Product',
                'pallet-types.view' => 'View Pallet Types',
                'pallet-types.create' => 'Create Pallet Type',
                'pallet-types.edit' => 'Edit Pallet Type',
                'pallet-types.update' => 'Save Pallet Type',
                'pallet-types.delete' => 'Delete Pallet Type',
            ],
            
            'Trailer Management' => [
                'trailer-types.view' => 'View Trailer Types',
                'trailer-types.create' => 'Create Trailer Type',
                'trailer-types.show' => 'View Trailer Type Details',
                'trailer-types.edit' => 'Edit Trailer Type',
                'trailer-types.update' => 'Save Trailer Type',
                'trailer-types.delete' => 'Delete Trailer Type',
                'trailer-types.restore' => 'Restore Trailer Type',
                'trailer-types.toggle' => 'Toggle Trailer Type Status',
                'dropped-trailers.view' => 'View Dropped Trailers',
                'dropped-trailers.reconnect.form' => 'View Reconnection Form',
                'dropped-trailers.reconnect' => 'Reconnect Dropped Trailer',
                'trailer-collection.view' => 'View Trailer Collection',
            ],
            
            'Time & Scheduling' => [
                'arrival-time-settings.view' => 'View Arrival Time Settings',
                'arrival-time-settings.create' => 'Create Arrival Setting',
                'arrival-time-settings.show' => 'View Arrival Setting',
                'arrival-time-settings.edit' => 'Edit Arrival Setting',
                'arrival-time-settings.update' => 'Save Arrival Setting',
                'arrival-time-settings.delete' => 'Delete Arrival Setting',
                'arrival-time-settings.preview' => 'Preview Arrival Settings',
            ],
            
            'User & System Management' => [
                'users.view' => 'View Users List',
                'users.create' => 'Create New User',
                'users.edit' => 'Edit User Information',
                'users.update' => 'Save User Changes',
                'users.assign-role' => 'Assign User Roles',
                'users.switch' => 'Switch to Different User',
                'users.switch-back' => 'Switch Back to Original User',
                'settings.view' => 'View General Settings',
                'settings.store' => 'Save General Settings',
                'settings.dashboard' => 'View Settings Dashboard',
                'settings.tipping-workflow' => 'Configure Tipping Workflow',
                'settings.pallet-types' => 'Manage Pallet Types',
                'settings.container-sizes' => 'Manage Container Sizes',
            ],
            
            'Reports & Analytics' => [
                'site-admin.search' => 'Global System Search',
                'site-admin.arrivals' => 'View All Site Arrivals',
                'site-admin.departures' => 'View All Site Departures',
                'warehouse.bookings' => 'View Warehouse Bookings',
                'warehouse.factory-bookings' => 'View Warehouse Factory Bookings',
                'warehouse.trailer-report' => 'View Warehouse Trailer Report',
                'warehouse.tipping-workflow' => 'View Warehouse Tipping',
            ],
            
            'Special Functions' => [
                'bookings.fix-historical-departures' => 'Fix Historical Departure Data',
                'bookings.empty-unit-collection' => 'Manage Empty Unit Collection',
                'tipping-guide.view' => 'View Tipping Process Guide',
                'recovery.access' => 'Access Recovery Page',
                'emergency-switch-back' => 'Emergency User Switch Back',
            ],
        ];
    }

    // Get flat list of all function keys
    public static function getAllFunctionKeys(): array
    {
        $functions = self::getAllFunctions();
        $keys = [];
        
        foreach ($functions as $category) {
            $keys = array_merge($keys, array_keys($category));
        }
        
        return $keys;
    }
}
