<x-layouts.reseller title="Deposit Info">
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-black text-dark uppercase tracking-tight">Deposit Info</h1>
                <p class="text-secondary mt-1">History of your deposit top-ups.</p>
            </div>

            <div class="bg-white px-6 py-3 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-3">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Current Balance</span>
                <span class="text-xl font-black text-primary">Rp {{ number_format(Auth::user()->balance, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Deposit History Table -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-8 border-b border-gray-100">
                <h3 class="font-bold text-dark text-lg">Deposit History</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr
                            class="bg-gray-50/50 text-xs font-black text-gray-400 uppercase tracking-wider text-left border-b border-gray-100">
                            <th class="px-8 py-4">Date</th>
                            <th class="px-8 py-4">Amount</th>
                            <th class="px-8 py-4">Note</th>
                            <th class="px-8 py-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($deposits as $deposit)
                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                <td class="px-8 py-4">
                                    <span class="font-bold text-dark text-sm">
                                        {{ $deposit->created_at->format('d M Y, H:i') }}
                                    </span>
                                </td>
                                <td class="px-8 py-4">
                                    <span class="font-black text-green-600 text-sm">
                                        + Rp {{ number_format($deposit->amount, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-8 py-4">
                                    <span class="text-xs text-secondary font-medium">
                                        {{ $deposit->note ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-8 py-4">
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-lg bg-green-50 text-green-600 text-[10px] font-black uppercase">
                                        Completed
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-12 text-center text-gray-400 font-medium">
                                    No deposit history found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-8 border-t border-gray-100">
                {{ $deposits->links() }}
            </div>
        </div>
    </div>
</x-layouts.reseller>
