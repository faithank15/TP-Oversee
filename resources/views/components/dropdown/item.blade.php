@props([
    'href' => null,
    'icon' => null,
    'variant' => 'default',
])

@php
    $classes = implode(' ', [
        'w-full flex items-center gap-3 px-4 py-2.5 text-sm font-semibold no-underline transition cursor-pointer text-left',
        $variant === 'danger' ? 'text-danger hover:bg-danger-soft' : 'text-ink hover:bg-accent-soft',
    ]);
@endphp

@if ($href)
    <a href="{{ $href }}" role="menuitem" {{ $attributes->class($classes) }}>
        @if ($icon)
            @svg('iconoir-' . $icon, 'w-5 h-5 shrink-0')
        @endif
        {{ $slot }}
    </a>
@else
    <button role="menuitem" {{ $attributes->class($classes) }}>
        @if ($icon)
            @svg('iconoir-' . $icon, 'w-5 h-5 shrink-0')
        @endif
        {{ $slot }}
    </button>
@endif
