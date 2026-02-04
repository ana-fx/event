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
            {{ ($global_settings['site_name'] ?? 'ANTIX') . ' | Reseller' }}
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
        @include('partials.reseller.sidebar')
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

    @stack('scripts')
</body>

</html>