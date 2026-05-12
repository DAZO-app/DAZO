<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/svg+xml" href="/DAZO-picto-carre-noir.svg">
    @php
        $configService = app(\App\Services\ConfigService::class);
        $appName = $configService->get('app_name', 'DAZO');
        $defaultDesc = "DAZO - Plateforme de décisions transparentes et collaboratives. Consultez et suivez les décisions ouvertes de notre organisation.";
    @endphp

    <title>{{ $meta['title'] ?? $appName }}</title>

    <!-- SEO / OpenGraph / Twitter -->
    <meta name="description" content="{{ $meta['description'] ?? $defaultDesc }}">
    <meta property="og:title" content="{{ $meta['title'] ?? $appName }}">
    <meta property="og:description" content="{{ $meta['description'] ?? $defaultDesc }}">
    <meta property="og:image" content="{{ $meta['image'] ?? asset('images/dazo-logo.png') }}">
    <meta property="og:type" content="{{ $meta['type'] ?? 'website' }}">
    <meta property="og:url" content="{{ url()->current() }}">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $meta['title'] ?? $appName }}">
    <meta name="twitter:description" content="{{ $meta['description'] ?? $defaultDesc }}">
    <meta name="twitter:image" content="{{ $meta['image'] ?? asset('images/dazo-logo.png') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Quill Editor -->
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>

    @vite(['resources/css/app.css', 'resources/css/dazo-theme.css', 'resources/js/app.js'])
</head>
<body>
    <div id="app">
        @if(isset($meta))
            {{-- Contenu visible uniquement par les moteurs de recherche ou avant le chargement de Vue --}}
            <div id="seo-content" style="padding: 2rem; max-width: 800px; margin: 0 auto; font-family: 'DM Sans', sans-serif;">
                <h1>{{ $meta['title'] }}</h1>
                <p style="font-size: 1.2rem; color: #64748b;">{{ $meta['description'] }}</p>
                <hr style="margin: 2rem 0; border: 0; border-top: 1px solid #e2e8f0;">
                <p>Chargement de l'application DAZO en cours...</p>
            </div>
        @else
            <div id="seo-content" style="padding: 2rem; max-width: 800px; margin: 0 auto; font-family: 'DM Sans', sans-serif;">
                <h1>{{ $appName }}</h1>
                <p style="font-size: 1.2rem; color: #64748b;">{{ $defaultDesc }}</p>
                <hr style="margin: 2rem 0; border: 0; border-top: 1px solid #e2e8f0;">
                <p>Chargement de l'application DAZO en cours...</p>
            </div>
        @endif
    </div>
</body>
</html>
