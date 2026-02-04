<x-layouts.admin>
    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div>
                <a href="{{ route('admin.scanners.index') }}"
                    class="inline-flex items-center text-sm text-gray-500 hover:text-dark mb-2 transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Scanner List
                </a>
                <h2 class="text-3xl font-bold text-gray-900">Edit Scanner</h2>
            </div>
        </div>

        @if (session('error'))
            <div class="mb-6 p-4 rounded-lg bg-red-50 text-red-700 border border-red-200">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <form action="{{ route('admin.scanners.update', $scanner) }}" method="POST" enctype="multipart/form-data" class="p-8">
                @csrf
                @method('PUT')

                <!-- Photo Section -->
                <div class="flex items-center gap-6 pb-8 border-b border-gray-100 mb-8">
                    <div class="shrink-0 relative">
                        @if($scanner->profile_photo_path)
                            <img src="{{ Str::startsWith($scanner->profile_photo_path, ['http', 'https']) ? $scanner->profile_photo_path : (file_exists(public_path($scanner->profile_photo_path)) ? asset($scanner->profile_photo_path) : asset('storage/' . $scanner->profile_photo_path)) }}" alt="{{ $scanner->name }}" class="w-24 h-24 rounded-full object-cover ring-4 ring-gray-50">
                        @else
                             <div class="w-24 h-24 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center text-3xl font-bold ring-4 ring-gray-50">
                                {{ $scanner->initials() }}
                            </div>
                        @endif
                        <label for="photo" class="absolute bottom-0 right-0 bg-white border border-gray-200 rounded-full p-1.5 shadow-sm cursor-pointer hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <input type="file" name="photo" id="photo" class="hidden" accept="image/*">
                        </label>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">Profile Photo</h3>
                        <p class="text-sm text-gray-500 mt-1">Scanner avatar. Max 1MB.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <div class="md:col-span-1">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $scanner->name) }}" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        @error('name') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-1">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $scanner->email) }}" required
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                        @error('email') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-1">
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $scanner->phone) }}"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
                    </div>

                    <div class="md:col-span-2">
                        <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                        <textarea name="bio" id="bio" rows="3"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">{{ old('bio', $scanner->bio) }}</textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                        <textarea name="address" id="address" rows="2"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">{{ old('address', $scanner->address) }}</textarea>
                    </div>

                    <div class="md:col-span-2 pt-6 border-t border-gray-100">
                        <h3 class="text-lg font-bold text-gray-900 mb-1">Security Update</h3>
                        <p class="text-sm text-gray-500 mb-6">Leave blank if you don't want to change the password.</p>
                    </div>

                    <div class="md:col-span-1">
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">New Password
                            (Optional)</label>
                        <input type="password" name="password" id="password"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                            placeholder="••••••••">
                        @error('password') <span class="text-red-500 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="md:col-span-1">
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Confirm
                            New Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                            placeholder="••••••••">
                    </div>
                </div>

                <div class="flex justify-end pt-8 border-t border-gray-100">
                    <button type="submit"
                        class="px-8 py-3 bg-primary text-white font-bold rounded-xl hover:bg-primary-700 transition-colors shadow-lg shadow-primary/25">
                        Update Scanner Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
