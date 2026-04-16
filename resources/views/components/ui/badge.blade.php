@props([
    'variant' => 'neutral',
])

@php
    $variantClasses = match ($variant) {
        'internal' => 'border-zinc-300 bg-zinc-100 text-zinc-700',
        'external' => 'border-brand-200 bg-brand-50 text-brand-800',
        'community' => 'border-zinc-300 bg-white text-zinc-700',
        'success' => 'border-green-200 bg-green-50 text-green-700',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
        'danger' => 'border-red-200 bg-red-50 text-red-700',
        'info' => 'border-sky-200 bg-sky-50 text-sky-700',
        default => 'border-zinc-200 bg-zinc-100 text-zinc-700',
    };
@endphp

<span {{ $attributes->class(['inline-flex items-center rounded-sm border px-2 py-1 text-[11px] font-semibold uppercase tracking-wide', $variantClasses]) }}>
    {{ $slot }}
</span>
