<x-layouts.admin>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-dark">Reseller Accounts</h1>
        <a href="{{ route('admin.resellers.create') }}"
            class="px-5 py-2.5 bg-primary text-white font-bold rounded-xl shadow-lg shadow-primary/30 hover:bg-primary/90 transition-all flex items-center gap-2">
            Add Reseller
        </a>
    </div>

    @if (session('success'))
        <div class="mb-6 p-4 rounded-lg bg-green-50 text-green-700 border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-gray-50 border-b border-gray-100 text-xs uppercase tracking-wider text-gray-500 font-bold">
                        <th class="px-6 py-4">Name</th>
                            <th class="px-6 py-4">Email</th>
                            <th class="px-6 py-4">Joined</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($resellers as $reseller)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                             <td class="px-6 py-4">
                                <div class="font-bold text-dark">{{ $reseller->name }}</div>
                            </td>

                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $reseller->email }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $reseller->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Status Badge -->
                                    <span class="px-2.5 py-1 text-xs font-bold rounded-lg {{ $reseller->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $reseller->is_active ? 'Active' : 'Disabled' }}
                                    </span>

                                    <!-- Toggle Active Button -->
                                    <form action="{{ route('admin.resellers.toggle-active', $reseller) }}" method="POST" class="inline-block">
                                        @csrf
                                        <button type="submit"
                                            class="p-2 text-gray-400 rounded-lg hover:text-primary hover:bg-primary/5 transition-colors"
                                            title="{{ $reseller->is_active ? 'Disable Account' : 'Enable Account' }}">
                                            @if($reseller->is_active)
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            @endif
                                        </button>
                                    </form>

                                    <a href="{{ route('admin.resellers.edit', $reseller) }}"
                                        class="p-2 text-gray-400 rounded-lg hover:text-primary hover:bg-primary/5 transition-colors"
                                        title="Edit">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>

                                    <form action="{{ route('admin.resellers.destroy', $reseller) }}" method="POST"
                                        onsubmit="return confirm('Delete this reseller?');" class="inline-block">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-2 text-gray-400 rounded-lg hover:text-red-600 hover:bg-red-50 transition-colors"
                                            title="Delete">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                         <tr>
                            <td colspan="{{ $view === 'financial' ? 5 : 4 }}" class="px-6 py-12 text-center text-gray-400">
                                <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-gray-300">
                                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                </div>
                                No reseller users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($resellers->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $resellers->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
