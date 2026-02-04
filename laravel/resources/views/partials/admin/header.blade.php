<header class="bg-white border-b border-gray-100 h-24 flex items-center justify-between px-4 md:px-8 sticky top-0 z-20">
    <!-- Left: Toggle Button -->
    <div class="flex items-center gap-4">
        <button @click="sidebarOpen = !sidebarOpen"
            class="lg:hidden p-2.5 rounded-xl bg-gray-50 text-gray-500 hover:bg-gray-100 hover:text-primary transition-all duration-200 focus:outline-none group">
            <!-- Hamburger Icon (Show when sidebar is closed) -->
            <svg x-show="!sidebarOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
    </div>

    <!-- Right: Actions -->
    <div class="flex items-center gap-4">


        <!-- User Profile Dropdown -->
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" @click.away="open = false" class="flex items-center gap-3 focus:outline-none">
                <div class="text-right hidden md:block">
                    <p class="text-sm font-bold text-gray-700 leading-none">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-400 mt-1 leading-none">{{ auth()->user()->role }}</p>
                </div>
                <div
                    class="w-12 h-12 bg-primary/10 text-primary rounded-full flex items-center justify-center text-sm font-bold ring-2 ring-white shadow-sm">
                    {{ auth()->user()->initials() ?? 'A' }}
                </div>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open" x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="transform opacity-0 scale-95"
                x-transition:enter-end="transform opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="transform opacity-100 scale-100"
                x-transition:leave-end="transform opacity-0 scale-95"
                class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50">

                <div class="px-4 py-3 border-b border-gray-50 md:hidden">
                    <p class="text-sm font-bold text-gray-900">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                </div>

                @if(Auth::user()->role === 'admin' || Auth::user()->role === 'super_admin')
                    <a href="{{ route('admin.profile') }}"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">Profile</a>
                @endif

                @if(Auth::user()->role === 'reseller')
                    <a href="{{ route('reseller.dashboard') }}"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">Dashboard</a>
                    <a href="{{ route('reseller.reports.index') }}"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">My Sales</a>
                    <a href="{{ route('reseller.deposits.index') }}"
                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">Deposit Info</a>
                @endif


                <div class="border-t border-gray-50 my-1"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors font-medium">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</header>