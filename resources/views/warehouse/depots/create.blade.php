
<x-warehouse-layout>
<div class="py-6 max-w-xl mx-auto">
  <h2 class="text-xl font-semibold mb-4">Create Depot</h2>

  <form method="POST" action="{{ route('app.depots.store') }}" class="space-y-4">
    @csrf

    <div>
      <label class="block font-medium">Name</label>
      <input type="text" name="name" class="w-full border p-2 rounded" value="{{ old('name') }}" required>
      @error('name')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
    </div>

    <div>
      <label class="block font-medium">Location</label>
      <input type="text" name="location" class="w-full border p-2 rounded" value="{{ old('location') }}">
      @error('location')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
    </div>

    <div>
      <label class="block font-medium">Cut Off Time</label>
      <input type="time" name="cut_off_time" class="w-full border p-2 rounded" value="{{ old('cut_off_time') }}" required>
      @error('cut_off_time')<p class="text-red-600 text-sm">{{ $message }}</p>@enderror
    </div>

    <div class="flex space-x-4">
      <a href="{{ route('app.depots.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</a>
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Create Depot</button>
    </div>
  </form>
</div>
</x-warehouse-layout>