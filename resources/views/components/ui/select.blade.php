@props([
    'name' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => null,
])

<div class="relative">
    <select
        @if (is_string($name) && $name !== '')
            name="{{ $name }}"
        @endif
        {{ $attributes->class(['h-10 w-full appearance-none rounded-sm border border-zinc-300 bg-white px-3 pe-9 text-sm text-zinc-900']) }}
    >
        @if (is_string($placeholder) && $placeholder !== '')
            <option value="">{{ $placeholder }}</option>
        @endif

        @foreach ($options as $value => $label)
            <option value="{{ $value }}" @selected((string) $selected === (string) $value)>{{ $label }}</option>
        @endforeach
    </select>

    <span class="pointer-events-none absolute inset-y-0 end-3 flex items-center text-zinc-500">
        <x-ui.icon name="chevron-down" class="size-4" />
    </span>
</div>
