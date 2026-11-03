@props([
    'variant' => 'accent',
])

@php
    $variants = [
        'accent' => 'bg-accent-soft border border-accent',
        'danger' => 'bg-danger-soft border border-danger text-danger',
        'warning' => 'bg-warning/15 border border-warning',
    ];
@endphp

<div {{ $attributes->class([
    'px-4 py-3 rounded-(--radius)',
    $variants[$variant] ?? $variants['accent'],
]) }}>{{ $slot }}</div>
