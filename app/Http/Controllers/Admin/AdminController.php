<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; // Import the base controller
use App\Models\Customer;
use App\Models\Depot;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class AdminController extends Controller
{
    // Display the list of users
    public function index()
    {
        $users = User::with(['roles', 'depots', 'customers'])->paginate(15);
        $roles = Role::all(); // Get all roles for the dropdown
        $depots = Depot::all();
        $customers = Customer::all();  // Get all customers for selection

        return view('warehouse.users.index', compact('users', 'roles', 'depots', 'customers'));
    }

    // Show the form for editing a user
    public function edit($id)
    {
        $user = User::with(['roles', 'depots', 'customers', 'functions', 'customRoles'])->findOrFail($id);
        
        // Check if current user can edit this user
        if (!$user->canBeEditedBy(auth()->user())) {
            if ($user->isProtectedSystemOwner()) {
                abort(403, 'This is a protected system owner account. Only they can edit their own profile.');
            } else {
                abort(403, 'You do not have permission to edit this user.');
            }
        }
        
        $roles = Role::all(); // Get all roles for the checkboxes
        $depots = Depot::all();
        $customers = Customer::all();  // Get all customers for selection
        $customRoles = \App\Models\CustomRole::active()->orderBy('display_name')->get();

        return view('warehouse.users.edit_comprehensive', compact('user', 'roles', 'depots', 'customers', 'customRoles'));
    }

    // Update the user's data
    public function update(Request $request, $id)
    {
        // Find the user first to check permissions
        $user = User::findOrFail($id);
        
        // Check if current user can edit this user (but allow Paul Carr to appear editable)
        if (!$user->canBeEditedBy(auth()->user()) && !$user->isProtectedSystemOwner()) {
            abort(403, 'You do not have permission to edit this user.');
        }
        
        // For protected system owner editing themselves, allow less strict validation
        $isProtectedOwnerEditingSelf = $user->isProtectedSystemOwner() && auth()->user()->id === $user->id;
        
        // Validate the input data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.$id,
            'is_active' => 'required|boolean',
            'role_ids' => $isProtectedOwnerEditingSelf ? 'nullable|array' : 'required|array', // Protected user can assign any roles
            'role_ids.*' => 'integer|exists:roles,id',
            'customer_ids' => 'nullable|array',            // Multiple customers (optional)
            'customer_ids.*' => 'exists:customers,id',       // Ensure all selected customers are valid
            'depot_ids' => $isProtectedOwnerEditingSelf ? 'nullable|array' : 'required|array', // Protected user not restricted
            'depot_ids.*' => 'exists:depots,id',           // Ensure all selected depots are valid
            'depot_id' => 'nullable|exists:depots,id',    // Default depot (optional)
            'customer_id' => 'nullable|exists:customers,id', // Legacy customer for customer role
            'function_keys' => 'nullable|array',           // Function assignments
            'function_keys.*' => 'string',                 // Function key strings
            'custom_role_ids' => 'nullable|array',         // Custom role assignments
            'custom_role_ids.*' => 'integer|exists:custom_roles,id',
        ]);

        // Update user basic fields
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->is_active = $validated['is_active'];
        $user->depot_id = $validated['depot_id']; // Set default depot
        $user->customer_id = $validated['customer_id']; // Legacy customer field
        
        // Protected system owner can never be disabled
        if ($user->isProtectedSystemOwner() && !$user->is_active) {
            $user->is_active = true; // Force to active
        }
        
        $user->save();

        // Check if this is Paul Carr being edited by someone else
        $isPaulCarrEditedByOther = $user->isProtectedSystemOwner() && auth()->user()->id !== $user->id;
        
        if (!$isPaulCarrEditedByOther) {
            // Sync multiple roles via pivot 
            $roleIds = $validated['role_ids'] ?? [];
            
            // Protected system owner can assign themselves any roles (including removing admin if they want)
            // but we ensure they always have access through other means
            $user->roles()->sync($roleIds);

            // Sync depots (many-to-many relationship)
            $depotIds = $validated['depot_ids'] ?? [];
            if ($user->isProtectedSystemOwner() && empty($depotIds)) {
                // Protected user gets access to all depots if none specified
                $depotIds = \App\Models\Depot::pluck('id')->toArray();
            }
            $user->depots()->sync($depotIds);

            // Sync multiple customers (many-to-many relationship)
            $customerIds = $validated['customer_ids'] ?? [];
            $user->customers()->sync($customerIds);

            // Sync custom roles
            $customRoleIds = $validated['custom_role_ids'] ?? [];
            $user->assignCustomRoles($customRoleIds);
        }

        // Handle individual function assignments (only as additions to custom roles)
        $assignedRoles = Role::whereIn('id', $validated['role_ids'])->pluck('name')->toArray();
        $isAdmin = in_array('admin', $assignedRoles);
        
        if (!$isAdmin && array_intersect(['warehouse', 'depot-admin', 'site-admin'], $assignedRoles)) {
            // For warehouse roles, individual functions work alongside custom roles
            $functionKeys = $validated['function_keys'] ?? [];
            
            // Always assign individual functions (they work in addition to custom roles)
            $validFunctionKeys = array_intersect($functionKeys, \App\Models\UserFunction::getAllFunctionKeys());
            $user->assignFunctions($validFunctionKeys);
        } else {
            // Clear functions for non-warehouse roles or admin
            $user->functions()->delete();
        }

        // If the password reset checkbox was checked, generate a new password
        if ($request->filled('reset_password')) {
            $newPassword = $this->generatePassword();
            $user->password = bcrypt($newPassword);
            $user->save();

            return redirect()->route('app.users.edit', $user->id)
                ->with('success', 'User updated successfully.')
                ->with('new_password', $newPassword);
        }

        return redirect()->route('app.users.index')
            ->with('success', 'User updated successfully.');
    }

    // Helper method to generate a random password
    private function generatePassword($length = 8)
    {
        // Generate a password of 3 words with at least 8 characters
        $words = ['apple', 'banana', 'cherry', 'date', 'elderberry', 'fig', 'grape', 'honeydew'];
        $password = implode('', array_map(function () use ($words) {
            return $words[array_rand($words)];
        }, range(1, 3))); // Generate 3 random words

        // Ensure the password is at least 8 characters
        return strlen($password) < $length
            ? $password.rand(10, 99)
            : $password;
    }

    // Store method for creating a user
    public function store(Request $request)
    {

        // DEBUG: dump all input
        // dd($request->all());

        // Validate the input data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'role_ids' => 'required|array',           // Multiple roles
            'role_ids.*' => 'integer|exists:roles,id',
            'customer_ids' => 'nullable|array',            // Multiple customers (optional)
            'customer_ids.*' => 'exists:customers,id',       // Ensure all selected customers are valid
            'depot_ids' => 'required|array',
            'depot_ids.*' => 'exists:depots,id',
            'depot_id' => 'nullable|exists:depots,id',    // Default depot (optional)
            'password' => 'required_without:generate_password|string|min:8',
            'generate_password' => 'nullable|boolean',
        ]);

        // Handle password generation
        $password = $validated['password'] ?? null;
        if ($request->filled('generate_password') || ! $password) {
            $password = $this->generatePassword();
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => bcrypt($password),
            'depot_id' => $validated['depot_id'],
        ]);

        // Sync multiple roles via pivot
        $user->roles()->sync($validated['role_ids']);

        // Sync depots
        $user->depots()->sync($validated['depot_ids']);

        // Sync multiple customers (many-to-many relationship)
        $customerIds = $validated['customer_ids'] ?? [];
        $user->customers()->sync($customerIds);

        // Show generated password if applicable
        if ($request->filled('generate_password') || ! $validated['password']) {
            return redirect()->route('app.users.edit', $user->id)
                ->with('success', 'User created successfully.')
                ->with('new_password', $password);
        }

        return redirect()->route('app.users.index')
            ->with('success', 'User created successfully.');
    }

    public function create()
    {
        $roles = Role::all();
        $depots = Depot::all();
        $customers = Customer::all();

        return view('warehouse.users.create', compact('roles', 'depots', 'customers'));
    }
}
