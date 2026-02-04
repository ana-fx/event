<div class="w-64 bg-gradient-to-b from-primary to-[#108c8d] text-white min-h-screen flex flex-col shadow-xl relative"
    x-data>
    <!-- Modern Creative Toggle Button (Desktop Only) -->
    <button @click="sidebarOpen = false"
        class="absolute left-full top-1/2 transform -translate-y-1/2 focus:outline-none z-50 group hidden lg:block"
        title="Collapse Sidebar">
        <div
            class="flex items-center justify-center w-4 h-12 bg-white text-gray-300 rounded-r-xl shadow-[4px_0_24px_rgba(0,0,0,0.1)] border-y border-r border-gray-100 group-hover:w-8 group-hover:text-primary transition-all duration-300 ease-out">
            <svg class="w-3.5 h-3.5 group-hover:scale-110 transition-transform duration-300" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path>
            </svg>
        </div>
    </button>

    <!-- Branding -->
    <div class="h-24 flex items-center justify-between px-6 md:px-8 border-b border-white/10">
        <div class="flex items-center gap-3">
            @if(isset($global_settings['site_logo_white']))
                <img src="{{ asset('storage/' . $global_settings['site_logo_white']) }}" class="h-8 w-auto">
            @elseif(isset($global_settings['site_logo']))
                <img src="{{ asset('storage/' . $global_settings['site_logo']) }}" class="h-8 w-auto brightness-0 invert">
            @else
                <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center shadow-lg">
                    <div class="w-2.5 h-2.5 bg-primary rounded-full"></div>
                </div>
                <span class="text-lg font-bold tracking-wider uppercase">ANTIX</span>
            @endif
        </div>

        <!-- Mobile Close Button -->
        <button @click="sidebarOpen = false" class="lg:hidden p-2 text-white/70 hover:text-white transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 py-8 px-4 flex flex-col gap-2 overflow-y-auto">
        <p class="px-4 text-[10px] font-black uppercase tracking-[0.2em] text-teal-100/50 mb-2">Reseller Panel</p>

        <a href="{{ route('reseller.dashboard') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-colors {{ request()->routeIs('reseller.dashboard') ? 'bg-white/10 shadow-inner border border-white/10 font-semibold text-white' : 'text-teal-50 hover:bg-white/10 font-medium' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('reseller.dashboard') ? 'opacity-100' : 'opacity-70' }}"
                viewBox="0 0 20 20" fill="currentColor">
                <path
                    d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
            </svg>
            Dashboard
        </a>

        <!-- Reports / Sales -->
        <a href="{{ route('reseller.reports.index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-colors {{ request()->routeIs('reseller.reports.*') ? 'bg-white/10 shadow-inner border border-white/10 font-semibold text-white' : 'text-teal-50 hover:bg-white/10 font-medium' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('reseller.reports.*') ? 'opacity-100' : 'opacity-70' }}"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
            </svg>
            My Sales
        </a>

        <a href="{{ route('reseller.deposits.index') }}"
            class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm transition-colors {{ request()->routeIs('reseller.deposits.*') ? 'bg-white/10 shadow-inner border border-white/10 font-semibold text-white' : 'text-teal-50 hover:bg-white/10 font-medium' }}">
            <svg class="w-5 h-5 {{ request()->routeIs('reseller.deposits.*') ? 'opacity-100' : 'opacity-70' }}"
                fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
            </svg>
            Deposit Info
        </a>

    </nav>

    <!-- User Section / Logout -->
    <div class="p-4 border-t border-white/10">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium text-teal-50 hover:bg-white/10 hover:text-white transition-colors">
                <svg class="w-5 h-5 opacity-70" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z"
                        clip-rule="evenodd" />
                </svg>
                Logout
            </button>
        </form>
    </div>

</div>