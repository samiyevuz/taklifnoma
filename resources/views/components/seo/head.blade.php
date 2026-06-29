@php
    /** @var \App\Support\Seo\SeoData|null $seo */
    $seo ??= null;
    $pageTitle = $title ?? $seo?->title ?? config('seo.site_name');
    $description = $seo?->description ?? ($metaDescription ?? null);
    $canonical = $seo?->canonical;
    $indexable = $seo?->indexable ?? false;
    $hreflang = $seo?->hreflang ?? [];
    $jsonLd = $seo?->jsonLd ?? [];
    $siteName = config('seo.site_name');
    $ogImage = $seo?->ogImage ?? asset(config('seo.default_og_image_path', 'favicon.svg'));
    $ogType = $seo?->ogType ?? 'website';
    $twitterHandle = config('seo.twitter_handle');
@endphp

<title>{{ $pageTitle }}</title>

@if ($description)
    <meta name="description" content="{{ $description }}">
@endif

@if (! $indexable)
    <meta name="robots" content="noindex, nofollow">
@endif

@if ($canonical)
    <link rel="canonical" href="{{ $canonical }}">
@endif

@foreach ($hreflang as $locale => $url)
    <link rel="alternate" hreflang="{{ $locale }}" href="{{ $url }}">
@endforeach

<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:type" content="{{ $ogType }}">
@if ($description)
    <meta property="og:description" content="{{ $description }}">
@endif
@if ($canonical)
    <meta property="og:url" content="{{ $canonical }}">
@endif
@if ($ogImage)
    <meta property="og:image" content="{{ $ogImage }}">
@endif

<meta name="twitter:card" content="{{ $ogImage ? 'summary_large_image' : 'summary' }}">
<meta name="twitter:title" content="{{ $pageTitle }}">
@if ($description)
    <meta name="twitter:description" content="{{ $description }}">
@endif
@if ($ogImage)
    <meta name="twitter:image" content="{{ $ogImage }}">
@endif
@if ($twitterHandle)
    <meta name="twitter:site" content="{{ $twitterHandle }}">
@endif

@foreach ($jsonLd as $schema)
    <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@endforeach
