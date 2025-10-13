{{-- resources/views/admin/bookings/create.blade.php --}}
<x-app-layout>
  <x-slot name="header">
    <style>
      header .max-w-7xl {
        max-width: none !important;
      }
    </style>
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl">Create Booking</h2>
      <a href="{{ route('app.bookings.index') }}"
         class="px-3 py-1 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 text-sm">
        ← Back to List
      </a>
    </div>
  </x-slot>
  <div class="bg-white min-h-screen px-4 py-4">
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
</x-app-layout>
