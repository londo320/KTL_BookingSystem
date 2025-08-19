<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Default Depot') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Select your default depot for operational dashboards. This depot will be shown automatically when you visit operational pages.') }}
        </p>
    </header>

    @if($accessibleDepots->count() > 0)
        <form method="post" action="{{ route('profile.update-default-depot') }}" class="mt-6 space-y-6">
            @csrf
            @method('patch')

            <div>
                <label for="depot_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Default Depot') }}</label>
                <select 
                    id="depot_id" 
                    name="depot_id" 
                    class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                >
                    <option value="">{{ __('— No Default Depot —') }}</option>
                    @foreach($accessibleDepots as $depot)
                        <option value="{{ $depot->id }}" {{ old('depot_id', $user->depot_id) == $depot->id ? 'selected' : '' }}>
                            {{ $depot->name }}
                        </option>
                    @endforeach
                </select>

                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('This will be your preferred depot for operations control and queue management pages. You can still switch to other depots you have access to.') }}
                </p>

                @error('depot_id')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-4">
                <button 
                    type="submit" 
                    class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150"
                >
                    {{ __('Save') }}
                </button>

                @if (session('status') === 'depot-updated')
                    <p
                        x-data="{ show: true }"
                        x-show="show"
                        x-transition
                        x-init="setTimeout(() => show = false, 2000)"
                        class="text-sm text-gray-600 dark:text-gray-400"
                    >{{ __('Default depot updated.') }}</p>
                @endif
            </div>
        </form>
    @else
        <div class="mt-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-md">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                        {{ __('No Depot Access') }}
                    </h3>
                    <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-300">
                        <p>{{ __('You do not have access to any depots. Contact your administrator to assign depot access.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</section>