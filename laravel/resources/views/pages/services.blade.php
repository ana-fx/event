<x-layouts.app title="Our Services">
    <div class="bg-white min-h-screen">
        <!-- Minimalist Hero -->
        <div class="pt-40 pb-40 relative overflow-hidden bg-dark">
            <!-- Decorative Grain/Noise -->
            <div class="absolute inset-0 opacity-[0.03] pointer-events-none"
                style="background-image: url('https://grainy-gradients.vercel.app/noise.svg');"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="lg:w-2/3">
                    <div class="h-1 w-20 bg-primary mb-12"></div>
                    <h1
                        class="text-6xl md:text-9xl font-heading font-black text-white tracking-tighter leading-none mb-12 animate-in fade-in slide-in-from-bottom-8 duration-1000">
                        {{ __('common.about_anntix') }}
                    </h1>
                    <p
                        class="text-2xl md:text-3xl text-gray-400 font-medium leading-tight max-w-2xl animate-in fade-in slide-in-from-bottom-12 duration-1000 delay-200">
                        {{ __('common.services_intro') }}
                    </p>
                </div>
            </div>

            <!-- Minimalist Large Background Text -->
            <div
                class="absolute bottom-0 right-0 translate-y-1/3 translate-x-1/4 opacity-5 pointer-events-none select-none">
                <span class="text-[30rem] font-black text-white leading-none tracking-tighter">ANTX</span>
            </div>
        </div>

        <!-- Narrative Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-20 relative z-20">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16 items-start">

                <!-- Large Quote/Vision -->
                <div class="lg:col-span-12 mb-32">
                    <div
                        class="bg-white p-12 md:p-24 border border-gray-100 shadow-2xl shadow-gray-200 relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-8">
                            <svg class="w-16 h-16 text-gray-50 group-hover:text-primary/10 transition-colors"
                                fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M14.017 21L14.017 18C14.017 16.8954 14.9124 16 16.017 16H19.017C19.5693 16 20.017 15.5523 20.017 15V9C20.017 8.44772 19.5693 8 19.017 8H16.017C14.9124 8 14.017 7.10457 14.017 6V4L21.017 4V15C21.017 18.3137 18.3307 21 15.017 21H14.017ZM3.01697 21L3.01697 18C3.01697 16.8954 3.9124 16 5.01697 16H8.01697C8.56925 16 9.01697 15.5523 9.01697 15V9C9.01697 8.44772 8.56925 8 8.01697 8H5.01697C3.9124 8 3.01697 7.10457 3.01697 6V4L10.017 4V15C10.017 18.3137 7.33068 21 4.01697 21H3.01697Z" />
                            </svg>
                        </div>
                        <h2 class="text-3xl md:text-5xl font-black text-dark leading-tight max-w-4xl relative z-10">
                            {{ __('common.services_quote') }}
                        </h2>
                    </div>
                </div>

                <!-- Mission & Core Features -->
                <div class="lg:col-span-5 py-8">
                    <h3 class="text-xs font-black text-primary uppercase tracking-[0.3em] mb-12">
                        {{ __('common.the_ecosystem') }}</h3>
                    <div class="space-y-16">
                        <div class="group">
                            <div class="h-px w-0 group-hover:w-12 bg-primary transition-all duration-500 mb-6"></div>
                            <h4 class="text-2xl font-black text-dark mb-4">{{ __('common.smart_distribution') }}</h4>
                            <p class="text-lg text-black/70 leading-relaxed">
                                {{ __('common.smart_distribution_desc') }}
                            </p>
                        </div>
                        <div class="group">
                            <div class="h-px w-0 group-hover:w-12 bg-primary transition-all duration-500 mb-6"></div>
                            <h4 class="text-2xl font-black text-dark mb-4">{{ __('common.live_insights') }}</h4>
                            <p class="text-lg text-black/70 leading-relaxed">
                                {{ __('common.live_insights_desc') }}
                            </p>
                        </div>
                        <div class="group">
                            <div class="h-px w-0 group-hover:w-12 bg-primary transition-all duration-500 mb-6"></div>
                            <h4 class="text-2xl font-black text-dark mb-4">{{ __('common.bank_grade_qr') }}</h4>
                            <p class="text-lg text-black/70 leading-relaxed">
                                {{ __('common.bank_grade_qr_desc') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Side Image/Visual Placeholder -->
                <div
                    class="lg:col-span-7 bg-gray-50 rounded-tl-[100px] aspect-square flex items-center justify-center relative overflow-hidden">
                    <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-secondary/5"></div>
                    <div class="text-center p-12">
                        <div class="text-8xl font-black text-dark/10 tracking-tighter mb-4 italic">EST. 2024</div>
                        <div class="h-1 w-12 bg-dark/20 mx-auto"></div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Footer Call to Action -->
        <div class="py-40 bg-white border-t border-gray-100 text-center">
            <h2 class="text-4xl md:text-6xl font-black text-dark tracking-tighter mb-12">
                {{ __('common.ready_expand_reach') }}
            </h2>
            <a href="{{ route('contact.index') }}" class="inline-flex items-center group">
                <span
                    class="text-2xl md:text-4xl font-bold text-primary group-hover:pr-6 transition-all">{{ __('common.become_partner') }}</span>
                <svg class="w-8 h-8 md:w-12 md:h-12 text-primary opacity-0 group-hover:opacity-100 transition-all -translate-x-12 group-hover:translate-x-0"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
            </a>
        </div>
    </div>
</x-layouts.app>