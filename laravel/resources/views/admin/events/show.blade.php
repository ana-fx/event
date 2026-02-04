<x-layouts.admin>
    <div class="max-w-6xl mx-auto space-y-8 pb-12" x-data="{ deleteModalOpen: false, formToSubmit: null }">
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <nav class="flex text-sm text-gray-500 mb-2 gap-2 items-center">
                    <a href="{{ route('admin.events.index') }}" class="hover:text-primary transition-colors">Events</a>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                    <span class="text-gray-900 font-bold">Event Details</span>
                </nav>
                <h2 class="text-4xl font-extrabold text-gray-900 tracking-tight">{{ $event->name }}</h2>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.events.edit', $event) }}"
                    class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl hover:bg-gray-50 transition-all shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                        </path>
                    </svg>
                    Edit Event
                </a>
                <a href="{{ route('admin.events.index') }}"
                    class="px-5 py-2.5 bg-primary text-white font-bold rounded-xl hover:bg-primary-700 transition-all shadow-lg shadow-primary/25 flex items-center gap-2">
                    Back to List
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Main Content (Left) -->
            <div class="lg:col-span-8 space-y-8">
                <!-- Banner Image -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden group">
                    <div class="aspect-video relative overflow-hidden bg-gray-100">
                        @if($event->banner_path)
                            <img src="{{ Str::startsWith($event->banner_path, ['http', 'https']) ? $event->banner_path : (file_exists(public_path($event->banner_path)) ? asset($event->banner_path) : asset('storage/' . $event->banner_path)) }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700"
                                alt="{{ $event->name }}">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-16 h-16 mb-2 opacity-20" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                                <span class="font-medium">No banner image uploaded</span>
                            </div>
                        @endif

                        <!-- Status Badge -->
                        <div class="absolute top-6 left-6">
                            <span @class([
                                'inline-flex px-4 py-1.5 rounded-full text-sm font-bold shadow-lg backdrop-blur-md text-white',
                                'bg-green-500/90' => $event->status === 'active',
                                'bg-gray-500/90' => $event->status === 'draft',
                                'bg-red-500/90' => !in_array($event->status, ['active', 'draft'])
                            ])>
                                {{ ucfirst($event->status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h7"></path>
                            </svg>
                            About this Event
                        </h3>
                        <div class="prose prose-blue max-w-none text-black prose-headings:text-black leading-relaxed">
                            {!! $event->description !!}
                        </div>
                    </div>

                    @if($event->terms)
                        <div class="pt-6 border-t border-gray-50">
                            <h3 class="text-lg font-bold text-gray-900 mb-3">Terms & Conditions</h3>
                            <div class="prose prose-sm max-w-none text-black prose-headings:text-black leading-relaxed">
                                {!! $event->terms !!}
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Location Map -->
                @if($event->google_map_embed)
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                        <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                </path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Location
                        </h3>
                        <p class="text-gray-600 mb-4">{{ $event->location }}, {{ $event->city }}, {{ $event->province }}
                            {{ $event->zip }}
                        </p>
                        <div
                            class="rounded-2xl overflow-hidden border border-gray-100 aspect-video w-full bg-gray-50 shadow-inner">
                            <div class="w-full h-full [&>iframe]:!w-full [&>iframe]:!h-full">
                                {!! $event->google_map_embed !!}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Ticket Information Table -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                            </svg>
                            Ticket Information
                        </h3>
                        <a href="{{ route('admin.events.tickets-report.create', ['event' => $event]) }}"
                            class="text-sm font-bold text-primary hover:text-primary-700 transition-colors">
                            + Add Ticket
                        </a>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="bg-gray-50 border-b border-gray-100 text-xs uppercase text-gray-500 font-bold tracking-wider">
                                    <th class="px-6 py-4 rounded-tl-xl text-dark">Ticket Type</th>
                                    <th class="px-6 py-4 text-dark">Price</th>
                                    <th class="px-6 py-4 text-dark">Quota</th>
                                    <th class="px-6 py-4 text-dark">Status</th>
                                    <th class="px-6 py-4 text-dark text-right rounded-tr-xl">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($event->tickets as $ticket)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-900">{{ $ticket->name }}</div>
                                            <div class="text-xs text-gray-500">
                                                {{ $ticket->description ?? 'No description' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-primary">
                                                {{ $ticket->price == 0 ? 'Free' : 'Rp. ' . number_format($ticket->price, 0, ',', '.') }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-gray-900">{{ $ticket->quota }}</div>
                                            <div class="text-xs text-gray-400">Limit:
                                                {{ $ticket->max_purchase_per_user }} / User
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            @if(!$ticket->is_active)
                                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                                    Disabled
                                                </span>
                                            @elseif($ticket->quota <= 0)
                                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                                    Sold Out
                                                </span>
                                            @else
                                                <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                                    Available
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <a href="{{ route('admin.tickets-report.edit', $ticket) }}"
                                                    class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all"
                                                    title="Edit Ticket">
                                                    <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path
                                                            d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                                    </svg>
                                                </a>

                                                <form action="{{ route('admin.tickets-report.update', $ticket) }}"
                                                    method="POST" class="inline-block">
                                                    @csrf
                                                    @method('PUT')
                                                    <!-- Preserve existing values -->
                                                    <input type="hidden" name="name" value="{{ $ticket->name }}">
                                                    <input type="hidden" name="price" value="{{ $ticket->price }}">
                                                    <input type="hidden" name="quota" value="{{ $ticket->quota }}">
                                                    <input type="hidden" name="max_purchase_per_user" value="{{ $ticket->max_purchase_per_user }}">
                                                    <input type="hidden" name="start_date" value="{{ $ticket->start_date->format('Y-m-d H:i') }}">
                                                    <input type="hidden" name="end_date" value="{{ $ticket->end_date->format('Y-m-d H:i') }}">
                                                    <input type="hidden" name="description" value="{{ $ticket->description }}">
                                                    
                                                    @if($ticket->is_active)
                                                        <!-- To Disable: Do NOT include 'is_active' input so request->has('is_active') is false -->
                                                        <button type="submit"
                                                            class="p-2 text-gray-400 hover:text-orange-600 hover:bg-orange-50 rounded-lg transition-all"
                                                            title="Disable Ticket">
                                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                            </svg>
                                                        </button>
                                                    @else
                                                        <!-- To Enable: Include 'is_active' input so request->has('is_active') is true -->
                                                        <input type="hidden" name="is_active" value="1">
                                                        <button type="submit"
                                                            class="p-2 text-gray-400 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-all"
                                                            title="Enable Ticket">
                                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                            </svg>
                                                        </button>
                                                    @endif
                                                </form>

                                                <form action="{{ route('admin.tickets-report.destroy', $ticket) }}"
                                                    method="POST"
                                                    @submit.prevent="formToSubmit = $el; deleteModalOpen = true"
                                                    class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all"
                                                        title="Delete Ticket">
                                                        <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd"
                                                                d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                                clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-400 italic">
                                            No ticket information available for this event.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Assigned Scanners Table -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8"
                    x-data="{ assignModalOpen: false }">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M3 4a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1V4zm2 2V5h1v1H5zM3 13a1 1 0 011-1h3a1 1 0 011 1v3a1 1 0 01-1 1H4a1 1 0 01-1-1v-3zm2 2v-1h1v1H5zM13 3a1 1 0 00-1 1v3a1 1 0 001 1h3a1 1 0 001-1V4a1 1 0 00-1-1h-3zm1 2v1h1V5h-1z"
                                    clip-rule="evenodd" />
                                <path
                                    d="M11 4a1 1 0 10-2 0v1a1 1 0 002 0V4zM10 7a1 1 0 011 1v1h2a1 1 0 110 2h-3a1 1 0 01-1-1V8a1 1 0 011-1zM16 9a1 1 0 100 2 1 1 0 000-2zM9 13a1 1 0 011-1h1a1 1 0 110 2v2a1 1 0 11-2 0v-3zM7 11a1 1 0 100-2H4a1 1 0 100 2h3zM17 13a1 1 0 01-1 1h-2a1 1 0 110-2h2a1 1 0 011 1zM16 17a1 1 0 100-2h-3a1 1 0 100 2h3z" />
                            </svg>
                            Assigned Scanners
                        </h3>
                        <button @click="assignModalOpen = !assignModalOpen"
                            class="px-4 py-2 bg-primary text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-primary/90 transition shadow-lg shadow-primary/20">
                            + Assign Scanner
                        </button>
                    </div>

                    <!-- Scan Assignment Form -->
                    <div x-show="assignModalOpen" @click.away="assignModalOpen = false" x-transition
                        class="mb-6 p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <form action="{{ route('admin.events.assign-scanner', $event) }}" method="POST"
                            class="flex gap-4 items-end">
                            @csrf
                            <div class="flex-1">
                                <label for="scanner_id" class="block text-sm font-bold text-gray-700 mb-1">Select
                                    Scanner</label>
                                <div class="relative" x-data="{
                                    open: false,
                                    selected: '',
                                    label: '-- Choose a scanner --'
                                }">
                                    <input type="hidden" name="scanner_id" :value="selected" required>
                                    <button type="button" @click="open = !open" @click.away="open = false"
                                        class="w-full flex items-center justify-between appearance-none bg-white border border-gray-200 text-gray-700 py-3 px-4 rounded-xl leading-tight focus:outline-none focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all font-medium">
                                        <span x-text="label"
                                            :class="selected === '' ? 'text-gray-400 font-medium' : 'text-dark font-bold'"></span>
                                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                            :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>

                                    <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        class="absolute z-50 w-full mt-2 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">
                                        <div class="px-5 py-3 bg-gray-50 border-b border-gray-100">
                                            <span
                                                class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Select
                                                Agent</span>
                                        </div>
                                        <div class="py-1 max-h-48 overflow-y-auto">
                                            @foreach($scanners as $scanner)
                                                <button type="button"
                                                    @click="selected = '{{ $scanner->id }}'; label = '{{ addslashes($scanner->name) }}'; open = false"
                                                    class="w-full px-5 py-2.5 text-left hover:bg-primary/5 transition-colors text-sm font-bold text-dark border-l-2 border-transparent hover:border-primary">
                                                    {{ $scanner->name }} <span
                                                        class="text-[10px] text-gray-400 font-medium ml-1">({{ $scanner->email }})</span>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit"
                                class="px-6 py-2 bg-primary text-white font-bold rounded-xl hover:bg-primary-700 transition-colors shadow-lg shadow-primary/25">
                                Assign
                            </button>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="bg-gray-50 border-b border-gray-100 text-xs uppercase text-gray-500 font-bold tracking-wider">
                                    <th class="px-6 py-4 rounded-tl-xl text-dark">Scanner Name</th>
                                    <th class="px-6 py-4 text-dark">Email</th>
                                    <th class="px-6 py-4 text-dark">Assigned At</th>
                                    <th class="px-6 py-4 text-dark text-right rounded-tr-xl">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($event->scanners as $scanner)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-900">{{ $scanner->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600">
                                            {{ $scanner->email }}
                                        </td>
                                        <td class="px-6 py-4 text-gray-500 text-sm">
                                            {{ $scanner->pivot->created_at->format('M d, Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <form action="{{ route('admin.events.unassign-scanner', [$event, $scanner]) }}"
                                                method="POST" @submit.prevent="formToSubmit = $el; deleteModalOpen = true"
                                                class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all"
                                                    title="Remove Access">
                                                    <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-400 italic">
                                            No scanners assigned to this event yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Assigned Resellers Table -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8"
                    x-data="{ assignResellerModalOpen: false }">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-primary" viewBox="0 0 20 20" fill="currentColor">
                                <path
                                    d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                            </svg>
                            Assigned Resellers
                        </h3>
                        <button @click="assignResellerModalOpen = !assignResellerModalOpen"
                            class="px-4 py-2 bg-primary text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-primary/90 transition shadow-lg shadow-primary/20">
                            + Assign Reseller
                        </button>
                    </div>

                    <!-- Reseller Assignment Form -->
                    <div x-show="assignResellerModalOpen" @click.away="assignResellerModalOpen = false" x-transition
                        class="mb-6 p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <form action="{{ route('admin.events.assign-reseller', $event) }}" method="POST"
                            class="flex gap-4 items-end">
                            @csrf
                            <div class="flex-1">
                                <label for="reseller_id" class="block text-sm font-bold text-gray-700 mb-1">Select
                                    Reseller</label>
                                <div class="relative" x-data="{
                                    open: false,
                                    selected: '',
                                    label: '-- Choose a reseller --'
                                }">
                                    <input type="hidden" name="reseller_id" :value="selected" required>
                                    <button type="button" @click="open = !open" @click.away="open = false"
                                        class="w-full flex items-center justify-between appearance-none bg-white border border-gray-200 text-gray-700 py-3 px-4 rounded-xl leading-tight focus:outline-none focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all font-medium">
                                        <span x-text="label"
                                            :class="selected === '' ? 'text-gray-400 font-medium' : 'text-dark font-bold'"></span>
                                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                            :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>

                                    <div x-show="open" x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        class="absolute z-50 w-full mt-2 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden">
                                        <div class="px-5 py-3 bg-gray-50 border-b border-gray-100">
                                            <span
                                                class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Select
                                                Reseller</span>
                                        </div>
                                        <div class="py-1 max-h-48 overflow-y-auto">
                                            @foreach($resellers as $reseller)
                                                <button type="button"
                                                    @click="selected = '{{ $reseller->id }}'; label = '{{ addslashes($reseller->name) }}'; open = false"
                                                    class="w-full px-5 py-2.5 text-left hover:bg-primary/5 transition-colors text-sm font-bold text-dark border-l-2 border-transparent hover:border-primary">
                                                    {{ $reseller->name }} <span
                                                        class="text-[10px] text-gray-400 font-medium ml-1">({{ $reseller->email }})</span>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <button type="submit"
                                class="px-6 py-2 bg-primary text-white font-bold rounded-xl hover:bg-primary-700 transition-colors shadow-lg shadow-primary/25">
                                Assign
                            </button>
                        </form>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="bg-gray-50 border-b border-gray-100 text-xs uppercase text-gray-500 font-bold tracking-wider">
                                    <th class="px-6 py-4 rounded-tl-xl text-dark">Reseller Name</th>
                                    <th class="px-6 py-4 text-dark">Email</th>
                                    <th class="px-6 py-4 text-dark">Assigned At</th>
                                    <th class="px-6 py-4 text-dark text-right rounded-tr-xl">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($event->resellers as $reseller)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-bold text-gray-900">{{ $reseller->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 text-gray-600">
                                            {{ $reseller->email }}
                                        </td>
                                        <td class="px-6 py-4 text-gray-500 text-sm">
                                            {{ $reseller->pivot->created_at->format('M d, Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <form
                                                action="{{ route('admin.events.unassign-reseller', [$event, $reseller]) }}"
                                                method="POST" @submit.prevent="formToSubmit = $el; deleteModalOpen = true"
                                                class="inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all"
                                                    title="Remove Access">
                                                    <svg class="w-5 h-5" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-8 text-center text-gray-400 italic">
                                            No resellers assigned to this event yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sidebar (Right) -->
            <div class="lg:col-span-4 space-y-8">
                <!-- Ticket Info Card -->
                <div
                    class="bg-gradient-to-b from-primary to-primary/80 rounded-3xl shadow-xl shadow-primary/20 p-8 text-white relative overflow-hidden">
                    <!-- Subtle background decoration -->
                    <div class="absolute -right-8 -bottom-8 w-32 h-32 bg-white/10 rounded-full blur-3xl"></div>

                    <h3 class="text-sm font-bold uppercase tracking-wider text-white/80 mb-1">Entry Ticket</h3>

                    @php
                        // Get the first ticket to display details, or finding the 'cheapest' one might be better?
                        // For now, let's grab the first one to fix the 'No tickets' bug.
                        $featuredTicket = $event->tickets->first();
                    @endphp

                    <div class="flex items-baseline gap-2 mb-8">
                        @if($featuredTicket)
                            <span class="text-4xl font-extrabold tracking-tight">
                                {{ $featuredTicket->price == 0 ? 'Free Entry' : 'Rp. ' . number_format($featuredTicket->price, 0, ',', '.') }}
                            </span>
                            @if($event->tickets->count() > 1)
                                <span class="text-sm text-white/60 font-medium">starts from</span>
                            @endif
                        @else
                            <span class="text-2xl font-bold text-white/60 italic">No tickets added yet</span>
                        @endif
                    </div>

                    @if($featuredTicket)
                        <div class="space-y-5 mb-8">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-11 h-11 rounded-2xl bg-white/15 flex items-center justify-center border border-white/10">
                                    <svg class="w-5 h-5 text-white/90" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-white/60 uppercase tracking-wide">Ticket Type</p>
                                    <p class="text-lg font-bold">{{ $featuredTicket->name }}</p>
                                    @if($event->tickets->count() > 1)
                                        <p class="text-xs text-white/50">+ {{ $event->tickets->count() - 1 }} other types</p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-11 h-11 rounded-2xl bg-white/15 flex items-center justify-center border border-white/10">
                                    <svg class="w-5 h-5 text-white/90" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-white/60 uppercase tracking-wide">Remaining Quota</p>
                                    <p class="text-lg font-bold">{{ $featuredTicket->quota }} <span
                                            class="text-sm font-medium text-white/60">tickets left</span></p>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('admin.tickets-report.edit', $featuredTicket) }}"
                            class="block w-full py-4 bg-white text-primary font-bold rounded-2xl text-center hover:bg-gray-50 transition-all shadow-lg active:scale-[0.98]">
                            Edit Ticket Pricing
                        </a>
                    @else
                        <a href="{{ route('admin.events.tickets-report.create', $event) }}"
                            class="block w-full py-4 bg-white text-primary font-bold rounded-2xl text-center hover:bg-gray-50 transition-all shadow-lg active:scale-[0.98]">
                            Add First Ticket
                        </a>
                    @endif
                </div>

                <!-- Logistics Card -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6">
                    <h4 class="text-sm font-bold text-gray-400 uppercase tracking-widest">Event Info</h4>

                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div
                                class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-400 font-bold mb-1">Date & Time</p>
                                <p class="font-bold text-gray-900">{{ $event->start_date->format('l, d M Y') }}</p>
                                <p class="text-sm text-gray-500 font-bold">{{ $event->start_date->format('H:i') }} -
                                    {{ $event->end_date->format('H:i') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div
                                class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-400 font-bold mb-1">Category</p>
                                <p class="font-bold text-gray-900">{{ $event->category }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div
                                class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-400 font-bold mb-1">Organizer</p>
                                @if($event->organizer_name)
                                    <div class="flex items-center gap-2">
                                        @if($event->organizer_logo_path)
                                            <img src="{{ Str::startsWith($event->organizer_logo_path, ['http', 'https']) ? $event->organizer_logo_path : (file_exists(public_path($event->organizer_logo_path)) ? asset($event->organizer_logo_path) : asset('storage/' . $event->organizer_logo_path)) }}"
                                                class="w-6 h-6 rounded-full object-cover">
                                        @endif
                                        <p class="font-bold text-gray-900">{{ $event->organizer_name }}</p>
                                    </div>
                                @else
                                    <p class="font-bold text-gray-900 italic opacity-50">Not specified</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fees & Commission Card -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6">
                    <h4 class="text-sm font-bold text-gray-400 uppercase tracking-widest">Fees & Commission</h4>

                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div
                                class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-400 font-bold mb-1">Reseller Fee</p>
                                <p class="font-bold text-gray-900">
                                    {{ number_format($event->reseller_fee_value, 0, ',', '.') }}
                                    <span class="text-xs text-gray-500 font-medium">
                                        {{ $event->reseller_fee_type === 'percent' ? '%' : 'IDR' }}
                                    </span>
                                </p>
                                <p class="text-xs text-gray-500 font-medium">Type:
                                    {{ ucfirst($event->reseller_fee_type) }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div
                                class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-400 font-bold mb-1">Organizer Fee (Online)</p>
                                <p class="font-bold text-gray-900">
                                    {{ number_format($event->organizer_fee_online, 0, ',', '.') }}
                                    <span class="text-xs text-gray-500 font-medium">
                                        {{ $event->organizer_fee_online_type === 'percent' ? '%' : 'IDR' }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div
                                class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                    </path>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm text-gray-400 font-bold mb-1">Organizer Fee (Reseller)</p>
                                <p class="font-bold text-gray-900">
                                    {{ number_format($event->organizer_fee_reseller, 0, ',', '.') }}
                                    <span class="text-xs text-gray-500 font-medium">
                                        {{ $event->organizer_fee_reseller_type === 'percent' ? '%' : 'IDR' }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- QR Code Card -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 space-y-6">
                    <h4 class="text-sm font-bold text-gray-400 uppercase tracking-widest">Event QR Code</h4>

                    @php


                        $eventUrl = url('/events/' . $event->slug);

                        $qrCode = new \Endroid\QrCode\QrCode(
                            data: $eventUrl,
                            encoding: new \Endroid\QrCode\Encoding\Encoding('UTF-8'),
                            errorCorrectionLevel: \Endroid\QrCode\ErrorCorrectionLevel::High,
                            size: 300,
                            margin: 10,
                            roundBlockSizeMode: \Endroid\QrCode\RoundBlockSizeMode::Margin,
                            foregroundColor: new \Endroid\QrCode\Color\Color(0, 0, 0),
                            backgroundColor: new \Endroid\QrCode\Color\Color(255, 255, 255)
                        );

                        $writer = new \Endroid\QrCode\Writer\PngWriter();
                        $result = $writer->write($qrCode);
                        $dataUri = $result->getDataUri();
                    @endphp

                    <div class="space-y-4">
                        <!-- QR Code Image -->
                        <div
                            class="flex justify-center p-6 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-200">
                            <img src="{{ $dataUri }}" alt="QR Code for {{ $event->name }}"
                                class="w-full max-w-[250px] h-auto">
                        </div>

                        <!-- Event URL -->
                        <div class="space-y-2">
                            <p class="text-xs text-gray-400 font-bold uppercase tracking-wide">Purchase Page URL</p>
                            <div class="flex items-center gap-2 p-3 bg-gray-50 rounded-xl border border-gray-100">
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1">
                                    </path>
                                </svg>
                                <a href="{{ $eventUrl }}" target="_blank"
                                    class="text-sm text-primary hover:text-primary-700 font-medium truncate transition-colors">
                                    {{ $eventUrl }}
                                </a>
                            </div>
                        </div>

                        <!-- Download Button -->
                        <a href="{{ $dataUri }}" download="qr-code-{{ $event->slug }}.png"
                            class="w-full py-3 bg-primary text-white font-bold rounded-xl text-center hover:bg-primary-700 transition-all shadow-lg shadow-primary/25 active:scale-[0.98] flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                            </svg>
                            Download QR Code
                        </a>

                        <!-- Info Text -->
                        <p class="text-xs text-gray-500 text-center leading-relaxed">
                            Scan this QR code to access the event purchase page directly. Save and use it on printed
                            materials, posters, or digital promotions.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <x-notifications.delete />
    </div>
</x-layouts.admin>
