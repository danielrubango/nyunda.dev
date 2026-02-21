@php
    $googleTagManagerId = config('services.analytics.google.tag_manager_id');
@endphp

@if (is_string($googleTagManagerId) && $googleTagManagerId !== '')
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $googleTagManagerId }}"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->
@endif
