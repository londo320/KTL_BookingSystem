@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-bold text-lg text-black dark:text-black']) }}>
    {{ $value ?? $slot }}
</label>
