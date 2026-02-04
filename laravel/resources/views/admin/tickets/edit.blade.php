<x-layouts.admin>
    <div class="max-w-3xl mx-auto">
        <h2 class="text-3xl font-bold text-gray-900 mb-8">Edit Ticket for {{ $event->name }}</h2>

        <form action="{{ route('admin.tickets-report.update', $ticket) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">

                <!-- Ticket Event (Readonly) -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Ticket Event</label>
                    <input type="text" value="{{ $event->name }}" disabled
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 bg-gray-50 text-gray-500 cursor-not-allowed">
                </div>

                <!-- Status -->
                <div class="flex items-center gap-3">
                    <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $ticket->is_active) ? 'checked' : '' }}
                        class="w-5 h-5 rounded text-primary focus:ring-primary/20 border-gray-300">
                    <label for="is_active" class="text-sm font-bold text-gray-700 select-none cursor-pointer">Active
                        Ticket</label>
                </div>

                <!-- Name -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Name</label>
                    <input type="text" name="name" value="{{ old('name', $ticket->name) }}" placeholder="Enter Name"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Price -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Price</label>
                    <input type="number" name="price" value="{{ old('price', $ticket->price) }}"
                        placeholder="Enter Price" step="1"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    <p class="text-xs text-gray-500 mt-1">* Input 0 to set free ticket</p>
                    @error('price') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Quota -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Quota</label>
                    <input type="number" name="quota" value="{{ old('quota', $ticket->quota) }}"
                        placeholder="Enter Quota"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    @error('quota') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Maximum Purchase User -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Maximum Purchase User</label>
                    <input type="number" name="max_purchase_per_user"
                        value="{{ old('max_purchase_per_user', $ticket->max_purchase_per_user) }}"
                        placeholder="Enter Maximum Purchase User"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    @error('max_purchase_per_user') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <!-- Sale Period -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div x-data
                        x-init="flatpickr($refs.picker, { enableTime: true, dateFormat: 'Y-m-d H:i', time_24hr: true, defaultDate: '{{ old('start_date', $ticket->start_date->format('Y-m-d H:i')) }}' })">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Sale Start Date</label>
                        <input x-ref="picker" type="text" name="start_date"
                            value="{{ old('start_date', $ticket->start_date->format('Y-m-d H:i')) }}"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all bg-white cursor-pointer">
                        @error('start_date') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div x-data
                        x-init="flatpickr($refs.picker, { enableTime: true, dateFormat: 'Y-m-d H:i', time_24hr: true, defaultDate: '{{ old('end_date', $ticket->end_date->format('Y-m-d H:i')) }}' })">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Sale End Date</label>
                        <input x-ref="picker" type="text" name="end_date"
                            value="{{ old('end_date', $ticket->end_date->format('Y-m-d H:i')) }}"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all bg-white cursor-pointer">
                        @error('end_date') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="4" placeholder="Enter Description"
                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">{{ old('description', $ticket->description) }}</textarea>
                    @error('description') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

            </div>

            <!-- Submit -->
            <div class="flex justify-end pt-4 pb-12">
                <button type="submit"
                    class="px-8 py-4 bg-primary text-white font-bold rounded-xl hover:bg-primary/90 transition-colors shadow-lg shadow-primary/25 text-lg w-full md:w-auto">
                    Update Ticket
                </button>
            </div>

        </form>
    </div>
</x-layouts.admin>