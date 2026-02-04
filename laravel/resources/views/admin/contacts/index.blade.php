<x-layouts.admin>
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-dark">Contact Messages</h1>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs uppercase tracking-wider text-gray-500 font-bold">
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">From</th>
                        <th class="px-6 py-4">Subject</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($contacts as $contact)
                        <tr class="hover:bg-gray-50/50 transition-colors {{ $contact->read_at ? '' : 'bg-primary/5' }}">
                            <td class="px-6 py-4">
                                @if($contact->read_at)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Read
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                        Unread
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $contact->created_at->format('M d, Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-medium text-dark">{{ $contact->name }}</div>
                                <div class="text-xs text-secondary">{{ $contact->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-dark font-medium">
                                {{ Str::limit($contact->subject, 30) }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.contacts.show', $contact) }}" class="text-primary hover:text-primary/80 font-bold text-sm">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                                No messages found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($contacts->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $contacts->links() }}
            </div>
        @endif
    </div>
</x-layouts.admin>
