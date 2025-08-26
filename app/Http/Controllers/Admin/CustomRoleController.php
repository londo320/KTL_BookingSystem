<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomRole;
use App\Models\UserFunction;
use Illuminate\Http\Request;

class CustomRoleController extends Controller
{
    /**
     * Display a listing of custom roles
     */
    public function index()
    {
        $roles = CustomRole::withCount('users')->paginate(10);
        return view('admin.custom-roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new custom role
     */
    public function create()
    {
        $allFunctions = UserFunction::getAllFunctions();
        return view('admin.custom-roles.create', compact('allFunctions'));
    }

    /**
     * Store a newly created custom role
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:custom_roles,name',
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'function_keys' => 'nullable|array',
            'function_keys.*' => 'string',
        ]);

        // Validate function keys exist
        if (isset($validated['function_keys'])) {
            $validFunctionKeys = array_intersect($validated['function_keys'], UserFunction::getAllFunctionKeys());
            $validated['function_keys'] = $validFunctionKeys;
        }

        CustomRole::create($validated);

        return redirect()->route('admin.custom-roles.index')
            ->with('success', 'Custom role created successfully.');
    }

    /**
     * Display the specified custom role
     */
    public function show(CustomRole $customRole)
    {
        $customRole->load('users');
        $allFunctions = UserFunction::getAllFunctions();
        
        return view('admin.custom-roles.show', compact('customRole', 'allFunctions'));
    }

    /**
     * Show the form for editing the specified custom role
     */
    public function edit(CustomRole $customRole)
    {
        $allFunctions = UserFunction::getAllFunctions();
        return view('admin.custom-roles.edit', compact('customRole', 'allFunctions'));
    }

    /**
     * Update the specified custom role
     */
    public function update(Request $request, CustomRole $customRole)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:custom_roles,name,' . $customRole->id,
            'display_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'function_keys' => 'nullable|array',
            'function_keys.*' => 'string',
        ]);

        // Validate function keys exist
        if (isset($validated['function_keys'])) {
            $validFunctionKeys = array_intersect($validated['function_keys'], UserFunction::getAllFunctionKeys());
            $validated['function_keys'] = $validFunctionKeys;
        } else {
            $validated['function_keys'] = [];
        }

        $customRole->update($validated);

        return redirect()->route('admin.custom-roles.index')
            ->with('success', 'Custom role updated successfully.');
    }

    /**
     * Remove the specified custom role
     */
    public function destroy(CustomRole $customRole)
    {
        // Check if role has users assigned
        if ($customRole->users()->count() > 0) {
            return redirect()->route('admin.custom-roles.index')
                ->with('error', 'Cannot delete role that has users assigned to it.');
        }

        $customRole->delete();

        return redirect()->route('admin.custom-roles.index')
            ->with('success', 'Custom role deleted successfully.');
    }

    /**
     * Toggle active status of custom role
     */
    public function toggle(CustomRole $customRole)
    {
        $customRole->update(['is_active' => !$customRole->is_active]);

        return redirect()->route('admin.custom-roles.index')
            ->with('success', 'Role status updated successfully.');
    }

    /**
     * Create predefined roles
     */
    public function createPredefined()
    {
        CustomRole::createPredefinedRoles();

        return redirect()->route('admin.custom-roles.index')
            ->with('success', 'Predefined roles created successfully.');
    }
}
