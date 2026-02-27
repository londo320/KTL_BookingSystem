{{-- resources/views/admin/bookings/create.blade.php --}}
<x-app-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl">Create Booking</h2>
      <a href="{{ route('app.bookings.index') }}"
         class="px-3 py-1 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 text-sm">
        ← Back to List
      </a>
    </div>
  </x-slot>
  <div class="py-6 max-w-7xl mx-auto">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

      {{-- Main Booking Form --}}
      <div class="lg:col-span-2 bg-white p-6 rounded shadow">
        <form action="{{ route('app.bookings.store') }}" method="POST">
          @csrf
          {{-- Include all the input fields --}}
          @include('admin.bookings._form')
          {{-- Form actions --}}
          <div class="mt-4 flex space-x-3">
            <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
              Save Booking
            </button>
            <a href="{{ route('app.bookings.index') }}"
               class="px-6 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
              Cancel
            </a>
          </div>
        </form>
      </div>

      {{-- Available Dates Sidebar --}}
      <div class="bg-red-100 p-6 rounded shadow border-4 border-red-500" style="min-height: 400px;">
        <h3 class="text-lg font-semibold mb-4 text-red-900">📅 Available Dates (SIDEBAR TEST)</h3>
        <div id="admin-date-sidebar" class="space-y-2">
          <p class="text-red-700 text-sm font-bold">Select customer & booking type to see available dates</p>
          <p class="text-red-700 text-xs">If you can see this RED box, the sidebar is working!</p>
        </div>
      </div>

    </div>
  </div>
</x-app-layout>
