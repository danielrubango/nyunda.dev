@php
    $googleMeasurementId = config('services.analytics.google.measurement_id');
@endphp

@if (is_string($googleMeasurementId) && $googleMeasurementId !== '')
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $googleMeasurementId }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $googleMeasurementId }}');
    </script>
@endif
