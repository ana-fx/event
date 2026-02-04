<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('common.management_login') }} - {{ config('app.name', 'ANTIX') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body
    class="font-sans antialiased text-dark bg-gray-50 min-h-screen flex items-center justify-center p-6 relative overflow-x-hidden">

    <!-- Background Decor -->
    <div
        class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-[600px] h-[600px] bg-primary/5 rounded-full blur-3xl -z-10">
    </div>
    <div
        class="absolute bottom-0 left-0 translate-y-1/2 -translate-x-1/4 w-[600px] h-[600px] bg-indigo-500/5 rounded-full blur-3xl -z-10">
    </div>

    <div
        class="w-full max-w-5xl flex flex-col md:flex-row bg-white rounded-[3rem] shadow-2xl overflow-hidden border border-white/20 min-h-[650px]">

        <!-- Left Side: Information & Brand -->
        <div class="w-full md:w-5/12 bg-dark p-12 text-white flex flex-col justify-between relative overflow-hidden">

            <div class="relative z-10">
                <a href="{{ route('home') }}"
                    class="inline-block mb-16 transform hover:scale-105 transition-transform duration-500">
                    @if(isset($global_settings['site_logo_white']))
                        <img src="{{ asset('storage/' . $global_settings['site_logo_white']) }}"
                            class="h-12 w-auto object-contain">
                    @elseif(isset($global_settings['site_logo']))
                        <img src="{{ asset('storage/' . $global_settings['site_logo']) }}"
                            class="h-12 w-auto object-contain brightness-0 invert">
                    @else
                        <div class="flex items-center gap-3 group">
                            <div
                                class="w-12 h-12 bg-primary rounded-2xl flex items-center justify-center transform group-hover:rotate-12 transition-transform duration-500 shadow-lg shadow-primary/20">
                                <div class="w-3 h-3 bg-white rounded-full"></div>
                            </div>
                            <span class="text-2xl font-black tracking-tighter uppercase whitespace-nowrap">ANTIX<span
                                    class="text-primary italic">.</span></span>
                        </div>
                    @endif
                </a>

                <div class="space-y-6">
                    <h1 class="text-5xl font-black leading-none tracking-tight">{{ __('common.access_console') }}</h1>

                    <div class="space-y-4 pt-8">
                        <div class="flex items-start gap-4">
                            <div class="w-8 h-8 rounded-xl bg-white/10 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-400 leading-relaxed pt-1">
                                {{ __('common.gateway_exclusive') }} <span
                                    class="text-white font-bold">{{ __('common.gateway_roles') }}</span></p>
                        </div>
                        <div class="flex items-start gap-4">
                            <div class="w-8 h-8 rounded-xl bg-primary/20 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-400 leading-relaxed pt-1">Event organizers and staff
                                {{ __('common.management_desc') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative z-10 pt-12">
                <div class="p-6 bg-white/5 border border-white/10 rounded-[2rem] backdrop-blur-md">
                    <p class="text-xs font-black uppercase tracking-[0.2em] text-primary mb-2">
                        {{ __('common.buying_tickets_q') }}</p>
                    <p class="text-xs text-gray-400 leading-relaxed mb-4">{{ __('common.no_account_needed') }}</p>
                    <a href="{{ route('home') }}"
                        class="inline-flex items-center gap-2 text-sm font-black text-white hover:text-primary transition-colors uppercase tracking-widest">
                        {{ __('common.browse_events') }}
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M17 8l4 4m0 0l-4 4m4-4H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="w-full md:w-7/12 bg-white p-12 lg:p-20 flex flex-col justify-center relative">

            <div class="max-w-sm mx-auto w-full">
                <div class="mb-12">
                    <h2 class="text-3xl font-black text-dark tracking-tight uppercase">{{ __('common.sign_in') }}</h2>
                    <p class="text-primary mt-2 font-medium">{{ __('common.enter_credentials') }}</p>
                </div>

                <form method="POST" action="{{ route('login.store') }}" class="space-y-6">
                    @csrf

                    <div class="space-y-2 group">
                        <label for="email"
                            class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 group-focus-within:text-primary transition-colors ml-1">{{ __('common.your_email') }}</label>
                        <div class="relative">
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                class="w-full px-6 py-4 bg-gray-50 border-gray-100 border-2 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all font-bold text-dark placeholder:text-gray-300 placeholder:font-medium"
                                placeholder="name@mail.com">
                        </div>
                        @error('email')
                            <p class="text-[10px] font-bold text-red-500 mt-1 ml-1 uppercase tracking-wider">{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="space-y-2 group" x-data="{ show: false }">
                        <div class="flex items-center justify-between px-1">
                            <label for="password"
                                class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 group-focus-within:text-primary transition-colors">{{ __('common.password') }}</label>
                            <a href="#"
                                class="text-[10px] font-black uppercase tracking-[0.1em] text-gray-400 hover:text-primary transition-colors">{{ __('common.forgot_password') }}</a>
                        </div>
                        <div class="relative">
                            <input id="password" :type="show ? 'text' : 'password'" name="password" required
                                class="w-full px-6 py-4 bg-gray-50 border-gray-100 border-2 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all font-bold text-dark placeholder:text-gray-300 placeholder:font-medium"
                                placeholder="••••••••">
                            <button type="button" @click="show = !show"
                                class="absolute right-6 top-1/2 -translate-y-1/2 text-gray-300 hover:text-primary focus:outline-none transition-colors">
                                <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" x-cloak>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-[10px] font-bold text-red-500 mt-1 ml-1 uppercase tracking-wider">{{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="flex items-center pt-2">
                        <label class="flex items-center gap-3 cursor-pointer group">
                            <div class="relative">
                                <input id="remember_me" type="checkbox" name="remember" class="sr-only peer">
                                <div
                                    class="w-10 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary transition-colors">
                                </div>
                            </div>
                            <span
                                class="text-xs font-black uppercase tracking-widest text-gray-500 group-hover:text-dark transition-colors">{{ __('common.stay_signed_in') }}</span>
                        </label>
                    </div>

                    <div class="pt-6">
                        <button type="submit"
                            class="w-full bg-dark text-white font-black py-5 px-4 rounded-2xl shadow-[0_20px_40px_-15px_rgba(0,0,0,0.3)] hover:bg-primary hover:shadow-primary/30 transition-all active:scale-95 text-xs uppercase tracking-[0.3em]">
                            {{ __('common.authorize_access') }}
                        </button>
                    </div>
                </form>

                <div class="mt-12 text-center">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-loose">
                        &copy; {{ date('Y') }} ANTIX {{ __('common.security_platform') }}.<br>
                        {{ __('common.restricted_access') }}.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>

</html>