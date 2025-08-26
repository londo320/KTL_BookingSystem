<x-warehouse-layout>
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
      <form method="POST" action="{{ route('app.settings.tipping-workflow') }}">
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
    {{-- Depot Map Management Section --}}
    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-semibold text-gray-800 mb-4">🗺️ Depot Map Management</h3>
      <p class="text-sm text-gray-600 mb-4">
        Configure and manage interactive depot maps showing real-time bay status and positions.
      </p>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('app.depot-map.index') }}" 
           class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
          <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
            🗺️
          </div>
          <div class="ml-4">
            <h4 class="font-medium text-gray-900">View Depot Map</h4>
            <p class="text-sm text-gray-500">See live bay status and operations</p>
          </div>
        </a>
        <a href="{{ route('app.depot-map.manage-positions') }}" 
           class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
          <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
            🎯
          </div>
          <div class="ml-4">
            <h4 class="font-medium text-gray-900">Position Bays</h4>
            <p class="text-sm text-gray-500">Drag and drop bay positions on map</p>
          </div>
        </a>
        <a href="{{ route('app.depots.index') }}" 
           class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
          <div class="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
            🏭
          </div>
          <div class="ml-4">
            <h4 class="font-medium text-gray-900">Manage Depots</h4>
            <p class="text-sm text-gray-500">Upload map files and depot settings</p>
          </div>
        </a>
        <a href="{{ route('app.tipping-bays.index') }}" 
           class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
          <div class="flex-shrink-0 w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
            🚛
          </div>
          <div class="ml-4">
            <h4 class="font-medium text-gray-900">Manage Bays</h4>
            <p class="text-sm text-gray-500">Configure tipping bay settings</p>
          </div>
        </a>
      </div>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
      <a href="{{ route('app.depots.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        📦 Manage Depots
      </a>
      <a href="{{ route('app.booking-types.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        🧱 Manage Booking Types
      </a>
      <a href="{{ route('app.slot-templates.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        🕒 Slot Duration Rules (Handball etc.)
      </a>
      <a href="{{ route('app.slot-capacity.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        ⚙️ Slot Generation Rules
      </a>
      <a href="{{ route('app.slots.generate.form') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        🧮 Generate Slots
      </a>
      <a href="{{ route('app.slot-usage.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        📊 Slot Usage Viewer
      </a>
      <a href="{{ route('app.products.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        📦 Products
      </a>
      <a href="{{ route('app.users.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        👥 Users Settings
      </a>
      <a href="{{ route('app.customers.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        👥 Customer Settings
      </a>
      <a href="{{ route('app.slotReleaseRules.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        ⚙️ Slot Rules Config
      </a>
      <a href="{{ route('app.settings.pallet-types') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        📦 Pallet Types
      </a>
      <a href="{{ route('app.trailer-types.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        🚛 Trailer Types
      </a>
      <a href="{{ route('app.carriers.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        🚚 Carrier Management
      </a>
      <a href="{{ route('app.tipping-locations.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        📍 Tipping Locations
      </a>
      <a href="{{ route('app.tipping-bays.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        🏗️ Tipping Bays
      </a>
      <a href="{{ route('app.depot-map.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        🗺️ Depot Map View
      </a>
      <a href="{{ route('app.depot-map.manage-positions') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        🎯 Position Bays on Map
      </a>
      <a href="{{ route('app.arrival-time-settings.index') }}" class="block p-4 bg-white shadow rounded hover:bg-gray-50">
        🕐 Arrival Time Rules
      </a>
@if($depots->count())
  <div class="col-span-2 mt-6">
    <h3 class="text-lg font-semibold mb-2">🔁 Customer Depot Product Rules</h3>
    <p class="text-sm text-gray-600 mb-2">
      <a href="{{ route('app.customer-depot-products.index') }}" class="text-blue-600 hover:underline">
        Manage Customer-Depot-Product relationships
      </a>
    </p>
  </div>
@endif
    </div>
  </div>
</x-warehouse-layout>
