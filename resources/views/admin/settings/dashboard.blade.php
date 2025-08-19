<x-app-layout>
  @include('layouts.admin-nav')
  
  <x-slot name="header">
    <h2 class="text-xl font-semibold">🛠 Admin Settings Panel</h2>
  </x-slot>

  <div class="max-w-4xl mx-auto py-6 space-y-6">
    
    @if (session('success'))
      <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded">
        {{ session('success') }}
      </div>
    @endif

    {{-- Tipping Workflow Settings --}}
    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-semibold text-gray-800 mb-4">🚛 Tipping Workflow Settings</h3>
      
      <form method="POST" action="{{ route('admin.settings.tipping-workflow') }}">
        @csrf
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
          <div>
            <h4 class="font-medium text-gray-800">Enable Tipping Workflow</h4>
            <p class="text-sm text-gray-600 mt-1">
              When enabled, enforces the structured tipping workflow process. 
              When disabled, allows manual bay assignments without workflow enforcement.
            </p>
          </div>
          <div class="flex items-center">
            <input type="hidden" name="tipping_workflow_enabled" value="0">
            <input 
              type="checkbox" 
              name="tipping_workflow_enabled" 
              value="1"
              {{ $tippingWorkflowEnabled ? 'checked' : '' }}
              onchange="this.form.submit()"
              class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
            >
            <span class="ml-2 text-sm font-medium text-gray-700">
              {{ $tippingWorkflowEnabled ? 'Enabled' : 'Disabled' }}
            </span>
          </div>
        </div>
      </form>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
      <a href="{{ route('admin.depots.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        📦 Manage Depots
      </a>

      <a href="{{ route('admin.booking-types.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        🧱 Manage Booking Types
      </a>

      <a href="{{ route('admin.slot-templates.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        🕒 Slot Duration Rules (Handball etc.)
      </a>

      <a href="{{ route('admin.slot-capacity.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        ⚙️ Slot Generation Rules
      </a>

      <a href="{{ route('admin.slots.generate.form') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        🧮 Generate Slots
      </a>

      <a href="{{ route('admin.slot-usage.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        📊 Slot Usage Viewer
      </a>

      <a href="{{ route('admin.products.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        📦 Products
      </a>
      
      <a href="{{ route('admin.users.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        👥 Users Settings
      </a>
      
      <a href="{{ route('admin.customers.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        👥 Customer Settings
      </a>

      <a href="{{ route('admin.slotReleaseRules.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        ⚙️ Slot Rules Config
      </a>

      <a href="{{ route('admin.settings.pallet-types') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        📦 Pallet Types
      </a>

      <a href="{{ route('admin.trailer-types.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        🚛 Trailer Types
      </a>

      <a href="{{ route('admin.carriers.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        🚚 Carrier Management
      </a>

      <a href="{{ route('admin.tipping-locations.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        📍 Tipping Locations
      </a>

      <a href="{{ route('admin.tipping-bays.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        🏗️ Tipping Bays
      </a>

      <a href="{{ route('admin.arrival-time-settings.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        🕐 Arrival Time Rules
      </a>

@if($depots->count())
  <div class="col-span-2 mt-6">
    <h3 class="text-lg font-semibold mb-2">🔁 Customer Depot Product Rules</h3>
    <p class="text-sm text-gray-600 mb-2">
      <a href="{{ route('admin.customer-depot-products.index') }}" class="text-blue-600 hover:underline">
        Manage Customer-Depot-Product relationships
      </a>
    </p>
  </div>
@endif

    </div>
  </div>
</x-app-layout>
