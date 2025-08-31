
<x-warehouse-layout>
<div class="py-6 max-w-7xl mx-auto">
    {{-- Success Message --}}
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
            {{ session('success') }}
        </div>
    @endif

    {{-- Create User Button --}}
    <div class="mb-4">
        <a href="{{ route('app.users.create') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            Create New User
        </a>
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
