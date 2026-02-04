<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return view('admin.profile', [
            'user' => $user,
        ]);
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Separate validation for basic info first
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:1000'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'max:1024'], // 1MB max
        ];

        // Check if sensitive data (email/password) is being changed
        if ($request->filled('password') || $request->input('email') !== $user->email) {
            $rules['current_password'] = ['required', 'current_password'];
            $rules['email'] = ['required', 'email', 'max:255', 'unique:users,email,'.$user->id];
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
        } else {
            // Just validate email format if provided, though we check changes above
            $rules['email'] = ['required', 'email', 'max:255', 'unique:users,email,'.$user->id];
        }

        $validated = $request->validate($rules);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('profile-photos', 'public');
            $user->profile_photo_path = $path;
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->phone = $validated['phone'] ?? null;
        $user->address = $validated['address'] ?? null;
        $user->bio = $validated['bio'] ?? null;

        if (! empty($validated['password'])) {
            $user->password = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }
}
