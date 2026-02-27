<x-app-layout>
  <x-slot name="header">
    <h2 class="text-xl font-semibold">🛠 Admin Settings Panel</h2>
  </x-slot>
  <div class="max-w-7xl mx-auto py-6 space-y-6">
    @if (session('success'))
      <div class="p-4 bg-green-100 border border-green-400 text-green-700 rounded">
        {{ session('success') }}
      </div>
    @endif

    {{-- System Settings --}}
    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">⚙️ System Settings</h3>

      {{-- Tipping Workflow --}}
      <form method="POST" action="{{ route('app.settings.tipping-workflow') }}" class="mb-4">
        @csrf
        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
          <div>
            <h4 class="font-medium text-gray-800">Enable Tipping Workflow</h4>
            <p class="text-sm text-gray-600 mt-1">
              When enabled, enforces the structured tipping workflow process.
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

      {{-- Module Management --}}
      <div class="space-y-3">
        <h4 class="font-medium text-gray-700 mt-4 mb-2">Module Management</h4>

        {{-- Inbound Module --}}
        <form method="POST" action="{{ route('app.settings.inbound-module') }}">
          @csrf
          <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
            <div>
              <h5 class="font-medium text-gray-800">Inbound Operations</h5>
              <p class="text-sm text-gray-600 mt-1">Container bookings, tipping workflow, inbound processes</p>
            </div>
            <div class="flex items-center">
              <input type="hidden" name="inbound_module_enabled" value="0">
              <input
                type="checkbox"
                name="inbound_module_enabled"
                value="1"
                {{ $inboundModuleEnabled ? 'checked' : '' }}
                onchange="this.form.submit()"
                class="w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500"
              >
              <span class="ml-2 text-sm font-medium text-gray-700">
                {{ $inboundModuleEnabled ? 'Enabled' : 'Disabled' }}
              </span>
            </div>
          </div>
        </form>

        {{-- Outbound Module --}}
        <form method="POST" action="{{ route('app.settings.outbound-module') }}">
          @csrf
          <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
            <div>
              <h5 class="font-medium text-gray-800">Outbound Operations</h5>
              <p class="text-sm text-gray-600 mt-1">Load management, WMS imports, delivery scheduling</p>
            </div>
            <div class="flex items-center">
              <input type="hidden" name="outbound_module_enabled" value="0">
              <input
                type="checkbox"
                name="outbound_module_enabled"
                value="1"
                {{ $outboundModuleEnabled ? 'checked' : '' }}
                onchange="this.form.submit()"
                class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
              >
              <span class="ml-2 text-sm font-medium text-gray-700">
                {{ $outboundModuleEnabled ? 'Enabled' : 'Disabled' }}
              </span>
            </div>
          </div>
        </form>

        {{-- Slot Generation Method --}}
        <form method="POST" action="{{ route('app.settings.slot-generation-method') }}">
          @csrf
          <div class="p-4 bg-blue-50 rounded-lg border border-blue-200">
            <div class="mb-3">
              <h5 class="font-medium text-gray-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Slot Generation Method
              </h5>
              <p class="text-sm text-gray-600 mt-1">Choose how slots are generated for bookings</p>
            </div>
            <div class="space-y-2">
              <label class="flex items-start p-3 border rounded-lg cursor-pointer {{ $slotGenerationMethod === 'bay' ? 'bg-blue-100 border-blue-500' : 'bg-white border-gray-300 hover:bg-gray-50' }}">
                <input
                  type="radio"
                  name="slot_generation_method"
                  value="bay"
                  {{ $slotGenerationMethod === 'bay' ? 'checked' : '' }}
                  onchange="this.form.submit()"
                  class="mt-1 h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                <div class="ml-3">
                  <span class="font-medium text-gray-900">Bay-Based Slot Generation</span>
                  <p class="text-xs text-gray-600 mt-1">
                    ✅ Per-bay operating hours<br>
                    ✅ Equipment requirements (handball capability)<br>
                    ✅ Customer bay assignments<br>
                    ✅ Multiple concurrent bookings (different bays)
                  </p>
                </div>
              </label>
              <label class="flex items-start p-3 border rounded-lg cursor-pointer {{ $slotGenerationMethod === 'template' ? 'bg-blue-100 border-blue-500' : 'bg-white border-gray-300 hover:bg-gray-50' }}">
                <input
                  type="radio"
                  name="slot_generation_method"
                  value="template"
                  {{ $slotGenerationMethod === 'template' ? 'checked' : '' }}
                  onchange="this.form.submit()"
                  class="mt-1 h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                <div class="ml-3">
                  <span class="font-medium text-gray-900">Template-Based Slot Generation</span>
                  <p class="text-xs text-gray-600 mt-1">
                    📋 Uses predefined slot templates<br>
                    📋 Simpler configuration<br>
                    📋 Legacy method
                  </p>
                </div>
              </label>
            </div>
          </div>
        </form>
      </div>
    </div>

    {{-- User Management --}}
    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">👥 User Management</h3>

      <form method="POST" action="{{ route('app.settings.admin-approval-emails') }}" class="mb-4">
        @csrf
        <div class="space-y-4">
          <div>
            <label for="admin_approval_emails" class="block text-sm font-medium text-gray-700 mb-2">
              Admin Approval Email Addresses
            </label>
            <textarea
              name="admin_approval_emails"
              id="admin_approval_emails"
              rows="3"
              class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              placeholder="admin@knowleslogistics.com, manager@knowleslogistics.com"
            >{{ $adminApprovalEmails }}</textarea>
            <p class="mt-1 text-sm text-gray-500">
              Enter email addresses separated by commas for new user registration notifications.
            </p>
          </div>
          <div class="flex justify-end">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
              Save Email Settings
            </button>
          </div>
        </div>
      </form>

      <div class="grid grid-cols-2 gap-3 mt-4">
        <a href="{{ route('app.users.index') }}" class="flex items-center p-3 border rounded hover:bg-gray-50">
          <span class="mr-2">👥</span>
          <span class="text-sm font-medium">Manage Users</span>
        </a>
        <a href="{{ route('app.customers.index') }}" class="flex items-center p-3 border rounded hover:bg-gray-50">
          <span class="mr-2">🏢</span>
          <span class="text-sm font-medium">Manage Customers</span>
        </a>
      </div>
    </div>

    {{-- Depot & Bay Management --}}
    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">🏭 Depot & Bay Management</h3>

      <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
        <a href="{{ route('app.depots.index') }}" class="flex items-center p-3 border rounded hover:bg-gray-50">
          <span class="mr-2">🏭</span>
          <span class="text-sm font-medium">Manage Depots</span>
        </a>
        <a href="{{ route('app.tipping-bays.index') }}" class="flex items-center p-3 border rounded hover:bg-gray-50">
          <span class="mr-2">🚛</span>
          <span class="text-sm font-medium">Manage Bays</span>
        </a>
        <a href="{{ route('app.tipping-locations.index') }}" class="flex items-center p-3 border rounded hover:bg-gray-50">
          <span class="mr-2">📍</span>
          <span class="text-sm font-medium">Tipping Locations</span>
        </a>
        <a href="{{ route('app.depot-map.index') }}" class="flex items-center p-3 border rounded hover:bg-gray-50">
          <span class="mr-2">🗺️</span>
          <span class="text-sm font-medium">View Depot Map</span>
        </a>
        @if($depots->count() > 0)
        <a href="{{ route('app.depot-map.manage-positions', $depots->first()) }}" class="flex items-center p-3 border rounded hover:bg-gray-50">
          <span class="mr-2">🎯</span>
          <span class="text-sm font-medium">Position Bays on Map</span>
        </a>
        @else
        <div class="flex items-center p-3 border rounded bg-gray-100 opacity-50">
          <span class="mr-2">🎯</span>
          <span class="text-sm font-medium">Position Bays (No depots)</span>
        </div>
        @endif
        <a href="{{ route('app.equipment-types.index') }}" class="flex items-center p-3 border rounded hover:bg-gray-50">
          <span class="mr-2">🛠️</span>
          <span class="text-sm font-medium">Equipment Types</span>
        </a>
      </div>
    </div>

    {{-- Booking Configuration --}}
    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">📋 Booking Configuration</h3>

      <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
        <a href="{{ route('app.booking-types.index') }}" class="flex items-center p-3 border rounded hover:bg-gray-50">
          <span class="mr-2">🧱</span>
          <span class="text-sm font-medium">Booking Types</span>
        </a>
        <a href="{{ route('app.bay-capacity-rules.index') }}" class="flex items-center p-3 border rounded hover:bg-gray-50">
          <span class="mr-2">🚪</span>
          <span class="text-sm font-medium">Bay Capacity Rules</span>
        </a>
        <a href="{{ route('app.duration-rules.index') }}" class="flex items-center p-3 border rounded hover:bg-gray-50">
          <span class="mr-2">⏱️</span>
          <span class="text-sm font-medium">Duration Rules (Case-Based)</span>
        </a>
        <a href="{{ route('app.arrival-time-settings.index') }}" class="flex items-center p-3 border rounded hover:bg-gray-50">
          <span class="mr-2">🕐</span>
          <span class="text-sm font-medium">Arrival Time Rules</span>
        </a>
      </div>
    </div>

    {{-- Slot Management --}}
    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">🕒 Slot Management</h3>

      <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
        <a href="{{ route('app.slot-templates.index') }}" class="flex items-center p-3 border rounded hover:bg-gray-50">
          <span class="mr-2">📅</span>
          <span class="text-sm font-medium">Slot Templates</span>
        </a>
        <a href="{{ route('app.slot-capacity.index') }}" class="flex items-center p-3 border rounded hover:bg-gray-50">
          <span class="mr-2">⚙️</span>
          <span class="text-sm font-medium">Slot Generation Rules</span>
        </a>
        <a href="{{ route('app.slots.generate.form') }}" class="flex items-center p-3 border rounded hover:bg-gray-50">
          <span class="mr-2">🧮</span>
          <span class="text-sm font-medium">Generate Slots (Depot)</span>
        </a>
        <a href="{{ route('app.bay-slot-generation.index') }}" class="flex items-center p-3 border rounded hover:bg-gray-50">
          <span class="mr-2">🚪</span>
          <span class="text-sm font-medium">Generate Slots (Bay)</span>
        </a>
        <a href="{{ route('app.slotReleaseRules.index') }}" class="flex items-center p-3 border rounded hover:bg-gray-50">
          <span class="mr-2">🔓</span>
          <span class="text-sm font-medium">Slot Release Rules</span>
        </a>
        <a href="{{ route('app.slot-usage.index') }}" class="flex items-center p-3 border rounded hover:bg-gray-50">
          <span class="mr-2">📊</span>
          <span class="text-sm font-medium">Slot Usage Viewer</span>
        </a>
      </div>
    </div>

    {{-- Product & Inventory --}}
    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">📦 Product & Inventory</h3>

      <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
        <a href="{{ route('app.products.index') }}" class="flex items-center p-3 border rounded hover:bg-gray-50">
          <span class="mr-2">📦</span>
          <span class="text-sm font-medium">Products</span>
        </a>
        <a href="{{ route('app.settings.pallet-types') }}" class="flex items-center p-3 border rounded hover:bg-gray-50">
          <span class="mr-2">🧱</span>
          <span class="text-sm font-medium">Pallet Types</span>
        </a>
        @if($depots->count())
        <a href="{{ route('app.customer-depot-products.index') }}" class="flex items-center p-3 border rounded hover:bg-gray-50">
          <span class="mr-2">🔁</span>
          <span class="text-sm font-medium">Customer-Depot Products</span>
        </a>
        @endif
      </div>
    </div>

    {{-- Transport & Logistics --}}
    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">🚚 Transport & Logistics</h3>

      <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
        <a href="{{ route('app.carriers.index') }}" class="flex items-center p-3 border rounded hover:bg-gray-50">
          <span class="mr-2">🚚</span>
          <span class="text-sm font-medium">Carriers/Hauliers</span>
        </a>
        <a href="{{ route('app.trailer-types.index') }}" class="flex items-center p-3 border rounded hover:bg-gray-50">
          <span class="mr-2">🚛</span>
          <span class="text-sm font-medium">Trailer Types</span>
        </a>
      </div>
    </div>

    {{-- System Tools --}}
    <div class="bg-white shadow rounded-lg p-6">
      <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">🔧 System Tools</h3>

      <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
        <a href="{{ route('app.test-email.index') }}" class="flex items-center p-3 border rounded hover:bg-gray-50">
          <span class="mr-2">📧</span>
          <span class="text-sm font-medium">Test Email System</span>
        </a>
      </div>
    </div>
  </div>
</x-app-layout>
