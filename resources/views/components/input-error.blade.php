@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-base text-red-700 dark:text-red-300 space-y-1 font-extrabold']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
