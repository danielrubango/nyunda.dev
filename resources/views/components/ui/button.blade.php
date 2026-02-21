@props([
    'href' => null,
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'md',
])

@php
    $baseClasses = 'inline-flex items-center justify-center gap-2 rounded-sm font-medium no-underline transition-colors focus-visible:outline-hidden disabled:cursor-not-allowed disabled:opacity-50';

    $sizeClasses = match ($size) {
        'sm' => 'h-8 px-2 text-sm',
        'lg' => 'h-10 px-4 text-sm',
        default => 'h-9 px-3 text-sm',
    };

    $variantClasses = match ($variant) {
        'secondary' => 'border border-zinc-300 bg-white text-zinc-800 hover:bg-zinc-100 focus-visible:border-zinc-500',
        'ghost' => 'border border-transparent text-zinc-700 hover:text-zinc-900 focus-visible:border-zinc-500',
        'danger' => 'border border-red-300 bg-red-600 text-white hover:bg-red-500 focus-visible:border-red-500',
        default => 'border border-brand-700 bg-brand-700 text-white hover:bg-brand-800 focus-visible:border-brand-800',
    };
@endphp

@if (is_string($href) && $href !== '')
    <a href="{{ $href }}" {{ $attributes->class([$baseClasses, $sizeClasses, $variantClasses]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class([$baseClasses, $sizeClasses, $variantClasses]) }}>
        {{ $slot }}
    </button>
@endif
