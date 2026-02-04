<x-layouts.admin>
    <div class="mb-6">
        <a href="{{ route('admin.contacts.index') }}"
            class="inline-flex items-center text-sm text-secondary hover:text-dark mb-4">
            <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Back to Messages
        </a>
        <h1 class="text-2xl font-bold text-dark">Message Details</h1>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Message Content -->
        <div class="md:col-span-2">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6">
                <div>
                    <h2 class="text-xl font-bold text-dark mb-2">{{ $contact->subject }}</h2>
                    <div class="flex items-center gap-2 text-sm text-secondary">
                        <span>Received {{ $contact->created_at->format('M d, Y \a\t H:i') }}</span>
                    </div>
                </div>

                <div class="prose max-w-none text-gray-600 border-t border-b border-gray-100 py-6">
                    {!! nl2br(e($contact->message)) !!}
                </div>

                <div class="flex gap-4">
                    <a href="mailto:{{ $contact->email }}"
                        class="px-6 py-2.5 bg-primary text-white font-bold rounded-xl shadow-lg shadow-primary/30 hover:bg-primary/90 transition-all text-sm">
                        Reply via Email
                    </a>
                    <form action="{{ route('admin.contacts.destroy', $contact) }}" method="POST"
                        onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-6 py-2.5 bg-red-50 text-red-600 font-bold rounded-xl hover:bg-red-100 transition-all text-sm">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sender Info -->
        <div class="md:col-span-1">
            <div class="bg-gray-50 rounded-3xl border border-gray-100 p-6">
                <h3 class="font-bold text-dark text-lg mb-4">Sender Details</h3>
                <div class="space-y-4">
                    <div>
                        <div class="text-xs text-secondary uppercase tracking-wider mb-1">Name</div>
                        <div class="font-medium text-dark">{{ $contact->name }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-secondary uppercase tracking-wider mb-1">Email</div>
                        <div class="font-medium text-dark">{{ $contact->email }}</div>
                    </div>
                    <div>
                        <div class="text-xs text-secondary uppercase tracking-wider mb-1">Status</div>
                        <div>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Read
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>