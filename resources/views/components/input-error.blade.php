@props(['messages' => []])

@if ($messages)
    <div {{ $attributes->merge(['class' => 'text-sm text-danger']) }} role="alert">
        @foreach ((array) $messages as $message)
            <p>{{ $message }}</p>
        @endforeach
    </div>
@endif
