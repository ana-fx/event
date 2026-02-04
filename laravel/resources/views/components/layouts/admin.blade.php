<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @if(isset($title))
            {{ ($global_settings['site_name'] ?? 'ANTIX') . ' | ' . $title }}
        @else
            {{ ($global_settings['site_name'] ?? 'ANTIX') . ' | Admin' }}
        @endif
    </title>
    @if(isset($global_settings['site_icon']))
        <link rel="icon" type="image/x-icon" href="{{ asset('storage/' . $global_settings['site_icon']) }}">
    @endif

    <!-- Scripts -->
    <!-- Flatpickr -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-gray-900 bg-gray-50 flex min-h-screen overflow-hidden" x-data="{ 
        sidebarOpen: window.innerWidth >= 1024,
        init() {
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) {
                    this.sidebarOpen = true;
                }
            });
        }
    }">

    <!-- Backdrop (Mobile Only) -->
    <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" @click="sidebarOpen = false"
        class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm z-40 lg:hidden" style="display: none;">
    </div>

    <!-- Sidebar Container -->
    <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed inset-y-0 left-0 z-50 lg:relative lg:z-30 lg:translate-x-0 flex-shrink-0" style="display: none;">
        @include('partials.admin.sidebar')
    </div>

    <!-- Main Content Wrapper -->
    <div class="flex-1 flex flex-col min-w-0 overflow-y-auto h-screen">

        <!-- Header -->
        @include('partials.admin.header')

        <!-- Page Content -->
        <main class="flex-1 p-8">
            {{ $slot }}
        </main>

        <!-- Footer -->
        @include('partials.admin.footer')
    </div>

    <!-- Global Notification System -->
    <div x-data="{
            show: {{ session('success') || session('error') || session('info') || session('warning') ? 'true' : 'false' }},
            type: '{{ session('success') ? 'success' : (session('error') ? 'error' : (session('warning') ? 'warning' : 'info')) }}',
            message: '{{ session('success') ?? (session('error') ?? (session('info') ?? session('warning'))) }}',
            init() {
                if (this.show) {
                    setTimeout(() => { this.show = false; }, 4000);
                }
            }
        }" x-show="show" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-2 scale-95" class="fixed bottom-6 right-6 z-[100] max-w-sm w-full"
        style="display: none;">

        <div :class="{
            'bg-emerald-50 border-emerald-100 shadow-emerald-500/10': type === 'success',
            'bg-rose-50 border-rose-100 shadow-rose-500/10': type === 'error',
            'bg-amber-50 border-amber-100 shadow-amber-500/10': type === 'warning',
            'bg-blue-50 border-blue-100 shadow-blue-500/10': type === 'info'
        }" class="flex items-start gap-4 p-4 rounded-2xl border shadow-xl relative overflow-hidden backdrop-blur-xl">

            <!-- Icon -->
            <div :class="{
                'bg-emerald-500 shadow-emerald-200': type === 'success',
                'bg-rose-500 shadow-rose-200': type === 'error',
                'bg-amber-500 shadow-amber-200': type === 'warning',
                'bg-blue-500 shadow-blue-200': type === 'info'
            }" class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center text-white shadow-lg">
                <!-- Success -->
                <svg x-show="type === 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                </svg>
                <!-- Error -->
                <svg x-show="type === 'error'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
                <!-- Warning -->
                <svg x-show="type === 'warning'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                    </path>
                </svg>
                <!-- Info -->
                <svg x-show="type === 'info'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>

            <!-- Content -->
            <div class="flex-1 pt-0.5">
                <h4 :class="{
                    'text-emerald-900': type === 'success',
                    'text-rose-900': type === 'error',
                    'text-amber-900': type === 'warning',
                    'text-blue-900': type === 'info'
                }" class="text-sm font-black uppercase tracking-wider mb-0.5" x-text="type"></h4>
                <p :class="{
                    'text-emerald-700': type === 'success',
                    'text-rose-700': type === 'error',
                    'text-amber-700': type === 'warning',
                    'text-blue-700': type === 'info'
                }" class="text-xs font-bold leading-relaxed" x-text="message"></p>
            </div>

            <!-- Close Button -->
            <button @click="show = false" :class="{
                'hover:bg-emerald-100 text-emerald-600': type === 'success',
                'hover:bg-rose-100 text-rose-600': type === 'error',
                'hover:bg-amber-100 text-amber-600': type === 'warning',
                'hover:bg-blue-100 text-blue-600': type === 'info'
            }" class="p-1 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>
    </div>

    @stack('scripts')
</body>

</html>