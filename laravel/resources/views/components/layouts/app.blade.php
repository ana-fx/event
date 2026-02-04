@props(['seo' => [], 'title' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @if(isset($seo['title']) && $seo['title'])
            {{ ($global_settings['site_name'] ?? 'ANTIX') . ' | ' . $seo['title'] }}
        @elseif($title)
            {{ ($global_settings['site_name'] ?? 'ANTIX') . ' | ' . $title }}
        @else
            {{ $global_settings['seo_title'] ?? ($global_settings['site_name'] ?? config('app.name', 'Laravel')) }}
        @endif
    </title>

    @php
        $description = $seo['description'] ?? ($global_settings['seo_description'] ?? 'Platform beli tiket event terpercaya di Indonesia. Cepat, aman, dan mudah.');
    @endphp
    <meta name="description" content="{{ $description }}">

    <!-- DNS Prefetch & Preconnect for Performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="dns-prefetch" href="//www.google-analytics.com">
    <link rel="dns-prefetch" href="//www.googletagmanager.com">

    @php
        $siteIcon = isset($global_settings['site_icon'])
            ? asset('storage/' . $global_settings['site_icon'])
            : asset('favicon.ico');
    @endphp
    <link rel="icon" href="{{ $siteIcon }}?v=3" type="image/x-icon">
    <link rel="icon" href="{{ $siteIcon }}?v=3" type="image/png" sizes="32x32">
    <link rel="icon" href="{{ $siteIcon }}?v=3" type="image/png" sizes="192x192">
    <link rel="apple-touch-icon" href="{{ $siteIcon }}?v=3">
    <link rel="shortcut icon" href="{{ $siteIcon }}?v=3">

    <!-- Canonical URL -->
    <!-- Canonical URL -->
    <link rel="canonical" href="{{ $seo['canonical'] ?? request()->fullUrl() }}">

    <!-- Hreflang Tags for Multilingual SEO -->
    <link rel="alternate" hreflang="id" href="{{ url()->current() }}?lang=id">
    <link rel="alternate" hreflang="en" href="{{ url()->current() }}?lang=en">
    <link rel="alternate" hreflang="x-default" href="{{ url()->current() }}">

    <!-- SEO & Social Sharing -->
    <meta name="keywords"
        content="{{ $seo['keywords'] ?? ($global_settings['seo_keywords'] ?? 'tiket event, konser, festival, seminar, workshop, event organizer indonesia, beli tiket online') }}">
    <meta name="author" content="{{ $global_settings['site_name'] ?? 'Anntix' }}">
    <meta name="robots"
        content="{{ $seo['robots'] ?? 'index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1' }}">

    <meta name="language" content="{{ app()->getLocale() }}">
    <meta name="geo.region" content="ID">
    <meta name="geo.placename" content="Indonesia">
    <meta name="theme-color" content="#ea580c">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="{{ $seo['type'] ?? 'website' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ $global_settings['site_name'] ?? config('app.name') }}">
    <meta property="og:title"
        content="{{ $seo['title'] ?? ($title ?? ($global_settings['seo_title'] ?? config('app.name'))) }}">
    <meta property="og:description" content="{{ $description }}">

    @php
        $ogImage = null;
        if (isset($seo['image']) && $seo['image']) {
            $ogImage = Str::startsWith($seo['image'], ['http', 'https'])
                ? $seo['image']
                : asset('storage/' . $seo['image']);
        } elseif (isset($global_settings['seo_image'])) {
            $ogImage = asset('storage/' . $global_settings['seo_image']);
        } elseif (isset($global_settings['site_icon'])) {
            $ogImage = asset('storage/' . $global_settings['site_icon']);
        }
    @endphp

    @if($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
    @endif

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:site" content="@anntix">
    <meta property="twitter:creator" content="@anntix">
    <meta property="twitter:title"
        content="{{ $seo['title'] ?? ($title ?? ($global_settings['seo_title'] ?? config('app.name'))) }}">
    <meta property="twitter:description" content="{{ $description }}">
    @if($ogImage)
        <meta property="twitter:image" content="{{ $ogImage }}">
        <meta property="twitter:image:alt" content="{{ $seo['title'] ?? config('app.name') }}">
    @endif

    <!-- Apple Mobile Web App -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ $global_settings['site_name'] ?? 'Anntix' }}">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    @php
        $schemaGraph = [
            // Organization
            [
                '@type' => 'Organization',
                '@id' => url('/') . '#organization',
                'name' => $global_settings['site_name'] ?? 'Anntix',
                'url' => url('/'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => isset($global_settings['site_logo']) ? asset('storage/' . $global_settings['site_logo']) : asset('logo.png'),
                ],
                'sameAs' => array_filter([
                    $global_settings['facebook_url'] ?? null,
                    $global_settings['instagram_url'] ?? null,
                    $global_settings['twitter_url'] ?? null,
                ]),
                'contactPoint' => [
                    '@type' => 'ContactPoint',
                    'telephone' => $global_settings['contact_phone'] ?? '+62877-5058-1589',
                    'contactType' => 'customer support',
                    'email' => $global_settings['contact_email'] ?? 'hallo@anntix.id',
                    'areaServed' => 'ID',
                    'availableLanguage' => ['id', 'en']
                ]
            ],
            // LocalBusiness - Google Business Profile Integration
            [
                '@type' => 'LocalBusiness',
                '@id' => url('/') . '#localbusiness',
                'name' => 'Anntix - Platform Tiket Event Indonesia',
                'image' => isset($global_settings['site_logo']) ? asset('storage/' . $global_settings['site_logo']) : asset('logo.png'),
                'telephone' => $global_settings['contact_phone'] ?? '+62877-5058-1589',
                'email' => $global_settings['contact_email'] ?? 'hallo@anntix.id',
                'url' => url('/'),
                'priceRange' => '$$',
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => $global_settings['contact_location'] ?? 'Tegal, Jawa Tengah',
                    'addressLocality' => 'Tegal',
                    'addressRegion' => 'Jawa Tengah',
                    'postalCode' => '52182',
                    'addressCountry' => 'ID'
                ],
                'geo' => [
                    '@type' => 'GeoCoordinates',
                    'latitude' => '-6.9175',  // Update dengan koordinat actual dari Google Maps
                    'longitude' => '109.1425'
                ],
                'openingHoursSpecification' => [
                    [
                        '@type' => 'OpeningHoursSpecification',
                        'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                        'opens' => '09:00',
                        'closes' => '17:00'
                    ],
                    [
                        '@type' => 'OpeningHoursSpecification',
                        'dayOfWeek' => ['Saturday'],
                        'opens' => '09:00',
                        'closes' => '14:00'
                    ]
                ],
                'sameAs' => array_filter([
                    $global_settings['facebook_url'] ?? null,
                    $global_settings['instagram_url'] ?? null,
                    $global_settings['twitter_url'] ?? null,
                ])
            ],
            // WebSite dengan Search Action
            [
                '@type' => 'WebSite',
                '@id' => url('/') . '#website',
                'url' => url('/'),
                'name' => $global_settings['site_name'] ?? 'Anntix',
                'description' => $global_settings['seo_description'] ?? 'Platform pembelian tiket event terpercaya di Indonesia',
                'publisher' => [
                    '@id' => url('/') . '#organization'
                ],
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => [
                        '@type' => 'EntryPoint',
                        'urlTemplate' => url('/events') . '?search={search_term_string}'
                    ],
                    'query-input' => 'required name=search_term_string'
                ]
            ],
        ];

        // Add BreadcrumbList if available
        if (isset($seo['breadcrumbs']) && is_array($seo['breadcrumbs'])) {
            $schemaGraph[] = [
                '@type' => 'BreadcrumbList',
                'itemListElement' => collect($seo['breadcrumbs'])->map(function ($crumb, $index) {
                    return [
                        '@type' => 'ListItem',
                        'position' => $index + 1,
                        'name' => $crumb['name'],
                        'item' => $crumb['url']
                    ];
                })->values()->toArray()
            ];
        }
    @endphp
    {!! json_encode([
    '@context' => 'https://schema.org',
    '@graph' => $schemaGraph
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
    </script>

    @stack('structured-data')


</head>

<body class="theme-event font-sans text-gray-900 antialiased bg-gray-50">
    <div class="min-h-screen flex flex-col">
        <!-- Navigation -->
        @include('partials.app.header')

        <!-- Page Content -->
        <main class="flex-1">
            {{ $slot }}
        </main>

        @include('partials.app.footer')
    </div>

    <!-- WhatsApp Floating Button -->
    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $global_settings['contact_whatsapp'] ?? '6281234567890') }}"
        target="_blank" rel="noopener noreferrer"
        class="fixed bottom-6 right-6 z-50 bg-green-500 hover:bg-green-600 text-white p-4 rounded-full shadow-2xl transition-all hover:scale-110 flex items-center justify-center group"
        aria-label="Contact us on WhatsApp">
        <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
            <path
                d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.017-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z" />
        </svg>
        <!-- Tooltip -->
        <span
            class="absolute right-full mr-4 bg-white text-dark px-3 py-1.5 rounded-lg text-sm font-bold shadow-xl opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">
            {{ __('common.chat_with_us') }}
        </span>
    </a>
    @stack('scripts')
</body>

</html>
