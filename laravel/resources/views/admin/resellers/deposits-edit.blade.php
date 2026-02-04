<x-layouts.admin title="Edit Deposit - {{ $reseller->name }}">
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.resellers.deposits', $reseller) }}"
                class="p-2 bg-white border border-gray-100 rounded-xl text-gray-400 hover:text-dark transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-dark">Edit Deposit</h1>
                <p class="text-xs text-gray-400 font-medium uppercase tracking-widest mt-0.5">Adjusting record for
                    {{ $reseller->name }}
                </p>
            </div>
        </div>
    </div>

    <div class="max-w-2xl px-0 sm:px-0">
        <div class="bg-white rounded-3xl sm:rounded-[2rem] shadow-sm border border-gray-100 p-6 sm:p-8">
            <h2 class="text-lg font-black text-dark uppercase tracking-tight mb-6">Modify Deposit Details</h2>

            <form action="{{ route('admin.resellers.deposits.update', [$reseller, $deposit]) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Deposit
                            Amount (Rp)</label>
                        <input type="number" name="amount" value="{{ old('amount', $deposit->amount) }}" required
                            min="1"
                            class="w-full px-5 sm:px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-primary/20 text-dark font-bold text-lg"
                            placeholder="e.g. 1000000">
                        <p class="text-[10px] text-gray-400 mt-2 italic font-medium leading-relaxed">* Changing this
                            will automatically adjust the reseller's current balance.</p>
                        @error('amount')
                            <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Note
                            (Optional)</label>
                        <textarea name="note" rows="4"
                            class="w-full px-5 sm:px-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-primary/20 text-dark font-medium text-sm"
                            placeholder="Reason for adjustment...">{{ old('note', $deposit->note) }}</textarea>
                        @error('note')
                            <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-4 pt-2">
                        <a href="{{ route('admin.resellers.deposits', $reseller) }}"
                            class="w-full sm:flex-1 py-4 sm:py-5 bg-gray-50 text-gray-500 font-black rounded-2xl hover:bg-gray-100 transition-all text-center uppercase tracking-widest text-xs order-2 sm:order-1">
                            Cancel
                        </a>
                        <button type="submit"
                            class="w-full sm:flex-[2] py-4 sm:py-5 bg-primary text-white font-black rounded-2xl shadow-xl shadow-primary/30 hover:bg-primary/90 transition-all active:scale-95 flex items-center justify-center gap-3 order-1 sm:order-2">
                            Update Record
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>