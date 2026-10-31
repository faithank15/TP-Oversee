@props([
    'variant' => 'primary',
    'size' => 'md',
    'block' => false,
    'href' => null,
])

@php
    $base = 'inline-flex items-center justify-center gap-2 rounded-pill font-semibold leading-tight border-2 border-transparent cursor-pointer no-underline transition hover:-translate-y-px active:translate-y-0 focus-visible:outline-none focus-visible:ring-[3px] focus-visible:ring-accent/45 disabled:opacity-45 disabled:cursor-not-allowed disabled:translate-y-0';

    $variants = [
        'primary' => 'bg-primary text-white hover:bg-primary-hover',
        'accent' => 'bg-accent text-ink hover:bg-accent-hover',
        'outline' => 'bg-transparent text-primary border-primary hover:bg-primary hover:text-white',
        'ghost' => 'bg-transparent text-ink hover:bg-accent-soft',
        'danger' => 'bg-transparent text-danger border-danger hover:bg-danger hover:text-white',
    ];

    $sizes = [
        'sm' => 'px-3.5 py-1.5 text-sm',
        'md' => 'px-5 py-2.5',
        'lg' => 'px-7 py-3.5 text-lg',
    ];

    $classes = implode(' ', [
        $base,
        $variants[$variant] ?? $variants['primary'],
        $sizes[$size] ?? $sizes['md'],
        $block ? 'flex w-full' : '',
    ]);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->class($classes) }}>{{ $slot }}</button>
@endif
