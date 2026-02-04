<x-layouts.admin title="Reseller Management">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-dark">Reseller Management</h1>
            <p class="text-xs text-gray-500 font-medium uppercase tracking-widest mt-1">Financial & Performance Overview</p>
        </div>
    </div>

    @php
        $totalGroupBalance = \App\Models\User::where('role', 'reseller')->sum('balance');
        $totalGroupSales = \App\Models\Transaction::where('status', 'paid')->whereNotNull('reseller_id')->sum('total_price');
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex items-center gap-5">
            <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center text-primary">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Active Resellers</p>
                <p class="text-2xl font-black text-dark">{{ $resellers->total() }}</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex items-center gap-5">
            <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center text-primary">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Group Total Debt</p>
                <p class="text-2xl font-black text-dark">Rp {{ number_format($totalGroupBalance) }}</p>
            </div>
        </div>

        <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm flex items-center gap-5">
            <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center text-primary">
                <svg class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                </svg>
            </div>
            <div>
                <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-0.5">Total Reseller Sales</p>
                <p class="text-2xl font-black text-dark">Rp {{ number_format($totalGroupSales) }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-gray-50 border-b border-gray-100 text-[10px] uppercase tracking-wider text-gray-400 font-black">
                        <th class="px-6 py-5">Reseller Name</th>
                        <th class="px-6 py-5 text-center">Total Deposited</th>
                        <th class="px-6 py-5 text-center">Sales Generated</th>
                        <th class="px-6 py-5 text-center">Outstanding Debt</th>
                        <th class="px-6 py-5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($resellers as $reseller)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="font-bold text-dark text-sm">{{ $reseller->name }}</div>
                                <div class="text-[10px] text-gray-400 font-medium tracking-wide">{{ $reseller->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-bold text-gray-600 text-sm">
                                    Rp{{ number_format($reseller->total_deposit_sum ?? 0) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-black text-primary text-sm">
                                    Rp{{ number_format($reseller->total_sales_sum ?? 0) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-black {{ $reseller->balance < 0 ? 'text-rose-600' : 'text-emerald-600' }} text-sm">
                                    Rp{{ number_format($reseller->balance) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.reports.transactions', ['reseller_id' => $reseller->id]) }}"
                                        class="p-2 text-gray-400 rounded-lg hover:text-primary hover:bg-primary/5 transition-all"
                                        title="View Sales">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.resellers.deposits', $reseller) }}"
                                        class="p-2 text-gray-400 rounded-lg hover:text-green-600 hover:bg-green-50 transition-all"
                                        title="Manage Deposits">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                         <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-gray-300">
                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                No resellers found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($resellers->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $resellers->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
