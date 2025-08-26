@extends('layouts.admin')

@section('content')
<div class="py-6 max-w-7xl mx-auto">
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('new_password'))
        <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-yellow-800"><strong>New password:</strong> {{ session('new_password') }}</p>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-2xl font-semibold text-gray-900">Edit User: {{ $user->name }}</h1>
            <p class="text-sm text-gray-600 mt-1">Configure user access, roles, and specific system functions</p>
        </div>

        <form method="POST" action="{{ route('app.users.update', $user->id) }}" class="p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Left Column: Basic Information --}}
                <div class="space-y-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">👤 Basic Information</h2>
                        
                        {{-- Name --}}
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                            <input type="text" id="name" name="name" required
                                   value="{{ old('name', $user->name) }}"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('name')<span class="text-red-600 text-xs">{{ $message }}</span>@enderror
                        </div>

                        {{-- Email --}}
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="email" name="email" required
                                   value="{{ old('email', $user->email) }}"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            @error('email')<span class="text-red-600 text-xs">{{ $message }}</span>@enderror
                        </div>

                        {{-- Password Reset --}}
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="reset_password" class="border-gray-300 rounded">
                                <span class="ml-2 text-sm">Reset to default password</span>
                            </label>
                        </div>
                    </div>

                    {{-- Roles Section --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">🏷️ User Roles</h3>
                        <div class="space-y-3">
                            @foreach($roles as $role)
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                                    <input type="checkbox" 
                                           name="role_ids[]" 
                                           value="{{ $role->id }}"
                                           class="border-gray-300 rounded"
                                           {{ in_array($role->id, old('role_ids', $user->roles->pluck('id')->toArray())) ? 'checked' : '' }}
                                           onchange="toggleFunctionSections()">
                                    <div class="ml-3">
                                        <span class="text-sm font-medium capitalize">{{ $role->name }}</span>
                                        <div class="text-xs text-gray-500">
                                            @if($role->name === 'admin')
                                                Full system access - All functions enabled
                                            @elseif($role->name === 'customer')
                                                Customer portal access only
                                            @elseif($role->name === 'warehouse')
                                                Warehouse operations - Functions configurable below
                                            @elseif($role->name === 'depot-admin')
                                                Depot management + warehouse functions
                                            @elseif($role->name === 'site-admin')
                                                Site operations + warehouse functions
                                            @endif
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('role_ids')<span class="text-red-600 text-xs">{{ $message }}</span>@enderror
                    </div>

                    {{-- Custom Roles Section --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">🏷️ Custom Roles</h3>
                        <div class="space-y-3">
                            @foreach($customRoles as $customRole)
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                                    <input type="checkbox" 
                                           name="custom_role_ids[]" 
                                           value="{{ $customRole->id }}"
                                           class="border-gray-300 rounded"
                                           {{ in_array($customRole->id, old('custom_role_ids', $user->customRoles->pluck('id')->toArray())) ? 'checked' : '' }}>
                                    <div class="ml-3">
                                        <span class="text-sm font-medium">{{ $customRole->display_name }}</span>
                                        <div class="text-xs text-gray-500">
                                            {{ count($customRole->getFunctionKeys()) }} functions assigned
                                        </div>
                                        @if($customRole->description)
                                            <div class="text-xs text-gray-400 mt-1">{{ $customRole->description }}</div>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @if($customRoles->count() == 0)
                            <div class="text-center py-4 bg-gray-50 rounded-lg">
                                <p class="text-sm text-gray-600">No custom roles available.</p>
                                <a href="{{ route('app.custom-roles.create') }}" class="text-blue-600 text-xs hover:underline">Create a custom role</a>
                            </div>
                        @endif
                        @error('custom_role_ids')<span class="text-red-600 text-xs">{{ $message }}</span>@enderror
                    </div>
                </div>

                {{-- Middle Column: Access Control --}}
                <div class="space-y-6">
                    {{-- Depot Access --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">🏢 Depot Access</h3>
                        
                        {{-- Depot Assignment --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Accessible Depots</label>
                            <div class="max-h-40 overflow-y-auto border rounded-md p-3 space-y-2">
                                @foreach($depots as $depot)
                                    <label class="flex items-center">
                                        <input type="checkbox" name="depot_ids[]" value="{{ $depot->id }}"
                                               class="border-gray-300 rounded"
                                               {{ in_array($depot->id, old('depot_ids', $user->depots->pluck('id')->toArray())) ? 'checked' : '' }}>
                                        <span class="ml-2 text-sm">{{ $depot->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('depot_ids')<span class="text-red-600 text-xs">{{ $message }}</span>@enderror
                        </div>

                        {{-- Default Depot --}}
                        <div class="mb-4">
                            <label for="depot_id" class="block text-sm font-medium text-gray-700 mb-1">Default Depot</label>
                            <select name="depot_id" id="depot_id" 
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">No Default Depot</option>
                                @foreach($depots as $depot)
                                    <option value="{{ $depot->id }}" 
                                            {{ $depot->id == old('depot_id', $user->depot_id) ? 'selected' : '' }}>
                                        {{ $depot->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Primary depot shown on dashboards</p>
                            @error('depot_id')<span class="text-red-600 text-xs">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    {{-- Customer Access --}}
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">🏭 Customer Access</h3>
                        
                        {{-- Primary Customer (for customer role) --}}
                        <div class="mb-4">
                            <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">
                                Primary Customer <span class="text-xs text-gray-500">(customer role)</span>
                            </label>
                            <select name="customer_id" id="customer_id" 
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <option value="">No Primary Customer</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" 
                                            {{ $customer->id == old('customer_id', $user->customer_id) ? 'selected' : '' }}>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Multiple Customers --}}
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Multiple Customers <span class="text-xs text-gray-500">(warehouse roles)</span>
                            </label>
                            <div class="max-h-32 overflow-y-auto border rounded-md p-3">
                                @foreach($customers->chunk(ceil($customers->count()/2)) as $customerChunk)
                                    <div class="space-y-1">
                                        @foreach($customerChunk as $customer)
                                            <label class="flex items-center text-xs">
                                                <input type="checkbox" name="customer_ids[]" value="{{ $customer->id }}"
                                                       class="border-gray-300 rounded"
                                                       {{ in_array($customer->id, old('customer_ids', $user->customers->pluck('id')->toArray())) ? 'checked' : '' }}>
                                                <span class="ml-2">{{ $customer->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Leave empty to access ALL customers</p>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Function Summary --}}
                <div>
                    <div class="sticky top-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">📊 Function Summary</h3>
                        
                        {{-- Current Functions Display --}}
                        <div id="function-summary" class="space-y-2">
                            @php
                                $userFunctions = $user->getFunctionKeys();
                                $totalFunctions = count(\App\Models\UserFunction::getAllFunctionKeys());
                            @endphp
                            
                            @if($user->hasRole('admin'))
                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                    <div class="text-sm font-medium text-blue-900">Admin Access</div>
                                    <div class="text-xs text-blue-600">All {{ $totalFunctions }} functions enabled</div>
                                </div>
                            @else
                                <div class="bg-gray-50 border border-gray-200 rounded-lg p-3">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ count($userFunctions) }} of {{ $totalFunctions }} functions assigned
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                        <div class="bg-blue-600 h-2 rounded-full" 
                                             style="width: {{ $totalFunctions > 0 ? (count($userFunctions) / $totalFunctions * 100) : 0 }}%"></div>
                                    </div>
                                </div>
                                
                                @if(count($userFunctions) > 0)
                                    <div class="max-h-40 overflow-y-auto text-xs space-y-1">
                                        @php
                                            $allFunctions = \App\Models\UserFunction::getAllFunctions();
                                            $categorizedFunctions = [];
                                            
                                            foreach($userFunctions as $functionKey) {
                                                foreach($allFunctions as $category => $functions) {
                                                    if(array_key_exists($functionKey, $functions)) {
                                                        $categorizedFunctions[$category][] = $functions[$functionKey];
                                                        break;
                                                    }
                                                }
                                            }
                                        @endphp
                                        
                                        @foreach($categorizedFunctions as $category => $functions)
                                            <div class="mb-2">
                                                <div class="font-medium text-gray-700">{{ $category }}</div>
                                                @foreach($functions as $function)
                                                    <div class="text-gray-600 ml-2">• {{ $function }}</div>
                                                @endforeach
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Function Assignment Section --}}
            <div id="function-assignment" class="mt-10 pt-8 border-t border-gray-200" style="display: none;">
                <div class="mb-6">
                    <h2 class="text-xl font-semibold text-gray-900 mb-2">🔧 Individual Function Assignment</h2>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start">
                            <div class="text-blue-600 mr-2">💡</div>
                            <div>
                                <p class="text-sm font-medium text-blue-900">Recommendation: Use Custom Roles Instead</p>
                                <p class="text-sm text-blue-700 mt-1">
                                    Individual functions are only used when <strong>no custom roles</strong> are assigned. 
                                    If you assign custom roles, these individual functions will be ignored.
                                </p>
                                <p class="text-xs text-blue-600 mt-2">
                                    <strong>💡 Tip:</strong> Use the "Sync from Custom Roles" button below to copy functions from your selected custom roles to individual functions (useful for customization or viewing permissions).
                                </p>
                            </div>
                        </div>
                    </div>
                    <p class="text-sm text-gray-600">
                        Select specific functions only if not using custom roles above. 
                        <span class="font-medium">Admin users automatically have access to all functions.</span>
                    </p>
                </div>

                @php
                    // Show all user functions (direct + from custom roles) for display
                    $directFunctions = $user->functions()->pluck('function_key')->toArray();
                    $allUserFunctions = $user->getFunctionKeys(); // Includes custom role functions
                    $userFunctions = old('function_keys', $directFunctions); // Form submission uses only direct functions
                    $displayFunctions = old('function_keys', $allUserFunctions); // Display shows all functions
                    $allFunctions = \App\Models\UserFunction::getAllFunctions();
                @endphp

                {{-- Function Categories --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($allFunctions as $category => $functions)
                        <div class="bg-gray-50 rounded-lg border border-gray-200">
                            {{-- Category Header --}}
                            <div class="px-4 py-3 bg-gray-100 border-b border-gray-200 rounded-t-lg">
                                <label class="flex items-center cursor-pointer">
                                    <input type="checkbox" 
                                           class="category-checkbox border-gray-300 rounded"
                                           data-category="{{ Str::slug($category) }}"
                                           onchange="toggleCategory('{{ Str::slug($category) }}')">
                                    <span class="ml-2 font-medium text-gray-900 text-sm">{{ $category }}</span>
                                    <span class="ml-auto text-xs text-gray-500">
                                        {{ count($functions) }} functions
                                    </span>
                                </label>
                            </div>

                            {{-- Category Functions --}}
                            <div class="p-4 space-y-2 max-h-80 overflow-y-auto">
                                @foreach($functions as $key => $label)
                                    <label class="flex items-start cursor-pointer hover:bg-white rounded p-1 -m-1">
                                        <input type="checkbox" 
                                               name="function_keys[]" 
                                               value="{{ $key }}"
                                               class="function-checkbox border-gray-300 rounded mt-0.5 flex-shrink-0"
                                               data-category="{{ Str::slug($category) }}"
                                               {{ in_array($key, $displayFunctions) ? 'checked' : '' }}
                                               onchange="updateCategoryCheckbox('{{ Str::slug($category) }}')">
                                        <div class="ml-2 flex-1">
                                            <span class="text-sm text-gray-900">{{ $label }}</span>
                                            <div class="text-xs text-gray-500 font-mono">{{ $key }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                @error('function_keys')<span class="text-red-600 text-sm mt-2">{{ $message }}</span>@enderror
            </div>

            {{-- Submit Actions --}}
            <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('app.users.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    ← Cancel
                </a>
                
                <div class="flex space-x-3">
                    <button type="button" onclick="syncFromCustomRoles()" 
                            class="px-4 py-2 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200">
                        📥 Sync from Custom Roles
                    </button>
                    <button type="button" onclick="selectAllFunctions()" 
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                        Select All Functions
                    </button>
                    <button type="button" onclick="clearAllFunctions()" 
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                        Clear All Functions
                    </button>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        💾 Save User Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
.category-checkbox:checked {
    background-color: #3B82F6;
    border-color: #3B82F6;
}

.function-checkbox:checked {
    background-color: #10B981;
    border-color: #10B981;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    toggleFunctionSections();
    updateAllCategoryCheckboxes();
});

function toggleFunctionSections() {
    const functionSection = document.getElementById('function-assignment');
    const warehouseRoles = ['warehouse', 'depot-admin', 'site-admin'];
    const roleCheckboxes = document.querySelectorAll('input[name="role_ids[]"]');
    
    let hasWarehouseRole = false;
    let isAdmin = false;
    
    roleCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
            const roleName = checkbox.parentElement.textContent.trim().toLowerCase();
            if (warehouseRoles.some(role => roleName.includes(role))) {
                hasWarehouseRole = true;
            }
            if (roleName.includes('admin') && !roleName.includes('depot') && !roleName.includes('site')) {
                isAdmin = true;
            }
        }
    });
    
    // Show function section for warehouse roles (but not pure admin)
    functionSection.style.display = (hasWarehouseRole && !isAdmin) ? 'block' : 'none';
}

function toggleCategory(categorySlug) {
    const categoryCheckbox = document.querySelector(`input[data-category="${categorySlug}"].category-checkbox`);
    const functionCheckboxes = document.querySelectorAll(`input[data-category="${categorySlug}"].function-checkbox`);
    
    const isChecked = categoryCheckbox.checked;
    functionCheckboxes.forEach(checkbox => {
        checkbox.checked = isChecked;
    });
}

function updateCategoryCheckbox(categorySlug) {
    const categoryCheckbox = document.querySelector(`input[data-category="${categorySlug}"].category-checkbox`);
    const functionCheckboxes = document.querySelectorAll(`input[data-category="${categorySlug}"].function-checkbox`);
    
    const checkedCount = Array.from(functionCheckboxes).filter(cb => cb.checked).length;
    const totalCount = functionCheckboxes.length;
    
    if (checkedCount === 0) {
        categoryCheckbox.checked = false;
        categoryCheckbox.indeterminate = false;
    } else if (checkedCount === totalCount) {
        categoryCheckbox.checked = true;
        categoryCheckbox.indeterminate = false;
    } else {
        categoryCheckbox.checked = false;
        categoryCheckbox.indeterminate = true;
    }
}

function updateAllCategoryCheckboxes() {
    const categories = new Set();
    document.querySelectorAll('.function-checkbox').forEach(checkbox => {
        categories.add(checkbox.dataset.category);
    });
    
    categories.forEach(category => {
        updateCategoryCheckbox(category);
    });
}

function selectAllFunctions() {
    document.querySelectorAll('.function-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
    document.querySelectorAll('.category-checkbox').forEach(checkbox => {
        checkbox.checked = true;
        checkbox.indeterminate = false;
    });
}

function clearAllFunctions() {
    document.querySelectorAll('.function-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.querySelectorAll('.category-checkbox').forEach(checkbox => {
        checkbox.checked = false;
        checkbox.indeterminate = false;
    });
}

function syncFromCustomRoles() {
    // Get all selected custom roles
    const selectedCustomRoles = [];
    document.querySelectorAll('input[name="custom_role_ids[]"]:checked').forEach(checkbox => {
        selectedCustomRoles.push(checkbox.value);
    });

    if (selectedCustomRoles.length === 0) {
        alert('Please select at least one custom role first.');
        return;
    }

    // Clear all current function selections
    clearAllFunctions();

    // Get functions from selected custom roles
    const customRoleFunctions = {
        @foreach($customRoles as $role)
        "{{ $role->id }}": {!! json_encode($role->getFunctionKeys()) !!},
        @endforeach
    };

    // Collect all functions from selected roles
    const functionsToSelect = new Set();
    selectedCustomRoles.forEach(roleId => {
        if (customRoleFunctions[roleId]) {
            customRoleFunctions[roleId].forEach(functionKey => {
                functionsToSelect.add(functionKey);
            });
        }
    });

    // Select the functions
    functionsToSelect.forEach(functionKey => {
        const checkbox = document.querySelector(`input[name="function_keys[]"][value="${functionKey}"]`);
        if (checkbox) {
            checkbox.checked = true;
        }
    });

    // Update category checkboxes
    updateAllCategoryCheckboxes();

    // Show confirmation message
    const count = functionsToSelect.size;
    const roleNames = selectedCustomRoles.map(roleId => {
        const roleCheckbox = document.querySelector(`input[name="custom_role_ids[]"][value="${roleId}"]`);
        const label = roleCheckbox ? roleCheckbox.closest('label').querySelector('span').textContent : roleId;
        return label;
    });

    alert(`Synced ${count} functions from custom role(s): ${roleNames.join(', ')}`);
}
</script>
@endsection