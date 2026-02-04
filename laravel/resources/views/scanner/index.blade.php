<x-layouts.scanner>
    <div x-data="scannerApp()" class="h-full flex flex-col pt-safe" x-cloak>

        <!-- Header -->
        <header
            class="bg-white border-b border-gray-100 h-24 flex items-center justify-between px-4 md:px-8 sticky top-0 z-20">
            <!-- Left: Toggle Button -->
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = !sidebarOpen"
                    class="lg:hidden p-2.5 rounded-xl bg-gray-50 text-gray-500 hover:bg-gray-100 hover:text-primary transition-all duration-200 focus:outline-none group">
                    <!-- Hamburger Icon (Show when sidebar is closed) -->
                    <svg x-show="!sidebarOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>

                <div class="hidden sm:block">
                    <h1 class="text-lg font-extrabold text-slate-900 tracking-tight leading-tight">Entry Scanner</h1>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">System
                            Online</span>
                    </div>
                </div>
            </div>

            <!-- Right: Logout / Profile -->
            <div class="flex items-center gap-4">
                <div class="text-right hidden md:block">
                    <p class="text-sm font-bold text-gray-700 leading-none">{{ auth()->user()->name }}</p>
                    <p class="text-[10px] text-gray-400 mt-1 font-black uppercase tracking-widest">
                        {{ auth()->user()->role }}
                    </p>
                </div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-12 h-12 bg-rose-50 text-rose-500 rounded-full flex items-center justify-center text-sm font-bold ring-2 ring-white shadow-sm hover:bg-rose-500 hover:text-white transition-all duration-200"
                        title="Log Out">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </header>

        <!-- Form Control -->
        <div class="p-6 space-y-4">
            <div class="space-y-1.5">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Current
                    Event</label>
                <div class="relative" x-data="{ open: false }">
                    <button type="button" @click="open = !open" @click.away="open = false"
                        class="w-full flex items-center justify-between bg-white border border-slate-200 rounded-2xl py-4 px-6 text-sm font-bold text-slate-900 focus:outline-none focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all shadow-sm">
                        <span
                            x-text="selectedEventId ? document.querySelector('[data-event-id=\''+selectedEventId+'\']')?.innerText : 'Select an Event...'"
                            :class="!selectedEventId ? 'text-slate-400' : 'text-slate-900'"></span>
                        <svg class="w-5 h-5 text-slate-400 transition-transform duration-200"
                            :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div x-show="open" x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                        class="absolute z-50 w-full mt-2 bg-white rounded-3xl shadow-3xl border border-slate-100 overflow-hidden">
                        <div class="px-6 py-4 bg-slate-50 border-b border-slate-100">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Select Event
                                to Scan</span>
                        </div>
                        <div class="py-2 max-h-60 overflow-y-auto">
                            <button type="button" @click="selectedEventId = ''; stopScanner(); open = false"
                                class="w-full px-6 py-3.5 text-left hover:bg-slate-50 text-sm font-bold text-slate-400">None
                                / Stop Scanner</button>
                            @foreach($events as $event)
                                <button type="button" data-event-id="{{ $event->id }}"
                                    @click="selectedEventId = '{{ $event->id }}'; stopScanner(); open = false"
                                    class="w-full px-6 py-3.5 text-left hover:bg-primary/5 text-sm font-bold text-slate-900 border-l-4 border-transparent hover:border-primary transition-all">
                                    {{ $event->name }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scanning Hub -->
        <main class="flex-1 px-6 flex flex-col gap-6 min-h-0">

            <!-- Idle State -->
            <div x-show="!selectedEventId"
                class="flex-1 flex flex-col items-center justify-center text-center p-8 bg-white/50 border-2 border-dashed border-slate-200 rounded-[2.5rem]">
                <div
                    class="w-16 h-16 bg-white rounded-3xl flex items-center justify-center border border-slate-100 shadow-sm mb-6">
                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 17h.01M8 11h8a2 2 0 012 2v8a2 2 0 01-2 2H8a2 2 0 01-2-2v-8a2 2 0 012-2z" />
                    </svg>
                </div>
                <h2 class="text-sm font-black text-slate-900 uppercase tracking-widest mb-1">Authorization Required</h2>
                <p class="text-xs text-slate-400 font-medium max-w-[180px]">Choose an active event from the list above
                    to start scanning.</p>
            </div>

            <!-- Active Scanning State -->
            <div x-show="selectedEventId" class="flex-1 flex flex-col gap-6 min-h-0" x-transition>

                <!-- Viewport Container -->
                <div
                    class="relative flex-1 bg-black rounded-[2.5rem] overflow-hidden shadow-2xl group border-[6px] border-white shadow-slate-200">
                    <div id="reader" class="w-full h-full object-cover"></div>

                    <!-- Scanning Line -->
                    <div x-show="isScanning"
                        class="absolute inset-x-0 h-0.5 bg-primary/40 animate-scan pointer-events-none z-20"></div>

                    <!-- Manual Entry Button Overlay -->
                    <div class="absolute bottom-6 inset-x-0 flex justify-center z-30">
                        <button @click="toggleManual()"
                            class="px-6 py-3 bg-white/10 backdrop-blur-md border border-white/20 rounded-full text-[10px] font-black text-white uppercase tracking-[0.2em] shadow-lg hover:bg-white/20 transition-all">Manual
                            Code</button>
                    </div>

                    <!-- Manual Input Panel -->
                    <div x-show="isManual" x-transition:enter="transition ease-out duration-300 transform"
                        x-transition:enter-start="translate-y-full"
                        class="absolute inset-0 z-[40] bg-white flex flex-col p-8 justify-center items-center text-center">
                        <div class="w-full max-w-xs space-y-8">
                            <div>
                                <h3 class="text-lg font-black text-slate-900 uppercase tracking-widest">Manual Entry
                                </h3>
                                <p class="text-xs text-slate-400 font-bold mt-1 uppercase tracking-wider">Type ticket
                                    reference</p>
                            </div>
                            <input type="text" x-model="manualCode" placeholder="Reference ID"
                                class="w-full border-b-4 border-slate-100 py-4 text-center text-2xl font-black text-slate-900 focus:outline-none focus:border-primary transition-all uppercase placeholder:text-slate-100">
                            <div class="grid grid-cols-2 gap-4">
                                <button type="button" @click="isManual = false; if(selectedEventId) initScanner()"
                                    class="py-4 text-xs font-black text-slate-400 uppercase tracking-widest">Cancel</button>
                                <button type="button" @click="verifyCode(manualCode)"
                                    class="py-4 bg-primary text-white rounded-2xl text-xs font-black uppercase tracking-widest shadow-lg shadow-primary/20">Check</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Control -->
                <div class="pb-6">
                    <button @click="toggleScanner()"
                        class="w-full py-5 rounded-[1.5rem] font-black uppercase tracking-widest text-xs transition-all flex items-center justify-center gap-3 shadow-xl"
                        :class="isScanning ? 'bg-rose-50 text-rose-600 border border-rose-100' : 'bg-primary text-white'">
                        <span x-text="isScanning ? 'Disable Camera' : 'Start QR Scanner'"></span>
                        <svg x-show="!isScanning" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </button>
                </div>
            </div>
        </main>

        <!-- Result Backdrop & Modal -->
        <template x-if="scanResult">
            <div class="fixed inset-0 z-[100] p-6 flex items-center justify-center" x-transition>
                <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-md" @click="resetScan()"></div>

                <div class="relative w-full max-w-sm bg-white rounded-[3rem] p-10 flex flex-col items-center text-center shadow-3xl overflow-hidden"
                    x-transition:enter="transition-all duration-500 ease-out transform"
                    x-transition:enter-start="scale-50 rotate-3 opacity-0">

                    <!-- Status Indicator -->
                    <div class="w-24 h-24 rounded-[2.5rem] flex items-center justify-center mb-8 shadow-2xl group animate-bounce-short"
                        :class="{
                            'bg-emerald-50 text-emerald-500': scanResult.status === 'success',
                            'bg-rose-50 text-rose-500': scanResult.status === 'warning' || scanResult.status === 'error',
                            'bg-amber-50 text-amber-500': scanResult.status === 'pending'
                        }">
                        <svg x-show="scanResult.status === 'success'" class="w-12 h-12" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7" />
                        </svg>
                        <svg x-show="scanResult.status === 'warning'" class="w-12 h-12" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <svg x-show="scanResult.status === 'pending'" class="w-12 h-12" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <svg x-show="scanResult.status === 'error'" class="w-12 h-12" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="4"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>

                    <div class="mb-10">
                        <h2 class="text-2xl font-black uppercase tracking-tight"
                            :class="{
                                'text-slate-900': scanResult.status === 'success' || scanResult.status === 'pending',
                                'text-rose-500': scanResult.status === 'warning' || scanResult.status === 'error'
                            }"
                            x-text="scanResult.status === 'success' ? 'Checked In' : scanResult.status === 'warning' ? 'Already Scanned' : scanResult.status === 'pending' ? 'Valid Ticket' : 'Invalid'">
                        </h2>
                        <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-widest"
                            x-text="scanResult.message"></p>
                    </div>

                    <!-- Expanded Data Hub -->
                    <div x-show="scanResult.data"
                        class="w-full space-y-4 mb-8 text-left max-h-[40vh] overflow-y-auto pr-2 scrollbar-hide">
                        <!-- Ticket Section -->
                        <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100">
                            <h4 class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-4">Ticket
                                Registry</h4>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-[10px] font-bold text-slate-400">Ref Code</span>
                                    <span
                                        class="text-xs font-black text-slate-900 tracking-wider font-mono bg-white px-2 py-0.5 rounded"
                                        x-text="scanResult.data.code"></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-[10px] font-bold text-slate-400">Category</span>
                                    <span class="text-xs font-black text-primary uppercase tracking-widest"
                                        x-text="scanResult.data.ticket_type"></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-[10px] font-bold text-slate-400">Quantity</span>
                                    <span class="text-sm font-black text-slate-900"
                                        x-text="'x' + scanResult.data.quantity"></span>
                                </div>
                                <!-- Show redeemed timestamp if already scanned -->
                                <div x-show="scanResult.already_redeemed"
                                    class="flex justify-between items-center pt-2 border-t border-slate-200">
                                    <span class="text-[10px] font-bold text-rose-500">Scanned At</span>
                                    <span class="text-xs font-black text-rose-500"
                                        x-text="scanResult.data.redeemed_at"></span>
                                </div>
                                <!-- Show scanner name if already scanned -->
                                <div x-show="scanResult.already_redeemed && scanResult.data.scanned_by"
                                    class="flex justify-between items-center">
                                    <span class="text-[10px] font-bold text-rose-500">Scanned By</span>
                                    <span class="text-xs font-black text-rose-500"
                                        x-text="scanResult.data.scanned_by"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Personal Section -->
                        <div class="bg-slate-50 rounded-2xl p-5 border border-slate-100">
                            <h4 class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-4">Identity
                                Profile</h4>
                            <div class="space-y-4">
                                <div>
                                    <span
                                        class="text-[9px] font-black text-slate-300 uppercase tracking-widest block mb-1">Full
                                        Name</span>
                                    <span class="text-sm font-black text-slate-900"
                                        x-text="scanResult.data.name"></span>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <span
                                            class="text-[9px] font-black text-slate-300 uppercase tracking-widest block mb-1">NIK</span>
                                        <span class="text-[11px] font-bold text-slate-700 font-mono"
                                            x-text="scanResult.data.nik"></span>
                                    </div>
                                    <div>
                                        <span
                                            class="text-[9px] font-black text-slate-300 uppercase tracking-widest block mb-1">Gender</span>
                                        <span class="text-[11px] font-bold text-slate-700 uppercase"
                                            x-text="scanResult.data.gender"></span>
                                    </div>
                                </div>
                                <div class="h-px bg-slate-200/50"></div>
                                <div>
                                    <span
                                        class="text-[9px] font-black text-slate-300 uppercase tracking-widest block mb-1">Contact
                                        Details</span>
                                    <div class="space-y-1">
                                        <span class="text-[11px] font-bold text-slate-700 block"
                                            x-text="scanResult.data.email"></span>
                                        <span class="text-[11px] font-bold text-slate-700 block"
                                            x-text="scanResult.data.phone"></span>
                                    </div>
                                </div>
                                <div>
                                    <span
                                        class="text-[9px] font-black text-slate-300 uppercase tracking-widest block mb-1">Location</span>
                                    <span class="text-[11px] font-bold text-slate-700"
                                        x-text="scanResult.data.city"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="w-full space-y-3">
                        <!-- If pending (not redeemed yet), show CHECK IN button -->
                        <button x-show="scanResult.status === 'pending' && !scanResult.already_redeemed"
                            @click="confirmCheckIn()"
                            class="w-full py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] transition-all hover:scale-105 active:scale-95 shadow-xl bg-emerald-500 text-white shadow-emerald-500/20">
                            Confirm Check-In
                        </button>

                        <!-- If already redeemed or error, show Next Scan button -->
                        <button x-show="scanResult.status !== 'pending' || scanResult.already_redeemed"
                            @click="resetScan()"
                            class="w-full py-5 rounded-2xl font-black text-xs uppercase tracking-[0.2em] transition-all hover:scale-105 active:scale-95 shadow-xl"
                            :class="{
                                'bg-emerald-500 text-white shadow-emerald-500/20': scanResult.status === 'success',
                                'bg-rose-500 text-white shadow-rose-500/20': scanResult.status === 'warning',
                                'bg-rose-500 text-white shadow-rose-500/20': scanResult.status === 'error'
                            }">
                            Next Scan
                        </button>

                        <!-- Cancel button (always available) -->
                        <button @click="resetScan()"
                            class="w-full py-3 text-xs font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-colors">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </template>
    </div>

    @push('scripts')
        <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
        <script>
            function scannerApp() {
                return {
                    selectedEventId: '',
                    manualCode: '',
                    isScanning: false,
                    isManual: false,
                    html5QrCode: null,
                    scanResult: null,

                    async initScanner() {
                        const reader = document.getElementById('reader');
                        this.html5QrCode = new Html5Qrcode("reader");
                        const config = {
                            fps: 20,
                            qrbox: { width: 220, height: 220 },
                            aspectRatio: 1.0,
                        };

                        try {
                            await this.html5QrCode.start(
                                { facingMode: "environment" },
                                config,
                                (text) => this.onScanSuccess(text),
                                () => { }
                            );
                            this.isScanning = true;
                            this.isManual = false;
                        } catch (err) {
                            alert("Camera Access Denied");
                        }
                    },

                    async stopScanner() {
                        if (this.html5QrCode && this.isScanning) {
                            await this.html5QrCode.stop();
                            this.isScanning = false;
                        }
                    },

                    toggleScanner() {
                        if (this.isScanning) this.stopScanner();
                        else {
                            if (!this.selectedEventId) return alert('Select an event');
                            this.initScanner();
                        }
                    },

                    toggleManual() {
                        this.stopScanner();
                        this.isManual = true;
                    },

                    onScanSuccess(code) {
                        if (this.scanResult) return;
                        this.verifyCode(code);
                        this.stopScanner();
                        if (navigator.vibrate) navigator.vibrate(50);
                    },

                    async verifyCode(code) {
                        if (!code) return;
                        try {
                            const response = await fetch("{{ route('scanner.verify') }}", {
                                method: "POST",
                                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                                body: JSON.stringify({ code: code, event_id: this.selectedEventId })
                            });
                            this.scanResult = await response.json();
                            if (navigator.vibrate) navigator.vibrate(this.scanResult.status === 'success' ? 100 : [100, 100]);
                        } catch (e) {
                            this.scanResult = { status: 'error', message: 'Connection issue' };
                        }
                    },

                    async confirmCheckIn() {
                        if (!this.scanResult || !this.scanResult.data) return;

                        try {
                            const response = await fetch("{{ route('scanner.redeem') }}", {
                                method: "POST",
                                headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                                body: JSON.stringify({
                                    transaction_id: this.scanResult.data.transaction_id,
                                    event_id: this.selectedEventId
                                })
                            });
                            const result = await response.json();

                            if (result.status === 'success') {
                                this.scanResult.status = 'success';
                                this.scanResult.message = result.message;
                                this.scanResult.already_redeemed = true;
                                if (navigator.vibrate) navigator.vibrate(200);
                            } else {
                                this.scanResult.status = 'error';
                                this.scanResult.message = result.message;
                                if (navigator.vibrate) navigator.vibrate([100, 50, 100]);
                            }
                        } catch (e) {
                            this.scanResult.status = 'error';
                            this.scanResult.message = 'Failed to check in';
                        }
                    },

                    resetScan() {
                        this.scanResult = null;
                        this.manualCode = '';
                        this.isManual = false;
                        if (this.selectedEventId) this.initScanner();
                    }
                }
            }
        </script>

        <style>
            @keyframes scan-line {
                0% {
                    top: 10%;
                    opacity: 0;
                }

                50% {
                    opacity: 1;
                }

                100% {
                    top: 90%;
                    opacity: 0;
                }
            }

            .animate-scan {
                animation: scan-line 2s infinite ease-in-out;
            }

            @keyframes bounce-short {

                0%,
                100% {
                    transform: translateY(0);
                }

                50% {
                    transform: translateY(-5px);
                }
            }

            .animate-bounce-short {
                animation: bounce-short 1s ease-in-out infinite;
            }

            .scrollbar-hide::-webkit-scrollbar {
                display: none;
            }

            .scrollbar-hide {
                -ms-overflow-style: none;
                scrollbar-width: none;
            }

            #reader video {
                object-fit: cover !important;
                border-radius: 2rem !important;
            }

            #reader__status_span,
            #reader__dashboard {
                display: none !important;
            }
        </style>
    @endpush
</x-layouts.scanner>