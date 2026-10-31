@props([
    'variant' => 'default',
])

@php
    $variants = [
        'default' => 'bg-background text-ink',
        'accent' => 'bg-accent text-ink',
        'dark' => 'bg-primary text-white',
        'danger' => 'bg-danger-soft text-danger',
        'warning' => 'bg-warning/15 text-warning',
    ];
@endphp

<span {{ $attributes->class([
    'inline-block px-3 py-0.5 text-[0.78rem] font-bold rounded-pill',
    $variants[$variant] ?? $variants['default'],
]) }}>{{ $slot }}</span>
