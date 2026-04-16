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
    $seoRobots = $seoData['robots'] ?? 'index,follow';
    $seoAlternates = $seoData['alternates'] ?? [];
    $seoSchema = $seoData['schema'] ?? [];
    $seoArticle = $seoData['article'] ?? [];
@endphp

<title>{{ $seoTitle }}</title>
<meta name="robots" content="{{ $seoRobots }}">

@if ($seoDescription)
    <meta name="description" content="{{ $seoDescription }}">
@endif

@if ($seoCanonical)
    <link rel="canonical" href="{{ $seoCanonical }}">
@endif

@foreach ($seoAlternates as $locale => $url)
    <link rel="alternate" hreflang="{{ $locale }}" href="{{ $url }}">
@endforeach

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

@if (($seoArticle['published_time'] ?? null) !== null)
    <meta property="article:published_time" content="{{ $seoArticle['published_time'] }}">
@endif
@if (($seoArticle['modified_time'] ?? null) !== null)
    <meta property="article:modified_time" content="{{ $seoArticle['modified_time'] }}">
@endif
@if (($seoArticle['author'] ?? null) !== null)
    <meta property="article:author" content="{{ $seoArticle['author'] }}">
@endif

@foreach ($seoSchema as $schemaItem)
    <script type="application/ld+json">{!! json_encode($schemaItem, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}</script>
@endforeach

@include('partials.analytics')
