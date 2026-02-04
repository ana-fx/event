<footer class="bg-dark text-white pt-20 pb-10 border-t border-white/5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex flex-col lg:flex-row justify-between gap-12 lg:gap-24 mb-16">

            <!-- Brand Section (Left) -->
            <div class="lg:w-5/12 space-y-8">
                <a href="{{ route('home') }}" class="inline-block group">
                    <div class="transition-transform group-hover:scale-105">
                        @if(isset($global_settings['site_logo_white']))
                            <img src="{{ asset('storage/' . $global_settings['site_logo_white']) }}"
                                class="h-10 w-auto object-contain">
                        @elseif(isset($global_settings['site_logo']))
                            <img src="{{ asset('storage/' . $global_settings['site_logo']) }}"
                                class="h-10 w-auto object-contain brightness-0 invert">
                        @else
                            <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center text-white">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                </a>
                <p class="text-gray-400 leading-relaxed text-sm max-w-sm">
                    {{ __('common.footer_tagline') }}
                </p>
                <div class="flex gap-4">
                    @if(isset($global_settings['social_facebook']) && $global_settings['social_facebook'] !== '#')
                        <a href="{{ $global_settings['social_facebook'] }}" target="_blank"
                            class="w-8 h-8 rounded-full bg-white/5 hover:bg-primary flex items-center justify-center transition-all hover:-translate-y-1">
                            <span class="sr-only">Facebook</span>
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.791-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                            </svg>
                        </a>
                    @endif

                    @if(isset($global_settings['social_twitter']) && $global_settings['social_twitter'] !== '#')
                        <a href="{{ $global_settings['social_twitter'] }}" target="_blank"
                            class="w-8 h-8 rounded-full bg-white/5 hover:bg-primary flex items-center justify-center transition-all hover:-translate-y-1">
                            <span class="sr-only">Twitter</span>
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z" />
                            </svg>
                        </a>
                    @endif

                    @if(isset($global_settings['social_instagram']) && $global_settings['social_instagram'] !== '#')
                        <a href="{{ $global_settings['social_instagram'] }}" target="_blank"
                            class="w-8 h-8 rounded-full bg-white/5 hover:bg-primary flex items-center justify-center transition-all hover:-translate-y-1">
                            <span class="sr-only">Instagram</span>
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                            </svg>
                        </a>
                    @endif

                    @if(isset($global_settings['social_tiktok']) && $global_settings['social_tiktok'] !== '#')
                        <a href="{{ $global_settings['social_tiktok'] }}" target="_blank"
                            class="w-8 h-8 rounded-full bg-white/5 hover:bg-primary flex items-center justify-center transition-all hover:-translate-y-1">
                            <span class="sr-only">TikTok</span>
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 448 512">
                                <path
                                    d="M448,209.91a210.06,210.06,0,0,1-122.77-39.25V349.38A162.55,162.55,0,1,1,185,188.31V278.2a74.62,74.62,0,1,0,52.23,71.18V0l88,0a121.18,121.18,0,0,0,1.86,22.17h0A122.18,122.18,0,0,0,381,102.39a121.43,121.43,0,0,0,67,20.14Z" />
                            </svg>
                        </a>
                    @endif
                </div>
            </div>

            <!-- Links Section (Right) -->
            <div class="lg:w-6/12 grid grid-cols-2 gap-8 md:gap-12">

                <!-- Company -->
                <div>
                    <h3 class="font-bold text-white mb-6">{{ __('common.company') }}</h3>
                    <ul class="space-y-3 text-sm text-gray-400">
                        <li><a href="{{ route('pages.about') }}"
                                class="hover:text-primary transition-colors">{{ __('common.about') }}</a></li>
                        <li><a href="{{ route('contact.index') }}"
                                class="hover:text-primary transition-colors">{{ __('common.contact') }}</a></li>
                        @guest
                            <li><a href="{{ route('login') }}"
                                    class="hover:text-primary transition-colors">{{ __('common.login') }}</a></li>
                        @endguest
                    </ul>
                </div>

                <!-- Contact - NAP Consistency for Local SEO -->
                <div>
                    <h3 class="font-bold text-white mb-6">{{ __('common.get_in_touch') }}</h3>
                    <ul class="space-y-4 text-sm text-gray-400">
                        <!-- Address - Must match Google Business Profile -->
                        <li class="flex items-start gap-3" itemscope itemtype="https://schema.org/PostalAddress">
                            <svg class="w-5 h-5 text-primary shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <a href="https://www.google.com/maps/search/?api=1&query=Jl.+Raya+Desa+Sidaharja,+Milangga,+Rangkasbitung,+Suradadi,+Tegal"
                                target="_blank" rel="noopener noreferrer"
                                class="hover:text-primary transition-colors leading-relaxed">
                                <span itemprop="streetAddress">Jl. Raya Desa Sidaharja, Milangga,
                                    Rangkasbitung</span><br>
                                <span itemprop="addressLocality">Suradadi</span>,
                                <span itemprop="addressRegion">Jawa Tengah</span>
                                <span itemprop="postalCode">52182</span>
                            </a>
                        </li>
                        <!-- Email -->
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-primary shrink-0" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <a href="mailto:{{ $global_settings['contact_email'] ?? 'hallo@anntix.id' }}"
                                class="hover:text-primary transition-colors" itemprop="email">{{
                                $global_settings['contact_email'] ?? 'hallo@anntix.id' }}</a>
                        </li>

                        <!-- WhatsApp -->
                        <li class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-primary shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z" />
                            </svg>
                            <a href="https://wa.me/6287750581589" target="_blank" rel="noopener noreferrer"
                                class="hover:text-primary transition-colors">WhatsApp: 0877-5058-1589</a>
                        </li>
                    </ul>
                </div>

            </div>

        </div>

        <div class="border-t border-white/5 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-gray-500 text-sm">
                &copy; {{ date('Y') }} Anntix. {{ __('common.all_rights_reserved') }}.
            </p>
            <div class="flex gap-6 text-sm">
                <a href="{{ route('pages.privacy') }}"
                    class="text-gray-500 hover:text-white transition-colors">{{ __('common.privacy_policy') }}</a>
                <a href="{{ route('pages.terms') }}"
                    class="text-gray-500 hover:text-white transition-colors">{{ __('common.terms_conditions') }}</a>
                <a href="{{ route('pages.cookie') }}"
                    class="text-gray-500 hover:text-white transition-colors">{{ __('common.cookie_policy') }}</a>
            </div>
        </div>
    </div>
</footer>