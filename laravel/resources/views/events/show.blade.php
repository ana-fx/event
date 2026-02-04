<x-layouts.app :seo="[
        'title' => $event->seo_title ?? $event->name,
        'description' => $event->seo_description ?? Str::limit(strip_tags($event->description), 160),
        'keywords' => $event->seo_keywords ?? 'tiket ' . $event->name . ', event ' . $event->city . ', konser, festival',
        'image' => $event->banner_path ?? $event->thumbnail_path,
        'type' => 'website',
        'breadcrumbs' => [
            ['name' => __('common.home'), 'url' => route('home')],
            ['name' => __('common.events'), 'url' => route('events.index')],
            ['name' => $event->name, 'url' => route('events.show', $event)]
        ]
    ]">

    @push('structured-data')
            <!-- Event Schema -->
            <script type="application/ld+json">
                    {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Event',
            'name' => $event->name,
            'description' => strip_tags($event->description),
            'startDate' => $event->start_date?->toIso8601String(),
            'endDate' => $event->end_date?->toIso8601String(),
            'eventStatus' => 'https://schema.org/EventScheduled',
            'eventAttendanceMode' => 'https://schema.org/OfflineEventAttendanceMode',
            'image' => [
                $event->banner_path
                ? (Str::startsWith($event->banner_path, ['http', 'https'])
                    ? $event->banner_path
                    : asset('storage/' . $event->banner_path))
                : asset('logo.png')
            ],
            'location' => [
                '@type' => 'Place',
                'name' => $event->location,
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => $event->address ?? $event->location,
                    'addressLocality' => $event->city,
                    'addressRegion' => $event->province,
                    'addressCountry' => 'ID'
                ]
            ],
            'offers' => $event->tickets->map(function ($ticket) use ($event) {
                return [
                    '@type' => 'Offer',
                    'name' => $ticket->name,
                    'price' => $ticket->price,
                    'priceCurrency' => 'IDR',
                    'availability' => $ticket->quota > 0 ? 'https://schema.org/InStock' : 'https://schema.org/SoldOut',
                    'url' => route('checkout.create', $event),
                    'validFrom' => $ticket->start_date?->toIso8601String()
                ];
            })->toArray(),
            'performer' => [
                '@type' => 'PerformingGroup',
                'name' => $event->organizer_name ?? 'Anntix'
            ],
            'organizer' => [
                '@type' => 'Organization',
                'name' => $event->organizer_name ?? 'Anntix',
                'url' => url('/')
            ]
        ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
                    </script>
    @endpush

    <div class="bg-white min-h-screen pt-32">

        <!-- Breadcrumb Navigation -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4">
            <nav class="flex items-center gap-2 text-sm mb-8" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="text-gray-500 hover:text-primary transition-colors">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                    </svg>
                </a>
                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                        clip-rule="evenodd" />
                </svg>
                <a href="{{ route('events.index') }}"
                    class="text-gray-500 hover:text-primary transition-colors">Events</a>
                <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                        clip-rule="evenodd" />
                </svg>
                <span class="text-gray-900 font-medium truncate">{{ Str::limit($event->name, 50) }}</span>
            </nav>
        </div>

        <!-- Content Split (7/5 Ratio) -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-24 lg:pb-32">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 lg:gap-24">

                <!-- Left: Detailed Prose -->
                <div class="lg:col-span-7 space-y-24">
                    <section>
                        <!-- Premium Thumbnail -->
                        <div
                            class="aspect-square w-full rounded-[2.5rem] overflow-hidden mb-16 shadow-2xl shadow-gray-200/50 bg-gray-50 border border-gray-100">
                            <img src="{{ Str::startsWith($event->thumbnail_path, ['http', 'https']) ? $event->thumbnail_path : (file_exists(public_path($event->thumbnail_path)) ? asset($event->thumbnail_path) : asset('storage/' . $event->thumbnail_path)) }}"
                                class="w-full h-full object-cover"
                                alt="Tiket {{ $event->name }} - Event {{ $event->category ?? 'Hiburan' }} di {{ $event->city }} {{ $event->start_date ? $event->start_date->format('d M Y') : '' }}"
                                loading="lazy">
                        </div>
                        <div class="mb-16">
                            <div class="inline-flex items-center gap-3 mb-6">
                                <span class="w-8 h-px bg-primary"></span>
                                <span
                                    class="text-[10px] font-black uppercase tracking-[0.3em] text-primary">{{ $event->category ?? __('common.general') }}</span>
                            </div>
                            <h1
                                class="text-6xl md:text-7xl font-heading font-black text-dark tracking-tighter leading-[0.9] mb-8">
                                {{ $event->name }}<span class="text-primary">.</span>
                            </h1>
                            <div class="flex items-center gap-6 text-gray-400">
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span
                                        class="text-xs font-bold uppercase tracking-widest">{{ $event->start_date->format('d M Y') }}</span>
                                </div>
                                <div class="w-1 h-1 rounded-full bg-gray-200"></div>
                                <div class="flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    </svg>
                                    <span class="text-xs font-bold uppercase tracking-widest">{{ $event->city }}</span>
                                </div>
                            </div>
                        </div>

                        <div
                            class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-12 flex items-center gap-4">
                            {{ __('common.the_narrative') }}
                            <div class="h-px flex-1 bg-gray-100"></div>
                        </div>
                        <div
                            class="prose prose-2xl prose-primary max-w-none text-black prose-headings:text-dark prose-headings:font-black prose-headings:tracking-tighter">
                            {!! $event->description !!}
                        </div>
                    </section>

                    <section>
                        <div
                            class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-12 flex items-center gap-4">
                            {{ __('common.venue_orientation') }}
                            <div class="h-px flex-1 bg-gray-100"></div>
                        </div>
                        <div class="space-y-12">
                            <div>
                                <h4 class="text-4xl font-black text-dark mb-4">{{ $event->location }}</h4>
                                <p class="text-xl text-gray-500 leading-relaxed font-medium">
                                    {{ $event->address ?? $event->city }}
                                </p>
                                <p class="text-gray-400">{{ $event->province }}</p>
                            </div>
                            @if($event->google_map_embed)
                                <div
                                    class="w-full aspect-video rounded-[2.5rem] overflow-hidden border border-gray-100 shadow-2xl group relative">
                                    <div
                                        class="w-full h-full [&>iframe]:!w-full [&>iframe]:!h-full transition-transform duration-700 group-hover:scale-105">
                                        {!! $event->google_map_embed !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </section>

                    @if($event->terms)
                        <section>
                            <div
                                class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-12 flex items-center gap-4">
                                {{ __('common.event_policy') }}
                                <div class="h-px flex-1 bg-gray-100"></div>
                            </div>
                            <div
                                class="prose prose-lg prose-primary max-w-none text-black/70 prose-headings:text-dark prose-headings:font-bold">
                                {!! $event->terms !!}
                            </div>
                        </section>
                    @endif
                </div>

                <!-- Right: Action & Summary -->
                <div class="lg:col-span-5">
                    <div class="sticky top-32 space-y-12">
                        <!-- Pricing Sidebar -->
                        <div class="bg-gray-50 p-12 rounded-[2.5rem] border border-gray-100 relative group">
                            <div
                                class="absolute -right-4 -bottom-4 text-9xl font-black text-gray-100/50 select-none group-hover:text-primary/5 transition-colors">
                                ANNTX</div>
                            <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4 relative z-10">
                                {{ __('common.access_pass') }}
                            </p>
                            @php $lowest = $event->tickets->min('price'); @endphp
                            <h2 class="text-6xl font-black text-primary tracking-tighter mb-8 relative z-10">
                                @if($lowest !== null)
                                    Rp {{ number_format($lowest, 0, ',', '.') }}<span class="text-primary/20">.</span>
                                @else
                                    TBA.
                                @endif
                            </h2>

                            <!-- Ticket List -->
                            @if($event->tickets->count() > 0)
                                <div class="space-y-4 mb-8 relative z-10">
                                    @foreach($event->tickets as $ticket)
                                        <div class="flex justify-between items-center text-sm border-b border-gray-200 pb-2">
                                            <span class="font-bold text-dark">{{ $ticket->name }}</span>
                                            <span class="font-black text-primary">Rp
                                                {{ number_format($ticket->price, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            <a href="{{ route('checkout.create', $event) }}"
                                class="w-full py-6 bg-dark text-white text-center font-black rounded-2xl shadow-2xl hover:bg-primary transition-all duration-300 transform hover:-translate-y-1 active:scale-95 text-xl flex items-center justify-center gap-3 relative z-10">
                                {{ __('common.reserve_tickets') }}
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </a>
                        </div>

                        <!-- Hosted By -->
                        <div class="bg-white p-12 space-y-12 border-t border-gray-100">
                            <div>
                                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.3em] mb-8">
                                    {{ __('common.executive_curator') }}
                                </h4>
                                <div class="flex items-center gap-6">
                                    <div
                                        class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center text-primary font-black text-2xl shadow-inner uppercase">
                                        {{ substr($event->organizer_name ?? 'A', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-black text-dark text-2xl tracking-tight leading-none mb-1">
                                            {{ $event->organizer_name ?? 'Anntix Official' }}
                                        </div>
                                        <div
                                            class="text-xs font-bold text-primary uppercase tracking-widest flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                            {{ __('common.verified_authority') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Discovery Feed -->
        @if(isset($relatedEvents) && $relatedEvents->count() > 0)
            <div class="bg-gray-50/20 py-32 border-t border-gray-100">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div
                        class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-16 flex items-center gap-4">
                        {{ __('common.related_events') }}
                        <div class="h-px flex-1 bg-gray-200"></div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-16">
                        @foreach($relatedEvents as $related)
                            @php
                                $img = $related->banner_path ?? $related->thumbnail_path;
                                $src = 'https://via.placeholder.com/1280x720';
                                if ($img) {
                                    $src = Str::startsWith($img, ['http', 'https'])
                                        ? $img
                                        : (file_exists(public_path($img)) ? asset($img) : asset('storage/' . $img));
                                }
                            @endphp
                            <a href="{{ route('events.show', $related) }}" class="group block">
                                <div class="relative aspect-video rounded-[2rem] overflow-hidden shadow-2xl mb-8">
                                    <img src="{{ $src }}"
                                        class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-110">
                                    <div class="absolute inset-0 bg-dark/20 group-hover:bg-dark/10 transition-colors"></div>
                                    <div class="absolute bottom-10 left-10 right-10 text-white">
                                        <div class="text-xs font-black uppercase tracking-widest mb-3 opacity-70">
                                            {{ $related->category ?? __('common.general') }}
                                        </div>
                                        <h4 class="text-3xl font-black tracking-tighter leading-[0.9]">{{ $related->name }}</h4>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif

    </div>
</x-layouts.app>
