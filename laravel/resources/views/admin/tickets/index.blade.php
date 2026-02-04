<x-layouts.admin>
    <div>
        <div class="flex flex-col xl:flex-row xl:items-center justify-between mb-8 gap-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.events.index') }}"
                    class="inline-flex items-center text-sm font-bold text-gray-400 hover:text-primary transition-colors gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Events
                </a>

            </div>

            <!-- Aggregate Saldo Widget -->
            <div class="flex flex-col md:flex-row items-stretch md:items-center gap-3">
                <div
                    class="bg-white p-2 rounded-2xl shadow-sm border border-gray-100 flex flex-wrap md:flex-nowrap items-center divide-x divide-gray-50">
                    <div class="px-5 py-2">
                        <div class="text-[9px] uppercase tracking-widest text-gray-400 font-bold mb-0.5">Total Saldo
                        </div>
                        <div class="font-black text-dark text-lg leading-tight">
                            Rp {{ number_format($totalSaldo, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="px-5 py-2">
                        <div class="text-[9px] uppercase tracking-widest text-gray-400 font-bold mb-0.5">Withdrawn</div>
                        <div class="font-black text-gray-500 text-lg leading-tight">
                            Rp {{ number_format($totalWithdrawn, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="px-5 py-2">
                        <div class="text-[9px] uppercase tracking-widest text-gray-400 font-bold mb-0.5">Available</div>
                        <div class="font-black text-primary text-xl leading-tight">
                            Rp {{ number_format($availableSaldo, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="px-5 py-2">
                        <div class="text-[9px] uppercase tracking-widest text-gray-400 font-bold mb-0.5">Platform Rev
                        </div>
                        <div class="font-black text-gray-500 text-base leading-tight">
                            Rp {{ number_format($totalPlatformRevenue, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-2 w-full md:w-auto">
                    <a href="{{ route('admin.events.withdrawals.create', $event) }}"
                        class="group flex items-center justify-center w-full px-5 py-3 bg-dark text-white rounded-2xl font-bold text-xs hover:bg-black transition-all shadow-lg shadow-dark/10 gap-2">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Withdraw
                    </a>
                    <button onclick="exportToExcel()"
                        class="group flex items-center justify-center w-full px-5 py-3 bg-white text-gray-400 rounded-2xl font-bold text-xs hover:bg-gray-50 transition-all border border-gray-100 shadow-sm gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        Export Excel
                    </button>
                </div>
            </div>
        </div>

        @if (session('success'))
            <div
                class="mb-6 p-4 rounded-3xl bg-emerald-50 text-emerald-700 border border-emerald-100 font-bold text-sm flex items-center gap-3">
                <svg class="w-5 h-5 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-3xl bg-red-50 text-red-700 border border-red-100 font-bold text-sm">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif



        <!-- Online Sales Report Section -->
        <div class="mt-16">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-3xl font-black text-dark tracking-tight">Online Revenue Digest</h2>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mt-2">Platform direct sales
                        analytics</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="px-5 py-2.5 bg-primary/5 rounded-2xl border border-primary/10 flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-primary animate-pulse"></div>
                        <span class="text-[9px] font-black text-primary uppercase tracking-widest">Online
                            Channel</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm shadow-primary/5 border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table id="online-table" class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-gray-50/50 border-b border-gray-100 text-[9px] uppercase tracking-wider text-gray-400 font-black">
                                <th class="px-4 py-3">No</th>
                                <th class="px-4 py-3 font-black text-dark">Ticket Variation</th>
                                <th class="px-4 py-3 text-center">Status</th>
                                <th class="px-4 py-3 text-center">Volume</th>
                                <th class="px-4 py-3 text-center">Stock</th>
                                <th class="px-4 py-3 text-right">Ticket Revenue</th>
                                <th class="px-4 py-3 text-right">Saldo</th>
                                <th class="px-4 py-3 text-right">Org Tax</th>
                                <th class="px-4 py-3 text-right">Handling Fee</th>
                                <th class="px-4 py-3 text-right">Platform Rev</th>
                                <th class="px-4 py-3 text-right">Service Fee</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @php
                                $oGrandQty = 0;
                                $oGrandTicketRevenue = 0;
                                $oGrandOrgTax = 0;
                                $oGrandNetRevenue = 0;
                                $oGrandService = 0;
                                $oGrandHandling = 0;
                                $oGrandAvailable = 0;
                                $oGrandQuota = 0;
                            @endphp
                            @foreach($tickets as $index => $ticket)
                                @php
                                    $qty = (int) ($ticket->online_qty_paid ?? 0);
                                    $totalPaid = (float) ($ticket->online_total_paid ?? 0);

                                    // Original Ticket Revenue
                                    $ticketRevenue = $qty * $ticket->price;

                                    // Platform Tax Deduction
                                    $orgFeePerUnit = $event->organizer_fee_online_type === 'percent'
                                        ? $ticket->price * ($event->organizer_fee_online / 100)
                                        : $event->organizer_fee_online;

                                    $orgTaxTotal = $qty * $orgFeePerUnit;
                                    $netRevenue = $ticketRevenue - $orgTaxTotal;

                                    $handlingOnly = $qty * $handlingFeeValue;
                                    $serviceOnly = $totalPaid > 0 ? max(0, $totalPaid - ($ticketRevenue + $handlingOnly)) : 0;

                                    $oGrandQty += $qty;
                                    $oGrandTicketRevenue += $ticketRevenue;
                                    $oGrandOrgTax += $orgTaxTotal;
                                    $oGrandNetRevenue += $netRevenue;
                                    $oGrandService += $serviceOnly;
                                    $oGrandHandling += $handlingOnly;

                                    // Stock Calculation
                                    $soldAll = $ticket->transactions_sum_quantity_paid ?? 0;
                                    $pendingAll = $ticket->transactions_sum_quantity_pending ?? 0;
                                    $currentAvailable = max(0, $ticket->quota - $soldAll - $pendingAll);

                                    $oGrandAvailable += $currentAvailable;
                                    $oGrandQuota += $ticket->quota;
                                @endphp
                                <tr class="hover:bg-primary/[0.02] transition-all group">
                                    <td class="px-4 py-3 text-xs font-bold text-gray-300">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <div
                                            class="font-black text-dark text-sm group-hover:text-primary transition-colors">
                                            {{ $ticket->name }}
                                        </div>
                                        <div class="text-[9px] text-gray-400 font-bold uppercase mt-1">@ Rp
                                            {{ number_format($ticket->price, 0, ',', '.') }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        @if($ticket->is_active)
                                            <span
                                                class="px-2 py-1 bg-emerald-100 text-emerald-700 rounded text-[10px] font-bold uppercase tracking-wider">Active</span>
                                        @else
                                            <span
                                                class="px-2 py-1 bg-red-100 text-red-700 rounded text-[10px] font-bold uppercase tracking-wider">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-3 py-1 bg-gray-100 rounded-full text-xs font-black text-dark">
                                            {{ number_format($qty) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="font-black text-dark text-sm">{{ number_format($currentAvailable) }}
                                        </div>
                                        <div class="text-[9px] text-gray-400 font-bold uppercase mt-0.5">/
                                            {{ number_format($ticket->quota) }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-dark text-sm">
                                        Rp {{ number_format($ticketRevenue, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-black text-primary text-sm">
                                        Rp {{ number_format($netRevenue, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-xs text-gray-500 font-bold">
                                        Rp {{ number_format($orgTaxTotal, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-xs text-primary font-black">
                                        Rp {{ number_format($handlingOnly, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-xs text-gray-500 font-bold">
                                        Rp {{ number_format($orgTaxTotal + $handlingOnly, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-xs text-primary font-black">
                                        Rp {{ number_format($serviceOnly, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50/80 border-t-2 border-primary/20 text-xs">
                            <tr class="font-black text-dark">
                                <td colspan="2" class="px-4 py-4 text-[11px] uppercase tracking-[0.25em] text-gray-400">
                                    Total Performance</td>
                                <td class="px-4 py-4 text-center text-lg text-dark">{{ number_format($oGrandQty) }}</td>
                                <td class="px-4 py-4 text-center text-lg text-dark">
                                    {{ number_format($oGrandAvailable) }}
                                    <span class="text-[9px] text-gray-400 block font-bold uppercase mt-0.5">/
                                        {{ number_format($oGrandQuota) }}</span>
                                </td>
                                <td class="px-4 py-4 text-right">Rp
                                    {{ number_format($oGrandTicketRevenue, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-4 text-right text-primary font-black">Rp
                                    {{ number_format($oGrandNetRevenue, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-4 text-right text-gray-500">Rp
                                    {{ number_format($oGrandOrgTax, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-4 text-right text-primary font-black text-sm">Rp
                                    {{ number_format($oGrandHandling, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-4 text-right text-gray-500 font-bold text-sm">Rp
                                    {{ number_format($oGrandOrgTax + $oGrandHandling, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-4 text-right text-primary font-black text-sm">Rp
                                    {{ number_format($oGrandService, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Reseller Sales Report Section -->
        <div class="mt-20 mb-32">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-3xl font-black text-dark tracking-tight">Reseller Sales Performance</h2>
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider mt-2">B2B Channel Distribution
                        Summary</p>
                </div>
                <div class="px-5 py-2.5 bg-primary/5 rounded-2xl border border-primary/10 flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full bg-primary animate-pulse"></div>
                    <span class="text-[9px] font-black text-primary uppercase tracking-widest">Reseller Channel</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm shadow-primary/5 border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table id="reseller-table" class="w-full text-left border-collapse">
                        <thead>
                            <tr
                                class="bg-gray-50/50 border-b border-gray-100 text-[9px] uppercase tracking-wider text-gray-400 font-black">
                                <th class="px-4 py-3">No</th>
                                <th class="px-4 py-3 font-black text-dark">Ticket Variation</th>
                                <th class="px-4 py-3 text-center">Volume</th>
                                <th class="px-4 py-3 text-right">Ticket Revenue</th>
                                <th class="px-4 py-3 text-right">Saldo</th>
                                <th class="px-4 py-3 text-right">Org Tax</th>
                                <th class="px-4 py-3 text-right">Commission Fee</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @php
                                $rGrandQty = 0;
                                $rGrandTicketRevenue = 0;
                                $rGrandOrgTax = 0;
                                $rGrandNetRevenue = 0;
                                $rGrandCommission = 0;
                            @endphp
                            @foreach($tickets as $index => $ticket)
                                @php
                                    $qty = (int) ($ticket->reseller_qty_paid ?? 0);
                                    $totalGross = (float) ($ticket->reseller_total_paid ?? 0);

                                    // Original Ticket Revenue
                                    $ticketRevenue = $qty * $ticket->price;

                                    // Platform Tax Deduction
                                    $orgFeePerUnit = $event->organizer_fee_reseller_type === 'percent'
                                        ? $ticket->price * ($event->organizer_fee_reseller / 100)
                                        : $event->organizer_fee_reseller;

                                    $orgTaxTotal = $qty * $orgFeePerUnit;
                                    $netRevenue = $ticketRevenue - $orgTaxTotal;

                                    $commissionOnly = max(0, $totalGross - $ticketRevenue);

                                    $rGrandQty += $qty;
                                    $rGrandTicketRevenue += $ticketRevenue;
                                    $rGrandOrgTax += $orgTaxTotal;
                                    $rGrandNetRevenue += $netRevenue;
                                    $rGrandCommission += $commissionOnly;
                                @endphp
                                <tr class="hover:bg-primary/[0.02] transition-all group">
                                    <td class="px-4 py-3 text-xs font-bold text-gray-300">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <div
                                            class="font-black text-dark text-sm group-hover:text-primary transition-colors">
                                            {{ $ticket->name }}
                                        </div>
                                        <div class="text-[9px] text-gray-400 font-bold uppercase mt-1">@ Rp
                                            {{ number_format($ticket->price, 0, ',', '.') }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-3 py-1 bg-gray-100 rounded-full text-xs font-black text-dark">
                                            {{ number_format($qty) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-dark text-sm">
                                        Rp {{ number_format($ticketRevenue, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-right font-black text-primary text-sm">
                                        Rp {{ number_format($netRevenue, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-xs text-gray-500 font-bold">
                                        Rp {{ number_format($orgTaxTotal, 0, ',', '.') }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-xs text-primary font-black">
                                        Rp {{ number_format($commissionOnly, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50/80 border-t-2 border-primary/20 text-xs">
                            <tr class="font-black text-dark">
                                <td colspan="2" class="px-4 py-4 text-[11px] uppercase tracking-[0.25em] text-gray-400">
                                    Total Performance</td>
                                <td class="px-4 py-4 text-center text-lg text-dark">{{ number_format($rGrandQty) }}</td>
                                <td class="px-4 py-4 text-right">Rp
                                    {{ number_format($rGrandTicketRevenue, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-4 text-right text-primary font-black">Rp
                                    {{ number_format($rGrandNetRevenue, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-4 text-right text-gray-500">Rp
                                    {{ number_format($rGrandOrgTax, 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-4 text-right text-primary font-black text-sm">Rp
                                    {{ number_format($rGrandCommission, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/xlsx/dist/xlsx.full.min.js"></script>
        <script>
            function exportToExcel() {
                const wb = XLSX.utils.book_new();

                const onlineTable = document.getElementById('online-table');
                if (onlineTable) {
                    const ws1 = XLSX.utils.table_to_sheet(onlineTable);
                    XLSX.utils.book_append_sheet(wb, ws1, "Online Sales");
                }

                const resellerTable = document.getElementById('reseller-table');
                if (resellerTable) {
                    const ws2 = XLSX.utils.table_to_sheet(resellerTable);
                    XLSX.utils.book_append_sheet(wb, ws2, "Reseller Sales");
                }

                XLSX.writeFile(wb, "Ticket_Report_{{ $event->slug }}_" + new Date().toISOString().split('T')[0] + ".xlsx");
            }
        </script>
    @endpush
</x-layouts.admin>