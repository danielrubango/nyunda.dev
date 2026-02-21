@props([
    'variant' => 'info',
])

@php
    $variantClasses = match ($variant) {
        'success' => 'border-green-200 bg-green-50 text-green-800',
        'error' => 'border-red-200 bg-red-50 text-red-800',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
        default => 'border-zinc-300 bg-zinc-100 text-zinc-800',
    };
@endphp

<div {{ $attributes->class(['rounded-sm border px-4 py-3 text-sm', $variantClasses]) }}>
    {{ $slot }}
</div>
