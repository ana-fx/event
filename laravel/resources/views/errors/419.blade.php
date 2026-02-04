<x-layouts.app :title="__('common.error_419')">
    <div
        class="relative min-h-screen flex items-center justify-center overflow-hidden bg-white selection:bg-primary selection:text-white -mt-16">

        <!-- Geometric background patterns (SVG) -->
        <div class="absolute inset-0 w-full h-full pointer-events-none opacity-[0.03]">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M0 100 L100 0 L100 100 Z" fill="currentColor" class="text-primary"></path>
            </svg>
        </div>

        <!-- Decorative Elements -->
        <div
            class="absolute top-[-10%] right-[-10%] w-[40vw] h-[40vw] rounded-full border border-primary/20 opacity-40 blur-[1px]">
        </div>
        <div class="absolute bottom-[-10%] left-[-10%] w-[35vw] h-[35vw] bg-primary/5 rounded-full blur-3xl"></div>

        <!-- Main Content -->
        <div class="relative z-10 w-full max-w-6xl px-6 flex flex-col md:flex-row items-center justify-between gap-16">

            <!-- Left: Text Context -->
            <div class="flex-1 text-center md:text-left space-y-8 order-2 md:order-1">

                <h2 class="text-5xl md:text-7xl font-heading font-black leading-tight tracking-tight text-dark">
                    419 <br>
                    <span class="text-primary">{{ __('common.error_419') }}</span>
                </h2>

                <p class="text-lg text-black max-w-md mx-auto md:mx-0 leading-relaxed opacity-90">
                    {{ __('common.error_419_message') }}
                </p>

                <div class="pt-4 flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                    <a href="{{ url()->previous() }}"
                        class="px-8 py-4 bg-primary text-white font-bold rounded-xl shadow-xl shadow-primary/30 hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-2 group">
                        <span>{{ __('common.refresh_page') }}</span>
                        <svg class="w-5 h-5 group-hover:rotate-180 transition-transform duration-500" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                            </path>
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Right: Massive Number -->
            <div class="flex-1 relative flex items-center justify-center order-1 md:order-2">
                <div class="absolute inset-0 bg-primary/10 blur-[80px] rounded-full transform rotate-12"></div>

                <h1
                    class="relative text-[12rem] md:text-[18rem] leading-none font-black text-transparent select-none z-10 pr-20 overflow-visible">
                    <span
                        class="relative z-20 bg-clip-text text-transparent bg-gradient-to-br from-dark to-primary opacity-90">419</span>
                </h1>
            </div>

        </div>
    </div>
</x-layouts.app>