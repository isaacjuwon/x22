@php
    use App\Settings\SeoSettings;
    use App\Settings\GeneralSettings;

    $seo     = app(SeoSettings::class);
    $general = app(GeneralSettings::class);

    $pageTitle       = filled($title ?? null) ? $title . ' - ' . $general->site_name : ($seo->meta_title ?: $general->site_name);
    $pageDescription = $seo->meta_description ?: $general->site_description;
    $pageOgImage     = $seo->og_image;
@endphp

<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>{{ $pageTitle }}</title>

{{-- Basic SEO --}}
@if ($pageDescription)
    <meta name="description" content="{{ $pageDescription }}" />
@endif
@if ($seo->meta_keywords)
    <meta name="keywords" content="{{ $seo->meta_keywords }}" />
@endif
@if (!$seo->index_site)
    <meta name="robots" content="noindex, nofollow" />
@endif

{{-- Open Graph --}}
<meta property="og:title" content="{{ $pageTitle }}" />
<meta property="og:type" content="{{ $seo->og_type ?: 'website' }}" />
@if ($pageDescription)
    <meta property="og:description" content="{{ $pageDescription }}" />
@endif
@if ($pageOgImage)
    <meta property="og:image" content="{{ $pageOgImage }}" />
@endif
<meta property="og:url" content="{{ url()->current() }}" />

{{-- Twitter Card --}}
<meta name="twitter:card" content="{{ $seo->twitter_card ?: 'summary_large_image' }}" />
<meta name="twitter:title" content="{{ $pageTitle }}" />
@if ($pageDescription)
    <meta name="twitter:description" content="{{ $pageDescription }}" />
@endif
@if ($pageOgImage)
    <meta name="twitter:image" content="{{ $pageOgImage }}" />
@endif
@if ($seo->twitter_site)
    <meta name="twitter:site" content="{{ $seo->twitter_site }}" />
@endif

{{-- Favicons --}}
<link rel="icon" href="/favicon.ico" sizes="any">
<link rel="icon" href="/favicon.svg" type="image/svg+xml">
<link rel="apple-touch-icon" href="/apple-touch-icon.png">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@livewireStyles
@livewireScriptConfig

{{-- Apply stored theme immediately to prevent flash --}}
<script>
    (function() {
        var theme = localStorage.getItem('theme');
        var isDark = theme === 'dark' || ((!theme || theme === 'system') && window.matchMedia('(prefers-color-scheme: dark)').matches);
        if (isDark) document.documentElement.classList.add('dark');
        else document.documentElement.classList.remove('dark');
    })();
</script>

{{-- Google Analytics --}}
@if ($seo->google_analytics_id)
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $seo->google_analytics_id }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ $seo->google_analytics_id }}');
    </script>
@endif

{{-- Google Tag Manager --}}
@if ($seo->google_tag_manager_id)
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','{{ $seo->google_tag_manager_id }}');</script>
@endif
