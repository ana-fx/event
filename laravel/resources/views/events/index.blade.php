<x-layouts.app :title="__('common.events')">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 pt-32">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

            <!-- Sidebar Filters -->
            <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm sticky top-32">
                    <h3 class="font-heading font-bold text-xl text-dark mb-6">{{ __('common.filters') }}</h3>

                    <form action="{{ route('events.index') }}" method="GET" class="space-y-6">

                        <!-- Search -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-widest">{{ __('common.search') }}</label>
                            <div class="relative">
                                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                <input type="text" name="search" value="{{ request('search') }}"
                                    placeholder="{{ __('common.keyword') }}"
                                    class="w-full pl-12 pr-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all text-sm font-medium">
                            </div>
                        </div>

                        <!-- Category -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-widest">{{ __('common.category') }}</label>
                            <select name="category" onchange="this.form.submit()"
                                class="w-full px-4 py-3 bg-gray-50 border-gray-200 rounded-xl text-sm font-medium text-dark focus:border-primary focus:ring-primary/20 cursor-pointer transition-colors appearance-none">
                                <option value="">{{ __('common.all_categories') }}</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- City -->
                        <div class="space-y-2">
                            <label class="text-xs font-bold text-gray-500 uppercase tracking-widest">{{ __('common.location') }}</label>
                            <select name="city" onchange="this.form.submit()"
                                class="w-full px-4 py-3 bg-gray-50 border-gray-200 rounded-xl text-sm font-medium text-dark focus:border-primary focus:ring-primary/20 cursor-pointer transition-colors appearance-none">
                                <option value="">{{ __('common.all_cities') }}</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @if(request()->anyFilled(['search', 'category', 'city']))
                            <div class="pt-4 border-t border-gray-100">
                                <a href="{{ route('events.index') }}"
                                    class="w-full py-3 bg-red-50 text-red-500 hover:bg-red-100 rounded-xl text-sm font-bold transition-colors flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    {{ __('common.clear_filters') }}
                                </a>
                            </div>
                        @endif

                    </form>
                </div>
            </div>

            <!-- Events Grid -->
            <div class="lg:col-span-3">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($events as $event)
                        <a href="{{ route('events.show', $event) }}"
                            class="group block bg-white rounded-2xl overflow-hidden border border-gray-100 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                            <div class="relative aspect-video overflow-hidden bg-gray-100">
                                @php
                                    $bannerUrl = $event->banner_path
                                        ? (Str::startsWith($event->banner_path, ['http', 'https'])
                                            ? $event->banner_path
                                            : asset('storage/' . $event->banner_path))
                                        : null;
                                @endphp

                                @if($bannerUrl)
                                    <img src="{{ $bannerUrl }}"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'"
                                        alt="{{ $event->name }}"
                                        class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                @endif

                                <div
                                    class="{{ $bannerUrl ? 'hidden' : 'flex' }} absolute inset-0 items-center justify-center text-gray-300 bg-gray-100">
                                    <svg class="w-12 h-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </div>

                                <div
                                    class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-dark shadow-sm z-10">
                                    {{ $event->category ?? __('common.general') }}
                                </div>
                            </div>
                            <div class="p-5">
                                <p class="text-primary text-[10px] font-bold uppercase tracking-wider mb-2">
                                    {{ $event->start_date->format('M d, Y • H:i') }}
                                </p>
                                <h3
                                    class="text-lg font-heading font-bold text-dark group-hover:text-primary transition-colors mb-2 line-clamp-2">
                                    {{ $event->name }}
                                </h3>
                                <p class="text-secondary text-xs flex items-center gap-1.5 mb-4">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    {{ Str::limit($event->location, 30) }}
                                </p>
                                <div class="flex items-center justify-between pt-3 border-t border-gray-50">
                                    <span class="text-[10px] text-gray-500 font-bold uppercase tracking-wide">{{ __('common.from') }}</span>
                                    <span class="text-base font-black text-dark">
                                        @php
                                            $lowestPriceTicket = $event->tickets->sortBy('price')->first();
                                        @endphp
                                        @if($lowestPriceTicket)
                                            Rp {{ number_format($lowestPriceTicket->price, 0, ',', '.') }}
                                        @else
                                            {{ __('common.free') }}
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div
                            class="col-span-full py-20 text-center bg-gray-50 rounded-3xl border border-dashed border-gray-200">
                            <div
                                class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm">
                                <svg class="w-8 h-8 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-dark">{{ __('common.no_events_found') }}</h3>
                            <p class="text-gray-400 text-sm mt-1">{{ __('common.try_adjust_filters') }}</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-12">
                    {{ $events->links() }}
                </div>
            </div>

        </div>
    </div>
</x-layouts.app>