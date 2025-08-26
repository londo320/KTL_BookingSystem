
<x-warehouse-layout>
<div class="py-6 max-w-7xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border">
        <div class="px-6 py-4 border-b border-gray-200">
            <h1 class="text-2xl font-semibold text-gray-900">Create Custom Role</h1>
            <p class="text-sm text-gray-600 mt-1">Define a new role with specific function permissions</p>
        </div>

        <form method="POST" action="{{ route('app.custom-roles.store') }}" class="p-6">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Left Column: Basic Information --}}
                <div class="space-y-6">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900 mb-4 border-b pb-2">📝 Role Information</h2>
                        
                        {{-- Role Name --}}
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Role Name (Slug)</label>
                            <input type="text" id="name" name="name" required
                                   value="{{ old('name') }}"
                                   placeholder="e.g., warehouse_supervisor"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Used internally (lowercase, underscores only)</p>
                            @error('name')<span class="text-red-600 text-xs">{{ $message }}</span>@enderror
                        </div>

                        {{-- Display Name --}}
                        <div class="mb-4">
                            <label for="display_name" class="block text-sm font-medium text-gray-700 mb-1">Display Name</label>
                            <input type="text" id="display_name" name="display_name" required
                                   value="{{ old('display_name') }}"
                                   placeholder="e.g., Warehouse Supervisor"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            <p class="text-xs text-gray-500 mt-1">Shown to users in interfaces</p>
                            @error('display_name')<span class="text-red-600 text-xs">{{ $message }}</span>@enderror
                        </div>

                        {{-- Description --}}
                        <div class="mb-4">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea id="description" name="description" rows="3"
                                      placeholder="Describe what this role can do..."
                                      class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">{{ old('description') }}</textarea>
                            @error('description')<span class="text-red-600 text-xs">{{ $message }}</span>@enderror
                        </div>

                        {{-- Active Status --}}
                        <div class="mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" value="1" checked
                                       class="border-gray-300 rounded">
                                <span class="ml-2 text-sm">Role is active</span>
                            </label>
                            <p class="text-xs text-gray-500 mt-1">Inactive roles cannot be assigned to users</p>
                        </div>
                    </div>
                </div>

                {{-- Middle & Right Columns: Function Assignment --}}
                <div class="lg:col-span-2">
                    <div class="mb-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-2">🔧 Function Permissions</h2>
                        <p class="text-sm text-gray-600">
                            Select the functions this role should have access to. Users assigned to this role will inherit all selected permissions.
                        </p>
                    </div>

                    {{-- Function Categories --}}
                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
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
                                                   {{ in_array($key, old('function_keys', [])) ? 'checked' : '' }}
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
            </div>

            {{-- Submit Actions --}}
            <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200">
                <a href="{{ route('app.custom-roles.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    ← Cancel
                </a>
                
                <div class="flex space-x-3">
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
                        💾 Create Role
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
    updateAllCategoryCheckboxes();
});

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
</script>
</x-warehouse-layout>