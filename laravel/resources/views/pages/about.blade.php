<x-layouts.app title="About Us">
    <div class="bg-white min-h-screen">
        <!-- Minimalist Hero -->


        <!-- Vision Section -->
        <div class="py-32 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
                    <div>
                        <div class="text-[10px] font-black text-primary uppercase tracking-[0.3em] mb-6">
                            {{ __('common.about_us') }}
                        </div>
                        <h2
                            class="text-4xl md:text-5xl font-heading font-black text-dark tracking-tighter leading-tight mb-8">
                            {{ __('common.about_description') }}
                        </h2>
                        <p class="text-lg text-black/70 leading-relaxed mb-10">
                            {{ __('common.about_story') }}
                        </p>

                        <div class="grid grid-cols-2 gap-10">
                            <div>
                                <div class="text-3xl font-black text-dark tracking-tighter mb-1">2024</div>
                                <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                                    {{ __('common.launched') }}</div>
                            </div>
                            <div>
                                <div class="text-3xl font-black text-dark tracking-tighter mb-1">100%</div>
                                <div class="text-xs font-bold text-gray-400 uppercase tracking-widest">
                                    {{ __('common.bootstrapped') }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="aspect-[4/5] rounded-[3rem] overflow-hidden bg-gray-100 shadow-2xl">
                            <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=2070&auto=format&fit=crop"
                                class="w-full h-full object-cover grayscale hover:grayscale-0 transition-all duration-700">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Values -->
        <div class="py-32 bg-gray-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-20">
                    <h2 class="text-4xl font-heading font-black text-dark tracking-tighter">
                        {{ __('common.our_values') }}</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-12">
                    <!-- Agility -->
                    <div
                        class="p-10 bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/50 border border-gray-100 group hover:-translate-y-2 transition-transform">
                        <div
                            class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center text-primary mb-8 group-hover:bg-primary group-hover:text-white transition-colors">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-black text-dark mb-4">{{ __('common.radical_agility') }}</h3>
                        <p class="text-gray-500 leading-relaxed">{{ __('common.radical_agility_desc') }}</p>
                    </div>

                    <!-- Transparency -->
                    <div
                        class="p-10 bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/50 border border-gray-100 group hover:-translate-y-2 transition-transform">
                        <div
                            class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center text-primary mb-8 group-hover:bg-primary group-hover:text-white transition-colors">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-black text-dark mb-4">{{ __('common.total_transparency') }}</h3>
                        <p class="text-gray-500 leading-relaxed">{{ __('common.total_transparency_desc') }}</p>
                    </div>

                    <!-- Obsession -->
                    <div
                        class="p-10 bg-white rounded-[2.5rem] shadow-xl shadow-gray-200/50 border border-gray-100 group hover:-translate-y-2 transition-transform">
                        <div
                            class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center text-primary mb-8 group-hover:bg-primary group-hover:text-white transition-colors">
                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-black text-dark mb-4">{{ __('common.customer_obsessed') }}</h3>
                        <p class="text-gray-500 leading-relaxed">{{ __('common.customer_obsessed_desc') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Join Us -->
        <div class="py-32 bg-dark">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white">
                <h2 class="text-5xl md:text-6xl font-heading font-black tracking-tighter mb-10">
                    {{ __('common.join_revolution') }}
                </h2>
                <a href="{{ route('events.index') }}"
                    class="inline-block px-12 py-5 bg-primary text-white font-black rounded-2xl shadow-2xl hover:-translate-y-1 transition-all">
                    {{ __('common.view_all') }} {{ __('common.events') }}
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>