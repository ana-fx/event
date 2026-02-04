<x-layouts.admin>
    <div class="max-w-4xl mx-auto" x-data="{ activeTab: 'profile' }">

        <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-bold text-gray-900">Account Settings</h2>

            <!-- Tabs Navigation -->
            <div class="flex bg-gray-100 p-1 rounded-xl">
                <button @click="activeTab = 'profile'"
                        :class="{ 'bg-white shadow text-gray-900': activeTab === 'profile', 'text-gray-500 hover:text-gray-700': activeTab !== 'profile' }"
                        class="px-4 py-2 rounded-lg text-sm font-bold transition-all">
                    Profile
                </button>
                <button @click="activeTab = 'security'"
                        :class="{ 'bg-white shadow text-gray-900': activeTab === 'security', 'text-gray-500 hover:text-gray-700': activeTab !== 'security' }"
                        class="px-4 py-2 rounded-lg text-sm font-bold transition-all">
                    Security
                </button>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-6 p-4 rounded-lg bg-green-50 text-green-700 border border-green-200">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-lg bg-red-50 text-red-700 border border-red-200">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden min-h-[500px]">

            <form action="{{ route('admin.profile.update') }}" method="POST" enctype="multipart/form-data" class="p-8">
                @csrf
                @method('PUT')

                <!-- Hidden Input for Tab State Persistence (Optional, if we want to submit per tab, but single form is easier) -->
                <!-- We will use a single form for now, but visually separate them. Ideally split into two forms/routes for strict separation -->

                <!-- PROFILE TAB -->
                <div x-show="activeTab === 'profile'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">

                     <!-- Photo Section -->
                    <div class="flex items-center gap-6 pb-8 border-b border-gray-100 mb-8">
                        <div class="shrink-0 relative">
                            @if($user->profile_photo_path)
                                <img src="{{ Str::startsWith($user->profile_photo_path, ['http', 'https']) ? $user->profile_photo_path : (file_exists(public_path($user->profile_photo_path)) ? asset($user->profile_photo_path) : asset('storage/' . $user->profile_photo_path)) }}" alt="{{ $user->name }}" class="w-24 h-24 rounded-full object-cover ring-4 ring-gray-50">
                            @else
                                 <div class="w-24 h-24 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center text-3xl font-bold ring-4 ring-gray-50">
                                    {{ $user->initials() }}
                                </div>
                            @endif
                            <label for="photo" class="absolute bottom-0 right-0 bg-white border border-gray-200 rounded-full p-1.5 shadow-sm cursor-pointer hover:bg-gray-50 transition-colors">
                                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                <input type="file" name="photo" id="photo" class="hidden" accept="image/*">
                            </label>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Profile Photo</h3>
                            <p class="text-sm text-gray-500 mt-1">Update your profile picture. Max 1MB.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                             <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                             <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        </div>

                         <div>
                             <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                             <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+1 (555) 000-0000" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        </div>

                         <div class="md:col-span-2">
                             <label class="block text-sm font-medium text-gray-700 mb-2">Bio / About</label>
                             <textarea name="bio" rows="3" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all" placeholder="Tell us a little about yourself...">{{ old('bio', $user->bio) }}</textarea>
                        </div>

                        <div class="md:col-span-2">
                             <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                             <textarea name="address" rows="2" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all" placeholder="123 Main St, City, Country">{{ old('address', $user->address) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- SECURITY TAB -->
                <div x-show="activeTab === 'security'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0" style="display: none;">

                    <div class="space-y-6 max-w-xl">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-1">Update Email & Password</h3>
                            <p class="text-sm text-gray-500 mb-6">Ensure your account is using a long, random password to stay secure.</p>
                        </div>

                        <div>
                             <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                             <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        </div>

                        <div class="pt-4 border-t border-gray-100 my-4"></div>

                        <div>
                             <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                             <input type="password" name="current_password" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all" placeholder="For verification">
                             <p class="text-xs text-gray-400 mt-1">Required to change email or password.</p>
                        </div>

                        <div>
                             <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                             <input type="password" name="password" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        </div>

                         <div>
                             <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                             <input type="password" name="password_confirmation" class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        </div>
                    </div>

                </div>

                <!-- Submit Button (Shared) -->
                <div class="flex justify-end pt-8 border-t border-gray-100 mt-8">
                    <button type="submit" class="px-8 py-3 bg-primary text-white font-bold rounded-xl hover:bg-primary-700 transition-colors shadow-lg shadow-primary/25">
                        Save Changes
                    </button>
                </div>

            </form>
        </div>
    </div>
</x-layouts.admin>
