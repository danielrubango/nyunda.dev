@props([
    'type' => 'text',
    'name' => null,
    'value' => null,
])

<input
    type="{{ $type }}"
    @if (is_string($name) && $name !== '')
        name="{{ $name }}"
    @endif
    @if ($value !== null)
        value="{{ $value }}"
    @endif
    {{ $attributes->class(['h-10 w-full rounded-sm border border-zinc-300 bg-white px-3 text-sm text-zinc-900 placeholder:text-zinc-400']) }}
>
