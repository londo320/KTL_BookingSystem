<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CustomRole extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'is_active',
        'function_keys',
    ];

    protected $casts = [
        'function_keys' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Users that have this custom role
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_custom_roles');
    }

    /**
     * Get all function keys for this role
     */
    public function getFunctionKeys(): array
    {
        return $this->function_keys ?? [];
    }

    /**
     * Check if role has a specific function
     */
    public function hasFunction(string $functionKey): bool
    {
        return in_array($functionKey, $this->getFunctionKeys());
    }

    /**
     * Assign functions to this role
     */
    public function assignFunctions(array $functionKeys): void
    {
        // Validate function keys exist
        $validFunctionKeys = array_intersect($functionKeys, UserFunction::getAllFunctionKeys());
        $this->function_keys = $validFunctionKeys;
        $this->save();
    }

    /**
     * Scope for active roles only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Create predefined roles
     */
    public static function createPredefinedRoles(): void
    {
        $roles = [
            // ===== WAREHOUSE ROLES =====
            [
                'name' => 'warehouse_operative',
                'display_name' => 'Warehouse Operative',
                'description' => 'Day-to-day warehouse operations - check-in vehicles, assign bays, update statuses',
                'is_active' => true,
                'function_keys' => [
                    'dashboard.warehouse',
                    'bookings.view',
                    'bookings.show',
                    'bookings.arrival',
                    'bookings.departure',
                    'bookings.assign-bay',
                    'bookings.clear-bay',
                    'tipping-workflow.dashboard',
                    'tipping-workflow.show',
                ]
            ],
            [
                'name' => 'warehouse_manager',
                'display_name' => 'Warehouse Manager',
                'description' => 'Full warehouse management - all operations, bookings, reporting',
                'is_active' => true,
                'function_keys' => [
                    'dashboard.warehouse',
                    'bookings.view',
                    'bookings.create',
                    'bookings.edit',
                    'bookings.delete',
                    'bookings.show',
                    'bookings.arrival',
                    'bookings.departure',
                    'bookings.assign-bay',
                    'bookings.transfer-bay',
                    'bookings.clear-bay',
                    'bookings.export.pdf',
                    'bookings.export.csv',
                    'factory-bookings.view',
                    'factory-bookings.create',
                    'factory-bookings.edit',
                    'tipping-workflow.dashboard',
                    'tipping-workflow.show',
                    'operations.assign-drop-zone',
                    'operations.shunt-to-bay',
                ]
            ],

            // ===== FORKLIFT DRIVER =====
            [
                'name' => 'forklift_driver',
                'display_name' => 'Forklift Driver',
                'description' => 'Equipment operator - view assigned tasks, update tipping progress',
                'is_active' => true,
                'function_keys' => [
                    'dashboard.warehouse',
                    'bookings.view',
                    'bookings.show',
                    'tipping-workflow.dashboard',
                    'tipping-workflow.show',
                    'tipping-workflow.start-tipping',
                    'tipping-workflow.complete-tipping',
                    'operations.start-tipping',
                    'operations.complete-tipping',
                ]
            ],

            // ===== YARD CONTROLLER =====
            [
                'name' => 'yard_controller',
                'display_name' => 'Yard Controller',
                'description' => 'Manages vehicle movements, parking assignments, bay allocation',
                'is_active' => true,
                'function_keys' => [
                    'dashboard.warehouse',
                    'bookings.view',
                    'bookings.show',
                    'bookings.assign-bay',
                    'bookings.transfer-bay',
                    'bookings.move-to-waiting',
                    'tipping-workflow.dashboard',
                    'tipping-workflow.show',
                    'tipping-workflow.move-to-location',
                    'operations.assign-drop-zone',
                ]
            ],

            // ===== GATE SECURITY =====
            [
                'name' => 'gate_security',
                'display_name' => 'Gate Security',
                'description' => 'Vehicle check-in/out at gate, verify bookings',
                'is_active' => true,
                'function_keys' => [
                    'dashboard.warehouse',
                    'bookings.view',
                    'bookings.show',
                    'bookings.arrival',
                    'bookings.arrival.form',
                ]
            ],

            // ===== VIEWER =====
            [
                'name' => 'viewer',
                'display_name' => 'Reports Viewer',
                'description' => 'Read-only access to bookings, reports, and analytics',
                'is_active' => true,
                'function_keys' => [
                    'dashboard.view',
                    'dashboard.warehouse',
                    'bookings.view',
                    'bookings.show',
                    'bookings.export.pdf',
                    'bookings.export.csv',
                    'factory-bookings.view',
                ]
            ],

            // ===== CUSTOMER ADMIN =====
            [
                'name' => 'customer_admin',
                'display_name' => 'Customer Admin',
                'description' => 'Company admin - manage users and all company bookings',
                'is_active' => true,
                'function_keys' => [
                    'customer.dashboard',
                    'bookings.view',
                    'bookings.create',
                    'bookings.edit',
                    'bookings.show',
                    'bookings.cancel',
                    'bookings.export.pdf',
                ]
            ],
        ];

        foreach ($roles as $roleData) {
            self::updateOrCreate(
                ['name' => $roleData['name']],
                $roleData
            );
        }
    }
}
