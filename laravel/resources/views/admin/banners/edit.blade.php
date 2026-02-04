<x-layouts.admin>
    <div class="max-w-5xl mx-auto">
        <div class="mb-8 flex items-center gap-4">
            <a href="{{ route('admin.banners.index') }}"
                class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm border border-gray-100 text-secondary hover:text-dark transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <h2 class="text-3xl font-bold text-gray-900">Edit Banner</h2>
        </div>

        <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            @method('PUT')

            <!-- 1. Banner Image -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-lg font-bold text-gray-900 mb-6 border-b border-gray-100 pb-2">Banner Image</h3>

                <div x-data="{ bannerPreview: '{{ asset('storage/' . $banner->image_path) }}' }">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Upload New Banner <span
                            class="text-xs font-normal text-gray-500 ml-1">(3:1 Recommended, Max 2MB)</span></label>
                    <div
                        class="border-2 border-dashed border-gray-300 rounded-xl w-full aspect-[3/1] overflow-hidden relative text-center hover:border-blue-500 transition-colors cursor-pointer bg-gray-50/50 flex items-center justify-center">

                        <!-- Input -->
                        <input type="file" name="image" accept="image/*"
                            class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20"
                            @change="const file = $event.target.files[0]; if(file){ bannerPreview = URL.createObjectURL(file); }">

                        <!-- Placeholder (Hidden when preview exists) -->
                        <div x-show="!bannerPreview" class="pointer-events-none z-10">
                            <p class="text-gray-500 font-medium">Drag & drop banner here ...</p>
                            <p class="text-xs text-gray-400 mt-1">(or click to select file)</p>
                        </div>

                        <!-- Preview -->
                        <template x-if="bannerPreview">
                            <img :src="bannerPreview" class="absolute inset-0 w-full h-full object-cover z-10">
                        </template>
                    </div>
                    @error('image') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- 2. Banner Details -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-lg font-bold text-gray-900 mb-6 border-b border-gray-100 pb-2">Banner Details</h3>

                <div class="grid grid-cols-1 gap-6">


                    <div x-data="{ linkType: '{{ $banner->link_url && str_contains($banner->link_url, route('events.index')) ? 'event' : 'url' }}' }">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Link Destination</label>

                        <!-- Toggle -->
                        <div class="flex items-center gap-4 mb-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="link_type" value="event" x-model="linkType" class="text-primary focus:ring-primary">
                                <span class="text-sm font-medium text-gray-700">Link to Event</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="link_type" value="url" x-model="linkType" class="text-primary focus:ring-primary">
                                <span class="text-sm font-medium text-gray-700">Custom URL</span>
                            </label>
                        </div>

                        <!-- Event Select -->
                        <div x-show="linkType === 'event'" x-data="{
                            open: false,
                            selected: '{{ old('event_id', $banner->link_url) }}',
                            label: 'Select an Event'
                        }" x-init="
                            @php
                                $currentEvent = null;
                                foreach($events as $event) {
                                    if (route('events.show', $event) == $banner->link_url) {
                                        $currentEvent = $event;
                                        break;
                                    }
                                }
                            @endphp
                            @if($currentEvent) label = '{{ addslashes($currentEvent->name) }}'; @endif
                        ">
                            <input type="hidden" name="event_id" :value="selected">
                            <div class="relative">
                                <button type="button" @click="open = !open" @click.away="open = false"
                                    class="w-full flex items-center justify-between px-6 py-4 rounded-2xl bg-gray-50 text-sm font-bold border border-gray-100 focus:bg-white focus:ring-4 focus:ring-primary/10 transition-all text-dark">
                                    <span x-text="label" :class="selected === '' ? 'text-gray-400 font-medium' : 'text-dark'"></span>
                                    <svg class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>

                                <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                    class="absolute z-50 w-full mt-2 bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden">
                                    <div class="px-6 py-4 bg-gray-50/50 border-b border-gray-100">
                                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Select Destination Event</span>
                                    </div>
                                    <div class="py-2 max-h-60 overflow-y-auto custom-scrollbar">
                                        <button type="button" @click="selected = ''; label = 'Select an Event'; open = false" class="w-full px-6 py-3 text-left hover:bg-primary/5 transition-colors text-sm font-bold text-dark">None (Clear Selection)</button>
                                        @foreach($events as $event)
                                            <button type="button" @click="selected = '{{ route('events.show', $event) }}'; label = '{{ addslashes($event->name) }}'; open = false" class="w-full px-6 py-3 text-left hover:bg-primary/5 transition-colors text-sm font-bold text-dark border-l-2 border-transparent hover:border-primary">{{ $event->name }}</button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <p class="text-xs text-gray-400 mt-2 ml-1">Selecting an event will automatically set the banner link destination.</p>
                        </div>

                        <!-- Custom URL Input -->
                        <div x-show="linkType === 'url'" style="display: none;">
                            <input type="url" name="link_url" value="{{ old('link_url', $banner->link_url) }}"
                                placeholder="https://example.com/promo"
                                class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        </div>
                        @error('link_url') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ $banner->is_active ? 'checked' : '' }}
                            class="rounded border-gray-300 text-primary focus:ring-primary w-5 h-5">
                        <label for="is_active" class="text-sm font-medium text-gray-700">Active Status</label>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-4">
                <button type="submit"
                    class="px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-500 text-white font-bold rounded-xl hover:from-blue-700 hover:to-blue-600 transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                    Update Banner
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
