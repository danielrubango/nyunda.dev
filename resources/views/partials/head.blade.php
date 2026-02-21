<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

@include('partials.seo-meta', [
    'seo' => $seo ?? ['title' => $title ?? config('app.name')],
])

<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|sora:400,500,600,700" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
