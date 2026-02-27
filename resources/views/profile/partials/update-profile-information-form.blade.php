<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600 dark:text-green-400">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        {{-- Switch User Feature Toggle (Only for paul.carr@knowleslogistics.com) --}}
        @if($user->email === 'paul.carr@knowleslogistics.com')
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="text-sm font-medium text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                            </svg>
                            Switch User Feature
                            @if($user->switch_user_enabled)
                                <span class="ml-2 px-2 py-0.5 text-xs bg-green-100 text-green-800 rounded-full">Currently Active</span>
                            @else
                                <span class="ml-2 px-2 py-0.5 text-xs bg-gray-100 text-gray-600 rounded-full">Currently Inactive</span>
                            @endif
                        </h3>
                        <p class="mt-1 text-sm text-gray-600">
                            Enable the ability to switch to other user accounts for testing and support purposes.
                            When disabled, the switch user dropdown will not appear in your navigation.
                        </p>
                    </div>
                    <div class="ml-4 flex-shrink-0">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="switch_user_enabled" value="0">
                            <input type="checkbox"
                                   id="switch_user_toggle"
                                   name="switch_user_enabled"
                                   value="1"
                                   {{ old('switch_user_enabled', $user->switch_user_enabled) ? 'checked' : '' }}
                                   class="sr-only peer"
                                   onchange="updateToggleLabel(this)">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                            <span id="toggle_label" class="ml-3 text-sm font-medium text-gray-900">
                                {{ old('switch_user_enabled', $user->switch_user_enabled) ? 'Enabled' : 'Disabled' }}
                            </span>
                        </label>
                    </div>

                    <script>
                    function updateToggleLabel(checkbox) {
                        const label = document.getElementById('toggle_label');
                        label.textContent = checkbox.checked ? 'Enabled' : 'Disabled';
                    }
                    </script>
                </div>
            </div>
        @endif

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
