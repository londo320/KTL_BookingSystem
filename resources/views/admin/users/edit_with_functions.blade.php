@extends('layouts.admin')

@section('content')
<div class="py-6 max-w-7xl mx-auto">
    {{-- Success Message --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Display the new password if it's passed to the view --}}
    @if(session('new_password'))
        <div class="mb-4 p-3 bg-yellow-100 text-yellow-800 rounded">
            <strong>Your new password is:</strong> {{ session('new_password') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm border p-6">
        <h2 class="text-xl font-semibold mb-6">Edit User: {{ $user->name }}</h2>

        {{-- User Edit Form --}}
        <form method="POST" action="{{ route('app.users.update', $user->id) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Left Column: Basic Info --}}
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Basic Information</h3>
                    
                    {{-- Name Field --}}
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" id="name" name="name" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Email Field --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" id="email" name="email" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Role Selection --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Assign Roles</label>
                        <div class="space-y-2">
                            @foreach($roles as $role)
                                <label class="inline-flex items-center">
                                    <input
                                        type="checkbox"
                                        id="role_{{ $role->id }}"
                                        name="role_ids[]"
                                        value="{{ $role->id }}"
                                        class="border-gray-300 rounded"
                                        {{ in_array($role->id, old('role_ids', $user->roles->pluck('id')->toArray())) ? 'checked' : '' }}
                                        onchange="toggleFunctionSection()"
                                    >
                                    <span class="ml-2 text-sm capitalize">{{ $role->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('role_ids')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Reset Password Option --}}
                    <div>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="reset_password" id="reset_password" class="border-gray-300 rounded">
                            <span class="ml-2 text-sm">Reset to Default Password</span>
                        </label>
                    </div>
                </div>

                {{-- Right Column: Access & Functions --}}
                <div class="space-y-4">
                    <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Access & Permissions</h3>
                    
                    {{-- Depots Selection --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Depot Access</label>
                        <div class="space-y-2 max-h-32 overflow-y-auto border rounded p-2">
                            @foreach($depots as $depot)
                                <label class="inline-flex items-center w-full">
                                    <input type="checkbox" id="depot_{{ $depot->id }}" name="depot_ids[]" value="{{ $depot->id }}" class="border-gray-300 rounded"
                                    {{ in_array($depot->id, old('depot_ids', $user->depots->pluck('id')->toArray())) ? 'checked' : '' }}>
                                    <span class="ml-2 text-sm">{{ $depot->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('depot_ids')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Default Depot Selection --}}
                    <div>
                        <label for="depot_id" class="block text-sm font-medium text-gray-700">Default Depot</label>
                        <select name="depot_id" id="depot_id" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">— No Default Depot —</option>
                            @foreach($depots as $depot)
                                <option value="{{ $depot->id }}" 
                                    @selected($depot->id == old('depot_id', $user->depot_id))>
                                    {{ $depot->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-gray-500 mt-1">
                            Default depot shown on dashboards (must have access above)
                        </p>
                        @error('depot_id')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror
                    </div>

                    {{-- Customer Assignment --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Customer Access</label>
                        
                        {{-- Legacy customer_id for customer role --}}
                        <div class="mb-3">
                            <label class="block text-sm text-gray-600">Primary Customer (customer role)</label>
                            <select name="customer_id" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <option value="">— No Primary Customer —</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" 
                                        @selected($customer->id == old('customer_id', $user->customer_id))>
                                        {{ $customer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Multiple customers --}}
                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Multiple Customers (warehouse roles)</label>
                            <div class="max-h-32 overflow-y-auto border rounded p-2 space-y-1">
                                @foreach($customers as $customer)
                                    <label class="inline-flex items-center w-full">
                                        <input
                                            type="checkbox"
                                            name="customer_ids[]"
                                            value="{{ $customer->id }}"
                                            class="border-gray-300 rounded"
                                            {{ in_array($customer->id, old('customer_ids', $user->customers->pluck('id')->toArray())) ? 'checked' : '' }}
                                        >
                                        <span class="ml-2 text-xs">{{ $customer->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                Leave empty to see ALL customers
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Function Assignment Section --}}
            <div id="function-section" class="mt-8 border-t pt-6" style="display: none;">
                <h3 class="text-lg font-medium text-gray-900 mb-4">🔧 Warehouse Functions</h3>
                <p class="text-sm text-gray-600 mb-4">Select specific functions this user can access. Admin users have access to all functions automatically.</p>
                
                @php
                    $userFunctions = old('function_keys', $user->getFunctionKeys());
                    $allFunctions = \App\Models\UserFunction::getAllFunctions();
                @endphp

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($allFunctions as $category => $functions)
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-3">{{ $category }}</h4>
                            <div class="space-y-2">
                                @foreach($functions as $key => $label)
                                    <label class="inline-flex items-start">
                                        <input
                                            type="checkbox"
                                            name="function_keys[]"
                                            value="{{ $key }}"
                                            class="border-gray-300 rounded mt-0.5"
                                            {{ in_array($key, $userFunctions) ? 'checked' : '' }}
                                        >
                                        <div class="ml-2">
                                            <span class="text-sm font-medium text-gray-900">{{ $label }}</span>
                                            <div class="text-xs text-gray-500">{{ $key }}</div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                @error('function_keys')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            {{-- Submit Button --}}
            <div class="flex justify-end mt-8 pt-6 border-t">
                <a href="{{ route('app.users.index') }}" class="mr-4 px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    toggleFunctionSection();
});

function toggleFunctionSection() {
    const functionSection = document.getElementById('function-section');
    const warehouseRoles = ['warehouse', 'depot-admin', 'site-admin'];
    const roleCheckboxes = document.querySelectorAll('input[name="role_ids[]"]');
    
    let hasWarehouseRole = false;
    roleCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
            const roleName = checkbox.parentElement.textContent.trim().toLowerCase();
            if (warehouseRoles.includes(roleName)) {
                hasWarehouseRole = true;
            }
        }
    });
    
    // Show function section for warehouse roles (but not admin - admin gets all functions automatically)
    const adminCheckbox = document.querySelector('input[name="role_ids[]"][value]');
    let isAdmin = false;
    roleCheckboxes.forEach(checkbox => {
        if (checkbox.checked) {
            const roleName = checkbox.parentElement.textContent.trim().toLowerCase();
            if (roleName === 'admin') {
                isAdmin = true;
            }
        }
    });
    
    if (hasWarehouseRole && !isAdmin) {
        functionSection.style.display = 'block';
    } else {
        functionSection.style.display = 'none';
    }
}
</script>
@endsection