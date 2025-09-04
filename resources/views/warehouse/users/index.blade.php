
<x-warehouse-layout>
<div class="py-6 max-w-7xl mx-auto">
    {{-- Success Message --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Create User Button & Filter --}}
    <div class="mb-4 flex items-center justify-between">
        <a href="{{ route('app.users.create') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Create New User
        </a>
        
        <div class="flex gap-2">
            <a href="{{ route('app.users.index') }}"
               class="px-3 py-2 {{ !$showDeleted ? 'bg-gray-800 text-white' : 'bg-gray-200 text-gray-700' }} rounded hover:bg-gray-700 hover:text-white">
                Active Users
            </a>
            <a href="{{ route('app.users.index', ['show_deleted' => 1]) }}"
               class="px-3 py-2 {{ $showDeleted ? 'bg-red-600 text-white' : 'bg-gray-200 text-gray-700' }} rounded hover:bg-red-700 hover:text-white">
                🗑️ Deleted Users
            </a>
        </div>
    </div>

    {{-- Search and Filters --}}
    <div class="mb-4 bg-white p-4 rounded-lg border">
        <form method="GET" class="flex flex-wrap gap-4 items-end">
            <input type="hidden" name="show_deleted" value="{{ $showDeleted ? '1' : '' }}">
            
            {{-- Search Box --}}
            <div class="flex-1 min-w-64">
                <label class="block text-sm font-medium text-gray-700 mb-1">Search Users</label>
                <input type="text" 
                       name="search" 
                       value="{{ $search }}"
                       placeholder="Search by name or email..."
                       class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            {{-- Customer Filter --}}
            <div class="min-w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Customer</label>
                <select name="customer_id" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Customers</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ $customerFilter == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            {{-- Depot Filter --}}
            <div class="min-w-48">
                <label class="block text-sm font-medium text-gray-700 mb-1">Filter by Depot</label>
                <select name="depot_id" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Depots</option>
                    @foreach($depots as $depot)
                        <option value="{{ $depot->id }}" {{ $depotFilter == $depot->id ? 'selected' : '' }}>
                            {{ $depot->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            {{-- Action Buttons --}}
            <div class="flex gap-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Search
                </button>
                <a href="{{ route('app.users.index', ['show_deleted' => $showDeleted ? '1' : '']) }}" 
                   class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                    Clear
                </a>
            </div>
        </form>
    </div>

    {{-- User List Table --}}
    <div class="overflow-x-auto bg-white border border-gray-200">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-4 py-2 text-left">Name</th>
            <th class="px-4 py-2 text-left">Email</th>
            <th class="px-4 py-2 text-left align-top">Roles</th>
            <th class="px-4 py-2 text-left">Customer Access</th>
            <th class="px-4 py-2 text-left">Depots</th>
            <th class="px-4 py-2 text-left align-top">Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $user)
            <tr class="border-t hover:bg-gray-50">
              {{-- Name --}}
              <td class="px-4 py-2">
                {{ $user->name }}
                @if(!($user->is_active ?? true))
                  <span class="ml-2 text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">❌ Disabled</span>
                @endif
              </td>
              {{-- Email --}}
              <td class="px-4 py-2">{{ $user->email }}</td>
              {{-- Roles --}}
              <td class="px-4 py-2 align-top">
                <div class="flex flex-wrap gap-1">
                  @foreach($user->roles as $role)
                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">
                      {{ $role->name }}
                    </span>
                  @endforeach
                </div>
              </td>
              {{-- Customer Access --}}
              <td class="px-4 py-2">
                <div class="text-xs">
                  {{-- Multiple customers assignment --}}
                  @if($user->customers->count() > 0)
                    <div class="flex flex-wrap gap-1">
                      @foreach($user->customers as $customer)
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full">
                          {{ $customer->name }}
                        </span>
                      @endforeach
                    </div>
                  @elseif($user->hasRole(['admin', 'site-admin', 'depot-admin']))
                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                      All Customers
                    </span>
                  @else
                    <span class="text-gray-500">No Access</span>
                  @endif
                </div>
              </td>
              {{-- Depots --}}
            <td class="px-4 py-2 align-top">
  <div class="flex flex-wrap gap-1">
    @foreach($user->depots as $depot)
      <span
        class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full"
        style="flex: 0 0 15%; white-space: nowrap;"
      >
        {{ $depot->name }}
      </span>
    @endforeach
  </div>
</td>
              {{-- Actions --}}
              <td class="px-4 py-2 align-top">
                <div class="flex flex-col space-y-1">
                  @if($showDeleted)
                    {{-- Actions for deleted users --}}
                    @if($user->canBeEditedBy(auth()->user()))
                      <a href="{{ route('app.users.edit', $user) }}"
                         class="inline-block text-center px-2 py-1 bg-yellow-500 text-white rounded-full hover:bg-yellow-600 text-xs">
                        View/Edit
                      </a>
                      <form action="{{ route('app.users.restore', $user) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="inline-block text-center px-2 py-1 bg-green-500 text-white rounded-full hover:bg-green-600 text-xs"
                                onclick="return confirm('Are you sure you want to restore this user?')">
                          Restore
                        </button>
                      </form>
                    @endif
                  @else
                    {{-- Actions for active users --}}
                    @if($user->canBeEditedBy(auth()->user()))
                      <a href="{{ route('app.users.edit', $user) }}"
                         class="inline-block text-center px-2 py-1 bg-yellow-500 text-white rounded-full hover:bg-yellow-600 text-xs">
                        Edit
                      </a>
                    @else
                      <span class="inline-block text-center px-2 py-1 bg-gray-300 text-gray-500 rounded-full text-xs cursor-not-allowed">
                          No Access
                      </span>
                    @endif
                    
                    @if(auth()->user()->hasRole('admin') || auth()->user()->isProtectedSystemOwner())
                      <form action="{{ route('app.users.destroy', $user) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit"
                                class="inline-block text-center px-2 py-1 bg-red-500 text-white rounded-full hover:bg-red-600 text-xs"
                                onclick="return confirm('Are you sure you want to delete this user?')">
                          Delete
                        </button>
                      </form>
                    @endif
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="text-center py-4">No users found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
      {{ $users->links() }}
    </div>
</div>
</x-warehouse-layout>
