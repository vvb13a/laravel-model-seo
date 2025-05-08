@php
    /**  @var Vvb13a\LaravelModelSeo\Services\SeoHandler $seo */
@endphp

<title>{{ $seo->getTitle() }}</title>
@if($description = $seo->getDescription())
    <meta name="description" content="{{ $description }}">
@endif
@if($keywords = $seo->getKeywords())
    <meta name="keywords" content="{{ $keywords }}">
@endif
@if($robots = $seo->getRobots())
    <meta name="robots" content="{{ $robots }}">
@endif
@if($canonicalUrl = $seo->getCanonicalUrl())
    <link rel="canonical" href="{{ $canonicalUrl }}">
@endif

<meta property="og:type" content="{{ $seo->getOgType() }}">
<meta property="og:title" content="{{ $seo->getOgTitle() }}">
@if($ogUrl = $seo->getOgUrl())
    <meta property="og:url" content="{{ $ogUrl }}">
@endif
@if($ogSiteName = $seo->getOgSiteName())
    <meta property="og:site_name" content="{{ $ogSiteName }}">
@endif
@if($ogDescription = $seo->getOgDescription())
    <meta property="og:description" content="{{ $ogDescription }}">
@endif
@if($ogImageUrl = $seo->getOgImageUrl())
    <meta property="og:image" content="{{ $ogImageUrl }}">
@endif
@if($ogLocale = $seo->getOgLocale())
    <meta property="og:locale" content="{{ $ogLocale }}">
@endif

<meta name="twitter:card" content="{{ $seo->getTwitterCard() }}">
<meta name="twitter:title" content="{{ $seo->getTwitterTitle() }}">
@if($twitterDescription = $seo->getTwitterDescription())
    <meta name="twitter:description" content="{{ $twitterDescription }}">
@endif
@if($twitterImageUrl = $seo->getTwitterImageUrl())
    <meta name="twitter:image" content="{{ $twitterImageUrl }}">
@endif
@if($twitterSite = $seo->getTwitterSite())
    <meta name="twitter:site" content="{{ $twitterSite }}">
@endif
@if($twitterCreator = $seo->getTwitterCreator())
    <meta name="twitter:creator" content="{{ $twitterCreator }}">
@endif

@if($jsonLdScript = $seo->getJsonLdScript())
    {!! $jsonLdScript !!}
@endif
