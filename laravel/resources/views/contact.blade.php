<x-layouts.app title="Contact">
    <!-- Header -->


    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-32 pb-16">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-16">

            <!-- Contact Info -->
            <div class="space-y-12">
                <div>
                    <h2 class="text-3xl font-heading font-bold text-dark mb-6">{{ __('common.contact_info') }}</h2>
                    <p class="text-black/70 leading-relaxed">
                        {{ __('common.get_in_touch_description') }}
                    </p>
                </div>

                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0 text-primary">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-dark text-lg">{{ __('common.office_address') }}</h3>
                            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($global_settings['contact_location'] ?? 'Tegal, Jawa Tengah') }}"
                                target="_blank" class="text-black/70 mt-1 hover:text-primary transition-colors">
                                {!! nl2br(e($global_settings['contact_location'] ?? "Tegal, Jawa Tengah")) !!}
                            </a>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0 text-primary">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-dark text-lg">{{ __('common.email_address') }}</h3>
                            <p class="text-black/70 mt-1">{{ $global_settings['contact_email'] ?? 'hallo@anntix.com'
                                }}</p>
                            @if(isset($settings['partnership_email']))
                                <p class="text-black/70">Partnership: {{ $settings['partnership_email'] }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center flex-shrink-0 text-primary">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-dark text-lg">{{ __('common.phone_number') }}</h3>
                            <p class="text-black/70 mt-1">
                                {{ $global_settings['contact_whatsapp'] ?? '+62 856-0045-7192' }}
                            </p>
                            <p class="text-black/70 text-sm">({{ __('common.monday_friday') }}, 9am - 5pm WIB)</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-xl shadow-gray-200/50">
                <form action="{{ route('contact.store') }}" method="POST" class="space-y-6">
                    @csrf

                    @if(session('success'))
                        <div class="p-4 bg-green-50 text-green-700 rounded-xl mb-6">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label for="name"
                                class="text-sm font-bold text-dark uppercase tracking-wide">{{ __('common.your_name') }}</label>
                            <input type="text" name="name" id="name" required
                                class="w-full px-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                        </div>
                        <div class="space-y-2">
                            <label for="email"
                                class="text-sm font-bold text-dark uppercase tracking-wide">{{ __('common.your_email') }}</label>
                            <input type="email" name="email" id="email" required
                                class="w-full px-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="subject"
                            class="text-sm font-bold text-dark uppercase tracking-wide">{{ __('common.subject') }}</label>
                        <input type="text" name="subject" id="subject" required
                            class="w-full px-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                    </div>

                    <div class="space-y-2">
                        <label for="message"
                            class="text-sm font-bold text-dark uppercase tracking-wide">{{ __('common.your_message') }}</label>
                        <textarea name="message" id="message" rows="5" required
                            class="w-full px-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"></textarea>
                    </div>

                    <div class="space-y-2">
                        <div class="cf-turnstile" data-sitekey="{{ config('services.turnstile.key') }}"></div>
                        @error('cf-turnstile-response')
                            <p class="text-sm text-red-600 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    @push('scripts')
                        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
                    @endpush

                    <button type="submit"
                        class="w-full py-4 bg-primary text-white font-bold rounded-xl hover:bg-primary/90 transition-all shadow-lg shadow-primary/30 hover:-translate-y-1">
                        {{ __('common.send_message') }}
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>