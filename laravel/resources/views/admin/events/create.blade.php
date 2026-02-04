<x-layouts.admin>
    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js" referrerpolicy="origin"></script>
        <script>
            tinymce.init({
                selector: 'textarea[name="description"], textarea[name="terms"]',
                plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
                toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
                height: 350,
                skin: 'oxide',
                promotion: false,
                branding: false,
                readonly: false,
                license_key: 'gpl',
                setup: function (editor) {
                    editor.on('change', function () {
                        editor.save();
                    });
                }
            });
        </script>
        <style>
            .tox-notifications-container {
                display: none !important;
            }

            .tox-statusbar__branding {
                display: none !important;
            }
        </style>
    @endpush
    <div class="max-w-5xl mx-auto">
        <h2 class="text-3xl font-bold text-gray-900 mb-8">Create Event</h2>

        <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <!-- 1. Banner & Thumbnail -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-lg font-bold text-gray-900 mb-6 border-b border-gray-100 pb-2">Banner & Thumbnail</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Banner -->
                    <div x-data="{ bannerPreview: null }">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Upload Banner <span
                                class="text-xs font-normal text-gray-500 ml-1">(16:9, Max 2MB)</span></label>
                        <div
                            class="border-2 border-dashed border-gray-300 rounded-xl w-full aspect-video overflow-hidden relative text-center hover:border-primary transition-colors cursor-pointer bg-gray-50/50 flex items-center justify-center">

                            <!-- Input -->
                            <input type="file" name="banner" accept="image/*"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20"
                                @change="const file = $event.target.files[0]; if(file){ bannerPreview = URL.createObjectURL(file); }">

                            <!-- Placeholder -->
                            <div x-show="!bannerPreview" class="pointer-events-none z-10">
                                <p class="text-gray-500 font-medium">Drag & drop files here ...</p>
                                <p class="text-xs text-gray-400 mt-1">(or click to select file)</p>
                            </div>

                            <!-- Preview -->
                            <template x-if="bannerPreview">
                                <img :src="bannerPreview" class="absolute inset-0 w-full h-full object-cover z-10">
                            </template>
                        </div>
                        @error('banner') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Thumbnail -->
                    <div x-data="{ thumbPreview: null }">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Upload Thumbnail <span
                                class="text-xs font-normal text-gray-500 ml-1">(1:1, Max 2MB)</span></label>
                        <div
                            class="border-2 border-dashed border-gray-300 rounded-xl w-full aspect-square max-w-sm overflow-hidden relative text-center hover:border-primary transition-colors cursor-pointer bg-gray-50/50 flex items-center justify-center">

                            <!-- Input -->
                            <input type="file" name="thumbnail" accept="image/*"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20"
                                @change="const file = $event.target.files[0]; if(file){ thumbPreview = URL.createObjectURL(file); }">

                            <!-- Placeholder -->
                            <div x-show="!thumbPreview" class="pointer-events-none z-10">
                                <p class="text-gray-500 font-medium text-sm">Drag & drop files here ...</p>
                                <p class="text-xs text-gray-400 mt-1">(or click to select file)</p>
                            </div>

                            <!-- Preview -->
                            <template x-if="thumbPreview">
                                <img :src="thumbPreview" class="absolute inset-0 w-full h-full object-cover z-10">
                            </template>
                        </div>
                        @error('thumbnail') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- 2. Event Detail -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-lg font-bold text-gray-900 mb-6 border-b border-gray-100 pb-2">Event Detail</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Event Name</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Enter Event Name"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Category</label>
                        <input type="text" name="category" value="{{ old('category') }}" placeholder="Enter Category"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        @error('category') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div x-data="{
                        open: false,
                        selected: '{{ old('status', 'draft') }}',
                        label: '{{ old('status') == 'active' ? 'Active' : 'Draft' }}'
                    }" class="relative">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Status</label>
                        <input type="hidden" name="status" :value="selected">
                        <button type="button" @click="open = !open" @click.away="open = false"
                            class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all bg-white text-dark font-bold">
                            <span x-text="label"
                                :class="selected === '' ? 'text-gray-400 font-medium' : 'text-dark'"></span>
                            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            class="absolute z-50 w-full mt-2 bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden">
                            <div class="py-1">
                                <button type="button" @click="selected = 'draft'; label = 'Draft'; open = false"
                                    class="w-full px-5 py-2.5 text-left hover:bg-primary/5 transition-colors text-sm font-bold text-dark border-l-4 border-transparent hover:border-primary">Draft</button>
                                <button type="button" @click="selected = 'active'; label = 'Active'; open = false"
                                    class="w-full px-5 py-2.5 text-left hover:bg-primary/5 transition-colors text-sm font-bold text-dark border-l-4 border-transparent hover:border-primary">Active</button>
                            </div>
                        </div>
                        @error('status') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div x-data
                        x-init="flatpickr($refs.picker, { enableTime: true, dateFormat: 'Y-m-d H:i', time_24hr: true, minDate: 'today', altInput: true, altFormat: 'd M Y, H:i', altInputClass: 'w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all bg-white cursor-pointer' })">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Start Date</label>
                        <input x-ref="picker" type="text" name="start_date" value="{{ old('start_date') }}"
                            placeholder="Select Start Date" class="hidden">
                        @error('start_date') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div x-data
                        x-init="flatpickr($refs.picker, { enableTime: true, dateFormat: 'Y-m-d H:i', time_24hr: true, minDate: 'today', altInput: true, altFormat: 'd M Y, H:i', altInputClass: 'w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all bg-white cursor-pointer' })">
                        <label class="block text-sm font-bold text-gray-700 mb-2">End Date</label>
                        <input x-ref="picker" type="text" name="end_date" value="{{ old('end_date') }}"
                            placeholder="Select End Date" class="hidden">
                        @error('end_date') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Description</label>
                        <textarea name="description" rows="4" placeholder="Enter Description"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">{{ old('description') }}</textarea>
                        @error('description') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Term & Condition</label>
                        <textarea name="terms" rows="4" placeholder="Enter Term & Condition"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">{{ old('terms') }}</textarea>
                        @error('terms') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- 3. Event Location -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-lg font-bold text-gray-900 mb-6 border-b border-gray-100 pb-2">Event Location</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Location (Venue)</label>
                        <input type="text" name="location" value="{{ old('location') }}" placeholder="Enter Location"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        @error('location') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Province</label>
                        <input type="text" name="province" value="{{ old('province') }}" placeholder="Enter Province"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        @error('province') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">City</label>
                        <input type="text" name="city" value="{{ old('city') }}" placeholder="Enter City"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        @error('city') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">ZIP</label>
                        <input type="text" name="zip" value="{{ old('zip') }}" placeholder="Enter ZIP"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        @error('zip') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <!-- Spacer or Map placeholder could go here -->
                    </div>

                    <div class="md:col-span-2" x-data="{ mapCode: '{{ old('google_map_embed') }}' }">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Google Map Embed Code</label>
                        <textarea name="google_map_embed" rows="3" x-model="mapCode"
                            placeholder="Paste Google Map Embed (iframe) code here..."
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all font-mono text-xs"></textarea>
                        @error('google_map_embed') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror

                        <div x-show="mapCode"
                            class="mt-4 rounded-xl overflow-hidden shadow-sm border border-gray-100 aspect-video w-full">
                            <div x-html="mapCode" class="w-full h-full [&_iframe]:w-full [&_iframe]:h-full"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Event Meta -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-lg font-bold text-gray-900 mb-6 border-b border-gray-100 pb-2">Event Meta</h3>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">SEO Title</label>
                        <input type="text" name="seo_title" value="{{ old('seo_title') }}" placeholder="Enter SEO Title"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">SEO Description</label>
                        <textarea name="seo_description" rows="3" placeholder="Enter SEO Description"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">{{ old('seo_description') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- 5. Event Organizer -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-lg font-bold text-gray-900 mb-6 border-b border-gray-100 pb-2">Event Organizer</h3>

                <div class="space-y-6">
                    <div>
                        <div x-data="{ organizerLogoPreview: null }">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Organizer Logo <span
                                    class="text-xs font-normal text-gray-500 ml-1">(Max 2MB)</span></label>
                            <div
                                class="border-2 border-dashed border-gray-300 rounded-xl max-h-48 h-48 w-48 overflow-hidden relative text-center hover:border-primary transition-colors cursor-pointer bg-gray-50/50 flex items-center justify-center">

                                <!-- Input -->
                                <input type="file" name="organizer_logo" accept="image/*"
                                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-20"
                                    @change="const file = $event.target.files[0]; if(file){ organizerLogoPreview = URL.createObjectURL(file); }">

                                <!-- Placeholder -->
                                <div x-show="!organizerLogoPreview" class="pointer-events-none z-10 px-4">
                                    <p class="text-gray-500 font-medium text-xs">Upload Logo</p>
                                </div>

                                <!-- Preview -->
                                <template x-if="organizerLogoPreview">
                                    <img :src="organizerLogoPreview"
                                        class="absolute inset-0 w-full h-full object-cover z-10">
                                </template>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Organizer Name</label>
                        <input type="text" name="organizer_name" value="{{ old('organizer_name') }}"
                            placeholder="Enter Organizer Name"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    </div>
                </div>
            </div>
            <!-- 6. Fees & Commission -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-lg font-bold text-gray-900 mb-6 border-b border-gray-100 pb-2">Fees & Commission
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div x-data="{
                            open: false,
                            selected: '{{ old('reseller_fee_type', 'fixed') }}',
                            label: '{{ old('reseller_fee_type') == 'percent' ? 'Percent (%)' : 'Fixed' }}'
                        }" class="relative">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Reseller Fee Type</label>
                        <input type="hidden" name="reseller_fee_type" :value="selected">
                        <button type="button" @click="open = !open" @click.away="open = false"
                            class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all bg-white text-dark font-bold">
                            <span x-text="label"></span>
                            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            class="absolute z-50 w-full mt-2 bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden">
                            <div class="py-1">
                                <button type="button" @click="selected = 'fixed'; label = 'Fixed'; open = false"
                                    class="w-full px-5 py-2.5 text-left hover:bg-primary/5 transition-colors text-sm font-bold text-dark border-l-4 border-transparent hover:border-primary">Fixed</button>
                                <button type="button" @click="selected = 'percent'; label = 'Percent (%)'; open = false"
                                    class="w-full px-5 py-2.5 text-left hover:bg-primary/5 transition-colors text-sm font-bold text-dark border-l-4 border-transparent hover:border-primary">Percent
                                    (%)</button>
                            </div>
                        </div>
                        @error('reseller_fee_type') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Reseller Fee Value</label>
                        <input type="number" name="reseller_fee_value" value="{{ old('reseller_fee_value', 0) }}"
                            min="0" step="0.01" placeholder="0"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        @error('reseller_fee_value') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="col-span-1 md:col-span-2 border-t border-gray-100"></div>

                    <!-- Organizer Fee Online -->
                    <div class="col-span-1 md:col-span-2 border-t border-gray-100"></div>

                    <div x-data="{
                            open: false,
                            selected: '{{ old('organizer_fee_online_type', 'fixed') }}',
                            label: '{{ old('organizer_fee_online_type') == 'percent' ? 'Percent (%)' : 'Fixed' }}'
                        }" class="relative">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Organizer Fee (Online Sales)
                            Type</label>
                        <input type="hidden" name="organizer_fee_online_type" :value="selected">
                        <button type="button" @click="open = !open" @click.away="open = false"
                            class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all bg-white text-dark font-bold">
                            <span x-text="label"></span>
                            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            class="absolute z-50 w-full mt-2 bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden">
                            <div class="py-1">
                                <button type="button" @click="selected = 'fixed'; label = 'Fixed'; open = false"
                                    class="w-full px-5 py-2.5 text-left hover:bg-primary/5 transition-colors text-sm font-bold text-dark border-l-4 border-transparent hover:border-primary">Fixed</button>
                                <button type="button" @click="selected = 'percent'; label = 'Percent (%)'; open = false"
                                    class="w-full px-5 py-2.5 text-left hover:bg-primary/5 transition-colors text-sm font-bold text-dark border-l-4 border-transparent hover:border-primary">Percent
                                    (%)</button>
                            </div>
                        </div>
                        @error('organizer_fee_online_type') <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Organizer Fee (Online Sales)
                            Value</label>
                        <input type="number" name="organizer_fee_online" value="{{ old('organizer_fee_online', 0) }}"
                            min="0" step="0.01" placeholder="0"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        @error('organizer_fee_online') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    <!-- Organizer Fee Reseller -->
                    <div x-data="{
                            open: false,
                            selected: '{{ old('organizer_fee_reseller_type', 'fixed') }}',
                            label: '{{ old('organizer_fee_reseller_type') == 'percent' ? 'Percent (%)' : 'Fixed' }}'
                        }" class="relative">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Organizer Fee (Reseller Sales)
                            Type</label>
                        <input type="hidden" name="organizer_fee_reseller_type" :value="selected">
                        <button type="button" @click="open = !open" @click.away="open = false"
                            class="w-full flex items-center justify-between px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all bg-white text-dark font-bold">
                            <span x-text="label"></span>
                            <svg class="w-4 h-4 text-gray-400 transition-transform duration-200"
                                :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            class="absolute z-50 w-full mt-2 bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden">
                            <div class="py-1">
                                <button type="button" @click="selected = 'fixed'; label = 'Fixed'; open = false"
                                    class="w-full px-5 py-2.5 text-left hover:bg-primary/5 transition-colors text-sm font-bold text-dark border-l-4 border-transparent hover:border-primary">Fixed</button>
                                <button type="button" @click="selected = 'percent'; label = 'Percent (%)'; open = false"
                                    class="w-full px-5 py-2.5 text-left hover:bg-primary/5 transition-colors text-sm font-bold text-dark border-l-4 border-transparent hover:border-primary">Percent
                                    (%)</button>
                            </div>
                        </div>
                        @error('organizer_fee_reseller_type') <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Organizer Fee (Reseller Sales)
                            Value</label>
                        <input type="number" name="organizer_fee_reseller"
                            value="{{ old('organizer_fee_reseller', 0) }}" min="0" step="0.01" placeholder="0"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        @error('organizer_fee_reseller') <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="flex justify-end pt-4 pb-12">
                <button type="submit"
                    class="px-8 py-4 bg-primary text-white font-bold rounded-xl hover:bg-primary-700 transition-colors shadow-lg shadow-primary/25 text-lg w-full md:w-auto">
                    Create Event
                </button>
            </div>

        </form>
    </div>
</x-layouts.admin>