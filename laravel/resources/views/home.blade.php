<x-layouts.app :seo="[
        'title' => $seoData['title'] ?? null,
        'description' => $seoData['description'] ?? null,
        'keywords' => $seoData['keywords'] ?? null,
        'breadcrumbs' => [
            ['name' => __('common.home'), 'url' => route('home')]
        ]
    ]">

    @push('structured-data')
            <!-- ItemList Schema for Featured Events -->
            <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => __('common.featured_events') . ' | Anntix',
            'description' => __('common.footer_tagline'),
            'numberOfItems' => $events->count(),
            'itemListElement' => $events->map(function ($event, $index) {
                return [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'url' => route('events.show', $event),
                    'item' => [
                        '@type' => 'Event',
                        'name' => $event->name,
                        'description' => strip_tags(Str::limit($event->description, 200)),
                        'startDate' => $event->start_date?->toIso8601String(),
                        'location' => [
                            '@type' => 'Place',
                            'name' => $event->location,
                            'address' => [
                                '@type' => 'PostalAddress',
                                'addressLocality' => $event->city,
                                'addressRegion' => $event->province,
                                'addressCountry' => 'ID'
                            ]
                        ],
                        'image' => $event->banner_path
                            ? (Str::startsWith($event->banner_path, ['http', 'https'])
                                ? $event->banner_path
                                : asset('storage/' . $event->banner_path))
                            : asset('logo.png'),
                        'offers' => [
                            '@type' => 'AggregateOffer',
                            'lowPrice' => $event->tickets->min('price') ?? 0,
                            'highPrice' => $event->tickets->max('price') ?? 0,
                            'priceCurrency' => 'IDR'
                        ]
                    ]
                ];
            })->toArray()
        ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
        </script>
    @endpush

    <div class="bg-white min-h-screen pb-20">

        <!-- Main Banner Carousel -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8 pt-32">
            <div x-data="{
                    activeSlide: 0,
                    slides: {{ $banners->map(fn($b) => [
    'img' => asset('storage/' . $b->image_path),
    'title' => $b->title,
    'link' => $b->link_url ?? route('events.index')
])->toJson() }},
                    next() { this.activeSlide = (this.activeSlide + 1) % this.slides.length },
                    prev() { this.activeSlide = (this.activeSlide - 1 + this.slides.length) % this.slides.length },
                    timer: null
                }" x-init="timer = setInterval(() => next(), 5000)" @mouseenter="clearInterval(timer)"
                @mouseleave="timer = setInterval(() => next(), 5000)"
                class="relative group rounded-3xl overflow-hidden shadow-2xl shadow-primary/10 aspect-[3/1] md:aspect-[3.5/1]">

                <!-- Slides -->
                <template x-for="(slide, index) in slides" :key="index">
                    <div x-show="activeSlide === index" x-transition:enter="transition transform duration-500 ease-out"
                        x-transition:enter-start="opacity-0 scale-105" x-transition:enter-end="opacity-100 scale-100"
                        x-transition:leave="transition transform duration-500 ease-in"
                        x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                        class="absolute inset-0 w-full h-full bg-gray-100">

                        <a :href="slide.link" class="block w-full h-full relative">
                            <!-- Image -->
                            <img :src="slide.img" :alt="slide.title + ' - Promo Tiket Event di Anntix'"
                                onerror="this.style.display = 'none'; this.nextElementSibling.style.display = 'flex'"
                                loading="lazy"
                                class="w-full h-full object-cover transition-transform duration-700 hover:scale-105">

                            <!-- Fallback Icon -->
                            <div
                                class="hidden w-full h-full absolute inset-0 items-center justify-center bg-gray-100 text-gray-300">
                                <svg class="w-20 h-20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        </a>
                    </div>
                </template>

                <!-- Navigation Arrows -->
                <button @click="prev()"
                    class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/10 backdrop-blur-md border border-white/20 rounded-full flex items-center justify-center text-white hover:bg-white/20 transition-all opacity-0 group-hover:opacity-100 z-10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button @click="next()"
                    class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-white/10 backdrop-blur-md border border-white/20 rounded-full flex items-center justify-center text-white hover:bg-white/20 transition-all opacity-0 group-hover:opacity-100 z-10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <!-- Pagination Dots -->
                <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex gap-2 z-10">
                    <template x-for="(slide, index) in slides" :key="index">
                        <button @click="activeSlide = index"
                            class="w-2.5 h-2.5 rounded-full transition-all duration-300"
                            :class="activeSlide === index ? 'bg-white w-8' : 'bg-white/50 hover:bg-white/80'">
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <!-- Featured Events Section -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-heading font-bold text-dark">{{ __('common.featured_events') }}</h2>
                <a href="{{ route('events.index') }}"
                    class="text-sm font-bold text-primary hover:text-primary/80">{{ __('common.view_all') }}</a>
            </div>

            <!-- Event List / Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($events as $event)
                    <a href="{{ auth()->user()?->role === 'reseller' ? route('reseller.transactions.create', $event) : route('events.show', $event) }}"
                        class="block group bg-white rounded-xl overflow-hidden border border-gray-100 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                        <!-- Landscape Image -->
                        <div class="aspect-video relative overflow-hidden bg-gray-100">
                            @php
                                $bannerUrl = $event->banner_path
                                    ? (Str::startsWith($event->banner_path, ['http', 'https'])
                                        ? $event->banner_path
                                        : asset('storage/' . $event->banner_path))
                                    : null;
                            @endphp

                            @if($bannerUrl)
                                <img src="{{ $bannerUrl }}"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'"
                                    alt="Tiket {{ $event->name }} - Event {{ $event->category ?? 'Hiburan' }} di {{ $event->city ?? 'Indonesia' }}, {{ $event->start_date ? $event->start_date->format('d M Y') : 'Coming Soon' }}"
                                    loading="lazy"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @endif

                            <div
                                class="{{ $bannerUrl ? 'hidden' : 'flex' }} absolute inset-0 items-center justify-center text-gray-300 bg-gray-100">
                                <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>

                            @if($event->created_at->diffInDays(now()) < 7)
                                <div
                                    class="absolute top-3 right-3 bg-accent text-dark px-2 py-1 rounded text-xs font-bold shadow-sm z-10">
                                    {{ __('common.new') }}
                                </div>
                            @endif
                        </div>

                        <!-- Content -->
                        <div class="p-4 space-y-3">
                            <div class="flex items-center gap-2 text-xs font-medium text-primary">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                {{ $event->start_date ? $event->start_date->format('d M Y') : 'TBA' }}
                            </div>

                            <h3
                                class="font-bold text-dark leading-tight group-hover:text-primary transition-colors line-clamp-2">
                                {{ $event->name }}
                            </h3>

                            <div class="flex items-center gap-2 text-xs text-secondary">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                {{ $event->location ?? $event->city ?? __('common.location_tba') }}
                            </div>

                            <div class="pt-3 border-t border-gray-50 mt-1 flex items-center justify-between">
                                <span class="text-xs text-black font-medium">{{ __('common.starts_from') }}</span>
                                <span class="font-extrabold text-dark text-sm">
                                    @php
                                        $lowestPriceTicket = $event->tickets->sortBy('price')->first();
                                    @endphp
                                    @if($lowestPriceTicket)
                                        Rp. {{ number_format($lowestPriceTicket->price, 0, ',', '.') }}
                                    @else
                                        {{ __('common.free_tba') }}
                                    @endif
                                </span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-full py-12 text-center text-gray-500">
                        <p>{{ __('common.no_events_found') }}</p>
                    </div>
                @endforelse
            </div>
        </div>



    </div>
</x-layouts.app>