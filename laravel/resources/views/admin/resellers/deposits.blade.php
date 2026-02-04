<x-layouts.admin title="{{ $reseller->name }} - Deposits">
    <div class="space-y-6">
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.reseller-management.index') }}"
                class="p-2 bg-white border border-gray-100 rounded-xl text-gray-400 hover:text-dark transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-dark">{{ $reseller->name }}</h1>
                <p class="text-xs text-gray-400 font-medium uppercase tracking-widest mt-0.5">Deposit Management</p>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <div class="bg-primary/5 border border-primary/10 px-6 py-3 rounded-2xl">
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Current Deposit</p>
                <p class="text-xl font-black text-primary">Rp{{ number_format($reseller->balance) }}</p>
            </div>
        </div>
    </div>

    <!-- Quick Lifetime Stats -->
    <div class="grid grid-cols-1 gap-4 mb-8">
        <div class="bg-white p-6 rounded-2xl border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Lifetime Gross Sales</p>
                <p class="text-2xl font-black text-dark tracking-tight">
                    Rp{{ number_format($reseller->resellerTransactions()->where('status', 'paid')->sum('total_price')) }}
                </p>
            </div>
            <div class="w-12 h-12 bg-gray-50 rounded-xl flex items-center justify-center text-gray-400">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 rounded-xl bg-green-50 text-green-700 border border-green-200 flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif

    <div class="flex flex-col-reverse lg:grid lg:grid-cols-12 gap-6 lg:gap-8">
        <!-- New Deposit Form -->
        <div class="lg:col-span-4">
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 p-8 sticky top-28">
                <h2 class="text-lg font-black text-dark uppercase tracking-tight mb-6">Input New Deposit</h2>

                <form action="{{ route('admin.resellers.deposits.store', $reseller) }}" method="POST">
                    @csrf
                    <div class="space-y-6">
                        <div>
                            <label
                                class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Deposit
                                Amount (Rp)</label>
                            <input type="number" name="amount" required min="1"
                                class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-primary/20 text-dark font-bold text-lg"
                                placeholder="e.g. 1000000">
                            @error('amount')
                                <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label
                                class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Note
                                (Optional)</label>
                            <textarea name="note" rows="3"
                                class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-primary/20 text-dark font-medium text-sm"
                                placeholder="Reason for deposit..."></textarea>
                            @error('note')
                                <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit"
                            class="w-full py-5 bg-primary text-white font-black rounded-2xl shadow-xl shadow-primary/30 hover:bg-primary/90 transition-all active:scale-95 flex items-center justify-center gap-3">
                            Confirm Deposit
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

        </div>

        <!-- Deposit History -->
        <div class="lg:col-span-8" x-data="{ deleteModalOpen: false, formToSubmit: null }">
            <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-8 py-6 border-b border-gray-50">
                    <h2 class="text-lg font-black text-dark uppercase tracking-tight">Deposit History</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                <th class="px-8 py-4">Date</th>
                                <th class="px-8 py-4">Amount</th>
                                <th class="px-8 py-4 hidden md:table-cell">Note</th>
                                <th class="px-8 py-4 hidden md:table-cell">Added By</th>
                                <th class="px-8 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($deposits as $deposit)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-8 py-6">
                                        <div class="text-sm font-bold text-dark">
                                            {{ $deposit->created_at->format('M d, Y') }}
                                        </div>
                                        <div class="text-[10px] text-gray-400 uppercase tracking-tight">
                                            {{ $deposit->created_at->format('H:i') }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="text-sm font-black text-green-600">
                                            + Rp{{ number_format($deposit->amount) }}
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 hidden md:table-cell">
                                        <p class="text-sm text-gray-500 italic">"{{ $deposit->note ?? 'No note' }}"</p>
                                    </td>
                                    <td class="px-8 py-6 hidden md:table-cell">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="w-6 h-6 rounded-lg bg-primary/10 flex items-center justify-center text-[10px] font-black text-primary">
                                                {{ $deposit->creator->initials() }}
                                            </div>
                                            <span class="text-xs font-bold text-dark">{{ $deposit->creator->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('admin.resellers.deposits.edit', [$reseller, $deposit]) }}" 
                                               class="p-2 text-gray-400 hover:text-primary hover:bg-primary/5 rounded-xl transition-all active:scale-90"
                                               title="Edit Deposit">
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                            <form action="{{ route('admin.resellers.deposits.destroy', [$reseller, $deposit]) }}" method="POST" @submit.prevent="formToSubmit = $el; deleteModalOpen = true">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-xl transition-all active:scale-90" title="Delete Deposit">
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
                                    <td colspan="5" class="px-8 py-12 text-center">
                                        <div
                                            class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-gray-300">
                                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <p class="text-gray-400 font-bold">No deposit history found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($deposits->hasPages())
                    <div class="px-8 py-6 bg-gray-50/50 border-t border-gray-50">
                        {{ $deposits->links() }}
                    </div>
                @endif
            </div>
            <!-- Delete Confirmation Modal -->
            <x-notifications.delete />
        </div>
    </div>
</div>
</x-layouts.admin>