<x-layouts.app :title="__('common.checkout')">
    <div class="bg-gray-50 min-h-screen pt-20">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">

                <!-- Left Column: Visual Summary (Sticky) -->
                <div class="lg:col-span-5 lg:sticky lg:top-24">
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl group">
                        <!-- Background Image with Overlay -->
                        <div class="absolute inset-0 bg-dark/40 group-hover:bg-dark/30 transition-colors z-10"></div>
                        <img src="{{ Str::startsWith($event->thumbnail_path, ['http', 'https']) ? $event->thumbnail_path : (file_exists(public_path($event->thumbnail_path)) ? asset($event->thumbnail_path) : asset('storage/' . $event->thumbnail_path)) }}"
                            alt="{{ $event->name }}"
                            class="w-full h-full object-cover object-center transform group-hover:scale-105 transition-transform duration-700">

                        <!-- Content Overlay -->
                        <div class="absolute inset-0 z-20 p-8 flex flex-col justify-between text-white">
                            <div>
                                <div
                                    class="inline-block px-3 py-1 mb-4 rounded-full bg-white/20 backdrop-blur-md border border-white/20 text-xs font-bold uppercase tracking-widest">
                                    {{ $event->category ?? __('common.event') }}
                                </div>
                                <h1
                                    class="text-4xl font-heading font-extrabold leading-tight shadow-black drop-shadow-lg">
                                    {{ $event->name }}
                                </h1>
                            </div>

                            <div class="space-y-4 bg-black/20 backdrop-blur-lg p-6 rounded-2xl border border-white/10">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-white/10 rounded-lg">
                                        <svg class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-white/60 font-medium uppercase tracking-wider">
                                            {{ __('common.date') }}</p>
                                        <p class="font-bold">{{ $event->start_date->format('d M Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <div class="p-2 bg-white/10 rounded-lg">
                                        <svg class="w-5 h-5 text-accent" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs text-white/60 font-medium uppercase tracking-wider">
                                            {{ __('common.location') }}
                                        </p>
                                        <p class="font-bold line-clamp-1">{{ $event->location }}</p>
                                    </div>
                                </div>
                                <div class="pt-4 mt-4 border-t border-white/10 flex justify-between items-center">
                                    <span
                                        class="text-sm font-medium text-white/80">{{ __('common.ticket_price') }}</span>
                                    <span class="text-2xl font-extrabold text-white">
                                        @php $lowest = $event->tickets->min('price'); @endphp
                                        @if($lowest !== null)
                                            Rp {{ number_format($lowest, 0, ',', '.') }}
                                        @else
                                            TBA
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Minimalist Form -->
                <div class="lg:col-span-7">
                    <form action="{{ route('checkout.store', $event) }}" method="POST" x-data='{
                        tickets: {{ $event->tickets->map(function ($t) {
    $available = $t->quota - (($t->paid_qty ?? 0) + ($t->pending_qty ?? 0));
    return [
        "id" => $t->id,
        "name" => $t->name,
        "price" => (int) $t->price,
        "limit" => (int) $t->max_purchase_per_user,
        "description" => $t->description,
        "quota" => max(0, $available)
    ];
})->toJson() }},
                        handlingFee: {{ (int) \App\Models\Setting::getValue("handling_fee", 0) }},
                        selectedTicketId: null,
                        selectedTicket: null,
                        quantity: 1,

                        init() {
                             const available = this.tickets.find(t => t.quota > 0);
                             if(available) this.selectTicket(available.id);
                        },

                        selectTicket(id) {
                            this.selectedTicketId = id;
                            this.selectedTicket = this.tickets.find(t => t.id === id);
                            this.quantity = 1;
                        },

                        get total() {
                            return this.selectedTicket ? ((this.selectedTicket.price + this.handlingFee) * this.quantity) : 0;
                        },
                        agreeTerms: false,
                        confirmData: false
                    }' class="space-y-10">
                        @csrf
                        <input type="hidden" name="ticket_id" :value="selectedTicketId">

                        <!-- Stock/Availability Notifications -->
                        @if ($errors->any())
                            <div
                                class="mb-8 p-5 bg-rose-50 border border-rose-100 rounded-[2rem] flex items-start gap-4 shadow-sm animate-shake">
                                <div
                                    class="w-10 h-10 rounded-2xl bg-rose-500 flex items-center justify-center text-white flex-shrink-0 shadow-lg shadow-rose-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                        </path>
                                    </svg>
                                </div>
                                <div class="flex-1 pt-1">
                                    <h4 class="text-sm font-black text-rose-900 uppercase tracking-widest mb-1">
                                        {{ __('common.availability_conflict') }}</h4>
                                    @foreach ($errors->all() as $error)
                                        <p class="text-[13px] font-bold text-rose-600/80 leading-relaxed">{{ $error }}</p>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Header -->
                        <div>
                            <h2 class="text-3xl font-heading font-bold text-dark mb-2">
                                {{ __('common.secure_your_spot') }}</h2>
                            <p class="text-black/70">{{ __('common.checkout_subtitle') }}</p>
                        </div>

                        <!-- Ticket Selection -->
                        <div class="space-y-4">
                            <h3 class="font-bold text-dark">{{ __('common.choose_ticket_type') }}</h3>
                            <div class="grid grid-cols-1 gap-4">
                                <template x-for="ticket in tickets" :key="ticket.id">
                                    <div @click="if(ticket.quota > 0) selectTicket(ticket.id)"
                                        class="cursor-pointer border-2 rounded-xl p-4 transition-all" :class="[
                                            selectedTicketId === ticket.id ? 'border-primary bg-primary/5' : 'border-gray-100 hover:border-gray-200',
                                            ticket.quota === 0 ? 'opacity-50 cursor-not-allowed bg-gray-50' : ''
                                        ]">
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <h4 class="font-bold text-dark" x-text="ticket.name"></h4>
                                                <p class="text-xs text-black/70 mt-1"
                                                    x-text="ticket.description || '{{ __('common.entry_ticket') }}'">
                                                </p>
                                            </div>
                                            <div class="text-right">
                                                <div class="font-bold text-primary"
                                                    x-text="ticket.price == 0 ? '{{ __('common.free') }}' : 'Rp ' + new Intl.NumberFormat('id-ID').format(ticket.price)">
                                                </div>
                                                <div class="text-xs font-medium"
                                                    :class="ticket.quota > 0 ? 'text-green-600' : 'text-red-500'"
                                                    x-text="ticket.quota > 0 ? '{{ __('common.available') }}' : '{{ __('common.sold_out') }}'">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Quantity Stepper (Only show if ticket selected) -->
                        <div class="pb-8 border-b border-gray-100" x-show="selectedTicket">
                            <div class="flex items-center justify-between bg-gray-50 p-4 rounded-xl">
                                <div>
                                    <h3 class="font-bold text-dark">{{ __('common.ticket_quantity') }}</h3>
                                    <p class="text-sm text-black/70">
                                        {{ __('common.max_purchase') }}: <span x-text="selectedTicket?.limit"></span>
                                    </p>
                                </div>

                                <!-- Minimalist Stepper -->
                                <div
                                    class="flex items-center gap-4 bg-white shadow-sm border border-gray-200 rounded-full px-2 py-1.5">
                                    <button type="button" @click="if(quantity > 1) quantity--"
                                        class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-black/70 transition-colors font-bold text-lg">
                                        −
                                    </button>
                                    <span class="font-bold text-dark w-6 text-center text-lg" x-text="quantity">1</span>
                                    <input type="hidden" name="quantity" :value="quantity">
                                    <button type="button" @click="if(quantity < selectedTicket.limit) quantity++"
                                        class="w-8 h-8 flex items-center justify-center rounded-full hover:bg-gray-100 text-black/70 transition-colors font-bold text-lg">
                                        +
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Inputs Grid -->
                        <div class="grid grid-cols-1 gap-6">

                            <!-- NIK & Gender Row -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="group">
                                    <label
                                        class="block text-xs font-bold text-black/70 uppercase tracking-wider mb-2 ml-1">{{ __('common.identity_number') }}</label>
                                    <input type="text" inputmode="numeric" name="nik" maxlength="20" minlength="12"
                                        pattern="\d{12,20}"
                                        class="w-full bg-white border-b-2 border-gray-100 px-4 py-3 text-dark font-medium focus:outline-none focus:border-primary transition-all rounded-xl hover:bg-gray-50 focus:bg-white"
                                        placeholder="{{ __('common.identity_placeholder') }}" required
                                        oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 20)">
                                </div>
                                <div class="group">
                                    <label
                                        class="block text-xs font-bold text-black/70 uppercase tracking-wider mb-2 ml-1">{{ __('common.gender') }}</label>
                                    <div class="relative" x-data="{
                                        open: false,
                                        selected: '',
                                         label: '{{ __('common.select_gender') }}',
                                         options: [
                                            { value: 'male', label: '{{ __('common.male') }}' },
                                            { value: 'female', label: '{{ __('common.female') }}' }
                                        ],
                                        select(value, label) {
                                            this.selected = value;
                                            this.label = label;
                                            this.open = false;
                                        }
                                    }" @click.outside="open = false">

                                        <!-- Hidden Input -->
                                        <input type="hidden" name="gender" :value="selected" required>

                                        <!-- Trigger -->
                                        <button type="button" @click="open = !open"
                                            class="relative w-full bg-white border-b-2 border-gray-100 pl-4 pr-10 py-3 text-left font-medium transition-all rounded-xl hover:bg-gray-50 focus:bg-white"
                                            :class="selected ? 'text-dark' : 'text-gray-400'">
                                            <span x-text="label" class="block truncate mr-2"></span>
                                            <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none">
                                                <svg class="w-5 h-5 text-gray-400 transition-transform duration-300"
                                                    :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M19 9l-7 7-7-7" />
                                                </svg>
                                            </div>
                                        </button>

                                        <!-- Clean Card Dropdown -->
                                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                                            x-transition:enter-start="opacity-0 translate-y-2"
                                            x-transition:enter-end="opacity-100 translate-y-0"
                                            x-transition:leave="transition ease-in duration-150"
                                            x-transition:leave-start="opacity-100 translate-y-0"
                                            x-transition:leave-end="opacity-0 translate-y-2"
                                            class="absolute top-full left-0 right-0 mt-2 bg-white rounded-xl shadow-[0_10px_40px_-5px_rgba(0,0,0,0.1)] border border-gray-100 z-50 overflow-hidden"
                                            style="display: none;">

                                            <!-- Static Header -->
                                            <div
                                                class="px-5 py-3 text-gray-400 text-sm font-bold uppercase tracking-wider border-b border-gray-50 bg-gray-50/50">
                                                {{ __('common.select_gender') }}
                                            </div>

                                            <div class="py-1">
                                                <template x-for="option in options" :key="option.value">
                                                    <div @click="select(option.value, option.label)"
                                                        class="px-5 py-3 hover:bg-gray-50 cursor-pointer flex items-center justify-between group transition-colors">
                                                        <span
                                                            class="text-dark font-medium group-hover:text-primary transition-colors"
                                                            x-text="option.label"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="group">
                                    <label
                                        class="block text-xs font-bold text-black/70 uppercase tracking-wider mb-2 ml-1">{{ __('common.full_name') }}</label>
                                    <input type="text" name="name"
                                        class="w-full bg-white border-b-2 border-gray-100 px-4 py-3 text-dark font-medium focus:outline-none focus:border-primary transition-all rounded-xl hover:bg-gray-50 focus:bg-white"
                                        placeholder="John Doe" required>
                                </div>

                                <div class="group">
                                    <label
                                        class="block text-xs font-bold text-black/70 uppercase tracking-wider mb-2 ml-1">{{ __('common.your_email') }}</label>
                                    <input type="email" name="email"
                                        class="w-full bg-white border-b-2 border-gray-100 px-4 py-3 text-dark font-medium focus:outline-none focus:border-primary transition-all rounded-xl hover:bg-gray-50 focus:bg-white"
                                        placeholder="john@example.com" required>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="group">
                                    <label
                                        class="block text-xs font-bold text-black/70 uppercase tracking-wider mb-2 ml-1">{{ __('common.phone_number') }}</label>
                                    <input type="tel" name="phone" maxlength="15"
                                        class="w-full bg-white border-b-2 border-gray-100 px-4 py-3 text-dark font-medium focus:outline-none focus:border-primary transition-all rounded-xl hover:bg-gray-50 focus:bg-white"
                                        placeholder="+62..." required
                                        oninput="this.value = this.value.replace(/[^0-9+]/g, '')">
                                </div>

                                <div class="group">
                                    <label
                                        class="block text-xs font-bold text-black/70 uppercase tracking-wider mb-2 ml-1">{{ __('common.city_residence') }}</label>
                                    <input type="text" name="city"
                                        class="w-full bg-white border-b-2 border-gray-100 px-4 py-3 text-dark font-medium focus:outline-none focus:border-primary transition-all rounded-xl hover:bg-gray-50 focus:bg-white"
                                        placeholder="Jakarta" required>
                                </div>
                            </div>
                        </div>


                        <!-- Terms & Confirmation -->
                        <div class="space-y-4 pt-4 border-t border-gray-100">
                            <label class="flex items-start gap-3 cursor-pointer group">
                                <div class="relative flex-shrink-0 mt-0.5 w-5 h-5">
                                    <input type="checkbox" x-model="agreeTerms" required
                                        class="peer w-full h-full border-2 border-gray-300 rounded-md checked:bg-primary checked:border-primary transition-all appearance-none cursor-pointer">
                                    <div
                                        class="absolute inset-0 flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none">
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                                <span
                                    class="text-xs text-gray-500 leading-relaxed group-hover:text-gray-700 transition-colors pt-0.5">
                                    {{ __('common.i_agree_to') }} <a href="{{ route('pages.terms') }}" target="_blank"
                                        class="font-bold text-primary hover:underline">{{ __('common.terms_conditions') }}</a>
                                    {{ __('common.and') }} <a href="{{ route('pages.privacy') }}" target="_blank"
                                        class="font-bold text-primary hover:underline">{{ __('common.privacy_policy') }}</a>.
                                </span>
                            </label>

                            <label class="flex items-start gap-3 cursor-pointer group">
                                <div class="relative flex-shrink-0 mt-0.5 w-5 h-5">
                                    <input type="checkbox" x-model="confirmData" required
                                        class="peer w-full h-full border-2 border-gray-300 rounded-md checked:bg-primary checked:border-primary transition-all appearance-none cursor-pointer">
                                    <div
                                        class="absolute inset-0 flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none">
                                        <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                </div>
                                <span
                                    class="text-xs text-gray-500 leading-relaxed group-hover:text-gray-700 transition-colors pt-0.5">
                                    {{ __('common.confirm_data_accurate') }}
                                </span>
                            </label>

                            <input type="hidden" name="terms_agreed" :value="agreeTerms ? 1 : 0">
                        </div>

                        <!-- Footer / Pay -->
                        <div
                            class="pt-8 mt-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-6 sm:gap-0">
                            <div class="w-full sm:w-auto text-center sm:text-left">
                                <p class="text-[10px] text-black/50 font-medium uppercase tracking-wider">
                                    {{ __('common.total_payable_amount') }}
                                </p>
                                <p class="text-3xl font-black text-primary tracking-tight">
                                    Rp <span x-text="new Intl.NumberFormat('id-ID').format(total)"></span>
                                </p>
                                <div class="flex items-center justify-center sm:justify-start gap-2 mt-1.5"
                                    x-show="selectedTicket">
                                    <span
                                        class="text-[10px] font-bold text-white bg-black/10 px-1.5 py-0.5 rounded leading-none">
                                        {{ __('common.inc_fees') }}
                                    </span>
                                    <span class="text-[10px] text-black/40">
                                        Rp <span x-text="new Intl.NumberFormat('id-ID').format(handlingFee)"></span>
                                        {{ __('common.handling_fee') }}
                                    </span>
                                </div>
                            </div>
                            <button type="submit" :disabled="!selectedTicket || !agreeTerms || !confirmData"
                                :class="(!selectedTicket || !agreeTerms || !confirmData) ? 'opacity-50 cursor-not-allowed' : 'hover:bg-primary/90 hover:scale-105 active:scale-95'"
                                class="w-full sm:w-auto px-8 py-4 bg-primary text-white font-bold rounded-2xl shadow-xl shadow-primary/30 transition-all flex items-center justify-center gap-2">
                                <span>{{ __('common.complete_order') }}</span>
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </button>
                        </div>



                    </form>
                </div>

            </div>
        </div>
    </div>
</x-layouts.app>