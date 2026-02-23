<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

@include('partials.seo-meta', [
    'seo' => $seo ?? ['title' => $title ?? config('app.name')],
])

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
<link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">
<link rel="manifest" href="/site.webmanifest">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|newsreader:400,500,600,700|source-serif-4:400,500,600,700" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
<script>
    (() => {
        try {
            window.localStorage.setItem('flux.appearance', 'light');
        } catch (error) {
            // Ignore localStorage restrictions.
        }

        document.documentElement.classList.remove('dark');
    })();
</script>
@fluxAppearance
