<x-layouts.app title="Privacy Policy">
    <div class="bg-white min-h-screen">
        <!-- Minimalist Hero -->
        <div class="pt-40 pb-20 relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/5 text-primary text-xs font-bold uppercase tracking-widest mb-6">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                    </span>
                    {{ __('common.privacy_commitment') }}
                </div>
                <h1 class="text-6xl md:text-8xl font-heading font-black text-dark tracking-tighter leading-none mb-8">
                    {{ __('common.privacy_policy_full') }}
                </h1>
                <p class="text-xl text-gray-500 max-w-2xl leading-relaxed">
                    {{ __('common.privacy_intro') }}
                </p>
            </div>

            <!-- Minimalist Decorative Element -->
            <div class="absolute top-0 right-0 -z-10 w-1/2 h-full bg-gray-50/50 skew-x-12 transform origin-top-right">
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-32">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-16">
                <!-- Sidebar Nav -->
                <div class="lg:col-span-3 hidden lg:block">
                    <div class="sticky top-32 space-y-4">
                        <div
                            class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6 border-b border-gray-100 pb-2">
                            {{ __('common.contents') }}
                        </div>
                        <a href="#collect"
                            class="block text-sm font-bold text-dark hover:text-primary transition-colors">1.
                            {{ __('common.data_collection') }}</a>
                        <a href="#usage"
                            class="block text-sm font-medium text-gray-400 hover:text-dark transition-colors">2.
                            {{ __('common.purpose_usage') }}</a>
                        <a href="#sharing"
                            class="block text-sm font-medium text-gray-400 hover:text-dark transition-colors">3.
                            {{ __('common.third_parties') }}</a>
                        <a href="#security"
                            class="block text-sm font-medium text-gray-400 hover:text-dark transition-colors">4.
                            {{ __('common.security_measures') }}</a>
                    </div>
                </div>

                <!-- Main Content -->
                <div
                    class="lg:col-span-9 prose prose-2xl prose-primary max-w-none text-black prose-headings:text-dark prose-headings:font-black prose-headings:tracking-tighter">
                    <section id="collect" class="mb-20">
                        <h2 class="text-4xl mb-8">{{ __('common.data_collection') }}</h2>
                        <p class="text-lg leading-relaxed text-black/80">
                            {{ __('common.data_collection_desc') }}
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 not-prose mt-12">
                            <div
                                class="group border-l-2 border-primary/20 hover:border-primary pl-8 py-4 transition-all">
                                <h4 class="font-black text-dark mb-2 text-sm uppercase tracking-widest">
                                    {{ __('common.direct_information') }}</h4>
                                <p class="text-gray-500 text-sm leading-relaxed">
                                    {{ __('common.direct_information_desc') }}</p>
                            </div>
                            <div
                                class="group border-l-2 border-primary/20 hover:border-primary pl-8 py-4 transition-all">
                                <h4 class="font-black text-dark mb-2 text-sm uppercase tracking-widest">
                                    {{ __('common.usage_data') }}</h4>
                                <p class="text-gray-500 text-sm leading-relaxed">{{ __('common.usage_data_desc') }}</p>
                            </div>
                        </div>
                    </section>

                    <section id="usage" class="mb-20">
                        <h2 class="text-4xl mb-8">{{ __('common.purpose_usage') }}</h2>
                        <p class="text-lg leading-relaxed text-black/80">{{ __('common.purpose_usage_desc') }}</p>
                        <ul class="space-y-4 text-lg list-none pl-0">
                            <li class="flex items-start gap-4">
                                <span class="w-6 h-px bg-primary mt-4 flex-shrink-0"></span>
                                {{ __('common.purpose_item_1') }}
                            </li>
                            <li class="flex items-start gap-4">
                                <span class="w-6 h-px bg-primary mt-4 flex-shrink-0"></span>
                                {{ __('common.purpose_item_2') }}
                            </li>
                            <li class="flex items-start gap-4">
                                <span class="w-6 h-px bg-primary mt-4 flex-shrink-0"></span>
                                {{ __('common.purpose_item_3') }}
                            </li>
                        </ul>
                    </section>

                    <section id="security" class="mb-20">
                        <h2 class="text-4xl mb-8">{{ __('common.security_measures') }}</h2>
                        <p class="text-lg leading-relaxed text-black/80">
                            {{ __('common.security_measures_desc') }}
                        </p>
                    </section>

                    <div
                        class="pt-20 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-8">
                        <div>
                            <div class="text-xs text-gray-400 uppercase tracking-widest mb-2 font-bold">
                                {{ __('common.contact_representative') }}</div>
                            <a href="mailto:hallo@anntix.id"
                                class="text-2xl font-black hover:text-primary transition-colors">hallo@anntix.id</a>
                        </div>
                        <div class="text-right">
                            <div class="text-xs text-gray-400 uppercase tracking-widest mb-2 font-bold">Last Update
                            </div>
                            <div class="text-lg font-bold text-dark">{{ date('F d, Y') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>