<x-layouts.admin title="Sales Reports">
    <!-- Header Section -->
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black text-dark tracking-tight">Performance Analytics</h2>
            <p class="text-gray-500 mt-1 font-medium">Deep dive into your sales data and event performance.</p>
        </div>

        <!-- Date Filter Form -->
        <form action="{{ route('admin.reports.index') }}" method="GET" class="flex items-center gap-2 bg-white p-2 rounded-2xl border border-gray-100 shadow-sm focus-within:shadow-md transition-shadow">
            <div class="flex items-center px-4 gap-2" x-data x-init="flatpickr($refs.startPicker, { dateFormat: 'Y-m-d', altInput: true, altFormat: 'd M Y', altInputClass: 'bg-transparent border-none p-0 text-sm font-bold text-dark focus:ring-0 cursor-pointer w-28 uppercase tracking-widest' })">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <input x-ref="startPicker" type="text" name="start_date" value="{{ $startDate }}"
                    class="hidden">
            </div>
            <div class="w-px h-6 bg-gray-100"></div>
            <div class="flex items-center px-4 gap-2" x-data x-init="flatpickr($refs.endPicker, { dateFormat: 'Y-m-d', altInput: true, altFormat: 'd M Y', altInputClass: 'bg-transparent border-none p-0 text-sm font-bold text-dark focus:ring-0 cursor-pointer w-28 uppercase tracking-widest' })">
                <input x-ref="endPicker" type="text" name="end_date" value="{{ $endDate }}"
                    class="hidden">
            </div>
            <button type="submit" class="p-3 bg-dark text-white rounded-xl hover:opacity-90 transition-all active:scale-95 shadow-lg shadow-dark/10">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </button>
        </form>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
        <!-- Revenue Card -->
        <div class="relative overflow-hidden bg-white rounded-[2.5rem] p-6 border border-gray-100 shadow-xl shadow-primary/5 group hover:shadow-primary/10 transition-all duration-500">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-2xl bg-primary/5 flex items-center justify-center text-primary shadow-inner">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Revenue</h3>
                    <p class="text-xs font-bold text-gray-400">Total Valid</p>
                </div>
            </div>
            <div class="text-2xl font-black text-dark tracking-tight">
                Rp {{ number_format($totalRevenue, 0, ',', '.') }}
            </div>
        </div>

        <!-- Tickets Sold -->
        <div class="relative overflow-hidden bg-white rounded-[2.5rem] p-6 border border-gray-100 shadow-xl shadow-primary/5 group hover:shadow-primary/10 transition-all duration-500">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-2xl bg-primary/5 flex items-center justify-center text-primary shadow-inner">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Sales</h3>
                    <p class="text-xs font-bold text-gray-400">Tickets Sold</p>
                </div>
            </div>
            <div class="text-3xl font-black text-dark tracking-tight">
                {{ number_format($ticketsSold) }}
            </div>
        </div>

        <!-- Scanned (Redeemed) -->
        <a href="{{ route('admin.reports.scanner') }}" class="relative overflow-hidden bg-white rounded-[2.5rem] p-6 border border-gray-100 shadow-xl shadow-primary/5 group hover:shadow-primary/10 transition-all duration-500 hover:scale-[1.02] cursor-pointer">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-2xl bg-primary/5 flex items-center justify-center text-primary shadow-inner">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                    </svg>
                </div>
                <div>
                   <h3 class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Entry</h3>
                   <p class="text-xs font-bold text-gray-400">Scanned Tickets</p>
                </div>
            </div>
            <div class="text-3xl font-black text-dark tracking-tight">
                {{ number_format($ticketsRedeemed) }}
            </div>
             <div class="absolute bottom-4 right-6 opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2 group-hover:translate-x-0">
                <span class="text-[10px] font-black uppercase tracking-widest text-primary">View Report →</span>
            </div>
        </a>

        <!-- Transactions Count -->
        <a href="{{ route('admin.reports.transactions') }}" class="relative overflow-hidden bg-white rounded-[2.5rem] p-6 border border-gray-100 shadow-xl shadow-primary/5 group hover:shadow-primary/10 transition-all duration-500 hover:scale-[1.02] cursor-pointer">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-2xl bg-primary/5 flex items-center justify-center text-primary shadow-inner">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                </div>
                <div>
                     <h3 class="text-[10px] font-black text-gray-500 uppercase tracking-widest">Ledger</h3>
                      <p class="text-xs font-bold text-gray-400">Transactions</p>
                </div>
            </div>
            <div class="text-3xl font-black text-dark tracking-tight">
                {{ number_format($totalTransactions) }}
            </div>
             <div class="absolute bottom-4 right-6 opacity-0 group-hover:opacity-100 transition-all duration-300 translate-x-2 group-hover:translate-x-0">
                <span class="text-[10px] font-black uppercase tracking-widest text-primary">View Report →</span>
            </div>
        </a>
    </div>

    <!-- Main Content Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
        <!-- Event Performance Table -->
        <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-primary/5 border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-50 flex items-center justify-between">
                <h3 class="text-xl font-black text-dark tracking-tight">Top Performance</h3>
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest bg-gray-100 px-3 py-1.5 rounded-full">By Revenue</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                            <th class="px-8 py-5">Event Intelligence</th>
                            <th class="px-8 py-5 text-right">Volume</th>
                            <th class="px-8 py-5 text-right">Revenue</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($revenueByEvent as $event)
                            <tr class="group hover:bg-primary/[0.02] transition-colors">
                                <td class="px-8 py-6 font-bold text-dark group-hover:text-primary transition-colors text-sm">{{ Str::limit($event->name, 40) }}</td>
                                <td class="px-8 py-6 text-right font-bold text-gray-500 text-sm">{{ number_format($event->total_tickets) }}</td>
                                <td class="px-8 py-6 text-right font-black text-emerald-600 text-sm">Rp {{ number_format($event->total_revenue, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                             <tr>
                                <td colspan="3" class="px-8 py-16 text-center text-gray-400 italic">No historical data found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Trend Analysis -->
        <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-primary/5 border border-gray-100 p-8 flex flex-col">
            <div class="flex items-center justify-between mb-10">
                <h3 class="text-xl font-black text-dark tracking-tight">Revenue Trend</h3>
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                    <span class="text-[10px] font-black text-primary uppercase tracking-widest">Live Pulse</span>
                </div>
            </div>

            <div class="flex-1 flex items-end gap-3 h-64 px-2">
                @if($chartRevenue->count() > 0)
                    @php $maxRevenue = $chartRevenue->max(); @endphp
                    @foreach($dailyRevenue as $day)
                        @php
                            $height = $maxRevenue > 0 ? ($day->revenue / $maxRevenue) * 100 : 0;
                        @endphp
                        <div class="flex-1 flex flex-col items-center group relative h-full justify-end">
                            <!-- Tooltip -->
                            <div class="absolute bottom-full mb-4 opacity-0 group-hover:opacity-100 transition-all transform translate-y-2 group-hover:translate-y-0 bg-dark text-white text-[10px] font-black rounded-lg py-2 px-3 whitespace-nowrap z-10 pointer-events-none shadow-xl uppercase tracking-widest">
                                Rp {{ number_format($day->revenue, 0, ',', '.') }}
                            </div>

                            <!-- Bar -->
                            <div class="w-full bg-primary/[0.08] rounded-2xl hover:bg-primary transition-all duration-500 relative cursor-pointer" style="height: {{ max($height, 5) }}%">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            </div>
                            <div class="mt-4 text-[9px] font-black text-gray-300 group-hover:text-primary uppercase transition-all rotate-45 md:rotate-0 origin-left">{{ \Carbon\Carbon::parse($day->date)->format('d M') }}</div>
                        </div>
                    @endforeach
                @else
                    <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 opacity-20">
                        <svg class="w-16 h-16 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                        <p class="font-black uppercase tracking-widest text-xs">Insight Unavailable</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.admin>
