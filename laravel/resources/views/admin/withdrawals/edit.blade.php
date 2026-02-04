<x-layouts.admin>
    <div class="max-w-2xl mx-auto space-y-8 pb-12">
        <!-- Back Link -->
        <div>
            <a href="{{ route('admin.events.withdrawals.create', $event) }}" class="inline-flex items-center text-sm font-bold text-gray-400 hover:text-primary transition-colors gap-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Back to Withdrawal History
            </a>
        </div>

        <!-- Main Card -->
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <form action="{{ route('admin.events.withdrawals.update', [$event, $withdrawal]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="px-8 pt-8 pb-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-2xl font-black text-dark tracking-tight">Edit Withdrawal</h3>
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-widest mt-1">Ref: {{ $withdrawal->reference }}</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <!-- Context Balance -->
                        <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-100">
                            <div class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-1">Available Balance (Including this withdrawal)</div>
                            <div class="text-xl font-black text-emerald-700">Rp {{ number_format($availableSaldo, 0, ',', '.') }}</div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Withdrawal Amount (Rp)</label>
                            <div class="relative">
                                <span class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-400 font-bold">Rp</span>
                                <input type="number" name="amount" required min="1" max="{{ $availableSaldo }}" step="0.01"
                                       value="{{ $withdrawal->amount }}"
                                       class="w-full pl-14 pr-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-primary/20 text-dark font-bold text-lg placeholder-gray-300 transition-all"
                                       placeholder="0">
                            </div>
                            @error('amount')
                                <p class="text-red-500 text-xs mt-2 ml-1 font-bold">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Internal Note (Optional)</label>
                            <textarea name="note" rows="3"
                                      class="w-full px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-primary/20 text-dark font-bold placeholder-gray-300 transition-all resize-none"
                                      placeholder="Add any specific details about this payout...">{{ old('note', $withdrawal->note) }}</textarea>
                            @error('note')
                                <p class="text-red-500 text-xs mt-2 ml-1 font-bold">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="px-8 pb-8">
                    <button type="submit" class="w-full py-4 bg-primary text-white rounded-2xl font-black text-sm hover:bg-dark transition-all shadow-xl shadow-primary/20">
                        Update Withdrawal
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
