<x-layouts.admin>
    <div class="max-w-5xl mx-auto space-y-8 pb-12">
        <!-- Back Link -->
        <div>
            <a href="{{ route('admin.events.tickets-report.index', $event) }}" class="inline-flex items-center text-sm font-bold text-gray-400 hover:text-primary transition-colors gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Reports
            </a>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8" x-data="{ open: false }">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Withdrawal History
                </h3>

                <button @click="open = true"
                    class="px-5 py-2.5 bg-primary text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-primary/90 transition shadow-lg shadow-primary/20 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Withdraw Funds
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase text-gray-500 font-bold tracking-wider">
                            <th class="px-6 py-4 rounded-tl-xl text-dark">Date</th>
                            <th class="px-6 py-4 text-dark">Reference</th>
                            <th class="px-6 py-4 text-dark">Note</th>
                            <th class="px-6 py-4 text-dark text-right">Amount</th>
                            <th class="px-6 py-4 rounded-tr-xl text-dark text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($withdrawals as $w)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 text-sm font-bold text-gray-500 whitespace-nowrap">
                                    {{ $w->created_at->format('M d, Y • H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-bold text-dark text-sm">{{ $w->reference }}</span>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-500 italic max-w-xs truncate">
                                    {{ $w->note ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-right font-black text-red-500 text-sm whitespace-nowrap">
                                    Rp {{ number_format($w->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.events.withdrawals.edit', [$event, $w]) }}"
                                           class="p-2 text-gray-400 hover:text-primary transition-colors bg-gray-50 hover:bg-primary/5 rounded-lg">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.events.withdrawals.destroy', [$event, $w]) }}" method="POST"
                                              onsubmit="return confirm('Are you sure you want to delete this withdrawal? This will return the amount to the available balance.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-gray-400 hover:text-red-500 transition-colors bg-gray-50 hover:bg-red-50 rounded-lg">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-200 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                        </svg>
                                        No withdrawals recorded yet.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Withdrawal Modal -->
            <div x-show="open" style="display: none;"
                 class="fixed inset-0 z-50 flex items-center justify-center bg-dark/60 backdrop-blur-sm p-4"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">

                <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg overflow-hidden relative"
                     @click.away="open = false">
                    <form action="{{ route('admin.events.withdrawals.store', $event) }}" method="POST">
                        @csrf
                        <div class="px-8 pt-8 pb-6">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h3 class="text-2xl font-black text-dark tracking-tight">Withdraw Funds</h3>
                                    <p class="text-xs text-gray-500 font-bold uppercase tracking-widest mt-1">Ref: {{ $event->name }}</p>
                                </div>
                                <button type="button" @click="open = false" class="text-gray-400 hover:text-dark transition-colors">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="space-y-6">
                                <!-- Context Balance in Modal -->
                                <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-100">
                                    <div class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-1">Available Balance</div>
                                    <div class="text-xl font-black text-emerald-700">Rp {{ number_format($availableSaldo, 0, ',', '.') }}</div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Withdrawal Amount (Rp)</label>
                                    <div class="relative">
                                        <span class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-400 font-bold">Rp</span>
                                        <input type="number" name="amount" required min="1" max="{{ $availableSaldo }}" step="0.01"
                                               class="w-full pl-14 pr-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-primary/20 text-dark font-bold text-lg placeholder-gray-300 transition-all"
                                               placeholder="0">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Internal Note (Optional)</label>
                                    <textarea name="note" rows="3"
                                              class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-primary/20 text-dark font-bold placeholder-gray-300 transition-all resize-none"
                                              placeholder="Add any specific details about this payout..."></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="px-8 pb-8">
                            <button type="submit" class="w-full py-4 bg-primary text-white rounded-2xl font-black text-sm hover:bg-dark transition-all shadow-xl shadow-primary/20">
                                Confirm Withdrawal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
