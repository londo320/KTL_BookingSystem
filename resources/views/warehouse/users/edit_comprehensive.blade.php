
<x-warehouse-layout>
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

    @if($user->isProtectedSystemOwner())
        <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <div class="text-blue-600 text-2xl mr-3">🔒</div>
                <div>
                    <h3 class="text-blue-900 font-semibold">Protected System Owner Account</h3>
                    <p class="text-blue-800 mt-1">
                        This is a protected system account. You have full access to manage your own permissions, roles, and access levels. 
                        No other users can edit this account, even admins.
                    </p>
                    @if(auth()->user()->id === $user->id)
                        <p class="text-blue-700 text-sm mt-2">
                            <strong>Note:</strong> You can assign/remove any roles and permissions, including admin access. 
                            The system will always ensure you maintain access to user management functions.
                        </p>
                    @endif
                </div>
            </div>
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

                        {{-- User Status --}}
                        <div class="mb-4">
                            <label for="is_active" class="block text-sm font-medium text-gray-700 mb-2">User Status</label>
                            <div class="space-y-2">
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50">
                                    <input type="radio" name="is_active" value="1" class="border-gray-300" 
                                           {{ old('is_active', $user->is_active ?? 1) == 1 ? 'checked' : '' }}>
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-green-700">✅ Active</span>
                                        <div class="text-xs text-gray-500">User can log in and access assigned functions</div>
                                    </div>
                                </label>
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 {{ $user->isProtectedSystemOwner() ? 'opacity-50' : '' }}">
                                    <input type="radio" name="is_active" value="0" class="border-gray-300"
                                           {{ old('is_active', $user->is_active ?? 1) == 0 ? 'checked' : '' }}
                                           {{ $user->isProtectedSystemOwner() ? 'disabled' : '' }}>
                                    <div class="ml-3">
                                        <span class="text-sm font-medium text-red-700">❌ Disabled</span>
                                        <div class="text-xs text-gray-500">
                                            @if($user->isProtectedSystemOwner())
                                                🔒 Protected system owner cannot be disabled
                                            @else
                                                User cannot log in or access any functions
                                            @endif
                                        </div>
                                    </div>
                                </label>
                            </div>
                            @error('is_active')<span class="text-red-600 text-xs">{{ $message }}</span>@enderror
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
                            
                            @if(!($user->is_active ?? true))
                                <div class="bg-red-50 border border-red-200 rounded-lg p-3 mb-3">
                                    <div class="text-sm font-medium text-red-900">❌ User Disabled</div>
                                    <div class="text-xs text-red-600">Cannot access any functions while disabled</div>
                                </div>
                            @endif
                            
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

                {{-- Super Simple Permission Level System --}}
                <div class="space-y-6">
                    
                    {{-- Copy From User (kept because you like it) --}}
                    <div class="bg-blue-50 rounded-lg border border-blue-200 p-4">
                        <h4 class="font-medium text-blue-900 mb-3">👥 Copy from existing user</h4>
                        <div class="flex gap-3">
                            <select id="clone-user-select" class="flex-1 border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <option value="">Choose someone similar...</option>
                                @php
                                    $similarUsers = \App\Models\User::with(['roles', 'functions'])
                                        ->whereHas('roles', function($query) {
                                            $query->whereIn('name', ['warehouse', 'depot-admin', 'site-admin']);
                                        })
                                        ->where('id', '!=', $user->id)
                                        ->orderBy('name')
                                        ->get();
                                @endphp
                                @foreach($similarUsers as $similarUser)
                                    <option value="{{ $similarUser->id }}" data-functions="{{ json_encode($similarUser->getFunctionKeys()) }}">
                                        {{ $similarUser->name }} ({{ $similarUser->roles->first()?->name ?? 'user' }})
                                    </option>
                                @endforeach
                            </select>
                            <button type="button" onclick="cloneUser()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Copy
                            </button>
                        </div>
                    </div>

                    {{-- OR Permission Level Presets --}}
                    <div class="text-center text-gray-500 text-sm font-medium">or choose a preset level</div>

                    <div class="bg-white rounded-lg border border-gray-200 p-6">
                        <h4 class="font-medium text-gray-900 mb-4 text-center">📋 Quick Permission Presets</h4>
                        
                        <div class="space-y-3">
                            <label class="flex items-start p-4 border-2 border-gray-200 rounded-lg hover:border-blue-300 cursor-pointer">
                                <input type="radio" name="permission_level" value="view" class="mt-1 mr-3" onchange="applyPermissionLevel('view')">
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">👀 View Only</div>
                                    <div class="text-sm text-gray-600 mt-1">Can see dashboards, bookings, and reports but can't make changes</div>
                                    <div class="text-xs text-gray-500 mt-1">Perfect for: Viewers, Read-only staff, Auditors</div>
                                </div>
                            </label>

                            <label class="flex items-start p-4 border-2 border-gray-200 rounded-lg hover:border-blue-300 cursor-pointer">
                                <input type="radio" name="permission_level" value="standard" class="mt-1 mr-3" onchange="applyPermissionLevel('standard')">
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">✏️ Standard Access</div>
                                    <div class="text-sm text-gray-600 mt-1">Can view + process arrivals, manage bookings, control tipping operations</div>
                                    <div class="text-xs text-gray-500 mt-1">Perfect for: Warehouse operators, Shift workers, Most staff</div>
                                </div>
                            </label>

                            <label class="flex items-start p-4 border-2 border-gray-200 rounded-lg hover:border-orange-300 cursor-pointer">
                                <input type="radio" name="permission_level" value="shunter" class="mt-1 mr-3" onchange="applyPermissionLevel('shunter')">
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">🚛 Shunter Operations</div>
                                    <div class="text-sm text-gray-600 mt-1">Specialized for trailer operations - move between bays, tipping, parking areas</div>
                                    <div class="text-xs text-gray-500 mt-1">Perfect for: Shunt drivers, Yard operators, Trailer handlers</div>
                                </div>
                            </label>

                            <label class="flex items-start p-4 border-2 border-gray-200 rounded-lg hover:border-blue-300 cursor-pointer">
                                <input type="radio" name="permission_level" value="full" class="mt-1 mr-3" onchange="applyPermissionLevel('full')">
                                <div class="flex-1">
                                    <div class="font-medium text-gray-900">⚙️ Full Control</div>
                                    <div class="text-sm text-gray-600 mt-1">Can view + edit + create/delete bookings, manage customers, configure settings</div>
                                    <div class="text-xs text-gray-500 mt-1">Perfect for: Supervisors, Managers, Admins</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Simple Preview --}}
                    <div class="bg-gray-50 rounded-lg border border-gray-200 p-4">
                        <div class="flex items-center justify-between mb-2">
                            <h5 class="font-medium text-gray-900">Current Access Level</h5>
                            <div id="access-level-badge" class="text-sm bg-gray-200 text-gray-700 px-3 py-1 rounded-full">
                                Not set
                            </div>
                        </div>
                        <div id="access-preview" class="text-sm text-gray-600">
                            Select an access level above to see what they'll be able to do.
                        </div>
                        
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="text-center">
                                <p class="text-sm text-gray-600 mb-3">Not quite right? Need precise control?</p>
                                <button type="button" onclick="showCustomOptions()" 
                                        class="px-4 py-2 bg-purple-100 text-purple-700 rounded-md hover:bg-purple-200 font-medium">
                                    🔧 Choose Individual Functions
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- OR Individual Function Selection --}}
                    <div class="text-center text-gray-500 text-sm font-medium">or select individual functions for precise control</div>

                    {{-- Individual Functions Selection --}}
                    <div id="custom-functions" class="hidden bg-purple-50 rounded-lg border border-purple-200 p-6">
                        <div class="mb-4">
                            <h5 class="font-medium text-purple-900 flex items-center">
                                🔧 Individual Function Selection
                                <button type="button" onclick="showCustomOptions()" class="ml-2 text-xs text-purple-600 hover:text-purple-800">
                                    × Close
                                </button>
                            </h5>
                            <p class="text-sm text-purple-700 mt-1">Select specific functions for precise permission control. These groups should make it easier to find what you need.</p>
                        </div>
                        
                        <div class="mb-3">
                            <input type="text" id="function-search" placeholder="Search functions..." 
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
                                   oninput="searchFunctions(this.value)">
                        </div>
                        
                        <div class="max-h-96 overflow-y-auto">
                            @foreach($allFunctions as $category => $functions)
                                <div class="mb-4">
                                    <div class="sticky top-0 bg-purple-100 px-3 py-2 rounded font-medium text-purple-900 text-sm mb-2">
                                        {{ $category }} ({{ count($functions) }} functions)
                                        <button type="button" onclick="toggleCategory('{{ Str::slug($category) }}')" 
                                                class="float-right text-purple-600 hover:text-purple-800">
                                            Toggle All
                                        </button>
                                    </div>
                                    <div class="space-y-1 pl-2" id="category-{{ Str::slug($category) }}">
                                        @foreach($functions as $key => $label)
                                            <label class="function-item flex items-center p-2 hover:bg-white rounded cursor-pointer text-sm"
                                                   data-search="{{ strtolower($label) }}" data-category="{{ Str::slug($category) }}">
                                                <input type="checkbox" 
                                                       name="function_keys[]" 
                                                       value="{{ $key }}"
                                                       class="border-gray-300 rounded mr-2 category-{{ Str::slug($category) }}"
                                                       {{ in_array($key, $displayFunctions) ? 'checked' : '' }}
                                                       onchange="updateCustomPreview()">
                                                <span class="text-gray-700">{{ $label }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
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
    updatePermissionsPreview();
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

// Job role templates (simplified presets)
const jobRoleTemplates = {
    'operator': [
        'dashboard.view', 'dashboard.warehouse', 'warehouse.dashboard',
        'bookings.view', 'bookings.show', 'bookings.arrival', 'bookings.departure',
        'bookings.arrival.form', 'bookings.assign-bay', 'bookings.clear-bay',
        'factory-bookings.view', 'factory-bookings.show', 'factory-bookings.mark-departed',
        'warehouse.bookings', 'warehouse.factory-bookings'
    ],
    'supervisor': [
        'dashboard.view', 'dashboard.warehouse', 'warehouse.dashboard',
        'bookings.view', 'bookings.show', 'bookings.arrival', 'bookings.departure',
        'bookings.arrival.form', 'bookings.assign-bay', 'bookings.clear-bay', 'bookings.transfer-bay',
        'bookings.move-to-waiting', 'bookings.start-tipping', 'bookings.complete-tipping',
        'factory-bookings.view', 'factory-bookings.show', 'factory-bookings.mark-departed',
        'factory-bookings.start-processing', 'factory-bookings.complete',
        'tipping-workflow.show', 'tipping-workflow.dashboard', 'tipping-workflow.drop-trailer',
        'tipping-workflow.move-to-location', 'tipping-workflow.move-to-bay',
        'operations.assign-drop-zone', 'operations.unit-depart', 'operations.shunt-to-bay',
        'operations-control.view', 'queue-management.view',
        'tipping-bays.view', 'tipping-bays.mark-available',
        'depot-map.view', 'depot-map.bay-status', 'depot-map.location-status',
        'settings.manage', 'warehouse.bookings', 'warehouse.factory-bookings'
    ],
    'manager': [
        'dashboard.view', 'dashboard.warehouse', 'warehouse.dashboard',
        'bookings.view', 'bookings.create', 'bookings.store', 'bookings.show', 'bookings.edit', 'bookings.update',
        'bookings.arrival', 'bookings.departure', 'bookings.arrival.form', 'bookings.assign-bay', 'bookings.clear-bay',
        'bookings.transfer-bay', 'bookings.move-to-waiting', 'bookings.start-tipping', 'bookings.complete-tipping',
        'bookings.rebook', 'bookings.cancel', 'bookings.history', 'bookings.export.pdf', 'bookings.export.csv',
        'factory-bookings.view', 'factory-bookings.create', 'factory-bookings.show', 'factory-bookings.edit',
        'factory-bookings.start-processing', 'factory-bookings.complete', 'factory-bookings.mark-departed',
        'tipping-workflow.show', 'tipping-workflow.dashboard', 'operations-control.view', 'queue-management.view',
        'tipping-locations.view', 'tipping-locations.create', 'tipping-bays.view', 'tipping-bays.create',
        'depot-map.view', 'customers.view', 'customers.show', 'customer-behavior.view',
        'carriers.view', 'trailer-types.view', 'priority-settings.view',
        'settings.manage', 'settings.manage.global', 'warehouse.bookings', 'warehouse.factory-bookings',
        'warehouse.trailer-report', 'trailer-location-report.view'
    ]
};

// Permission level templates for the new simplified system
const permissionLevels = {
    'view': [
        // Dashboard & Navigation
        'dashboard.view', 'dashboard.warehouse', 'warehouse.dashboard',
        
        // Bookings - View Only
        'bookings.view', 'bookings.view-streamlined', 'bookings.show', 'bookings.search', 'bookings.history',
        'factory-bookings.view', 'factory-bookings.show',
        
        // Operations - View Only
        'tipping-workflow.dashboard', 'tipping-workflow.show', 'operations-control.view', 'queue-management.view',
        'tipping-locations.view', 'tipping-bays.view', 'depot-map.view', 'depot-map.bay-status', 'depot-map.location-status',
        
        // Management - View Only
        'customers.view', 'customers.show', 'carriers.view', 'carriers.show', 'depots.view', 'depots.show',
        'products.view', 'products.show', 'users.view', 'slots.view', 'slot-templates.view',
        
        // Reports & Analytics
        'warehouse.bookings', 'warehouse.factory-bookings', 'warehouse.trailer-report', 'customer-behavior.view',
        'slot-usage.view', 'trailer-location-report.view'
    ],
    
    'standard': [
        // All view permissions
        'dashboard.view', 'dashboard.warehouse', 'warehouse.dashboard',
        'bookings.view', 'bookings.view-streamlined', 'bookings.show', 'bookings.search', 'bookings.history',
        'factory-bookings.view', 'factory-bookings.show',
        'tipping-workflow.dashboard', 'tipping-workflow.show', 'operations-control.view', 'queue-management.view',
        'tipping-locations.view', 'tipping-bays.view', 'depot-map.view', 'depot-map.bay-status', 'depot-map.location-status',
        'customers.view', 'customers.show', 'carriers.view', 'carriers.show', 'depots.view', 'depots.show',
        'products.view', 'products.show', 'users.view', 'slots.view', 'slot-templates.view',
        'warehouse.bookings', 'warehouse.factory-bookings', 'warehouse.trailer-report', 'customer-behavior.view',
        'slot-usage.view', 'trailer-location-report.view',
        
        // Standard operations (create/edit but not delete)
        'bookings.create', 'bookings.store', 'bookings.edit', 'bookings.update', 'bookings.arrival', 'bookings.departure',
        'bookings.assign-bay', 'bookings.transfer-bay', 'bookings.move-to-waiting', 'bookings.clear-bay',
        'bookings.start-tipping', 'bookings.complete-tipping', 'bookings.rebook',
        'factory-bookings.create', 'factory-bookings.edit', 'factory-bookings.start-processing', 'factory-bookings.complete',
        'tipping-workflow.drop-trailer', 'tipping-workflow.move-to-location', 'tipping-workflow.move-to-bay',
        'tipping-workflow.start-tipping', 'tipping-workflow.complete-tipping',
        'customers.create', 'customers.edit', 'customers.update', 'carriers.create', 'carriers.edit', 'carriers.update'
    ],

    'shunter': [
        // Essential viewing for context
        'dashboard.view', 'dashboard.warehouse', 'warehouse.dashboard',
        'bookings.view', 'bookings.view-streamlined', 'bookings.show',
        'factory-bookings.view', 'factory-bookings.show',
        
        // Core shunter operations - trailer management and movement
        'tipping-workflow.dashboard', 'tipping-workflow.show', 'tipping-workflow.drop-trailer', 
        'tipping-workflow.move-to-location', 'tipping-workflow.drop-trailer-detached', 'tipping-workflow.move-to-bay',
        'tipping-workflow.start-tipping', 'tipping-workflow.complete-tipping', 'tipping-workflow.unit-depart',
        'tipping-workflow.collection-arrival', 'tipping-workflow.collection-depart', 'tipping-workflow.trailer-depart',
        
        // Factory workflow operations for trailer handling
        'factory-booking-workflow.drop-trailer', 'factory-booking-workflow.move-to-location',
        'factory-booking-workflow.drop-trailer-detached', 'factory-booking-workflow.move-to-bay',
        'factory-booking-workflow.start-tipping', 'factory-booking-workflow.complete-tipping',
        'factory-booking-workflow.trailer-depart',
        
        // Operations control for bay and zone management
        'operations.assign-drop-zone', 'operations.unit-depart', 'operations.shunt-to-bay',
        'operations.start-tipping', 'operations.complete-tipping', 'operations.move-to-collection-zone',
        'operations.record-collection', 'operations.available-locations', 'operations.available-bays',
        'operations-control.view', 'queue-management.view',
        
        // Bay and location management
        'tipping-locations.view', 'tipping-bays.view', 'tipping-bays.mark-available',
        'depot-map.view', 'depot-map.bay-status', 'depot-map.change-bay', 'depot-map.location-status',
        'depot-map.refresh',
        
        // Trailer specific operations
        'trailer-operations-dashboard.view', 'dropped-trailers.view', 'dropped-trailers.reconnect.form',
        'dropped-trailers.reconnect', 'trailer-collection.view', 'trailer-location-report.view',
        
        // Basic booking operations related to trailer movement
        'bookings.assign-bay', 'bookings.transfer-bay', 'bookings.move-to-waiting', 'bookings.clear-bay',
        'bookings.start-tipping', 'bookings.complete-tipping',
        
        // Warehouse reporting for trailer status
        'warehouse.bookings', 'warehouse.factory-bookings', 'warehouse.trailer-report'
    ],
    
    'full': [
        // All standard permissions plus admin functions
        'dashboard.view', 'dashboard.warehouse', 'warehouse.dashboard',
        'bookings.view', 'bookings.view-streamlined', 'bookings.show', 'bookings.search', 'bookings.history',
        'bookings.create', 'bookings.store', 'bookings.edit', 'bookings.update', 'bookings.delete', 'bookings.cancel',
        'bookings.arrival', 'bookings.departure', 'bookings.assign-bay', 'bookings.transfer-bay', 'bookings.move-to-waiting',
        'bookings.clear-bay', 'bookings.start-tipping', 'bookings.complete-tipping', 'bookings.rebook', 'bookings.unbook',
        'factory-bookings.view', 'factory-bookings.create', 'factory-bookings.show', 'factory-bookings.edit',
        'factory-bookings.start-processing', 'factory-bookings.complete', 'factory-bookings.mark-departed',
        'tipping-workflow.dashboard', 'tipping-workflow.show', 'tipping-workflow.drop-trailer', 'tipping-workflow.move-to-location',
        'tipping-workflow.move-to-bay', 'tipping-workflow.start-tipping', 'tipping-workflow.complete-tipping',
        'operations-control.view', 'queue-management.view', 'tipping-locations.view', 'tipping-locations.create',
        'tipping-locations.edit', 'tipping-locations.update', 'tipping-locations.delete', 'tipping-bays.view',
        'tipping-bays.create', 'tipping-bays.edit', 'tipping-bays.update', 'tipping-bays.delete',
        'depot-map.view', 'depot-map.manage-positions', 'depot-map.update-position', 'depot-map.bay-status',
        'customers.view', 'customers.create', 'customers.show', 'customers.edit', 'customers.update', 'customers.delete',
        'carriers.view', 'carriers.create', 'carriers.show', 'carriers.edit', 'carriers.update', 'carriers.delete',
        'depots.view', 'depots.create', 'depots.show', 'depots.edit', 'depots.update', 'depots.delete',
        'products.view', 'products.create', 'products.show', 'products.edit', 'products.update', 'products.delete',
        'users.view', 'users.create', 'users.edit', 'users.update', 'users.assign-role',
        'slots.view', 'slots.create', 'slots.edit', 'slots.update', 'slots.delete', 'slots.generate',
        'slot-templates.view', 'slot-templates.create', 'slot-templates.edit', 'slot-templates.update', 'slot-templates.delete',
        'settings.manage', 'settings.view', 'settings.store', 'settings.dashboard',
        'warehouse.bookings', 'warehouse.factory-bookings', 'warehouse.trailer-report', 'customer-behavior.view',
        'slot-usage.view', 'trailer-location-report.view', 'bookings.export.pdf', 'bookings.export.csv'
    ]
};

// Apply permission level (new simplified system)
function applyPermissionLevel(level) {
    const functions = permissionLevels[level] || [];
    
    // Clear all checkboxes first
    document.querySelectorAll('input[name="function_keys[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    
    // Check the functions for this level
    functions.forEach(functionKey => {
        const checkbox = document.querySelector(`input[name="function_keys[]"][value="${functionKey}"]`);
        if (checkbox) {
            checkbox.checked = true;
        }
    });

    // Update the access level display
    const badge = document.getElementById('access-level-badge');
    const preview = document.getElementById('access-preview');
    
    const levelInfo = {
        'view': { name: 'View Only', color: 'bg-gray-100 text-gray-700', description: 'Can see dashboards, bookings, and reports but cannot make changes.' },
        'standard': { name: 'Standard Access', color: 'bg-green-100 text-green-700', description: 'Can view and process arrivals, manage bookings, control basic tipping operations.' },
        'shunter': { name: 'Shunter Operations', color: 'bg-orange-100 text-orange-700', description: 'Specialized trailer operations - move trailers between bays, manage tipping, control parking areas.' },
        'full': { name: 'Full Control', color: 'bg-blue-100 text-blue-700', description: 'Complete access - can view, edit, create/delete bookings, manage customers, configure settings.' }
    };
    
    const info = levelInfo[level];
    if (info) {
        badge.textContent = info.name;
        badge.className = `text-sm px-3 py-1 rounded-full ${info.color}`;
        preview.textContent = info.description;
    }
    
    updatePermissionsPreview();
    alert(`Applied ${functions.length} functions for ${info.name} level`);
}

// Show custom function selector
function showCustomOptions() {
    const customSection = document.getElementById('custom-functions');
    customSection.classList.toggle('hidden');
    
    if (!customSection.classList.contains('hidden')) {
        // Scroll to the custom section
        customSection.scrollIntoView({ behavior: 'smooth' });
    }
}

// Search through functions
function searchFunctions(searchTerm) {
    const term = searchTerm.toLowerCase();
    const items = document.querySelectorAll('.function-item');
    
    items.forEach(item => {
        const searchText = item.dataset.search || '';
        if (searchText.includes(term)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
}

// Update custom preview
function updateCustomPreview() {
    const checkedBoxes = document.querySelectorAll('input[name="function_keys[]"]:checked');
    const count = checkedBoxes.length;
    
    // Update access level badge to show custom
    const badge = document.getElementById('access-level-badge');
    const preview = document.getElementById('access-preview');
    
    badge.textContent = `Custom (${count} functions)`;
    badge.className = 'text-sm px-3 py-1 rounded-full bg-purple-100 text-purple-700';
    preview.textContent = `Custom selection with ${count} individual functions configured.`;
    
    // Clear permission level radio buttons
    document.querySelectorAll('input[name="permission_level"]').forEach(radio => {
        radio.checked = false;
    });
}

// Toggle entire category
function toggleCategory(categorySlug) {
    const checkboxes = document.querySelectorAll(`.category-${categorySlug}`);
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = !allChecked;
    });
    
    updateCustomPreview();
}

// Clone user functionality
function cloneUser() {
    const select = document.getElementById('clone-user-select');
    const selectedOption = select.options[select.selectedIndex];
    
    if (!selectedOption.value) {
        alert('Please select a user to copy from.');
        return;
    }
    
    const functionsData = selectedOption.getAttribute('data-functions');
    const userName = selectedOption.textContent.split(' (')[0];
    
    try {
        const functionsToSelect = JSON.parse(functionsData || '[]');
        
        // Clear current selection
        clearAllFunctions();
        
        // Apply functions
        functionsToSelect.forEach(functionKey => {
            const checkbox = document.querySelector(`input[name="function_keys[]"][value="${functionKey}"]`);
            if (checkbox) {
                checkbox.checked = true;
            }
        });
        
        updatePermissionsPreview();
        
        // Success message
        const successMsg = document.createElement('div');
        successMsg.className = 'mt-2 p-2 bg-green-100 text-green-800 rounded text-sm';
        successMsg.textContent = `✅ Copied ${functionsToSelect.length} permissions from ${userName}`;
        select.parentNode.appendChild(successMsg);
        
        setTimeout(() => successMsg.remove(), 3000);
        
    } catch (e) {
        alert('Error copying functions. Please try again.');
    }
}

// Job role selector
function showJobRoleSelector() {
    const selector = document.getElementById('job-role-selector');
    selector.classList.toggle('hidden');
}

function applyJobRole(roleType) {
    const template = jobRoleTemplates[roleType];
    if (!template) return;
    
    // Clear current selection
    clearAllFunctions();
    
    // Apply job role functions
    template.forEach(functionKey => {
        const checkbox = document.querySelector(`input[name="function_keys[]"][value="${functionKey}"]`);
        if (checkbox) {
            checkbox.checked = true;
        }
    });
    
    updatePermissionsPreview();
    
    // Visual feedback
    document.querySelectorAll('.job-role-btn').forEach(btn => {
        btn.classList.remove('border-blue-500', 'bg-blue-50');
    });
    event.target.closest('.job-role-btn').classList.add('border-blue-500', 'bg-blue-50');
    
    const roleNames = {
        'operator': 'Warehouse Operator',
        'supervisor': 'Shift Supervisor', 
        'manager': 'Department Manager'
    };
    
    alert(`Applied ${roleNames[roleType]} permissions (${template.length} functions)`);
}

// Simple permissions preview
function updatePermissionsPreview() {
    const checkedBoxes = document.querySelectorAll('input[name="function_keys[]"]:checked');
    const count = checkedBoxes.length;
    
    // Update count
    const badge = document.getElementById('function-count-badge');
    badge.textContent = `${count} functions`;
    badge.className = count > 0 ? 'text-sm bg-blue-100 text-blue-800 px-3 py-1 rounded-full' : 'text-sm bg-gray-100 text-gray-600 px-3 py-1 rounded-full';
    
    // Show what they can do in plain English
    const preview = document.getElementById('permissions-preview');
    
    if (count === 0) {
        preview.innerHTML = '<div class="text-gray-500 italic">No permissions selected - user won\'t be able to access warehouse functions.</div>';
        return;
    }
    
    const selectedFunctions = Array.from(checkedBoxes).map(cb => cb.value);
    const capabilities = [];
    
    // Check for key capabilities
    if (selectedFunctions.some(f => f.includes('dashboard'))) {
        capabilities.push('📊 View dashboard');
    }
    if (selectedFunctions.some(f => f.includes('bookings.view'))) {
        capabilities.push('📋 View bookings');
    }
    if (selectedFunctions.some(f => f.includes('bookings.create'))) {
        capabilities.push('➕ Create bookings');
    }
    if (selectedFunctions.some(f => f.includes('bookings.arrival'))) {
        capabilities.push('🚛 Process arrivals');
    }
    if (selectedFunctions.some(f => f.includes('factory-bookings'))) {
        capabilities.push('🏭 Manage factory operations');
    }
    if (selectedFunctions.some(f => f.includes('tipping'))) {
        capabilities.push('🚛 Control tipping operations');
    }
    if (selectedFunctions.some(f => f.includes('operations'))) {
        capabilities.push('⚙️ Site operations control');
    }
    if (selectedFunctions.some(f => f.includes('customers'))) {
        capabilities.push('👥 Manage customers');
    }
    if (selectedFunctions.some(f => f.includes('reports') || f.includes('export'))) {
        capabilities.push('📊 Generate reports');
    }
    if (selectedFunctions.some(f => f.includes('settings'))) {
        capabilities.push('⚙️ Configure settings');
    }
    
    if (capabilities.length === 0) {
        preview.innerHTML = '<div class="text-gray-600">Custom function selection - see advanced options for details.</div>';
    } else {
        preview.innerHTML = `<div class="text-gray-700"><strong>They can:</strong><br>${capabilities.join(', ')}</div>`;
    }
}

// Advanced options
function showAdvanced() {
    const advanced = document.getElementById('advanced-options');
    advanced.classList.toggle('hidden');
}

// Simple search
function simpleSearch(searchTerm) {
    const term = searchTerm.toLowerCase();
    const items = document.querySelectorAll('.simple-function-item');
    
    items.forEach(item => {
        const searchText = item.dataset.search;
        if (searchText.includes(term)) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
}

function clearAllFunctions() {
    document.querySelectorAll('input[name="function_keys[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    updatePermissionsPreview();
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

    updatePermissionsPreview();

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
</x-warehouse-layout>