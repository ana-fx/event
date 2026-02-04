<nav class="fixed w-full z-[100] transition-all duration-500 bg-white/80 backdrop-blur-2xl border-b border-gray-100/50"
    x-data="{
        mobileMenuOpen: false,
        scrolled: false,
        activeLink: '{{ request()->url() }}'
     }" @scroll.window="scrolled = (window.pageYOffset > 50)"
    :class="{ 'py-2 shadow-[0_10px_40px_-15px_rgba(0,0,0,0.05)]': scrolled, 'py-0': !scrolled }">

    <div class="max-w-7xl mx-auto px-6 lg:px-10">
        <div class="flex justify-between items-center">

            <!-- Logo area -->
            <div class="flex-shrink-0">
                <a href="{{ route('home') }}" class="group flex items-center gap-3">
                    <div
                        class="relative flex items-center justify-center transition-transform duration-500 group-hover:scale-105">
                        @if(isset($global_settings['site_logo']))
                            <img src="{{ asset('storage/' . $global_settings['site_logo']) }}"
                                class="w-auto object-contain transition-all duration-500"
                                :class="scrolled ? 'h-[40px]' : 'h-[64px]'">
                        @else
                            <div class="bg-dark rounded-xl flex items-center justify-center text-white transition-all duration-500"
                                :class="scrolled ? 'w-[50px] h-[50px]' : 'w-16 h-16'">
                                <svg class="transition-all duration-500" :class="scrolled ? 'w-6 h-6' : 'w-8 h-8'"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                </a>
            </div>

            <!-- Desktop Navigation: Clean Editorial Style -->
            <div class="hidden md:flex items-center gap-12" data-nosnippet>
                <div class="flex items-center gap-10">
                    @php
                        $navLinks = [
                            ['name' => 'Home', 'route' => 'home'],
                            ['name' => 'Events', 'route' => 'events.index'],
                            ['name' => 'About Us', 'route' => 'pages.about'],
                            ['name' => 'Contact', 'route' => 'contact.index']
                        ];
                    @endphp

                    @foreach($navLinks as $link)
                        <a href="{{ route($link['route']) }}"
                            class="relative py-2 text-[11px] font-black uppercase tracking-[0.3em] transition-all duration-500 group text-dark/60 hover:text-dark"
                            :class="{ 'text-primary': '{{ route($link['route']) }}' === activeLink }">
                            {{ $link['name'] }}
                            <!-- Editorial Underline -->
                            <span
                                class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary transition-all duration-500 group-hover:w-full"
                                :class="{ 'w-full': '{{ route($link['route']) }}' === activeLink }"></span>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Right: Language Switcher + Auth -->
            <div class="hidden md:flex items-center gap-4" data-nosnippet>
                <!-- Language Switcher -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" @click.away="open = false"
                        class="flex items-center gap-2 px-4 py-2 rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors group">
                        @php
                            $currentLocale = app()->getLocale();
                            $locales = [
                                'id' => ['name' => 'ID', 'flag' => '🇮🇩', 'full' => 'Bahasa Indonesia'],
                                'en' => ['name' => 'EN', 'flag' => '🇬🇧', 'full' => 'English']
                            ];
                        @endphp
                        <span class="text-xl">{{ $locales[$currentLocale]['flag'] }}</span>
                        <span class="text-xs font-bold text-dark">{{ $locales[$currentLocale]['name'] }}</span>
                        <svg class="w-4 h-4 text-gray-400 transition-transform group-hover:text-dark"
                            :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <!-- Language Dropdown -->
                    <div x-show="open" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl py-2 border border-gray-100 z-50">
                        @foreach($locales as $code => $locale)
                            <a href="{{ route('language.switch', $code) }}"
                                class="flex items-center gap-3 px-4 py-2.5 text-sm font-medium hover:bg-gray-50 transition-colors {{ $currentLocale === $code ? 'text-primary bg-primary/5' : 'text-dark' }}">
                                <span class="text-2xl">{{ $locale['flag'] }}</span>
                                <span class="flex-1">{{ $locale['full'] }}</span>
                                @if($currentLocale === $code)
                                    <svg class="w-4 h-4 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>

                @auth
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false"
                            class="flex items-center gap-3 py-1 group focus:outline-none">
                            <div
                                class="w-10 h-10 rounded-2xl bg-primary/10 flex items-center justify-center border-2 border-transparent group-hover:border-primary/30 transition-all duration-500">
                                <span class="font-black text-xs text-primary">{{ Auth::user()->initials() }}</span>
                            </div>
                            <div class="text-left">
                                <p class="text-[9px] font-black uppercase tracking-widest text-gray-400">
                                    {{ __('common.account') }}</p>
                                <p class="text-xs font-black text-dark">{{ Str::limit(Auth::user()->name, 12) }}</p>
                            </div>
                        </button>

                        <!-- Premium Dropdown -->
                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-4"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="absolute right-0 mt-4 w-56 bg-white rounded-[2rem] shadow-2xl py-6 border border-gray-100 z-50 overflow-hidden">

                            <div class="px-8 mb-4">
                                <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">
                                    {{ __('common.signed_in_as') }}</p>
                                <p class="text-xs font-black text-dark truncate">{{ Auth::user()->email }}</p>
                            </div>

                            <div class="space-y-1">
                                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'super_admin')
                                    <a href="{{ route('admin.dashboard') }}"
                                        class="flex items-center gap-3 px-8 py-3 text-xs font-bold text-dark hover:bg-gray-50 hover:text-primary transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                        </svg>
                                        {{ __('common.admin_panel') }}
                                    </a>
                                @endif

                                @if(Auth::user()->role === 'reseller')
                                    <a href="{{ route('reseller.dashboard') }}"
                                        class="flex items-center gap-3 px-8 py-3 text-xs font-bold text-dark hover:bg-gray-50 hover:text-primary transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                        </svg>
                                        {{ __('common.reseller_panel') }}
                                    </a>
                                @endif

                                @if(Auth::user()->role === 'scanner')
                                    <a href="{{ route('scanner.index') }}"
                                        class="flex items-center gap-3 px-8 py-3 text-xs font-bold text-dark hover:bg-gray-50 hover:text-primary transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                        </svg>
                                        {{ __('common.scan_tickets') }}
                                    </a>
                                @endif

                                <div class="h-px bg-gray-50 mx-8 my-2"></div>

                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit"
                                        class="flex items-center gap-3 w-full px-8 py-3 text-xs font-bold text-red-500 hover:bg-red-50 transition-colors">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        {{ __('common.sign_out') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}"
                        class="px-8 py-3 bg-dark text-white text-[11px] font-black uppercase tracking-[0.2em] rounded-2xl hover:bg-primary transition-all duration-500 shadow-xl hover:-translate-y-1">
                        Login
                    </a>
                @endauth
            </div>

            <!-- Mobile: Minimalist Trigger -->
            <div class="md:hidden flex items-center">
                <button @click="mobileMenuOpen = !mobileMenuOpen"
                    class="p-3 bg-gray-50 rounded-2xl text-dark hover:text-primary transition-all duration-300">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M4 8h16M4 16h16" />
                        <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Overlay -->
    <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
        class="md:hidden absolute top-24 inset-x-6 z-50" data-nosnippet>

        <div class="bg-white rounded-[3rem] shadow-2xl border border-gray-100 overflow-hidden p-8">
            <div class="space-y-6">
                @foreach($navLinks as $link)
                    <a href="{{ route($link['route']) }}"
                        class="block text-3xl font-black text-dark tracking-tighter hover:text-primary transition-colors">
                        {{ $link['name'] }}<span class="text-primary text-sm">.</span>
                    </a>
                @endforeach

                <div class="h-px bg-gray-100 my-8"></div>

                @auth
                    <div class="flex items-center gap-4 mb-8">
                        <div
                            class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary font-black">
                            {{ Auth::user()->initials() }}
                        </div>
                        <div>
                            <p class="text-lg font-black text-dark">{{ Auth::user()->name }}</p>
                            <p class="text-xs font-medium text-gray-400">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                @endauth

                @guest
                    <a href="{{ route('login') }}"
                        class="block w-full py-5 bg-dark text-white text-center font-black rounded-[1.5rem] tracking-widest text-sm uppercase transition-all hover:bg-primary">
                        {{ __('common.login') }}
                    </a>
                @else
                    <a href="{{ route('contact.index') }}"
                        class="block w-full py-5 bg-gray-50 text-dark text-center font-black rounded-[1.5rem] tracking-widest text-sm uppercase">
                        {{ __('common.contact') }}
                    </a>
                @endguest

                <!-- Language Switcher for Mobile -->
                <div class="grid grid-cols-2 gap-3 mt-6">
                    @php
                        $currentLocale = app()->getLocale();
                        $locales = [
                            'id' => ['name' => 'ID', 'flag' => '🇮🇩', 'full' => 'Bahasa Indonesia'],
                            'en' => ['name' => 'EN', 'flag' => '🇬🇧', 'full' => 'English']
                        ];
                    @endphp
                    @foreach($locales as $code => $locale)
                        <a href="{{ route('language.switch', $code) }}"
                            class="flex items-center justify-center gap-2 py-3 rounded-xl transition-colors {{ $currentLocale === $code ? 'bg-primary text-white' : 'bg-gray-100 text-dark hover:bg-gray-200' }}">
                            <span class="text-xl">{{ $locale['flag'] }}</span>
                            <span class="text-sm font-bold">{{ $locale['name'] }}</span>
                        </a>
                    @endforeach
                </div>

                @auth
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full mt-4 py-4 text-center font-bold text-red-500">
                            {{ __('common.sign_out') }}
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </div>
</nav>
