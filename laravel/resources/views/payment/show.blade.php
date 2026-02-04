<x-layouts.app :title="__('common.payment')">
    @php
        // Base values
        $subtotal = $transaction->ticket->price * $transaction->quantity;

        // Fee Logic
        $isReseller = $transaction->reseller_id ? true : false;
        $handlingFee = 0;

        if ($isReseller) {
            // Reseller Commission / Fee
            if ($transaction->event->reseller_fee_type === 'fixed') {
                $handlingFee = $transaction->event->reseller_fee_value;
            } else {
                $handlingFee = ($transaction->ticket->price * ($transaction->event->reseller_fee_value / 100));
            }
        } else {
            // Standard Standard Fee
            $handlingFee = (int) \App\Models\Setting::getValue('handling_fee', 0);
        }

        $baseTotal = $subtotal + ($handlingFee * $transaction->quantity);

        // Fee Constants
        $qrisPercent = (float) \App\Models\Setting::getValue('fee_qris_percent', 0);
        $bankFixed = (int) \App\Models\Setting::getValue('fee_bank_fixed', 0);

        // Potential Fees
        $qrisFee = floor($baseTotal * ($qrisPercent / 100)); // 0.7% of base total
        $serviceFee = 0;
    @endphp
    <div class="bg-white min-h-screen pt-28 pb-20 px-4 sm:px-6">
        <div class="max-w-7xl mx-auto">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-20" x-data="{
                paymentMethod: null,
                baseTotal: {{ $baseTotal }},
                qrisFee: {{ $qrisFee }},
                bankFee: {{ $bankFixed }},
                get currentTotal() {
                    if (this.paymentMethod === 'qris') {
                        return this.baseTotal + this.qrisFee;
                    } else if (this.paymentMethod === 'bank_transfer') {
                        return this.baseTotal + this.bankFee;
                    }
                    return this.baseTotal;
                },
                get displayFee() {
                    if (this.paymentMethod === 'qris') {
                        return this.qrisFee;
                    }
                    return this.bankFee;
                },
                get feeLabel() {
                    if (this.paymentMethod === 'qris') {
                        return 'QRIS Fee (0.7%)';
                    }
                    return 'Admin Fee';
                }
            }">

                <!-- LEFT COLUMN: Payment Action & Event Info -->
                <div class="lg:col-span-7">

                    <div
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/5 text-primary text-xs font-bold uppercase tracking-widest mb-6">
                        <span class="relative flex h-2 w-2">
                            <span
                                class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                        </span>
                        {{ __('common.awaiting_payment') }}
                    </div>

                    <h1 class="text-3xl md:text-5xl font-heading font-bold text-dark mb-6 uppercase tracking-tight">
                        {{ __('common.complete_your_payment') }}
                    </h1>

                    <div class="space-y-2 text-lg text-black/70 mb-8">
                        <p>{{ __('common.payment_instruction_desc') }}</p>
                        <div class="flex flex-col gap-1">
                            <p>{{ __('common.transaction_id') }}: <span
                                    class="font-bold text-dark">{{ $transaction->code }}</span></p>
                            @if($transaction->reseller_id)
                                <p>{{ __('common.processed_by') }}: <span
                                        class="font-bold text-dark">{{ $transaction->reseller->name }}</span></p>
                            @endif
                        </div>
                    </div>

                    @if(auth()->check() && $transaction->reseller_id && auth()->id() == $transaction->reseller_id)
                        <!-- Reseller Direct Payment Mode -->
                        <div class="mb-12 p-6 bg-primary/5 rounded-2xl border border-primary/20"
                            x-data="{ showModal: false }">
                            <h3 class="font-bold text-dark text-lg uppercase mb-2">{{ __('common.reseller_payment_mode') }}
                            </h3>

                            <p class="text-sm text-black/60 mb-6">{{ __('common.reseller_payment_desc') }}</p>

                            <!-- Warning Notification -->
                            <div class="mb-6 p-4 bg-yellow-50 border border-yellow-100 rounded-xl flex items-start gap-3">
                                <svg class="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <p class="text-sm text-yellow-800 leading-relaxed">
                                    <strong>{{ __('common.ready_to_process') }}</strong>
                                    {{ __('common.reseller_confirm_paid_desc') }}
                                    <br><br>
                                    {{ __('common.check_personal_data_desc') }}
                                </p>
                            </div>

                            <form action="{{ route('payment.reseller.complete', $transaction->code) }}" method="POST"
                                x-ref="resellerForm">
                                @csrf
                                <button type="button" @click="showModal = true"
                                    class="w-full px-12 py-5 bg-primary hover:bg-primary-dark shadow-xl hover:-translate-y-1 text-white font-black rounded-2xl transition-all duration-300 transform active:scale-95 text-lg flex items-center justify-center gap-3">
                                    {{ __('common.confirm_process_subscription') }}
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </button>
                            </form>

                            <!-- Confirmation Modal -->
                            <div x-show="showModal" style="display: none;"
                                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
                                x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                                x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                                x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
                                <div class="bg-white rounded-[2rem] shadow-2xl w-full max-w-md p-8 relative transform transition-all"
                                    @click.away="showModal = false" x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                                    x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-200"
                                    x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 scale-95 translate-y-4">

                                    <div class="text-center mb-6">
                                        <div
                                            class="w-16 h-16 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-2xl font-black text-dark uppercase tracking-tight">
                                            {{ __('common.confirm_payment_q') }}
                                        </h3>
                                        <p class="text-gray-500 mt-2">
                                            <strong>Rp{{ number_format($baseTotal) }}</strong>
                                            {{ __('common.reseller_balance_deduction_desc') }}
                                        </p>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <button @click="showModal = false"
                                            class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-dark font-bold rounded-xl transition-colors">
                                            {{ __('common.cancel') }}
                                        </button>
                                        <button @click="$refs.resellerForm.submit()"
                                            class="px-6 py-3 bg-primary hover:bg-primary-dark text-white font-bold rounded-xl shadow-lg transition-transform active:scale-95">
                                            {{ __('common.yes_process') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Single Pay Now Button --}}
                        {{-- Payment Selection --}}
                        <div class="mb-16">
                            <h3 class="font-bold text-dark text-xl mb-6 uppercase">{{ __('common.select_payment_method') }}
                            </h3>

                            <div class="grid grid-cols-1 gap-4 mb-8">
                                <!-- QRIS Option -->
                                <label
                                    class="relative flex items-center p-5 rounded-2xl border-2 border-gray-100 cursor-pointer transition-all duration-200 hover:bg-gray-50 focus-within:ring-2 ring-primary group"
                                    :class="{'!border-primary bg-primary/5': paymentMethod === 'qris'}">
                                    <input type="radio" name="payment_method" value="qris" class="hidden"
                                        @change="paymentMethod = 'qris'">
                                    <div class="flex items-center gap-4 w-full">
                                        <div class="w-6 h-6 rounded-full border-2 border-gray-300 flex-shrink-0 flex items-center justify-center transition-colors"
                                            :class="{'!border-primary': paymentMethod === 'qris'}">
                                            <div class="w-3 h-3 rounded-full bg-primary transform scale-0 transition-transform duration-200"
                                                :class="{'!scale-100': paymentMethod === 'qris'}"></div>
                                        </div>
                                        <div class="flex-1 flex flex-col md:flex-row md:items-center justify-between gap-2">
                                            <div>
                                                <div class="font-bold text-dark text-lg">QRIS / E-Wallet</div>
                                                <div class="text-sm text-gray-500">GoPay, ShopeePay, Dana, dll.</div>
                                            </div>
                                            <div
                                                class="text-xs font-bold px-3 py-1 bg-gray-100 rounded-lg text-gray-600 self-start md:self-center whitespace-nowrap">
                                                Fee 0.7%
                                            </div>
                                        </div>
                                    </div>
                                </label>

                                <!-- Bank Transfer Option -->
                                <label
                                    class="relative flex items-center p-5 rounded-2xl border-2 border-gray-100 cursor-pointer transition-all duration-200 hover:bg-gray-50 focus-within:ring-2 ring-primary group"
                                    :class="{'!border-primary bg-primary/5': paymentMethod === 'bank_transfer'}">
                                    <input type="radio" name="payment_method" value="bank_transfer" class="hidden"
                                        @change="paymentMethod = 'bank_transfer'">
                                    <div class="flex items-center gap-4 w-full">
                                        <div class="w-6 h-6 rounded-full border-2 border-gray-300 flex-shrink-0 flex items-center justify-center transition-colors"
                                            :class="{'!border-primary': paymentMethod === 'bank_transfer'}">
                                            <div class="w-3 h-3 rounded-full bg-primary transform scale-0 transition-transform duration-200"
                                                :class="{'!scale-100': paymentMethod === 'bank_transfer'}"></div>
                                        </div>
                                        <div class="flex-1 flex flex-col md:flex-row md:items-center justify-between gap-2">
                                            <div>
                                                <div class="font-bold text-dark text-lg">Bank Transfer (VA)</div>
                                                <div class="text-sm text-gray-500">BCA, Mandiri, BNI, BRI, dll.</div>
                                            </div>
                                            <div
                                                class="text-xs font-bold px-3 py-1 bg-gray-100 rounded-lg text-gray-600 self-start md:self-center whitespace-nowrap">
                                                + Admin Fee
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <button id="pay-button" :disabled="!paymentMethod"
                                class="w-full px-12 py-6 bg-primary hover:bg-primary-dark text-white font-black rounded-2xl shadow-xl transition-all duration-300 transform hover:-translate-y-1 active:scale-95 text-xl flex items-center justify-center gap-4 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-none">
                                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <span>{{ __('common.proceed_to_payment') }}</span>
                            </button>
                            <p class="text-center text-sm text-gray-500 mt-4 h-6" x-show="!paymentMethod"
                                x-transition.opacity>
                                {{ __('common.select_method_first') }}
                            </p>
                        </div>
                    @endif

                    <!-- Event Banner -->
                    <div class="relative rounded-xl overflow-hidden group mb-12">
                        <img src="{{
    Str::startsWith($transaction->event->thumbnail_path, 'http')
    ? $transaction->event->thumbnail_path
    : (file_exists(public_path($transaction->event->thumbnail_path))
        ? asset($transaction->event->thumbnail_path)
        : asset('storage/' . $transaction->event->thumbnail_path))
                        }}"
                            class="w-full h-80 object-cover brightness-75 group-hover:brightness-50 transition-all duration-500">
                        <div class="absolute inset-0 flex items-center justify-center p-8 text-center">
                            <h3
                                class="text-4xl md:text-5xl font-heading font-bold text-white tracking-widest uppercase shadow-black drop-shadow-2xl">
                                {{ $transaction->event->name }}
                            </h3>
                        </div>
                    </div>

                    <!-- Information -->
                    <div>
                        <h3 class="font-bold text-dark text-xl mb-4 uppercase">{{ __('common.important_information') }}
                        </h3>
                        <p class="text-black/70 mb-4 leading-relaxed">
                            {{ __('common.payment_confirmation_email_desc') }}
                            <strong>{{ $transaction->email }}</strong>.
                            {{ __('common.show_qr_at_gate') }}
                        </p>
                    </div>

                </div>

                <!-- RIGHT COLUMN: Order Details -->
                <div class="lg:col-span-5 space-y-12">

                    <!-- Price Summary Highlight -->
                    <div class="bg-gray-50 p-8 rounded-2xl border border-gray-100 text-center">
                        <p class="text-xs font-black text-gray-400 uppercase tracking-widest mb-2">
                            {{ __('common.total_payable_amount') }}
                        </p>
                        <h2 class="text-5xl font-black text-primary tracking-tighter">
                            Rp {{ number_format($transaction->total_price, 0, ',', '.') }}
                        </h2>
                    </div>

                    <!-- Items Summary -->
                    <div>
                        <h2 class="font-bold text-dark text-xl mb-6 uppercase border-b border-gray-100 pb-2">
                            {{ __('common.your_selection') }}
                        </h2>

                        <div class="space-y-4">
                            <!-- Table Header -->
                            <div
                                class="grid grid-cols-12 text-xs font-bold text-black/70 uppercase tracking-wider pb-2 border-b border-gray-100">
                                <div class="col-span-8">{{ __('common.ticket_type') }}</div>
                                <div class="col-span-2 text-right">{{ __('common.ticket_quantity') }}</div>
                                <div class="col-span-2 text-right">{{ __('common.subtotal') }}</div>
                            </div>

                            <!-- Item Row -->
                            <div class="grid grid-cols-12 text-sm py-4 border-b border-gray-100 items-center">
                                <div class="col-span-8 pr-4">
                                    <div class="font-bold text-dark">{{ $transaction->ticket->name }}</div>
                                    <div class="text-xs text-black/70 mt-1">{{ $transaction->event->name }}</div>
                                </div>
                                <div class="col-span-2 text-right font-medium text-dark">
                                    {{ $transaction->quantity }}
                                </div>
                                <div class="col-span-2 text-right font-bold text-dark">
                                    {{ number_format($transaction->ticket->price * $transaction->quantity, 0, ',', '.') }}
                                </div>
                            </div>

                            <!-- Totals -->
                            <div class="pt-4 space-y-2">
                                <div class="flex justify-between text-sm">
                                    <span class="text-black/70">{{ __('common.subtotal') }}</span>
                                    <span class="font-medium text-dark">Rp
                                        {{ number_format($transaction->ticket->price * $transaction->quantity, 0, ',', '.') }}</span>
                                </div>
                                @if($handlingFee > 0)
                                    <div class="flex justify-between text-sm">
                                        <span
                                            class="text-black/70">{{ $transaction->reseller_id ? __('common.reseller_fee') : __('common.handling_fee') }}</span>
                                        <span class="font-medium text-dark">Rp
                                            {{ number_format($handlingFee * $transaction->quantity, 0, ',', '.') }}</span>
                                    </div>
                                @endif

                                @if(!$transaction->reseller_id && $serviceFee > 0)
                                    <div class="flex justify-between text-sm text-primary font-bold">
                                        <span>{{ __('common.service_fee') }}</span>
                                        <span id="service-fee-display">Rp
                                            {{ number_format($serviceFee, 0, ',', '.') }}</span>
                                    </div>
                                @endif
                                <div class="flex justify-between text-xl font-black pt-4 border-t border-gray-100">
                                    <span class="text-dark uppercase">{{ __('common.grand_total') }}</span>
                                    <span class="text-primary tracking-tighter" id="grand-total-display">
                                        <span x-show="!paymentMethod">Rp
                                            {{ number_format($baseTotal, 0, ',', '.') }}</span>
                                        <span x-show="paymentMethod"
                                            x-text="'Rp ' + currentTotal.toLocaleString('id-ID')"></span>
                                    </span>
                                </div>

                                <!-- Fee Breakdown (shows when method selected) -->
                                <div x-show="paymentMethod" x-transition
                                    class="mt-4 p-4 bg-gray-50 rounded-xl border border-gray-200">
                                    <div class="flex justify-between text-sm mb-2">
                                        <span class="text-gray-600">Base Total</span>
                                        <span class="font-bold text-dark"
                                            x-text="'Rp ' + baseTotal.toLocaleString('id-ID')"></span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600" x-text="feeLabel"></span>
                                        <span class="font-bold text-primary"
                                            x-text="'Rp ' + displayFee.toLocaleString('id-ID')"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Identity -->
                    <div>
                        <h3 class="font-bold text-dark text-xl mb-6 uppercase border-b border-gray-100 pb-2">
                            {{ __('common.buyer_details') }}
                        </h3>

                        <div class="space-y-4">
                            <!-- Rows -->
                            <div class="grid grid-cols-12 text-sm py-3 border-b border-gray-50 items-center">
                                <div class="col-span-4 font-bold text-black/70 uppercase text-[10px] tracking-widest">
                                    {{ __('common.nik_label') }}
                                </div>
                                <div class="col-span-8 font-medium text-dark">{{ $transaction->nik }}</div>
                            </div>

                            <div class="grid grid-cols-12 text-sm py-3 border-b border-gray-50 items-center">
                                <div class="col-span-4 font-bold text-black/70 uppercase text-[10px] tracking-widest">
                                    {{ __('common.full_name') }}
                                </div>
                                <div class="col-span-8 font-medium text-dark">{{ $transaction->name }}</div>
                            </div>

                            <div class="grid grid-cols-12 text-sm py-3 border-b border-gray-50 items-center">
                                <div class="col-span-4 font-bold text-black/70 uppercase text-[10px] tracking-widest">
                                    {{ __('common.email') }}
                                </div>
                                <div class="col-span-8 font-medium text-dark">{{ $transaction->email }}</div>
                            </div>

                            <div class="grid grid-cols-12 text-sm py-3 border-b border-gray-50 items-center">
                                <div class="col-span-4 font-bold text-black/70 uppercase text-[10px] tracking-widest">
                                    {{ __('common.phone') }}
                                </div>
                                <div class="col-span-8 font-medium text-dark">{{ $transaction->phone }}</div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script
            src="{{ config('midtrans.is_production') ? 'https://app.midtrans.com/snap/snap.js' : 'https://app.sandbox.midtrans.com/snap/snap.js' }}"
            data-client-key="{{ config('midtrans.client_key') }}"></script>
        <script type="text/javascript">
            const payButton = document.getElementById('pay-button');

            payButton.onclick = function () {
                const originalHTML = payButton.innerHTML;
                payButton.innerHTML = "{{ __('common.processing') }}";
                payButton.disabled = true;

                // Fetch Token
                // We assume AlpineJS 'paymentMethod' is accessible via x-data scope logic or simple DOM check if Alpine scope is tricky here.
                // Since this script is outside Alpine scope, we grab the checked radio input manually.
                const selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;

                fetch("{{ route('payment.token', $transaction->code) }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ payment_method: selectedMethod })
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            alert(data.error);
                            payButton.disabled = false;
                            payButton.innerHTML = originalHTML;
                            return;
                        }

                        // Open Snap
                        snap.pay(data.snap_token, {
                            onSuccess: function (result) {
                                fetch("{{ route('payment.update', $transaction->code) }}", {
                                    method: "POST",
                                    headers: {
                                        "Content-Type": "application/json",
                                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                    },
                                    body: JSON.stringify(result)
                                })
                                    .then(response => response.json())
                                    .then(() => {
                                        window.location.href = "{{ route('payment.success', $transaction->code) }}";
                                    });
                            },
                            onPending: function (result) { alert("{{ __('common.waiting_for_payment') }}"); },
                            onClose: function () {
                                alert("{{ __('common.payment_failed') }}");
                                payButton.disabled = false;
                                payButton.innerHTML = originalHTML;
                            },
                            onClose: function () {
                                payButton.disabled = false;
                                payButton.innerHTML = originalHTML;
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert("{{ __('common.something_went_wrong_try_again') }}");
                        payButton.disabled = false;
                        payButton.innerHTML = originalHTML;
                    });
            };
        </script>
    @endpush
</x-layouts.app>