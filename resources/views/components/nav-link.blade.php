@props([
    'href' => '#',
    'active' => false,
])

<a href="{{ $href }}"
    @if ($active) aria-current="page" @endif
    {{ $attributes->class([
        'flex items-center gap-3 px-4 py-2 rounded-(--radius) font-semibold transition',
        'bg-primary text-white' => $active,
        'text-ink hover:bg-accent-soft' => ! $active,
    ]) }}>
    {{ $slot }}
</a>
