<x-layouts.admin title="Transaction Details | {{ $transaction->code }}">
    <!-- Header Section (Premium) -->
    <div class="mb-10 flex flex-col lg:flex-row lg:items-center justify-between gap-8">
        <div class="flex items-center gap-6">
            <a href="{{ route('admin.reports.transactions') }}"
                class="w-14 h-14 rounded-3xl bg-white border border-gray-100 flex items-center justify-center text-secondary hover:text-primary hover:shadow-2xl hover:-translate-x-1 transition-all active:scale-95 group">
                <svg class="w-6 h-6 transform group-hover:scale-110 transition-transform" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <div class="flex items-center gap-3 mb-1">
                    <h2 class="text-4xl font-black text-dark tracking-tighter">Transaction Dossier</h2>
                    <span
                        class="px-3 py-1 bg-primary/5 text-primary text-[10px] font-black uppercase tracking-[0.2em] rounded-full border border-primary/10">ID:{{ $transaction->id }}</span>
                    @if($transaction->reseller_id)
                        <span class="px-3 py-1 bg-purple-50 text-purple-600 text-[10px] font-black uppercase tracking-[0.2em] rounded-full border border-purple-100">Reseller: {{ $transaction->reseller->name }}</span>
                    @else
                        <span class="px-3 py-1 bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-[0.2em] rounded-full border border-blue-100">Online Checkout</span>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">RECORD REFERENCE</span>
                    <span
                        class="font-mono text-sm font-black text-primary bg-primary/5 px-2 py-0.5 rounded-lg border border-primary/5">{{ $transaction->code }}</span>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4">
            @if($transaction->status === 'paid')
                <a href="{{ route('payment.success', $transaction->code) }}" target="_blank"
                    class="hidden md:flex items-center gap-2 bg-white text-emerald-600 px-5 py-3 rounded-2xl border border-emerald-100 hover:bg-emerald-50 hover:border-emerald-200 transition-all font-bold text-xs shadow-sm group h-14">
                    <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    <span>View Success Page</span>
                </a>
            @endif

            @if($transaction->status === 'paid')
                <div
                    class="flex items-center gap-4 bg-white p-2 pr-6 rounded-[2rem] border border-gray-100 shadow-xl shadow-emerald-500/5">
                    <div
                        class="w-12 h-12 rounded-2xl bg-emerald-500 flex items-center justify-center text-white shadow-lg shadow-emerald-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.2em]">Settled</div>
                        <div class="text-sm font-black text-dark tracking-tight">Payment Verified</div>
                    </div>
                </div>
            @elseif($transaction->status === 'pending')
                <div
                    class="flex items-center gap-4 bg-white p-2 pr-6 rounded-[2rem] border border-gray-100 shadow-xl shadow-amber-500/5">
                    <div
                        class="w-12 h-12 rounded-2xl bg-amber-400 flex items-center justify-center text-white animate-pulse shadow-lg shadow-amber-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-[10px] font-black text-amber-600 uppercase tracking-[0.2em]">Awaiting</div>
                        <div class="text-sm font-black text-dark tracking-tight">Processing Entry</div>
                    </div>
                </div>
            @else
                <div
                    class="flex items-center gap-4 bg-white p-2 pr-6 rounded-[2rem] border border-gray-100 shadow-xl shadow-rose-500/5">
                    <div
                        class="w-12 h-12 rounded-2xl bg-rose-500 flex items-center justify-center text-white shadow-lg shadow-rose-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div>
                        <div class="text-[10px] font-black text-rose-600 uppercase tracking-[0.2em]">Voided</div>
                        <div class="text-sm font-black text-dark tracking-tight">Record Terminated</div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if($transaction->status === 'paid')
        <!-- Top Level Spotlight: Digital Access Pass -->
        <div class="mb-12">
            <div
                class="bg-white rounded-[3rem] shadow-2xl shadow-primary/5 border border-gray-100 p-2 overflow-hidden relative group">
                <div
                    class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-full -mr-32 -mt-32 blur-3xl transition-all group-hover:scale-110">
                </div>
                <div class="flex flex-col lg:flex-row items-center gap-12 p-10 lg:p-16 relative z-10">
                    <div class="flex-shrink-0">
                        @php
                            $qrCode = (new Endroid\QrCode\Builder\Builder(
                                writer: new Endroid\QrCode\Writer\SvgWriter(),
                                validateResult: false,
                                data: $transaction->code,
                                encoding: new Endroid\QrCode\Encoding\Encoding('UTF-8'),
                                errorCorrectionLevel: Endroid\QrCode\ErrorCorrectionLevel::High,
                                size: 280,
                                margin: 10,
                                foregroundColor: new Endroid\QrCode\Color\Color(17, 24, 39)
                            ))->build();
                        @endphp
                        <div class="relative">
                            <div class="absolute inset-0 bg-primary/10 blur-[40px] rounded-full scale-75 animate-pulse">
                            </div>
                            <div
                                class="relative p-6 bg-white rounded-[2.5rem] border-2 border-gray-50 shadow-2xl flex items-center justify-center group-hover:scale-[1.02] transition-transform duration-500">
                                <img src="{{ $qrCode->getDataUri() }}" class="w-56 h-56 lg:w-64 lg:h-64">
                                <div
                                    class="absolute -bottom-4 bg-dark text-white text-[9px] font-black px-4 py-2 rounded-full uppercase tracking-widest shadow-xl">
                                    Verified Identity</div>
                            </div>
                        </div>
                    </div>

                    <div class="flex-1 flex flex-col items-center lg:items-start text-center lg:text-left">
                        <div class="text-[10px] font-black text-primary uppercase tracking-[0.3em] mb-4">Digital Access
                            Credential</div>
                        <h3 class="text-5xl font-black text-dark tracking-tighter leading-tight mb-6">This access pass is
                            <span class="text-emerald-500">active.</span></h3>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-8 w-full max-w-2xl">
                            <div class="space-y-1">
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Entry Key</div>
                                <div class="text-sm font-mono font-black text-dark uppercase select-all">
                                    {{ $transaction->code }}</div>
                            </div>
                            <div class="space-y-1">
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Verification
                                    Status</div>
                                <div class="flex items-center gap-2">
                                    <span
                                        class="w-2 h-2 rounded-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.5)]"></span>
                                    <span class="text-sm font-black text-dark uppercase tracking-tighter">Settled</span>
                                </div>
                            </div>
                            <div class="space-y-1 col-span-2 md:col-span-1">
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Authorized By
                                </div>
                                <div class="text-sm font-black text-dark uppercase tracking-tighter">System Intelligence
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Content Architecture -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
        <!-- Main Intel: Dossier & Matrix -->
        <div class="lg:col-span-8 space-y-10">
            <!-- Customer Dossier Card -->
            <div class="bg-white rounded-[3rem] shadow-xl shadow-primary/5 border border-gray-100 p-10 lg:p-12">
                <div class="flex items-center justify-between mb-12">
                    <h3 class="text-2xl font-black text-dark tracking-tight flex items-center gap-4">
                        <span class="w-2 h-10 bg-primary rounded-full"></span>
                        Customer Profile
                    </h3>
                    <div
                        class="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center text-secondary border border-gray-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                    <div class="space-y-1 group">
                        <div
                            class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] group-hover:text-primary transition-colors">
                            Identified Name</div>
                        <div class="text-2xl font-black text-dark tracking-tight">{{ $transaction->name }}</div>
                    </div>
                    <div class="space-y-1 group">
                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] group-hover:text-primary transition-colors">Digital Handle</div>
                        <a href="mailto:{{ $transaction->email }}" class="block text-2xl font-bold text-dark tracking-tight hover:text-primary transition-colors cursor-pointer">{{ $transaction->email }}</a>
                        <a href="tel:{{ $transaction->phone }}" class="block text-xs font-black text-primary uppercase tracking-widest mt-1 hover:opacity-70 transition-opacity cursor-pointer">{{ $transaction->phone }}</a>
                    </div>
                    <div class="space-y-1">
                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">National ID (NIK)
                        </div>
                        <div
                            class="text-xl font-mono font-black text-dark bg-gray-50 px-4 py-3 rounded-2xl border border-gray-100 inline-block tracking-tighter">
                            {{ $transaction->nik ?? 'NOT PROVIDED' }}</div>
                    </div>
                    <div class="space-y-1">
                        <div class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Demographics
                            Architecture</div>
                        <div class="flex items-center gap-3 mt-2">
                            <div
                                class="px-5 py-2.5 bg-dark text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-dark/10">
                                {{ $transaction->gender ?? '-' }}</div>
                            <div
                                class="px-5 py-2.5 bg-gray-50 text-dark rounded-xl text-xs font-black uppercase tracking-widest border border-gray-100">
                                {{ $transaction->city ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Event Matrix Card -->
            <div
                class="bg-white rounded-[3rem] shadow-xl shadow-primary/5 border border-gray-100 p-10 lg:p-12 overflow-hidden relative">
                <div class="absolute top-0 left-0 w-2 h-full bg-emerald-500/20"></div>
                <div class="flex items-center justify-between mb-12">
                    <h3 class="text-2xl font-black text-dark tracking-tight flex items-center gap-4">
                        <span class="w-2 h-10 bg-emerald-500 rounded-full"></span>
                        Event Matrix
                    </h3>
                </div>

                <div class="flex flex-col md:flex-row gap-12">
                    <div class="w-full md:w-2/5">
                        <div
                            class="aspect-[4/5] rounded-[2.5rem] overflow-hidden shadow-[0_30px_60px_-15px_rgba(0,0,0,0.15)] border-8 border-white bg-gray-50">
                            @if($transaction->event && $transaction->event->thumbnail_path)
                                <img src="{{ Str::startsWith($transaction->event->thumbnail_path, ['http', 'https']) ? $transaction->event->thumbnail_path : (file_exists(public_path($transaction->event->thumbnail_path)) ? asset($transaction->event->thumbnail_path) : asset('storage/' . $transaction->event->thumbnail_path)) }}"
                                    class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center opacity-20 bg-gray-100">
                                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                        </path>
                                    </svg>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="flex-1 space-y-10">
                        <div>
                            <div class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.3em] mb-2">
                                INTELLIGENCE SUBJECT</div>
                            <h4 class="text-4xl font-black text-dark tracking-tighter leading-none">
                                {{ $transaction->event->name ?? 'SYSTEM ARCHIVE' }}</h4>
                        </div>

                        <div class="grid grid-cols-2 gap-10">
                            <div class="group">
                                <div
                                    class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 group-hover:text-primary">
                                    Tier Logic</div>
                                <div class="text-xl font-black text-dark">{{ $transaction->ticket->name ?? 'N/A' }}
                                </div>
                                <div class="text-xs font-bold text-primary mt-0.5">@ Rp
                                    {{ number_format($transaction->ticket->price ?? 0, 0, ',', '.') }}</div>
                            </div>
                            <div class="group">
                                <div
                                    class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1 group-hover:text-primary">
                                    Volume Matrix</div>
                                <div class="text-3xl font-black text-dark">x{{ $transaction->quantity }}</div>
                                <div class="text-[10px] font-black text-secondary uppercase tracking-[0.2em]">Units
                                    Confirmed</div>
                            </div>
                        </div>

                        <div class="p-8 bg-gray-50 rounded-[2rem] border border-gray-100/50">
                            <div class="text-[10px] font-black text-secondary uppercase tracking-[0.2em] mb-3">Protocol
                                Overview</div>
                            <p class="text-sm font-medium text-secondary/80 leading-relaxed italic">
                                "{{ Str::limit(strip_tags($transaction->event->description) ?? 'No subject protocol details recorded for this archive.', 180) }}"
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Financial & Timeline -->
        <div class="lg:col-span-4 space-y-10">
            <!-- Settlement Insight (Creative Dark Card) -->
            <div
                class="bg-dark rounded-[3rem] shadow-[0_40px_80px_-20px_rgba(17,24,39,0.3)] overflow-hidden text-white relative">
                <div class="absolute inset-0 bg-gradient-to-br from-primary/20 to-transparent"></div>
                <div class="relative p-10">
                    <div
                        class="text-[10px] font-black text-teal-400 uppercase tracking-[0.3em] mb-8 border-b border-white/5 pb-4 text-center">
                        Settlement Breakout</div>

                    <div class="space-y-6 mb-10">
                        <div class="flex justify-between items-center group">
                            <span
                                class="text-[10px] font-black text-teal-100/30 uppercase tracking-[0.2em] group-hover:text-teal-400 transition-colors">Core
                                Asset</span>
                            <span class="text-base font-bold text-teal-50">Rp
                                {{ number_format($transaction->quantity * ($transaction->ticket->price ?? 0), 0, ',', '.') }}</span>
                        </div>
                        @if(!$transaction->reseller_id)
                        <div class="flex justify-between items-center group">
                            <span
                                class="text-[10px] font-black text-teal-100/30 uppercase tracking-[0.2em] group-hover:text-teal-400 transition-colors">Handling
                                Fee</span>
                            <span class="text-base font-bold text-teal-50">Rp
                                {{ number_format($handlingTotal, 0, ',', '.') }}</span>
                        </div>
                        @endif
                        <div class="flex justify-between items-center group/item">
                            <span
                                class="text-[10px] font-black text-teal-100/30 uppercase tracking-[0.2em] group-hover/item:text-teal-400 transition-colors">Gateway
                                IQ</span>
                            <span class="text-base font-bold text-teal-50">Rp
                                {{ number_format($serviceFee, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="pt-10 border-t-2 border-dashed border-white/10 mb-8">
                        <div
                            class="text-[10px] font-black text-emerald-400 uppercase tracking-[0.4em] mb-4 text-center opacity-70">
                            Unified Total</div>
                        <div class="text-5xl font-black text-white tracking-tighter text-center">
                            <span
                                class="text-2xl align-top mr-1 text-teal-400 opacity-50">Rp</span>{{ number_format($transaction->total_price, 0, ',', '.') }}
                        </div>
                    </div>

                    @if($transaction->reseller_id)
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-6 bg-white/[0.03] rounded-3xl text-center border border-white/5">
                                <div class="text-[9px] font-black text-teal-500 uppercase tracking-widest mb-1 italic">Reseller Agent</div>
                                <div class="text-xs font-black text-white/90 uppercase tracking-widest truncate" title="{{ $transaction->reseller->name }}">
                                    {{ Str::limit($transaction->reseller->name, 15) }}
                                </div>
                            </div>
                            <div class="p-6 bg-white/[0.03] rounded-3xl text-center border border-white/5">
                                <div class="text-[9px] font-black text-teal-500 uppercase tracking-widest mb-1 italic">Channel</div>
                                <div class="text-xs font-black text-white/90 uppercase tracking-widest">
                                    {{ $transaction->payment_type ?? 'MANUAL' }}</div>
                            </div>
                        </div>
                    @else
                        <div class="p-6 bg-white/[0.03] rounded-3xl text-center border border-white/5">
                            <div class="text-[9px] font-black text-teal-500 uppercase tracking-widest mb-1 italic">Channel
                                Strategy</div>
                            <div class="text-sm font-black text-white/90 uppercase tracking-widest">
                                {{ $transaction->payment_type ?? 'PENDING' }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Lifecycle Intelligence -->
            <div class="bg-white rounded-[3rem] shadow-xl shadow-primary/5 border border-gray-100 p-10">
                <h3 class="text-sm font-black text-dark uppercase tracking-[0.25em] mb-10 flex items-center gap-3">
                    <span class="w-2 h-2 rounded-full bg-primary mb-0.5"></span>
                    Ledger Lifecycle
                </h3>
                <div class="space-y-12 relative">
                    <div class="absolute left-[11px] top-2 bottom-2 w-0.5 bg-gray-100"></div>

                    <!-- Stage 1 -->
                    <div class="flex gap-8 relative group">
                        <div
                            class="w-6 h-6 rounded-full bg-white border-4 border-primary group-hover:scale-125 transition-transform shadow-lg z-10">
                        </div>
                        <div>
                            <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Entry
                                Initialized</div>
                            <div class="text-sm font-black text-dark mt-0.5">Record established</div>
                            <div class="text-[10px] font-bold text-primary/60 mt-1 uppercase tracking-tighter">
                                {{ $transaction->created_at->format('d M Y | H:i:s') }}</div>
                        </div>
                    </div>

                    <!-- Stage 2 -->
                    @if($transaction->status === 'paid')
                        <div class="flex gap-8 relative group">
                            <div
                                class="w-6 h-6 rounded-full bg-white border-4 border-emerald-500 group-hover:scale-125 transition-transform shadow-lg z-10">
                            </div>
                            <div>
                                <div class="text-[10px] font-black text-emerald-500 uppercase tracking-widest">Global
                                    Verified</div>
                                <div class="text-sm font-black text-dark mt-0.5">Settlement confirmed</div>
                                <div class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-tighter">
                                    {{ $transaction->updated_at->format('d M Y | H:i:s') }}</div>
                                <div
                                    class="mt-4 px-4 py-2.5 bg-gray-50 rounded-2xl border border-gray-100 text-[10px] font-mono font-black text-dark break-all leading-relaxed shadow-inner">
                                    MID: {{ $transaction->midtrans_transaction_id ?? 'INT-SEQ-9921' }}
                                </div>
                            </div>
                        </div>
                    @elseif($transaction->status === 'pending')
                        <div class="flex gap-8 relative opacity-40 group">
                            <div
                                class="w-6 h-6 rounded-full bg-white border-4 border-amber-400 group-hover:scale-125 transition-transform shadow-lg z-10 animate-pulse">
                            </div>
                            <div>
                                <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest">In Suspension
                                </div>
                                <div class="text-sm font-black text-dark mt-0.5">Awaiting gateway sync</div>
                            </div>
                        </div>
                    @else
                        <div class="flex gap-8 relative group">
                            <div
                                class="w-6 h-6 rounded-full bg-white border-4 border-rose-500 group-hover:scale-125 transition-transform shadow-lg z-10">
                            </div>
                            <div>
                                <div class="text-[10px] font-black text-rose-500 uppercase tracking-widest">Entry Terminated
                                </div>
                                <div class="text-sm font-black text-dark mt-0.5">Operational pulse lost</div>
                                <div class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-tighter">
                                    {{ $transaction->updated_at->format('d M Y | H:i:s') }}</div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- System Key Intelligence -->
            <div class="bg-gray-50 rounded-[2.5rem] border border-gray-100 p-8 group">
                <div class="flex items-center gap-3 mb-5">
                    <div
                        class="w-10 h-10 rounded-xl bg-white flex items-center justify-center text-secondary border border-gray-100 group-hover:text-primary transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                            </path>
                        </svg>
                    </div>
                    <h5 class="text-[10px] font-black text-secondary uppercase tracking-[0.2em]">Snap System Key</h5>
                </div>
                <div
                    class="bg-white px-6 py-5 rounded-[2rem] border border-gray-100 font-mono text-[10px] font-black text-primary break-all shadow-inner select-all leading-relaxed">
                    {{ $transaction->snap_token ?? 'UNSENT-SEQ-ARCHIVE-0X9' }}
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
