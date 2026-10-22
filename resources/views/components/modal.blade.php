@props(['name', 'show' => false, 'focusable' => false])

<div
    x-data="{ show: @js($show) }"
    x-on:open-modal.window="$event.detail === '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail === '{{ $name }}' ? show = false : null"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    style="display: none;"
    class="position-fixed top-0 start-0 w-100 h-100 overflow-auto bg-dark bg-opacity-50 p-3"
    role="dialog"
    aria-modal="true"
    aria-label="Hộp thoại xác nhận"
>
    <div class="min-vh-100 d-flex align-items-center justify-content-center" x-on:click.self="show = false">
        <div class="bg-white rounded shadow p-4 w-100" style="max-width: 32rem;" @if ($focusable) x-trap="show" @endif>
            {{ $slot }}
        </div>
    </div>
</div>
