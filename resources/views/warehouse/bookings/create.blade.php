{{-- resources/views/admin/bookings/create.blade.php --}}
<x-warehouse-layout>
  <x-slot name="header">
    <div class="flex items-center justify-between">
      <h2 class="font-semibold text-xl">Create Booking Paul</h2>
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
        {{-- Display validation errors --}}
        @if ($errors->any())
          <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">
                  There were {{ $errors->count() }} error(s) with your submission:
                </h3>
                <div class="mt-2 text-sm text-red-700">
                  <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              </div>
            </div>
          </div>
        @endif

        {{-- Display success messages --}}
        @if (session('success'))
          <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
              </div>
              <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
              </div>
            </div>
          </div>
        @endif

        {{-- Debug info (remove in production) --}}
        @if(app()->isLocal())
          <div class="mb-4 p-3 bg-gray-100 border rounded text-xs">
            <strong>Debug Info:</strong><br>
            Form action: {{ route('app.bookings.store') }}<br>
            CSRF token: {{ csrf_token() }}<br>
            Errors count: {{ $errors->count() }}<br>
            Request method: {{ request()->method() }}<br>
            @if(session('general'))
              General error: {{ session('general') }}<br>
            @endif
          </div>
        @endif

        <form action="{{ route('app.bookings.store') }}" method="POST">
          @csrf
          {{-- Include all the input fields --}}
          @include('admin.bookings._form')
          {{-- Form actions --}}
          <div class="mt-6 flex space-x-3">
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
              Save Booking
            </button>
            <a href="{{ route('app.bookings.index') }}"
               class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">
              Cancel
            </a>
          </div>
        </form>
      </div>

      {{-- Available Dates Sidebar --}}
      <div class="bg-white p-6 rounded shadow">
        <h3 class="text-lg font-semibold mb-4">📅 Available Dates</h3>
        <div id="admin-date-sidebar" class="space-y-2">
          <p class="text-gray-500 text-sm">Select customer & booking type to see available dates</p>
        </div>
      </div>

    </div>
  </div>
</x-warehouse-layout>
