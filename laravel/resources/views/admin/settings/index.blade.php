<x-layouts.admin title="Global Settings">
    <div class="space-y-8">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-black text-dark uppercase tracking-tight">Global Settings</h1>
                <p class="text-gray-400 mt-1">Configure your website's identity, SEO, and contact information.</p>
            </div>
        </div>

        @if($errors->any())
            <div class="p-4 bg-gray-50 border border-gray-100 text-gray-500 rounded-2xl animate-fade-in">
                <ul class="list-disc list-inside text-sm font-bold">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div
                class="p-4 bg-green-50 border border-gray-100 text-dark rounded-2xl flex items-center gap-3 animate-fade-in">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="font-bold text-sm">{{ session('success') }}</span>
            </div>
        @endif

        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data"
            class="space-y-8" x-data="{ 
                  logoPreview: '{{ isset($settings['site_logo']) ? asset('storage/' . $settings['site_logo']) : '' }}',
                  logoWhitePreview: '{{ isset($settings['site_logo_white']) ? asset('storage/' . $settings['site_logo_white']) : '' }}',
                  iconPreview: '{{ isset($settings['site_icon']) ? asset('storage/' . $settings['site_icon']) : '' }}',
                  handleLogo(e) {
                      const file = e.target.files[0];
                      if (file) this.logoPreview = URL.createObjectURL(file);
                  },
                  handleLogoWhite(e) {
                      const file = e.target.files[0];
                      if (file) this.logoWhitePreview = URL.createObjectURL(file);
                  },
                  handleIcon(e) {
                      const file = e.target.files[0];
                      if (file) this.iconPreview = URL.createObjectURL(file);
                  }
              }">
            @csrf
            @method('PUT')

            <!-- Identity & Branding -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-50 bg-gray-50/50 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.172-1.172a4 4 0 115.656 5.656L17 13" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-black text-dark uppercase tracking-tight">Site Identity</h2>
                </div>
                <div class="p-5 space-y-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-2">
                            <label class="text-xs font-black text-dark uppercase tracking-widest px-1">Site Name</label>
                            <input type="text" name="site_name" value="{{ $settings['site_name'] ?? '' }}"
                                class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all font-medium">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="space-y-2">
                            <label class="text-xs font-black text-dark uppercase tracking-widest px-1">Logo
                                (Dark/Normal)</label>
                            <input type="file" name="site_logo" class="hidden" id="logo-input" @change="handleLogo">
                            <label for="logo-input"
                                class="flex flex-col items-center justify-center h-24 bg-gray-50 border-2 border-dashed border-gray-100 rounded-2xl cursor-pointer hover:bg-gray-100 transition-all relative overflow-hidden">
                                <template x-if="logoPreview">
                                    <img :src="logoPreview" class="h-10 object-contain relative z-10">
                                </template>
                                <template x-if="!logoPreview">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </template>
                            </label>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black text-dark uppercase tracking-widest px-1">Logo
                                (White/Light)</label>
                            <input type="file" name="site_logo_white" class="hidden" id="logo-white-input"
                                @change="handleLogoWhite">
                            <label for="logo-white-input"
                                class="flex flex-col items-center justify-center h-24 bg-dark border-2 border-dashed border-gray-700 rounded-2xl cursor-pointer hover:bg-gray-900 transition-all relative overflow-hidden">
                                <template x-if="logoWhitePreview">
                                    <img :src="logoWhitePreview" class="h-10 object-contain relative z-10">
                                </template>
                                <template x-if="!logoWhitePreview">
                                    <svg class="w-6 h-6 text-gray-600" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </template>
                            </label>
                        </div>
                        <div class="space-y-2">
                            <label class="text-xs font-black text-dark uppercase tracking-widest px-1">Favicon
                                (Icon)</label>
                            <input type="file" name="site_icon" class="hidden" id="icon-input" @change="handleIcon">
                            <label for="icon-input"
                                class="flex flex-col items-center justify-center h-24 bg-gray-50 border-2 border-dashed border-gray-100 rounded-2xl cursor-pointer hover:bg-gray-100 transition-all relative overflow-hidden">
                                <template x-if="iconPreview">
                                    <img :src="iconPreview" class="h-10 w-10 object-contain relative z-10">
                                </template>
                                <template x-if="!iconPreview">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 3v4M3 5h4M6 17v4m-2-4h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                    </svg>
                                </template>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEO Settings -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-50 bg-gray-50/50 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gray-50 text-dark flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-black text-dark uppercase tracking-tight">SEO Configuration</h2>
                </div>
                <div class="p-5 space-y-6">
                    <div class="space-y-2">
                        <label class="text-xs font-black text-dark uppercase tracking-widest px-1">Meta Title</label>
                        <input type="text" name="seo_title" value="{{ $settings['seo_title'] ?? '' }}"
                            class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all font-medium"
                            placeholder="Primary title for search engines">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-black text-dark uppercase tracking-widest px-1">Meta
                            Description</label>
                        <textarea name="seo_description" rows="3"
                            class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all font-medium"
                            placeholder="Briefly describe what your site is about...">{{ $settings['seo_description'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-50 bg-gray-50/50 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gray-50 text-dark flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-black text-dark uppercase tracking-tight">Contact Information</h2>
                </div>
                <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-2">
                        <label class="text-xs font-black text-dark uppercase tracking-widest px-1">Contact Email</label>
                        <input type="email" name="contact_email" value="{{ $settings['contact_email'] ?? '' }}"
                            class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all font-medium">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-black text-dark uppercase tracking-widest px-1">WhatsApp Info</label>
                        <input type="text" name="contact_whatsapp" value="{{ $settings['contact_whatsapp'] ?? '' }}"
                            class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all font-medium"
                            placeholder="e.g. +62 812 3456 7890">
                    </div>
                    <div class="md:col-span-2 space-y-2">
                        <label class="text-xs font-black text-dark uppercase tracking-widest px-1">Physical
                            Location</label>
                        <textarea name="contact_location" rows="2"
                            class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all font-medium"
                            placeholder="Full address of the office or venue...">{{ $settings['contact_location'] ?? '' }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Social Media -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-50 bg-gray-50/50 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gray-50 text-dark flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.172 13.828a4 4 0 015.656 0l4 4a4 4 0 11-5.656 5.656l-1.101-1.102" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-black text-dark uppercase tracking-tight">Social Media</h2>
                </div>
                <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-2">
                        <label class="text-xs font-black text-dark uppercase tracking-widest px-1">Facebook URL</label>
                        <input type="text" name="social_facebook" value="{{ $settings['social_facebook'] ?? '' }}"
                            class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all font-medium"
                            placeholder="https://facebook.com/your-page">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-black text-dark uppercase tracking-widest px-1">Twitter URL</label>
                        <input type="text" name="social_twitter" value="{{ $settings['social_twitter'] ?? '' }}"
                            class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all font-medium"
                            placeholder="https://twitter.com/your-profile">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-black text-dark uppercase tracking-widest px-1">Instagram URL</label>
                        <input type="text" name="social_instagram" value="{{ $settings['social_instagram'] ?? '' }}"
                            class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all font-medium"
                            placeholder="https://instagram.com/your-profile">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-black text-dark uppercase tracking-widest px-1">TikTok URL</label>
                        <input type="text" name="social_tiktok" value="{{ $settings['social_tiktok'] ?? '' }}"
                            class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all font-medium"
                            placeholder="https://tiktok.com/@your-profile">
                    </div>
                </div>
            </div>

            <!-- Payment Configuration -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-5 py-3 border-b border-gray-50 bg-gray-50/50 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gray-50 text-dark flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h2 class="text-xl font-black text-dark uppercase tracking-tight">Payment Configuration</h2>
                </div>
                <div class="p-5 grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div class="space-y-2">
                        <label class="text-xs font-black text-dark uppercase tracking-widest px-1">QRIS Fee (%)</label>
                        <p class="text-xs text-gray-500 px-1 mb-2">Percentage fee for QRIS (e.g. 0.7%)</p>
                        <input type="number" step="0.01" name="fee_qris_percent"
                            value="{{ $settings['fee_qris_percent'] ?? '' }}"
                            class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all font-medium"
                            placeholder="0.7">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-black text-dark uppercase tracking-widest px-1">Bank Transfer
                            Fee</label>
                        <p class="text-xs text-gray-500 px-1 mb-2">Fixed fee for Bank (e.g. 4000)</p>
                        <input type="number" name="fee_bank_fixed" value="{{ $settings['fee_bank_fixed'] ?? '' }}"
                            class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all font-medium"
                            placeholder="4000">
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-black text-dark uppercase tracking-widest px-1">Handling Fee</label>
                        <p class="text-xs text-gray-500 px-1 mb-2">Platform fee per transaction (IDR)</p>
                        <input type="number" name="handling_fee" value="{{ $settings['handling_fee'] ?? '' }}"
                            class="w-full px-4 py-3 bg-gray-50 border-gray-100 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary transition-all font-medium"
                            placeholder="e.g. 5000">
                    </div>
                </div>
            </div>

            <!-- Action Button -->
            <div class="flex justify-end pt-4">
                <button type="submit"
                    class="px-12 py-5 bg-primary text-white font-black rounded-2xl shadow-xl shadow-primary/20 hover:bg-dark hover:-translate-y-1 transition-all active:scale-95 text-lg uppercase tracking-widest">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>