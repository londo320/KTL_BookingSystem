<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

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
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
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

        // For admin/site roles, if no specific customers assigned, they can see all customers
        if (empty($assignedCustomerIds)) {
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

        // Admin/site roles can see all if no specific customers assigned
        return $this->customers()->count() === 0;
    }
}
