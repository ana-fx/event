<x-layouts.app title="Terms of Service">
    <div class="bg-white min-h-screen">
        <!-- Minimalist Hero -->
        <div class="pt-40 pb-20 relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/5 text-primary text-xs font-bold uppercase tracking-widest mb-6">
                    {{ __('common.legal_framework') }}
                </div>
                <h1 class="text-6xl md:text-8xl font-heading font-black text-dark tracking-tighter leading-none mb-8">
                    {{ __('common.terms_of_service') }}
                </h1>
                <p class="text-xl text-gray-500 max-w-2xl leading-relaxed">
                    {{ __('common.terms_intro') }}
                </p>
            </div>

            <!-- Abstract background shape -->
            <div class="absolute bottom-0 right-0 -z-10 w-1/3 h-1/2 bg-gray-50 rounded-tl-[200px]"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-32">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">
                <!-- Navigation -->
                <div class="lg:col-span-3 hidden lg:block">
                    <div class="sticky top-32">
                        <div class="text-[10px] font-black text-primary uppercase tracking-[0.2em] mb-8">
                            {{ __('common.legal_directory') }}</div>
                        <nav class="space-y-6">
                            <a href="#intro" class="group flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full bg-primary"></span>
                                <span
                                    class="text-sm font-bold text-dark group-hover:translate-x-1 transition-transform">1.
                                    {{ __('common.introduction') }}</span>
                            </a>
                            <a href="#services" class="group flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full bg-gray-200 group-hover:bg-primary"></span>
                                <span class="text-sm font-medium text-gray-400 group-hover:text-dark transition-all">2.
                                    {{ __('common.our_services') }}</span>
                            </a>
                            <a href="#payments" class="group flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full bg-gray-200 group-hover:bg-primary"></span>
                                <span class="text-sm font-medium text-gray-400 group-hover:text-dark transition-all">3.
                                    {{ __('common.payments') }}</span>
                            </a>
                            <a href="#conduct" class="group flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full bg-gray-200 group-hover:bg-primary"></span>
                                <span class="text-sm font-medium text-gray-400 group-hover:text-dark transition-all">4.
                                    {{ __('common.conduct') }}</span>
                            </a>
                        </nav>
                    </div>
                </div>

                <!-- Main content area -->
                <div
                    class="lg:col-span-9 prose prose-2xl prose-primary max-w-none text-black prose-headings:text-dark prose-headings:font-black prose-headings:tracking-tighter">
                    <section id="intro" class="mb-24 py-4">
                        <h2 class="text-4xl mb-10">{{ __('common.introduction') }}</h2>
                        <div class="text-lg leading-relaxed space-y-6 text-black/80">
                            <p>
                                {{ __('common.terms_welcome') }}
                            </p>
                            <p>
                                {{ __('common.terms_apply') }}
                            </p>
                        </div>
                    </section>

                    <section id="services" class="mb-24">
                        <h2 class="text-4xl mb-10">{{ __('common.our_services') }}</h2>
                        <div class="text-lg leading-relaxed text-black/80">
                            <p>{{ __('common.services_desc') }}</p>
                        </div>
                        <div class="not-prose mt-12 mb-16 px-10 py-10 border-y border-gray-100 relative">
                            <div class="absolute top-0 left-0 w-1 h-20 bg-primary"></div>
                            <h4 class="text-dark font-black uppercase tracking-widest text-sm mb-4">
                                {{ __('common.anntix_charter') }}</h4>
                            <p class="text-black/70 text-lg leading-relaxed max-w-2xl font-medium">
                                {{ __('common.charter_desc') }}
                            </p>
                        </div>
                    </section>

                    <section id="payments" class="mb-24">
                        <h2 class="text-4xl mb-10">{{ __('common.payments_refunds') }}</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 not-prose">
                            <div class="space-y-4">
                                <div class="text-4xl font-black text-primary/30 tracking-tighter">01</div>
                                <h4 class="font-bold text-xl text-dark">{{ __('common.clear_pricing') }}</h4>
                                <p class="text-gray-500 text-base">{{ __('common.clear_pricing_desc') }}</p>
                            </div>
                            <div class="space-y-4">
                                <div class="text-4xl font-black text-primary/30 tracking-tighter">02</div>
                                <h4 class="font-bold text-xl text-dark">{{ __('common.strict_refunds') }}</h4>
                                <p class="text-gray-500 text-base">{{ __('common.strict_refunds_desc') }}</p>
                            </div>
                        </div>
                    </section>

                    <div
                        class="pt-20 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-10">
                        <div class="flex items-center gap-6">
                            <div class="w-12 h-12 rounded-full bg-dark flex items-center justify-center text-white">
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    {{ __('common.legal_desk') }}</div>
                                <a href="mailto:hallo@anntix.id"
                                    class="text-xl font-bold hover:text-primary transition-colors">hallo@anntix.id</a>
                            </div>
                        </div>
                        <p class="text-gray-400 text-sm font-medium tracking-tight whitespace-nowrap">
                            {{ __('common.regulatory_affairs') }} &copy; {{ date('Y') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>