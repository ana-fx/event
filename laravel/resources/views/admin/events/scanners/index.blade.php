<x-layouts.admin>
    <div x-data="{ assignModalOpen: false }">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Event Scanners</h2>
                <p class="text-sm text-gray-500">Manage scanners for event: <span
                        class="font-bold">{{ $event->name }}</span></p>
            </div>
            <button @click="assignModalOpen = true"
                class="px-6 py-3 bg-primary text-white text-sm font-bold rounded-xl hover:bg-primary/90 transition shadow-lg shadow-primary/30">
                + Assign Scanner
            </button>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 rounded-lg bg-green-50 text-green-700 border border-green-200">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <!-- Table Header Controls (Search/Show) - Consistent with tickets -->
            <div class="p-4 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="flex items-center gap-3 text-sm text-gray-400 font-bold" x-data="{ open: false, selected: '10' }">
                    <span>Show</span>
                    <div class="relative">
                        <button type="button" @click="open = !open" @click.away="open = false"
                            class="bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100 flex items-center gap-2 text-dark hover:bg-white transition-all">
                            <span x-text="selected"></span>
                            <svg class="w-3 h-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" class="absolute top-full left-0 mt-1 bg-white border border-gray-100 rounded-xl shadow-xl z-50 py-1 min-w-[60px]" style="display: none;">
                            <button type="button" @click="selected = '10'; open = false" class="w-full px-4 py-2 text-left hover:bg-gray-50 text-dark">10</button>
                            <button type="button" @click="selected = '25'; open = false" class="w-full px-4 py-2 text-left hover:bg-gray-50 text-dark">25</button>
                            <button type="button" @click="selected = '50'; open = false" class="w-full px-4 py-2 text-left hover:bg-gray-50 text-dark">50</button>
                        </div>
                    </div>
                    <span>entries</span>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-600 w-full md:w-auto">
                    <span>Search:</span>
                    <input type="text"
                        class="border-gray-200 rounded-lg text-sm w-full md:w-64 focus:ring-primary focus:border-primary">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-50 text-xs uppercase text-gray-500 font-bold tracking-wider">
                            <th class="px-6 py-4">No</th>
                            <th class="px-6 py-4">Name</th>
                            <th class="px-6 py-4">Email</th>
                            <th class="px-6 py-4">Role</th>
                            <th class="px-6 py-4">Joined Date</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($event->scanners as $index => $scanner)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $index + 1 }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        {{-- <img src="{{ $scanner->profile_photo_url }}" alt=""
                                            class="w-8 h-8 rounded-full bg-gray-200"> --}}
                                        <div class="font-bold text-gray-900">{{ $scanner->name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $scanner->email }}
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ ucfirst($scanner->role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $scanner->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form
                                        action="{{ route('admin.events.unassign-scanner', ['event' => $event, 'scanner' => $scanner]) }}"
                                        method="POST" class="inline"
                                        onsubmit="return confirm('Are you sure you want to remove this scanner from the event?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Unassign Scanner">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    No scanners assigned to this event yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Assign Scanner Modal -->
        <div x-show="assignModalOpen" style="display: none;"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" x-transition>
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 relative"
                @click.away="assignModalOpen = false">
                <button @click="assignModalOpen = false"
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <h3 class="text-xl font-bold text-gray-900 mb-4">Assign Scanner</h3>

                <form action="{{ route('admin.events.assign-scanner', $event) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="scanner_id" class="block text-sm font-medium text-gray-700 mb-1">Select
                            Scanner</label>
                        <div class="relative" x-data="{
                            open: false,
                            selected: '{{ $availableScanners->first()->id ?? '' }}',
                            label: '{{ $availableScanners->first() ? $availableScanners->first()->name : 'No available scanners' }}'
                        }">
                            <input type="hidden" name="scanner_id" :value="selected" required>
                            <button type="button" @click="open = !open" @click.away="open = false"
                                class="w-full flex items-center justify-between appearance-none bg-white border border-gray-200 text-gray-700 py-3 px-4 rounded-xl leading-tight focus:outline-none focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all font-medium">
                                <span x-text="label" :class="selected === '' ? 'text-gray-400 font-medium' : 'text-dark font-bold'"></span>
                                <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                class="absolute z-50 w-full mt-2 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">
                                <div class="px-5 py-3 bg-gray-50 border-b border-gray-100">
                                    <span class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Select Scanner Agent</span>
                                </div>
                                <div class="py-1 max-h-48 overflow-y-auto">
                                    @forelse($availableScanners as $scanner)
                                        <button type="button" @click="selected = '{{ $scanner->id }}'; label = '{{ addslashes($scanner->name) }}'; open = false"
                                            class="w-full px-5 py-2.5 text-left hover:bg-primary/5 transition-colors text-sm font-bold text-dark border-l-2 border-transparent hover:border-primary">
                                            {{ $scanner->name }} <span class="text-[10px] text-gray-400 font-medium ml-1">({{ $scanner->email }})</span>
                                        </button>
                                    @empty
                                        <div class="px-5 py-3 text-sm text-gray-400">No available scanners found</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit"
                            class="px-6 py-3 bg-primary text-white font-bold rounded-xl hover:bg-primary/90 transition shadow-lg shadow-primary/30"
                            @if($availableScanners->isEmpty()) disabled @endif>
                            Assign
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.admin>
