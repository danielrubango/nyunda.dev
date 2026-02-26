@props([
    'name',
])

@if ($name === 'search')
    <svg {{ $attributes->class(['size-4']) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M15.5 15.5L20 20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
        <circle cx="10.5" cy="10.5" r="6.5" stroke="currentColor" stroke-width="1.8" />
    </svg>
@elseif ($name === 'share')
    <svg {{ $attributes->class(['size-4']) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M16 8L8 12L16 16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
        <circle cx="18" cy="7" r="2" stroke="currentColor" stroke-width="1.8" />
        <circle cx="6" cy="12" r="2" stroke="currentColor" stroke-width="1.8" />
        <circle cx="18" cy="17" r="2" stroke="currentColor" stroke-width="1.8" />
    </svg>
@elseif ($name === 'copy')
    <svg {{ $attributes->class(['size-4']) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <rect x="9" y="9" width="10" height="11" rx="1.5" stroke="currentColor" stroke-width="1.8" />
        <path d="M6 15V5.5C6 4.67157 6.67157 4 7.5 4H16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
    </svg>
@elseif ($name === 'thumb-up')
    <svg {{ $attributes->class(['size-4']) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M9 10V20H5V10H9Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
        <path d="M9 19H16.2C16.9963 19 17.6944 18.4735 17.9123 17.7076L19.6723 11.5232C19.9359 10.5964 19.2398 9.67773 18.2763 9.67773H13V6.5C13 5.67157 12.3284 5 11.5 5C11.0454 5 10.6166 5.20617 10.3325 5.56L9 7.21875V19Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
    </svg>
@elseif ($name === 'heart')
    <svg {{ $attributes->class(['size-4']) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M12 20.2L10.85 19.15C6.2 14.9 3 11.975 3 8.4C3 5.475 5.325 3.2 8.2 3.2C9.825 3.2 11.385 3.975 12 5.2C12.615 3.975 14.175 3.2 15.8 3.2C18.675 3.2 21 5.475 21 8.4C21 11.975 17.8 14.9 13.15 19.15L12 20.2Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round" />
    </svg>
@elseif ($name === 'eye')
    <svg {{ $attributes->class(['size-4']) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M2.1 12.38C2.877 14.394 4.29558 16.0876 6.14 17.19C7.98442 18.2924 10.148 18.7356 12.27 18.45C14.392 18.1644 16.3855 17.1685 18 15.59C19.2432 14.3771 20.1827 13.0366 20.715 11.52C19.345 7.63 15.715 4.53 11.215 4.53C6.715 4.53 3.085 7.63 2.1 12.38Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
        <circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8" />
    </svg>
@elseif ($name === 'eye-off')
    <svg {{ $attributes->class(['size-4']) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M3 3L21 21" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
        <path d="M10.6 10.6C10.2373 10.9627 10 11.4627 10 12C10 13.1046 10.8954 14 12 14C12.5373 14 13.0373 13.7627 13.4 13.4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
        <path d="M9.9 5.3C10.5839 5.10553 11.2874 5.00753 12 5.01C16.5 5.01 20.13 8.11 21.5 12C20.9677 13.5166 20.0282 14.8571 18.785 15.87" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
        <path d="M6.23 7.53C4.34131 8.63092 2.8871 10.3422 2.1 12.38C2.877 14.394 4.29558 16.0876 6.14 17.19C7.98442 18.2924 10.148 18.7356 12.27 18.45" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
    </svg>
@elseif ($name === 'trash')
    <svg {{ $attributes->class(['size-4']) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M4 7H20" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
        <path d="M9 7V5.5C9 4.67157 9.67157 4 10.5 4H13.5C14.3284 4 15 4.67157 15 5.5V7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
        <path d="M8 7L8.8 18.2C8.86689 19.1365 9.64756 19.86 10.5864 19.86H13.4136C14.3524 19.86 15.1331 19.1365 15.2 18.2L16 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
        <path d="M10.5 10.5V16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
        <path d="M13.5 10.5V16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
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
@elseif ($name === 'corner-down-right')
    <svg {{ $attributes->class(['size-4']) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M7 5V11C7 12.1046 7.89543 13 9 13H17" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
        <path d="M13 9L17 13L13 17" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
    </svg>
@elseif ($name === 'google')
    <svg {{ $attributes->class(['size-4']) }} viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <path d="M21 12.25C21 17.15 17.7 20.65 12.2 20.65C7.15 20.65 3 16.5 3 11.45C3 6.4 7.15 2.25 12.2 2.25C14.65 2.25 16.7 3.15 18.2 4.65L15.95 6.8C15.05 5.95 13.8 5.4 12.2 5.4C8.85 5.4 6.15 8.15 6.15 11.45C6.15 14.75 8.85 17.5 12.2 17.5C15.25 17.5 16.8 15.75 17.2 14.2H12.2V11.2H20.8C20.95 11.75 21 12 21 12.25Z" fill="currentColor" />
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
