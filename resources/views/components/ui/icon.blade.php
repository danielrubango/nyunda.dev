@props([
    'name',
])

@if ($name === 'search')
    <svg {{ $attributes->class(['size-4']) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M15.5 15.5L20 20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
        <circle cx="10.5" cy="10.5" r="6.5" stroke="currentColor" stroke-width="1.8" />
    </svg>
@elseif ($name === 'external-link')
    <svg {{ $attributes->class(['size-4']) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M14 4H20V10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
        <path d="M20 4L11 13" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
        <path d="M20 14V19C20 19.5523 19.5523 20 19 20H5C4.44772 20 4 19.5523 4 19V5C4 4.44772 4.44772 4 5 4H10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
    </svg>
@elseif ($name === 'chevron-down')
    <svg {{ $attributes->class(['size-4']) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M6 9L12 15L18 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
    </svg>
@endif
