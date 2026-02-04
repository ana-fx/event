<x-layouts.app title="Cookie Policy">
    <div class="bg-white min-h-screen">
        <!-- Minimalist Hero -->
        <div class="pt-40 pb-20 relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/5 text-primary text-xs font-bold uppercase tracking-widest mb-6 border border-primary/10">
                    {{ __('common.cookie_governance') }}
                </div>
                <h1 class="text-6xl md:text-8xl font-heading font-black text-dark tracking-tighter leading-none mb-8">
                    {{ __('common.cookie_policy_full') }}
                </h1>
                <p class="text-xl text-gray-500 max-w-2xl leading-relaxed">
                    {{ __('common.cookie_intro') }}
                </p>
            </div>

            <!-- Minimalist decorative line -->
            <div class="absolute top-1/2 right-0 w-32 h-px bg-gray-100"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-32">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">
                <!-- Sidebar Nav -->
                <div class="lg:col-span-3 hidden lg:block">
                    <div class="sticky top-32">
                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-8">
                            {{ __('common.overview') }}</div>
                        <nav class="space-y-6">
                            <a href="#definition" class="group flex items-center gap-3">
                                <span class="w-1 h-1 rounded-full bg-primary"></span>
                                <span class="text-sm font-bold text-dark group-hover:px-2 transition-all">1.
                                    {{ __('common.what_are_cookies') }}</span>
                            </a>
                            <a href="#usage" class="group flex items-center gap-3">
                                <span class="w-1 h-1 rounded-full bg-gray-200 group-hover:bg-primary"></span>
                                <span class="text-sm font-medium text-gray-400 group-hover:text-dark transition-all">2.
                                    {{ __('common.usage_strategy') }}</span>
                            </a>
                            <a href="#types" class="group flex items-center gap-3">
                                <span class="w-1 h-1 rounded-full bg-gray-200 group-hover:bg-primary"></span>
                                <span class="text-sm font-medium text-gray-400 group-hover:text-dark transition-all">3.
                                    {{ __('common.cookie_types') }}</span>
                            </a>
                            <a href="#control" class="group flex items-center gap-3">
                                <span class="w-1 h-1 rounded-full bg-gray-200 group-hover:bg-primary"></span>
                                <span class="text-sm font-medium text-gray-400 group-hover:text-dark transition-all">4.
                                    {{ __('common.user_control') }}</span>
                            </a>
                        </nav>
                    </div>
                </div>

                <!-- Main Content Area -->
                <div
                    class="lg:col-span-9 prose prose-2xl prose-primary max-w-none text-black prose-headings:text-dark prose-headings:font-black prose-headings:tracking-tighter">
                    <section id="definition" class="mb-24">
                        <h2 class="text-4xl mb-10">{{ __('common.what_are_cookies') }}</h2>
                        <div class="text-lg leading-relaxed text-black/80">
                            <p>
                                {{ __('common.cookies_definition') }}
                            </p>
                        </div>
                    </section>

                    <section id="usage" class="mb-24">
                        <h2 class="text-4xl mb-10">{{ __('common.how_we_use_cookies') }}</h2>
                        <div class="text-lg leading-relaxed text-black/80 space-y-6">
                            <p>{{ __('common.cookies_usage_desc') }}</p>
                            <p>{{ __('common.cookies_privacy') }}</p>
                        </div>
                    </section>

                    <section id="types" class="mb-24">
                        <h2 class="text-4xl mb-10">{{ __('common.types_of_cookies') }}</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 not-prose mt-8">
                            <div class="p-8 border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                                <h4 class="text-primary font-black text-xs uppercase tracking-widest mb-4">
                                    {{ __('common.functional') }}</h4>
                                <h5 class="text-xl font-bold text-dark mb-4">{{ __('common.essential_operations') }}
                                </h5>
                                <p class="text-gray-500 text-sm leading-relaxed">{{ __('common.functional_desc') }}</p>
                            </div>
                            <div class="p-8 border-t border-gray-100 hover:bg-gray-50/50 transition-colors">
                                <h4 class="text-primary font-black text-xs uppercase tracking-widest mb-4">
                                    {{ __('common.analytical') }}</h4>
                                <h5 class="text-xl font-bold text-dark mb-4">{{ __('common.performance_tracking') }}
                                </h5>
                                <p class="text-gray-500 text-sm leading-relaxed">{{ __('common.analytical_desc') }}</p>
                            </div>
                        </div>
                    </section>

                    <section id="control" class="mb-24">
                        <h2 class="text-4xl mb-10">{{ __('common.controlling_cookies') }}</h2>
                        <div class="text-lg leading-relaxed text-black/80">
                            <p>
                                {{ __('common.control_cookies_desc') }}
                            </p>
                        </div>
                    </section>

                    <div
                        class="pt-20 border-t border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-10">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 rounded-full bg-primary/5 text-primary flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div>
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                    {{ __('common.enquiries') }}</div>
                                <a href="mailto:hallo@anntix.id"
                                    class="text-lg font-bold hover:text-primary transition-colors">hallo@anntix.id</a>
                            </div>
                        </div>
                        <div class="md:text-right">
                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                {{ __('common.last_modified') }}</div>
                            <p class="text-lg font-bold text-dark">{{ date('F d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>