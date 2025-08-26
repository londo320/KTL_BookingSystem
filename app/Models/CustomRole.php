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
            [
                'name' => 'warehouse_operator',
                'display_name' => 'Warehouse Operator',
                'description' => 'Basic warehouse operations - view bookings, manage arrivals/departures',
                'function_keys' => [
                    'dashboard.warehouse',
                    'bookings.view',
                    'bookings.arrival',
                    'bookings.departure',
                    'bookings.assign-bay',
                    'tipping-workflow.dashboard',
                ]
            ],
            [
                'name' => 'warehouse_supervisor',
                'display_name' => 'Warehouse Supervisor',
                'description' => 'Extended warehouse operations - create/edit bookings, manage workflows',
                'function_keys' => [
                    'dashboard.warehouse',
                    'bookings.view',
                    'bookings.create',
                    'bookings.edit',
                    'bookings.arrival',
                    'bookings.departure',
                    'bookings.assign-bay',
                    'bookings.transfer-bay',
                    'factory-bookings.view',
                    'factory-bookings.create',
                    'tipping-workflow.dashboard',
                    'warehouse.trailer-report',
                    'operations.assign-drop-zone',
                    'operations.shunt-to-bay',
                ]
            ],
            [
                'name' => 'warehouse_manager',
                'display_name' => 'Warehouse Manager',
                'description' => 'Full warehouse management - all operations plus reporting and export',
                'function_keys' => [
                    'dashboard.warehouse',
                    'bookings.view',
                    'bookings.create',
                    'bookings.edit',
                    'bookings.delete',
                    'bookings.arrival',
                    'bookings.departure',
                    'bookings.assign-bay',
                    'bookings.transfer-bay',
                    'bookings.export.pdf',
                    'bookings.export.csv',
                    'bookings.export.excel',
                    'factory-bookings.view',
                    'factory-bookings.create',
                    'factory-bookings.edit',
                    'tipping-workflow.dashboard',
                    'warehouse.trailer-report',
                    'warehouse.tipping-workflow',
                    'operations.assign-drop-zone',
                    'operations.shunt-to-bay',
                    'operations.start-tipping',
                    'operations.complete-tipping',
                    'customer-behavior.view',
                ]
            ],
            [
                'name' => 'reports_viewer',
                'display_name' => 'Reports Viewer',
                'description' => 'Read-only access to reports and analytics',
                'function_keys' => [
                    'dashboard.warehouse',
                    'bookings.view',
                    'warehouse.trailer-report',
                    'bookings.export.pdf',
                    'bookings.export.csv',
                    'bookings.export.excel',
                    'customer-behavior.view',
                    'factory-bookings.view',
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
