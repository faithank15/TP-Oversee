@props([
    'title' => null,
])

<section {{ $attributes->class('bg-card rounded-(--radius) shadow-card p-6') }}>
    @if ($title || isset($header))
        <div class="mb-4">
            @isset($header)
                {{ $header }}
            @else
                <h3 class="text-xl font-bold m-0">{{ $title }}</h3>
            @endisset
        </div>
    @endif

    {{ $slot }}

    @isset($footer)
        <div class="mt-6 pt-4 border-t border-border flex gap-3 justify-end">
            {{ $footer }}
        </div>
    @endisset
</section>
