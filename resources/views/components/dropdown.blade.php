@props([
    'width' => 'w-52',
])

@php
    $id = 'dropdown-' . \Illuminate\Support\Str::random(8);
@endphp

<div class="inline-flex">
    <button type="button" data-dropdown-trigger aria-controls="{{ $id }}" aria-haspopup="menu" aria-expanded="false"
        {{ $trigger->attributes->class('p-1.5 rounded-(--radius-sm) text-muted hover:bg-background hover:text-ink transition cursor-pointer') }}>
        {{ $trigger }}
    </button>

    <div id="{{ $id }}" data-dropdown-menu role="menu"
        {{ $attributes->class(['hidden fixed z-50 bg-card rounded-(--radius) shadow-card border border-border py-1.5', $width]) }}>
        {{ $slot }}
    </div>
</div>
