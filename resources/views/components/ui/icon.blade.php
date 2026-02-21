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
@elseif ($name === 'linkedin')
    <svg {{ $attributes->class(['size-4']) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <rect x="4" y="4" width="16" height="16" rx="2.5" stroke="currentColor" stroke-width="1.8" />
        <path d="M8 10V16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
        <circle cx="8" cy="8" r="1" fill="currentColor" />
        <path d="M12 16V13C12 11.8954 12.8954 11 14 11C15.1046 11 16 11.8954 16 13V16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
    </svg>
@elseif ($name === 'github')
    <svg {{ $attributes->class(['size-4']) }} viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
        <path d="M12 2C6.477 2 2 6.595 2 12.263C2 16.797 4.865 20.644 8.839 22C9.339 22.096 9.521 21.778 9.521 21.506C9.521 21.262 9.512 20.455 9.508 19.596C6.726 20.222 6.139 18.237 6.139 18.237C5.685 17.038 5.03 16.719 5.03 16.719C4.122 16.08 5.098 16.093 5.098 16.093C6.102 16.166 6.631 17.147 6.631 17.147C7.523 18.718 8.971 18.263 9.542 18.001C9.633 17.332 9.89 16.875 10.175 16.614C7.954 16.353 5.62 15.46 5.62 11.474C5.62 10.338 6.013 9.411 6.657 8.68C6.553 8.417 6.207 7.356 6.754 5.92C6.754 5.92 7.594 5.644 9.504 6.981C10.303 6.753 11.16 6.639 12 6.635C12.84 6.639 13.698 6.753 14.498 6.981C16.406 5.644 17.244 5.92 17.244 5.92C17.792 7.356 17.446 8.417 17.343 8.68C17.988 9.411 18.379 10.338 18.379 11.474C18.379 15.47 16.041 16.35 13.813 16.606C14.171 16.927 14.492 17.556 14.492 18.521C14.492 19.904 14.48 21.172 14.48 21.506C14.48 21.781 14.66 22.101 15.167 22C19.137 20.642 22 16.796 22 12.263C22 6.595 17.523 2 12 2Z" />
    </svg>
@elseif ($name === 'x')
    <svg {{ $attributes->class(['size-4']) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M5 4L10.9 12.2L5.4 20H8.1L12.2 14.2L16.4 20H19L12.9 11.6L18.1 4H15.4L11.6 9.6L7.6 4H5Z" fill="currentColor" />
    </svg>
@endif
