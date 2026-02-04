<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
        content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ ($global_settings['site_name'] ?? 'ANNTIX') }} | Ticket Scanner</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Modern Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        [x-cloak] {
            display: none !important;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #0f172a;
            -webkit-tap-highlight-color: transparent;
        }

        /* Mobile safe area */
        .pb-safe {
            padding-bottom: env(safe-area-inset-bottom);
        }

        .pt-safe {
            padding-top: env(safe-area-inset-top);
        }
    </style>
</head>

<body class="h-full overflow-hidden antialiased selection:bg-primary selection:text-white" x-data="{ 
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

    <!-- Main Flex Container -->
    <div class="flex h-full">
        <!-- Sidebar Container -->
        <div x-show="sidebarOpen" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="fixed inset-y-0 left-0 z-50 lg:relative lg:z-30 lg:translate-x-0 flex-shrink-0"
            style="display: none;">
            @include('partials.scanner.sidebar')
        </div>

        <!-- Main Content Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto h-screen">
            {{ $slot }}
        </div>
    </div>

    @stack('scripts')
</body>

</html>