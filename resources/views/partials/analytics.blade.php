@php
    $googleMeasurementId = config('services.analytics.google.measurement_id');
    $googleStreamId = config('services.analytics.google.stream_id');
    $googleTagManagerId = config('services.analytics.google.tag_manager_id');
@endphp

@if (is_string($googleTagManagerId) && $googleTagManagerId !== '')
    <!-- Google Tag Manager -->
    <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','{{ $googleTagManagerId }}');
    </script>
    <!-- End Google Tag Manager -->
@endif

@if (is_string($googleMeasurementId) && $googleMeasurementId !== '')
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $googleMeasurementId }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}

        window.nyundaAnalytics = {
            measurementId: @js($googleMeasurementId),
            streamId: @js($googleStreamId),
        };

        gtag('js', new Date());
        gtag('config', '{{ $googleMeasurementId }}');
    </script>
@endif
