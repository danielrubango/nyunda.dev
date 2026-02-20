@php
    /** @var array<string, string|null> $seoData */
    $seoData = $seo ?? [];
    $seoTitle = $seoData['title'] ?? config('app.name');
    $seoDescription = $seoData['description'] ?? null;
    $seoCanonical = $seoData['canonical_url'] ?? null;
    $seoType = $seoData['og_type'] ?? 'website';
    $seoImage = $seoData['image_url'] ?? null;
    $seoSiteName = $seoData['site_name'] ?? config('app.name');
    $seoTwitterCard = $seoData['twitter_card'] ?? ($seoImage ? 'summary_large_image' : 'summary');
@endphp

<title>{{ $seoTitle }}</title>

@if ($seoDescription)
    <meta name="description" content="{{ $seoDescription }}">
@endif

@if ($seoCanonical)
    <link rel="canonical" href="{{ $seoCanonical }}">
@endif

<meta property="og:type" content="{{ $seoType }}">
<meta property="og:title" content="{{ $seoTitle }}">
@if ($seoDescription)
    <meta property="og:description" content="{{ $seoDescription }}">
@endif
@if ($seoCanonical)
    <meta property="og:url" content="{{ $seoCanonical }}">
@endif
<meta property="og:site_name" content="{{ $seoSiteName }}">

@if ($seoImage)
    <meta property="og:image" content="{{ $seoImage }}">
@endif

<meta name="twitter:card" content="{{ $seoTwitterCard }}">
<meta name="twitter:title" content="{{ $seoTitle }}">
@if ($seoDescription)
    <meta name="twitter:description" content="{{ $seoDescription }}">
@endif
@if ($seoImage)
    <meta name="twitter:image" content="{{ $seoImage }}">
@endif

@include('partials.analytics')
