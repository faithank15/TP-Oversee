@props([
    'name',
    'label' => null,
    'value' => null,
    'options' => [],
    'placeholder' => null,
    'hint' => null,
])

@php
    $hasError = $errors->has($name);
    $selected = old($name, $value);
@endphp

<div class="flex flex-col gap-2 mb-4">
    @if ($label)
        <label for="{{ $name }}" class="font-semibold text-[0.925rem]">{{ $label }}</label>
    @endif

    <select
        id="{{ $name }}"
        name="{{ $name }}"
        @error($name) aria-invalid="true" @enderror
        {{ $attributes->class([
            'w-full px-3.5 py-2.5 pr-10 bg-card rounded-(--radius) border-[1.5px] transition appearance-none',
            // 'bg-[url("data:image/svg+xml,%3Csvg%20xmlns=%27http://www.w3.org/2000/svg%27%20width=%2712%27%20height=%278%27%20fill=%27none%27%20stroke=%27%23002037%27%20stroke-width=%272%27%3E%3Cpath%20d=%27M1%201l5%205%205-5%27/%3E%3C/svg%3E")] bg-no-repeat bg-[right_0.9rem_center]',
            'hover:border-primary focus:outline-none focus:ring-[3px]',
            'border-border focus:border-accent focus:ring-accent/45' => ! $hasError,
            'border-danger focus:border-danger focus:ring-danger/30' => $hasError,
        ]) }}
    >
        @if ($placeholder)
            <option value="" @selected($selected === null || $selected === '')>{{ $placeholder }}</option>
        @endif

        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected((string) $selected === (string) $optionValue)>{{ $optionLabel }}</option>
        @endforeach

        {{ $slot }}
    </select>

    @if ($hint && ! $hasError)
        <span class="text-[0.825rem] text-muted">{{ $hint }}</span>
    @endif

    @error($name)
        <span class="text-[0.825rem] text-danger">{{ $message }}</span>
    @enderror
</div>
