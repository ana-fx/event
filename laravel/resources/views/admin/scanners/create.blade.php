<x-layouts.admin>
    <div class="max-w-xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('admin.scanners.index') }}"
                class="inline-flex items-center text-sm text-secondary hover:text-dark mb-4">
                <svg class="w-4 h-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Back to List
            </a>
            <h1 class="text-2xl font-bold text-dark">Create New Scanner</h1>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
            <form action="{{ route('admin.scanners.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="space-y-2">
                    <label for="name" class="text-sm font-bold text-dark uppercase tracking-wide">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                        class="w-full px-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-2">
                    <label for="email" class="text-sm font-bold text-dark uppercase tracking-wide">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required
                        class="w-full px-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                    @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-2">
                    <label for="password" class="text-sm font-bold text-dark uppercase tracking-wide">Password</label>
                    <input type="password" name="password" id="password" required
                        class="w-full px-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                    @error('password') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <div class="space-y-2">
                    <label for="password_confirmation"
                        class="text-sm font-bold text-dark uppercase tracking-wide">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="w-full px-4 py-3 bg-gray-50 border-gray-200 rounded-xl focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                </div>

                <div class="pt-4">
                    <button type="submit"
                        class="w-full py-4 bg-primary text-white font-bold rounded-xl hover:bg-primary/90 transition-all shadow-lg shadow-primary/30 hover:-translate-y-1">
                        Create Scanner
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>