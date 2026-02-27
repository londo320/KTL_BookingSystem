<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\CustomPasswordReset;

class User extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;
    use SoftDeletes; // Add HasRoles trait

    // Mass assignable attributes
    protected $fillable = [
        'name',
        'email',
        'password',
        'email_verified_at',
        'depot_id',
        'is_active',
        'switch_user_enabled',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
        'switch_user_enabled' => 'boolean',
    ];

    // Relationship with depots (many-to-many for access)
    public function depots()
    {
        return $this->belongsToMany(Depot::class, 'depot_user');
    }

    // Relationship with default depot (one-to-one)
    public function defaultDepot()
    {
        return $this->belongsTo(Depot::class, 'depot_id');
    }

    // Relationship with customers (Many-to-Many) - New for multiple customer assignment
    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'customer_user');
    }

    // Relationship with user functions
    public function functions(): HasMany
    {
        return $this->hasMany(UserFunction::class);
    }

    public function depotIds()
    {
        return $this->depots->pluck('id')->toArray();
    }

    public function getCustomerId(): ?int
    {
        // Return first assigned customer ID, or null if none
        return $this->customers()->first()?->id;
    }

    public function belongsToDepot(int $depotId): bool
    {
        return $this->depots->pluck('id')->contains($depotId);
    }

    /**
     * Check if user is active and can access the system
     */
    public function isActive(): bool
    {
        return $this->is_active ?? true; // Default to active if field doesn't exist
    }

    /**
     * Check if this is the protected system owner
     */
    public function isProtectedSystemOwner(): bool
    {
        return $this->email === 'paul.carr@knowleslogistics.com';
    }

    /**
     * Check if current authenticated user can edit this user
     */
    public function canBeEditedBy(?User $editor = null): bool
    {
        $editor = $editor ?? auth()->user();
        
        // If no editor (not authenticated), deny
        if (!$editor) {
            return false;
        }
        
        // Protected system owner can always edit themselves
        if ($this->isProtectedSystemOwner() && $editor->id === $this->id) {
            return true;
        }
        
        // Allow all admin users to appear to edit Paul Carr (middleware will handle protection)
        // This makes the UI appear normal while backend protection is handled elsewhere
        
        // Protected system owner can edit anyone
        if ($editor->isProtectedSystemOwner()) {
            return true;
        }
        
        // Regular users can be edited by admins or themselves
        return $editor->hasRole('admin') || $editor->id === $this->id;
    }

    /**
     * Check if user has access to user management functions
     */
    public function canAccessUserManagement(): bool
    {
        // Protected system owner always has access
        if ($this->isProtectedSystemOwner()) {
            return true;
        }
        
        // Regular admin access
        return $this->hasRole('admin') || $this->hasFunction('users.view');
    }

    /**
     * Get all depot IDs this user has access to
     */
    public function getAccessibleDepotIds(): array
    {
        // Get assigned depots from many-to-many relationship
        $assignedDepotIds = $this->depots()->pluck('depots.id')->toArray();
        
        // If no depots assigned, admin/site-admin can see all depots
        if (empty($assignedDepotIds) && ($this->hasRole('admin') || $this->hasRole('site-admin'))) {
            return \App\Models\Depot::pluck('id')->toArray();
        }
        
        return $assignedDepotIds;
    }

    /**
     * Get all customer IDs this user has access to
     * - All roles: Uses many-to-many customers relationship
     * - If no customers assigned, admins can see all customers
     */
    public function getAccessibleCustomerIds(): array
    {
        // Get assigned customers from many-to-many relationship
        $assignedCustomerIds = $this->customers()->pluck('customers.id')->toArray();

        // If user has customer role and no customers assigned, they see nothing
        if ($this->hasRole('customer')) {
            return $assignedCustomerIds;
        }

        // For admin/site/warehouse/depot-admin roles, if no specific customers assigned, they can see all customers
        if (empty($assignedCustomerIds) && ($this->hasRole('admin') || $this->hasRole('site-admin') || $this->hasRole('warehouse') || $this->hasRole('depot-admin'))) {
            return Customer::pluck('id')->toArray();
        }

        return $assignedCustomerIds;
    }

    /**
     * Check if user can access a specific customer
     */
    public function canAccessCustomer(int $customerId): bool
    {
        return in_array($customerId, $this->getAccessibleCustomerIds());
    }

    /**
     * Check if user can see all customers (admin/site roles with no specific assignment)
     */
    public function canSeeAllCustomers(): bool
    {
        // Customer role can never see all customers
        if ($this->hasRole('customer')) {
            return false;
        }

        // Admin/site/warehouse/depot-admin roles can see all if no specific customers assigned
        return $this->customers()->count() === 0 && ($this->hasRole('admin') || $this->hasRole('site-admin') || $this->hasRole('warehouse') || $this->hasRole('depot-admin'));
    }

    /**
     * Custom roles relationship
     */
    public function customRoles(): BelongsToMany
    {
        return $this->belongsToMany(CustomRole::class, 'user_custom_roles');
    }

    /**
     * Check if user has a specific function
     */
    public function hasFunction(string $functionKey): bool
    {
        // Admin role has all functions
        if ($this->hasRole('admin')) {
            return true;
        }

        // Check if user has this specific function assigned directly
        if ($this->functions()->where('function_key', $functionKey)->exists()) {
            return true;
        }

        // Check if any of the user's custom roles has this function
        foreach ($this->customRoles()->active()->get() as $role) {
            if ($role->hasFunction($functionKey)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all function keys for this user
     */
    public function getFunctionKeys(): array
    {
        // Admin role has all functions
        if ($this->hasRole('admin')) {
            return UserFunction::getAllFunctionKeys();
        }

        $functionKeys = [];
        
        // Get directly assigned functions
        $functionKeys = array_merge($functionKeys, $this->functions()->pluck('function_key')->toArray());

        // Get functions from custom roles
        foreach ($this->customRoles()->active()->get() as $role) {
            $functionKeys = array_merge($functionKeys, $role->getFunctionKeys());
        }

        return array_unique($functionKeys);
    }

    /**
     * Assign custom roles to user
     */
    public function assignCustomRoles(array $customRoleIds): void
    {
        $this->customRoles()->sync($customRoleIds);
    }

    /**
     * Assign functions to user
     */
    public function assignFunctions(array $functionKeys): void
    {
        // Remove existing functions
        $this->functions()->delete();

        // Add new functions
        foreach ($functionKeys as $functionKey) {
            $this->functions()->create(['function_key' => $functionKey]);
        }
    }

    /**
     * Check if user has warehouse access (warehouse role or admin)
     */
    public function hasWarehouseAccess(): bool
    {
        return $this->hasRole(['warehouse', 'admin', 'depot-admin', 'site-admin']);
    }
    
    /**
     * Override save method to protect Paul Carr from changes by others
     */
    public function save(array $options = []): bool
    {
        // Check if this is Paul Carr and someone else is trying to edit him
        if ($this->email === 'paul.carr@knowleslogistics.com') {
            $currentUser = auth()->user();
            
            // If Paul Carr is editing himself, allow the change
            if ($currentUser && $currentUser->id === $this->id) {
                return parent::save($options);
            }
            
            // Someone else is trying to edit Paul Carr - simulate success but don't save
            return true; // Return true to simulate successful save
        }
        
        // For all other users, proceed normally
        return parent::save($options);
    }
    
    /**
     * Override update method to protect Paul Carr from changes by others
     */
    public function update(array $attributes = [], array $options = []): bool
    {
        // Check if this is Paul Carr and someone else is trying to edit him
        if ($this->email === 'paul.carr@knowleslogistics.com') {
            $currentUser = auth()->user();
            
            // If Paul Carr is editing himself, allow the change
            if ($currentUser && $currentUser->id === $this->id) {
                return parent::update($attributes, $options);
            }
            
            // Someone else is trying to edit Paul Carr - simulate success but don't save
            return true; // Return true to simulate successful update
        }
        
        // For all other users, proceed normally
        return parent::update($attributes, $options);
    }

    /**
     * Send the password reset notification.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomPasswordReset($token));
    }
}
