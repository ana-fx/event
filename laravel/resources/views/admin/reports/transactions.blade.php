<x-layouts.admin title="Transaction Report">
    <!-- Header Section -->
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h2 class="text-4xl font-black text-dark tracking-tight">Transactions</h2>
            <p class="text-gray-500 mt-1 font-medium">Monitor and manage all customer payments in real-time.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="exportToExcel()"
                class="group px-6 py-3.5 bg-white text-dark font-bold rounded-2xl hover:bg-gray-50 transition-all shadow-sm border border-gray-100 flex items-center gap-3 active:scale-95">
                <div
                    class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-dark group-hover:scale-110 transition-transform">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                </div>
                <span>Export Excel</span>
            </button>
        </div>
    </div>

    <!-- Filter & Statistics Card -->
    <div class="bg-white rounded-2xl shadow-sm shadow-primary/5 border border-gray-100 p-5 mb-10">
        <form action="{{ route('admin.reports.transactions') }}" method="GET"
            class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
            <div class="md:col-span-3">
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">Search</label>
                <div class="relative group">
                    <div
                        class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400 group-focus-within:text-primary transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                        class="w-full pl-12 pr-4 py-4 rounded-2xl border-none bg-gray-50 focus:bg-white focus:ring-4 focus:ring-primary/10 outline-none transition-all text-sm font-medium placeholder:text-gray-400">
                </div>
            </div>

            <!-- Source Filter -->
            <div class="md:col-span-3" x-data="{
                open: false,
                selected: '{{ request('source') ?: '' }}',
                label: '{{ request('source') == 'online' ? 'Online' : (request('source') == 'reseller' ? 'Reseller' : 'All Sources') }}'
            }">
                <label
                    class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-1">Source</label>
                <input type="hidden" name="source" :value="selected">
                <div class="relative">
                    <button type="button" @click="open = !open" @click.away="open = false"
                        class="w-full flex items-center justify-between px-6 py-4 rounded-2xl bg-gray-50 text-sm font-bold border-none focus:ring-4 focus:ring-primary/10 transition-all text-dark">
                        <span x-text="label"
                            :class="selected === '' ? 'text-gray-400 font-medium' : 'text-dark'"></span>
                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                            :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>

                    <div x-show="open" x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        class="absolute z-50 w-full mt-2 bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50/50 border-b border-gray-100">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Select
                                Source</span>
                        </div>
                        <div class="py-2">
                            <button type="button" @click="selected = ''; label = 'All Sources'; open = false"
                                class="w-full px-6 py-3 text-left hover:bg-primary/5 transition-colors text-sm font-bold text-dark">All
                                Sources</button>
                            <button type="button" @click="selected = 'online'; label = 'Online'; open = false"
                                class="w-full px-6 py-3 text-left hover:bg-primary/5 transition-colors text-sm font-bold text-dark">Online</button>
                            <button type="button" @click="selected = 'reseller'; label = 'Reseller'; open = false"
                                class="w-full px-6 py-3 text-left hover:bg-primary/5 transition-colors text-sm font-bold text-dark">Reseller</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Filter -->
            <div class="md:col-span-3" x-data="{
                open: false,
                selected: '{{ request('status') ?: '' }}',
                label: '{{ request('status') == 'pending' ? 'Pending' : (request('status') == 'paid' ? 'Paid' : (request('status') == 'failed' ? 'Failed' : 'All Status')) }}'
            }">
                <label
                    class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-3 ml-1">Status</label>
                <input type="hidden" name="status" :value="selected">
                <div class="relative">
                    <button type="button" @click="open = !open" @click.away="open = false"
                        class="w-full flex items-center justify-between px-6 py-4 rounded-2xl bg-gray-50 text-sm font-bold border-none focus:ring-4 focus:ring-primary/10 transition-all text-dark">
                        <span x-text="label"
                            :class="selected === '' ? 'text-gray-400 font-medium' : 'text-dark'"></span>
                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                            :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>

                    <div x-show="open" x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        class="absolute z-50 w-full mt-2 bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">
                        <div class="px-6 py-4 bg-gray-50/50 border-b border-gray-100">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Select
                                Status</span>
                        </div>
                        <div class="py-2">
                            <button type="button" @click="selected = ''; label = 'All Status'; open = false"
                                class="w-full px-6 py-3 text-left hover:bg-primary/5 transition-colors text-sm font-bold text-dark">All
                                Status</button>
                            <button type="button" @click="selected = 'pending'; label = 'Pending'; open = false"
                                class="w-full px-6 py-3 text-left hover:bg-primary/5 transition-colors text-sm font-bold text-dark">Pending</button>
                            <button type="button" @click="selected = 'paid'; label = 'Paid'; open = false"
                                class="w-full px-6 py-3 text-left hover:bg-primary/5 transition-colors text-sm font-bold text-dark">Paid</button>
                            <button type="button" @click="selected = 'failed'; label = 'Failed'; open = false"
                                class="w-full px-6 py-3 text-left hover:bg-primary/5 transition-colors text-sm font-bold text-dark">Failed</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="md:col-span-3 flex gap-2">
                <button type="submit"
                    class="flex-1 py-4 bg-dark text-white font-black rounded-2xl hover:opacity-90 transition-all shadow-lg active:scale-95 uppercase tracking-widest text-xs">
                    Apply
                </button>
                <a href="{{ route('admin.reports.transactions') }}"
                    class="px-5 py-4 bg-gray-100 text-dark font-black rounded-2xl hover:bg-gray-200 transition-all active:scale-95 uppercase tracking-widest text-xs">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Main Table Container -->
    <div class="bg-white rounded-2xl shadow-2xl shadow-primary/5 border border-gray-100 overflow-hidden"
        x-data="{ confirmModalOpen: false, actionUrl: '' }">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="transaction-table">
                <thead>
                    <tr
                        class="bg-gray-50 border-b border-gray-100 text-xs uppercase text-gray-500 font-bold tracking-wider">
                        <th class="px-4 py-3">
                            Transaction</th>
                        <th class="px-4 py-3">Customer
                        </th>
                        <th class="px-4 py-3">Source</th>
                        <th class="px-4 py-3">Event Detail</th>
                        <th class="px-4 py-3 text-right">
                            Amount Breakout</th>
                        <th class="px-4 py-3 text-center">
                            Status</th>
                        <th class="px-4 py-3 text-right">
                            Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($transactions as $index => $transaction)
                        @php
                            $ticketSales = $transaction->quantity * ($transaction->ticket->price ?? 0);

                            // Adjust Fee Logic based on Source
                            if ($transaction->reseller_id) {
                                $handlingTotal = 0; // Resellers don't have Handling Fee
                                $extraFee = (float) $transaction->total_price - $ticketSales; // Remaining is Reseller Fee
                            } else {
                                $handlingTotal = $transaction->quantity * $handlingFeeValue;
                                $extraFee = (float) $transaction->total_price - ($ticketSales + $handlingTotal); // Remaining is Service Fee
                            }

                            if ($extraFee < 0)
                                $extraFee = 0;
                        @endphp
                        <tr class="group hover:bg-primary/[0.02] transition-colors">
                            <td class="px-4 py-3 leading-none">
                                <a href="{{ route('admin.reports.transactions.show', $transaction) }}"
                                    class="block group/item">
                                    <div
                                        class="font-mono text-[11px] font-black text-gray-600 tracking-tighter mb-1 select-all underline decoration-dotted decoration-gray-300 underline-offset-4 group-hover/item:text-dark transition-colors">
                                        {{ $transaction->code }}
                                    </div>
                                </a>
                                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                    {{ $transaction->created_at->format('d M Y, H:i') }}
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-black text-dark mb-0.5">{{ $transaction->name }}</div>
                                <div class="flex flex-col gap-0.5">
                                    <a href="mailto:{{ $transaction->email }}"
                                        class="text-[10px] font-bold text-primary hover:text-primary transition-colors">{{ $transaction->email }}</a>
                                    <a href="tel:{{ $transaction->phone }}"
                                        class="text-[10px] font-black text-gray-500 hover:opacity-70 transition-opacity">{{ $transaction->phone }}</a>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                @if($transaction->reseller_id)
                                    <div class="flex flex-col">
                                        <span
                                            class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100/80 text-gray-500 border border-gray-100 w-max mb-1 uppercase tracking-tighter">Reseller</span>
                                        <div class="text-[10px] font-black text-dark truncate max-w-[120px]"
                                            title="{{ $transaction->reseller->name ?? 'Deleted' }}">
                                            {{ $transaction->reseller->name ?? 'Deleted' }}
                                        </div>
                                    </div>
                                @else
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-primary/10 text-primary border border-primary/10 w-max uppercase tracking-tighter">Online</span>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span
                                    class="inline-block text-xs font-black text-gray-500 group-hover:text-primary transition-colors bg-gray-100 px-2.5 py-1 rounded-lg mb-1">{{ $transaction->event->name ?? 'Deleted Event' }}</span>
                                <div class="text-[10px] font-bold text-primary uppercase tracking-wider ml-1">
                                    {{ $transaction->ticket->name ?? 'N/A' }} <span
                                        class="text-gray-400 italic mx-1">x{{ $transaction->quantity }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="text-base font-black text-dark tracking-tight mb-0.5">Rp
                                    {{ number_format($transaction->total_price, 0, ',', '.') }}
                                </div>
                                <div class="flex items-center justify-end gap-2">
                                    @if($transaction->reseller_id)
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Fee:
                                            {{ number_format($extraFee, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Svc:
                                            {{ number_format($extraFee, 0, ',', '.') }}</span>
                                        <span class="w-1 h-1 rounded-full bg-gray-200"></span>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-tighter">Hdl:
                                            {{ number_format($handlingTotal, 0, ',', '.') }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($transaction->status === 'paid')
                                    <span
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl text-[10px] font-black uppercase tracking-widest bg-gray-50 text-dark border border-gray-100 shadow-sm ">
                                        <div class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></div>
                                        Accepted
                                    </span>
                                @elseif($transaction->status === 'pending')
                                    <span
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl text-[10px] font-black uppercase tracking-widest bg-gray-50 text-gray-500 border border-gray-100 shadow-sm ">
                                        <div class="w-1.5 h-1.5 rounded-full bg-gray-400 animate-pulse"></div>
                                        Pending
                                    </span>
                                @else
                                    <span
                                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-2xl text-[10px] font-black uppercase tracking-widest bg-gray-50 text-gray-400 border border-gray-100 opacity-60">
                                        <div class="w-1.5 h-1.5 rounded-full bg-gray-300"></div>
                                        Failed
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($transaction->status === 'paid')
                                        <button type="button"
                                            @click="copyToClipboard('{{ route('payment.success', $transaction->code) }}')"
                                            class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-emerald-500 hover:text-white transition-all active:scale-95 shadow-sm border border-gray-100"
                                            title="Copy Success URL">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3">
                                                </path>
                                            </svg>
                                        </button>
                                        <button type="button"
                                            @click="confirmModalOpen = true; actionUrl = '{{ route('admin.reports.resend-email', $transaction) }}'"
                                            class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gray-50 text-gray-400 hover:bg-primary hover:text-white transition-all active:scale-95 shadow-sm border border-gray-100"
                                            title="Resend Success Email">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                            </svg>
                                        </button>
                                    @else
                                        <div class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gray-50/50 text-gray-300 cursor-not-allowed shadow-sm border border-gray-100"
                                            title="Email only for paid transactions">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                            </svg>
                                        </div>
                                    @endif

                                    <a href="{{ route('admin.reports.transactions.show', $transaction) }}"
                                        class="inline-flex items-center justify-center w-10 h-10 rounded-xl bg-gray-50 text-primary hover:bg-dark hover:text-white transition-all active:scale-95 shadow-sm border border-gray-100"
                                        title="View Details">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-32 text-center">
                                <div class="flex flex-col items-center justify-center opacity-30 group">
                                    <div
                                        class="w-20 h-20 rounded-[2rem] bg-gray-100 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-500">
                                        <svg class="w-10 h-10 text-dark" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                            </path>
                                        </svg>
                                    </div>
                                    <p class="text-xl font-black text-dark tracking-tight">Empty Vault</p>
                                    <p class="text-sm font-medium text-primary mt-1">No transaction records match your
                                        criteria.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Premium Pagination -->
        @if($transactions->hasPages())
            <div class="px-4 py-6 bg-gray-50/50 border-t border-gray-100">
                {{ $transactions->links() }}
            </div>
        @endif

        <!-- Confirmation Modal -->
        <div x-show="confirmModalOpen" style="display: none;"
            class="fixed inset-0 z-[100] flex items-center justify-center bg-black/50 backdrop-blur-sm" x-transition>
            <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md p-5 relative border border-gray-100"
                @click.away="confirmModalOpen = false">
                <div class="flex flex-col items-center text-center">
                    <div
                        class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center text-primary mb-6">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                            </path>
                        </svg>
                    </div>

                    <h3 class="text-2xl font-black text-dark mb-2 tracking-tight">Resend Confirmation?</h3>
                    <p class="text-sm font-medium text-gray-500 mb-8 leading-relaxed">
                        You are about to resend the success email to the customer. This action cannot be undone.
                    </p>

                    <div class="flex gap-4 w-full">
                        <button type="button" @click="confirmModalOpen = false"
                            class="flex-1 py-3.5 rounded-xl text-sm font-bold text-gray-500 bg-gray-100 hover:bg-gray-200 transition-colors uppercase tracking-wider">
                            Cancel
                        </button>
                        <form :action="actionUrl" method="POST" class="flex-1">
                            @csrf
                            <button type="submit"
                                class="w-full py-3.5 rounded-xl text-sm font-bold text-white bg-primary hover:bg-primary-700 shadow-lg shadow-primary/30 transition-all active:scale-95 uppercase tracking-wider">
                                Confirm Send
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>
        <script>
            function exportToExcel() {
                const table = document.getElementById('transaction-table');
                const wb = XLSX.utils.table_to_book(table, { sheet: "Transactions" });
                XLSX.writeFile(wb, "ANNTIX_Transaction_Intelligence_" + new Date().toISOString().split('T')[0] + ".xlsx");
            }

            function copyToClipboard(text) {
                // Should we use the fallback or modern API?
                if (!navigator.clipboard) {
                    var textArea = document.createElement("textarea");
                    textArea.value = text;
                    textArea.style.position = "fixed";
                    textArea.style.top = "0";
                    textArea.style.left = "0";
                    document.body.appendChild(textArea);
                    textArea.focus();
                    textArea.select();

                    try {
                        var successful = document.execCommand('copy');
                        if(successful) window.dispatchEvent(new CustomEvent('notify', { detail: 'Success URL copied to clipboard!' }));
                    } catch (err) {
                        console.error('Fallback: Oops, unable to copy', err);
                    }

                    document.body.removeChild(textArea);
                    return;
                }

                navigator.clipboard.writeText(text).then(function() {
                    window.dispatchEvent(new CustomEvent('notify', { detail: 'Success URL copied to clipboard!' }));
                }, function(err) {
                    console.error('Async: Could not copy text: ', err);
                });
            }
        </script>

        <!-- Tailwind Notification Toast -->
        <div x-data="{ show: false, message: '' }"
             @notify.window="show = true; message = $event.detail; setTimeout(() => show = false, 3000)"
             class="fixed bottom-6 right-6 z-[150] flex flex-col gap-2"
             style="display: none;"
             x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-2">

            <div class="bg-dark text-white rounded-2xl shadow-2xl py-4 px-6 flex items-center gap-4 border border-gray-700/50 min-w-[300px]">
                <div class="h-10 w-10 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-400 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <div>
                    <h4 class="font-bold text-sm">Success</h4>
                    <p class="text-xs text-gray-400" x-text="message"></p>
                </div>
                <button @click="show = false" class="ml-auto text-gray-500 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    @endpush
</x-layouts.admin>
