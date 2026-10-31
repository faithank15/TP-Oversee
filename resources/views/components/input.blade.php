@props([
    'name',
    'label' => null,
    'type' => 'text',
    'value' => null,
    'hint' => null,
])

@php
    $hasError = $errors->has($name);
    $fieldValue = old($name, $value);
@endphp

<div class="flex flex-col gap-2 mb-4">
    @if ($label)
        <label for="{{ $name }}" class="font-semibold text-[0.925rem]">{{ $label }}</label>
    @endif

    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        value="{{ $fieldValue }}"
        @error($name) aria-invalid="true" @enderror
        {{ $attributes->class([
            'w-full px-3.5 py-2.5 bg-card rounded-(--radius) border-[1.5px] transition',
            'placeholder:text-muted/80',
            'hover:border-primary focus:outline-none focus:ring-[3px]',
            'border-border focus:border-accent focus:ring-accent/45' => ! $hasError,
            'border-danger focus:border-danger focus:ring-danger/30' => $hasError,
        ]) }}
    />

    @if ($hint && ! $hasError)
        <span class="text-[0.825rem] text-muted">{{ $hint }}</span>
    @endif

    @error($name)
        <span class="text-[0.825rem] text-danger">{{ $message }}</span>
    @enderror
</div>
