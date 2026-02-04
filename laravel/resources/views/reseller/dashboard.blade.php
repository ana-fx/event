<x-layouts.reseller title="Dashboard">
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-black text-dark uppercase tracking-tight">Reseller Dashboard</h1>
                <p class="text-secondary mt-1">Welcome back, <span
                        class="text-primary font-bold">{{ Auth::user()->name }}</span>. Here's your performance
                    overview.</p>
            </div>
            <div></div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Sales -->
            <div
                class="bg-gradient-to-br from-primary to-[#108c8d] rounded-2xl p-5 text-white shadow-xl shadow-primary/20 relative overflow-hidden group">
                <p class="text-teal-100 font-bold uppercase tracking-wider text-[10px] mb-1">Total Sales</p>
                <h3 class="text-2xl font-black tracking-tight">Rp {{ number_format($stats['total_sales']) }}</h3>
                <div class="mt-4 flex items-center gap-2">
                    <span class="px-2 py-1 bg-white/10 text-white text-[10px] font-bold rounded-lg">+0%</span>
                </div>
            </div>

            <!-- Commission Earned -->
            <div
                class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group">
                <p class="text-gray-400 font-bold uppercase tracking-wider text-[10px] mb-1">Total Deposit</p>
                <h3 class="text-2xl font-black text-primary tracking-tight">Rp {{ number_format($stats['total_commission']) }}</h3>
                <div class="mt-4 flex items-center gap-2">
                    <span class="px-2 py-1 bg-primary/10 text-primary text-[10px] font-bold rounded-lg uppercase">Deposit</span>
                </div>
            </div>



            <!-- Deposit Balance -->
            <div
                class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group">
                <p class="text-gray-400 font-bold uppercase tracking-wider text-[10px] mb-1">Deposit Balance</p>
                <h3 class="text-2xl font-black text-primary tracking-tight">Rp {{ number_format($stats['current_balance']) }}</h3>
                <div class="mt-4 flex items-center gap-2">
                    <span class="px-2 py-1 bg-primary/10 text-primary text-[10px] font-bold rounded-lg uppercase">Debt</span>
                </div>
            </div>

            <!-- Tickets Sold -->
            <!-- Tickets Sold By Name -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group">
                <p class="text-gray-400 font-bold uppercase tracking-wider text-[10px] mb-3">Ticket Sold By Name</p>
                
                @if(isset($stats['tickets_by_name']) && $stats['tickets_by_name']->count() > 0)
                    <div class="space-y-3 max-h-[100px] overflow-y-auto pr-2 custom-scrollbar">
                        @foreach($stats['tickets_by_name'] as $name => $count)
                        <div class="flex justify-between items-center text-xs">
                            <span class="font-bold text-dark truncate w-2/3" title="{{ $name }}">{{ $name }}</span>
                            <span class="px-2 py-0.5 bg-primary/10 text-primary font-black rounded-lg">{{ $count }}</span>
                        </div>
                        @endforeach
                    </div>
                @else
                    <h3 class="text-2xl font-black text-gray-300 tracking-tight">-</h3>
                    <div class="mt-2 text-[10px] font-bold text-gray-400">No sales recorded</div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Active Events -->
            <div class="lg:col-span-2 bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50 flex items-center justify-between">
                    <h2 class="text-xl font-black text-dark uppercase tracking-tight">Active Events</h2>
                </div>
                <div class="overflow-x-auto">
                    @if($events->count() > 0)
                        <table class="w-full text-left">
                            <tbody class="divide-y divide-gray-50">
                                @foreach($events as $event)
                                    <tr class="group hover:bg-gray-50/50 transition-colors">
                                        <td class="px-8 py-6">
                                            <div class="flex items-center gap-4">
                                                <img src="{{
                                                    Str::startsWith($event->thumbnail_path, 'http')
                                                    ? $event->thumbnail_path
                                                    : (file_exists(public_path($event->thumbnail_path))
                                                        ? asset($event->thumbnail_path)
                                                        : asset('storage/' . $event->thumbnail_path))
                                                }}"
                                                    class="w-16 h-16 rounded-xl object-cover shadow-sm">
                                                <div>
                                                    <h4 class="font-bold text-dark text-lg">{{ $event->name }}</h4>
                                                    <div class="flex items-center gap-2 text-sm text-gray-400 mt-1">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                        <span>{{ $event->start_date->format('d M Y') }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 text-right">
                                            <a href="{{ route('reseller.transactions.create', $event) }}"
                                                class="inline-block px-6 py-3 bg-primary text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-primary/90 transition-all shadow-lg shadow-primary/20">
                                                Buy Ticket
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="p-8 text-center py-12">
                            <div
                                class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-gray-300">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <h4 class="text-lg font-bold text-dark mb-1">No active events</h4>
                            <p class="text-sm text-secondary">Check back later for new events.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Profile -->
            <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50 bg-primary/5">
                    <h2 class="text-xl font-black text-dark uppercase tracking-tight text-center">Your Profile</h2>
                </div>
                <div class="p-8 text-center">
                    <div class="mb-6">
                        @if(Auth::user()->profile_photo_path)
                            <img src="{{
                                Str::startsWith(Auth::user()->profile_photo_path, 'http')
                                ? Auth::user()->profile_photo_path
                                : (file_exists(public_path(Auth::user()->profile_photo_path))
                                    ? asset(Auth::user()->profile_photo_path)
                                    : asset('storage/' . Auth::user()->profile_photo_path))
                            }}"
                                class="w-24 h-24 rounded-[2rem] mx-auto object-cover ring-4 ring-primary/5">
                        @else
                            <div
                                class="w-24 h-24 bg-primary/10 text-primary rounded-[2rem] flex items-center justify-center text-3xl font-black mx-auto ring-4 ring-primary/5">
                                {{ Auth::user()->initials() }}
                            </div>
                        @endif
                    </div>
                    <h3 class="text-xl font-black text-dark uppercase tracking-tight leading-none mb-1">
                        {{ Auth::user()->name }}
                    </h3>
                    <p class="text-xs font-bold text-primary uppercase tracking-widest mb-4">Official Reseller</p>
                    <p class="text-sm text-gray-400 px-4">{{ Auth::user()->bio ?? 'No bio provided.' }}</p>

                    <div class="mt-8 pt-8 border-t border-gray-50 space-y-3">
                        <div class="flex items-center justify-between text-left">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Email</span>
                            <span class="text-xs font-black text-dark">{{ Auth::user()->email }}</span>
                        </div>
                        <div class="flex items-center justify-between text-left">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Phone</span>
                            <span class="text-xs font-black text-dark">{{ Auth::user()->phone ?? '-' }}</span>
                        </div>
                    </div>

                    <a href="{{ route('admin.profile') }}"
                        class="mt-8 block w-full py-4 bg-gray-50 hover:bg-primary hover:text-white text-dark text-xs font-black uppercase tracking-widest rounded-2xl transition-all">
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.reseller>
