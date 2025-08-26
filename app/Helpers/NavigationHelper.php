<?php

namespace App\Helpers;

use App\Models\Setting;

class NavigationHelper
{
    /**
     * Generate dynamic navigation items based on user functions
     */
    public static function getNavigationItems(): array
    {
        $user = auth()->user();
        if (!$user) {
            return [];
        }

        // Check module availability
        $inboundEnabled = Setting::get('inbound_module_enabled', true);
        $outboundEnabled = Setting::get('outbound_module_enabled', false);

        $nav = [];

        // Dashboard
        if ($user->hasFunction('dashboard.view') || $user->hasRole('admin')) {
            $nav[] = [
                'name' => 'Dashboard',
                'route' => 'app.dashboard',
                'icon' => '📊',
                'active' => request()->routeIs('app.dashboard', 'warehouse.dashboard'),
            ];
        }

        // Slots Management
        if ($user->hasFunction('slots.view') || $user->hasRole('admin')) {
            $nav[] = [
                'name' => 'Slots',
                'route' => 'app.slots.index',
                'icon' => '📅',
                'active' => request()->routeIs('app.slots.*'),
            ];
        }

        // Bookings Dropdown (Inbound Module)
        $bookingItems = [];
        if ($inboundEnabled && ($user->hasFunction('bookings.view') || $user->hasRole('admin'))) {
            $bookingItems[] = [
                'name' => 'Bookings (Full View)',
                'route' => 'app.bookings.index',
                'description' => 'Complete booking management with all features',
                'active' => request()->routeIs('app.bookings.index') || 
                          (request()->routeIs('app.bookings.*') && !request()->routeIs('app.bookings.streamlined')),
            ];
        }

        if ($inboundEnabled && ($user->hasFunction('bookings.view-streamlined') || $user->hasRole('admin'))) {
            $bookingItems[] = [
                'name' => 'Bookings (Live View)',
                'route' => 'app.bookings.streamlined',
                'description' => 'Streamlined interface with live updates',
                'active' => request()->routeIs('app.bookings.streamlined'),
            ];
        }

        if ($inboundEnabled && ($user->hasFunction('factory-bookings.view') || $user->hasRole('admin'))) {
            $bookingItems[] = [
                'name' => 'Factory Bookings',
                'route' => 'app.factory-bookings.index',
                'description' => 'Factory inbound operations with 60-min tipping',
                'active' => request()->routeIs('app.factory-bookings.*'),
            ];
        }

        if (count($bookingItems) > 0) {
            $nav[] = [
                'name' => 'Bookings',
                'icon' => '📋',
                'dropdown' => $bookingItems,
                'active' => collect($bookingItems)->contains('active', true),
            ];
        }

        // Customer Analysis
        if ($user->hasFunction('customer-behavior.view') || $user->hasRole('admin')) {
            $nav[] = [
                'name' => 'Customer Analysis',
                'route' => 'app.customer-behavior.index',
                'icon' => '📊',
                'active' => request()->routeIs('app.customer-behavior.*'),
                'color' => 'purple',
            ];
        }

        // Depot Map
        if ($user->hasFunction('depot-map.view') || $user->hasRole('admin')) {
            $nav[] = [
                'name' => 'Depot Map',
                'route' => 'app.depot-map.index',
                'icon' => '🗺️',
                'active' => request()->routeIs('app.depot-map.*'),
                'color' => 'green',
            ];
        }

        // Tipping Operations Dropdown (Inbound Module)
        $tippingItems = [];
        if ($inboundEnabled && ($user->hasFunction('tipping-workflow.dashboard') || $user->hasRole('admin'))) {
            $tippingItems[] = [
                'name' => 'Dashboard',
                'route' => 'app.tipping-workflow.dashboard',
                'active' => request()->routeIs('app.tipping-workflow.*'),
            ];
        }

        if ($inboundEnabled && ($user->hasFunction('tipping-locations.view') || $user->hasRole('admin'))) {
            $tippingItems[] = [
                'name' => 'Drop Locations',
                'route' => 'app.tipping-locations.index',
                'active' => request()->routeIs('app.tipping-locations.*'),
            ];
        }

        if ($inboundEnabled && ($user->hasFunction('tipping-bays.view') || $user->hasRole('admin'))) {
            $tippingItems[] = [
                'name' => 'Tipping Bays',
                'route' => 'app.tipping-bays.index',
                'active' => request()->routeIs('app.tipping-bays.*'),
            ];
        }

        if ($inboundEnabled && ($user->hasFunction('queue-management.view') || $user->hasRole('admin'))) {
            $tippingItems[] = [
                'name' => 'Queue Management',
                'route' => 'app.queue-management',
                'active' => request()->routeIs('app.queue-management'),
            ];
        }

        if ($inboundEnabled && ($user->hasFunction('operations-control.view') || $user->hasRole('admin'))) {
            $tippingItems[] = [
                'name' => 'Site Operations Control',
                'route' => 'app.operations-control',
                'active' => request()->routeIs('app.operations-control'),
            ];
        }

        if ($inboundEnabled && ($user->hasFunction('warehouse.trailer-report') || $user->hasRole('admin'))) {
            $tippingItems[] = [
                'name' => 'Trailer Location Report',
                'route' => 'app.trailer-report',
                'active' => request()->routeIs('app.trailer-report', 'warehouse.trailer-report'),
            ];
        }

        // Factory Tipping Configuration (for operational users)
        if ($user->hasFunction('settings.manage')) {
            if (count($tippingItems) > 0) {
                $tippingItems[] = ['divider' => true];
            }
            
            $tippingItems[] = [
                'name' => 'Factory Tipping Targets',
                'route' => 'app.settings.factory-tipping-targets',
                'icon' => '🏭',
                'active' => request()->routeIs('app.settings.factory-tipping-targets*'),
            ];
        }

        if (count($tippingItems) > 0) {
            $nav[] = [
                'name' => 'Tipping',
                'icon' => '🚛',
                'dropdown' => $tippingItems,
                'active' => collect($tippingItems)->contains('active', true),
                'color' => 'orange',
            ];
        }

        // Outbound Operations Dropdown
        $outboundItems = [];
        if ($outboundEnabled && ($user->hasFunction('outbound.loads.view') || $user->hasRole(['warehouse', 'depot-admin', 'admin']))) {
            $outboundItems[] = [
                'name' => 'Load Dashboard',
                'route' => 'outbound.loads.index',
                'description' => 'Manage outbound loads and deliveries',
                'active' => request()->routeIs('outbound.loads.*'),
            ];
        }

        if ($outboundEnabled && ($user->hasFunction('outbound.imports.view') || $user->hasRole(['warehouse', 'depot-admin', 'admin']))) {
            $outboundItems[] = [
                'name' => 'WMS File Imports',
                'route' => 'outbound.imports.dashboard',
                'description' => 'Upload and process WMS files',
                'active' => request()->routeIs('outbound.imports.*'),
            ];
        }

        if ($outboundEnabled && ($user->hasFunction('outbound.arrivals.view') || $user->hasRole(['warehouse', 'depot-admin', 'admin']))) {
            $outboundItems[] = [
                'name' => 'Driver Arrivals',
                'route' => 'outbound.arrivals.dashboard',
                'description' => 'Register driver arrivals and match orders',
                'active' => request()->routeIs('outbound.arrivals.*', 'outbound.physical-loads.*'),
            ];
        }

        if ($outboundEnabled && ($user->hasFunction('outbound.addresses.view') || $user->hasRole(['warehouse', 'depot-admin', 'admin']))) {
            $outboundItems[] = [
                'name' => 'Customer Addresses',
                'route' => 'outbound.addresses.index',
                'description' => 'Manage delivery addresses',
                'active' => request()->routeIs('outbound.addresses.*'),
            ];
        }

        if (count($outboundItems) > 0) {
            $nav[] = [
                'name' => 'Outbound',
                'icon' => '📦',
                'dropdown' => $outboundItems,
                'active' => collect($outboundItems)->contains('active', true),
                'color' => 'blue',
            ];
        }

        // Settings Dropdown
        $settingsItems = [];
        
        // Site Configuration (for depot-admin and site-admin)
        if ($user->hasRole(['depot-admin', 'site-admin', 'admin'])) {
            if ($user->hasFunction('depots.view') || $user->hasRole('admin')) {
                $settingsItems[] = [
                    'name' => 'Depots',
                    'route' => 'app.depots.index',
                    'icon' => '🏭',
                    'active' => request()->routeIs('app.depots.*'),
                ];
            }

            if ($user->hasFunction('products.view') || $user->hasRole('admin')) {
                $settingsItems[] = [
                    'name' => 'Products',
                    'route' => 'app.products.index',
                    'icon' => '📦',
                    'active' => request()->routeIs('app.products.*'),
                ];
            }

            if ($user->hasFunction('customers.view') || $user->hasRole('admin')) {
                $settingsItems[] = [
                    'name' => 'Customers',
                    'route' => 'app.customers.index',
                    'icon' => '👥',
                    'active' => request()->routeIs('app.customers.*'),
                ];
            }

            if ($user->hasFunction('slot-templates.view') || $user->hasRole('admin')) {
                $settingsItems[] = [
                    'name' => 'Slot Templates',
                    'route' => 'app.slot-templates.index',
                    'icon' => '📅',
                    'active' => request()->routeIs('app.slot-templates.*'),
                ];
            }

            if ($user->hasFunction('booking-types.view') || $user->hasRole('admin')) {
                $settingsItems[] = [
                    'name' => 'Booking Types',
                    'route' => 'app.booking-types.index',
                    'icon' => '📝',
                    'active' => request()->routeIs('app.booking-types.*'),
                ];
            }
        }

        // Admin Settings (for admin only) OR protected system owner
        if ($user->hasRole('admin') || $user->isProtectedSystemOwner()) {
            if (count($settingsItems) > 0) {
                $settingsItems[] = ['divider' => true];
            }
            
            $settingsItems[] = [
                'name' => 'System Settings',
                'route' => 'app.settings.dashboard',
                'icon' => '🛠️',
                'active' => false,
            ];

            $settingsItems[] = [
                'name' => 'User Management',
                'route' => 'app.users.index',
                'icon' => '👤',
                'active' => request()->routeIs('app.users.*'),
            ];

            $settingsItems[] = [
                'name' => 'Custom Roles',
                'route' => 'app.custom-roles.index',
                'icon' => '🏷️',
                'active' => request()->routeIs('app.custom-roles.*'),
            ];
        }


        if (count($settingsItems) > 0) {
            $nav[] = [
                'name' => 'Settings',
                'icon' => '⚙️',
                'dropdown' => $settingsItems,
                'active' => collect($settingsItems)->contains('active', true),
            ];
        }

        return $nav;
    }

    /**
     * Get the route prefix based on current context
     */
    public static function getRoutePrefix(): string
    {
        return request()->route()->getPrefix() === 'depot-admin' ? 'depot.' : 'app.';
    }
}