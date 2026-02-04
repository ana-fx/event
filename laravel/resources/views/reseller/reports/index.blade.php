<x-layouts.reseller title="Sales Report">
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-black text-dark uppercase tracking-tight">Sales Report</h1>
                <p class="text-secondary mt-1">Overview of your ticket sales and commissions.</p>
            </div>

            <!-- Filters -->
            <form method="GET" class="flex flex-wrap items-center gap-3">
            <!-- Event Filter -->
            <div x-data="{
                open: false,
                selected: '{{ request('event_id') ?: '' }}',
                label: '{{ request('event_id') ? ($events->firstWhere('id', request('event_id'))->name ?? 'All Events') : 'All Events' }}'
            }">
                <input type="hidden" name="event_id" :value="selected">
                <div class="relative min-w-[200px]">
                    <button type="button" @click="open = !open" @click.away="open = false"
                        class="w-full flex items-center justify-between px-5 py-2.5 rounded-xl bg-white text-sm font-bold border border-gray-100 focus:ring-4 focus:ring-primary/10 transition-all text-dark">
                        <span x-text="label" :class="selected === '' ? 'text-gray-400 font-medium' : 'text-dark'"></span>
                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        class="absolute z-50 w-full mt-2 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">
                        <div class="px-5 py-3 bg-gray-50/50 border-b border-gray-100">
                            <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Select Event</span>
                        </div>
                        <div class="py-1 max-h-48 overflow-y-auto custom-scrollbar">
                            <button type="button" @click="selected = ''; label = 'All Events'; open = false" class="w-full px-5 py-2.5 text-left hover:bg-primary/5 transition-colors text-sm font-bold text-dark">All Events</button>
                            @foreach($events as $event)
                                <button type="button" @click="selected = '{{ $event->id }}'; label = '{{ addslashes($event->name) }}'; open = false" class="w-full px-5 py-2.5 text-left hover:bg-primary/5 transition-colors text-sm font-bold text-dark">{{ $event->name }}</button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

                <div x-data x-init="flatpickr($refs.startPicker, { dateFormat: 'Y-m-d', altInput: true, altFormat: 'd M Y', altInputClass: 'px-6 py-2.5 min-w-[150px] rounded-xl border border-gray-100 bg-white text-sm font-bold focus:ring-4 focus:ring-primary/10 transition-all outline-none text-dark cursor-pointer' })">
                    <input x-ref="startPicker" type="text" name="start_date" value="{{ request('start_date') }}" placeholder="Start Date"
                        class="hidden">
                </div>

                <div x-data x-init="flatpickr($refs.endPicker, { dateFormat: 'Y-m-d', altInput: true, altFormat: 'd M Y', altInputClass: 'px-6 py-2.5 min-w-[150px] rounded-xl border border-gray-100 bg-white text-sm font-bold focus:ring-4 focus:ring-primary/10 transition-all outline-none text-dark cursor-pointer' })">
                    <input x-ref="endPicker" type="text" name="end_date" value="{{ request('end_date') }}" placeholder="End Date"
                        class="hidden">
                </div>

                <button type="submit"
                    class="px-6 py-2.5 bg-primary text-white font-bold rounded-xl hover:bg-primary-dark transition-colors shadow-lg shadow-primary/20 active:scale-95">
                    Filter
                </button>
            </form>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <div
                class="bg-gradient-to-br from-primary to-[#108c8d] rounded-2xl p-5 text-white shadow-xl shadow-primary/20 relative overflow-hidden group">
                <p class="text-teal-100 font-bold uppercase tracking-wider text-[10px] mb-1">Total Revenue</p>
                <h3 class="text-2xl font-black tracking-tight">Rp {{ number_format($totalSales, 0, ',', '.') }}</h3>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 relative overflow-hidden group">
                <p class="text-gray-400 font-bold uppercase tracking-wider text-[10px] mb-1">Total Earned</p>
                <h3 class="text-2xl font-black text-primary tracking-tight">Rp {{ number_format($totalCommission, 0, ',', '.') }}</h3>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 relative overflow-hidden group">
                <p class="text-gray-400 font-bold uppercase tracking-wider text-[10px] mb-1">Deposit Balance</p>
                <h3 class="text-2xl font-black text-primary tracking-tight">Rp {{ number_format(Auth::user()->balance, 0, ',', '.') }}</h3>
            </div>

            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 relative overflow-hidden group">
                <p class="text-gray-400 font-bold uppercase tracking-wider text-[10px] mb-1">Tickets Sold</p>
                <h3 class="text-2xl font-black text-dark tracking-tight">{{ number_format($totalTickets) }}</h3>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-100">
                <h3 class="font-bold text-dark text-lg">Transaction History</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr
                            class="bg-gray-50/50 text-xs font-black text-gray-400 uppercase tracking-wider text-left border-b border-gray-100">
                            <th class="px-8 py-4">Transaction ID</th>
                            <th class="px-8 py-4">Event</th>
                            <th class="px-8 py-4">Customer</th>
                            <th class="px-8 py-4">Qty</th>
                            <th class="px-8 py-4 text-right">Net Total</th>
                            <th class="px-8 py-4 text-right">Commission</th>
                            <th class="px-8 py-4">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($transactions as $trx)
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-8 py-4">
                                    <span class="font-bold text-dark font-mono text-xs">{{ $trx->code }}</span>
                                </td>
                                <td class="px-8 py-4">
                                    <div class="font-bold text-dark text-sm">{{ $trx->event->name }}</div>
                                    <div class="text-xs text-secondary mt-0.5">{{ $trx->ticket->name }}</div>
                                </td>
                                <td class="px-8 py-4">
                                    <div class="font-bold text-dark text-sm">{{ $trx->name }}</div>
                                    <a href="mailto:{{ $trx->email }}" class="block text-xs text-secondary hover:text-primary transition-colors mt-0.5">{{ $trx->email }}</a>
                                    <a href="tel:{{ $trx->phone }}" class="block text-xs text-secondary hover:text-primary transition-colors mt-0.5">{{ $trx->phone }}</a>
                                </td>
                                <td class="px-8 py-4">
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-lg bg-gray-100 text-gray-600 text-[10px] font-black uppercase">
                                        {{ $trx->quantity }} Tix
                                    </span>
                                </td>
                                <td class="px-8 py-4 text-right">
                                    <span class="font-bold text-dark text-sm">Rp
                                        {{ number_format($trx->total_price - $trx->commission, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-8 py-4 text-right">
                                    <span class="font-black text-primary text-sm">Rp
                                        {{ number_format($trx->commission, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-8 py-4">
                                    <span class="text-[10px] font-bold text-gray-400">
                                        {{ $trx->created_at->format('d/m/y H:i') }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-8 py-12 text-center text-gray-400 font-medium">
                                    No sales transactions found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-8 border-t border-gray-100">
                {{ $transactions->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-layouts.reseller>
