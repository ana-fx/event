<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = \App\Models\Setting::all()->pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'site_name' => 'nullable|string|max:255',
            'site_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'site_logo_white' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'site_icon' => 'nullable|image|mimes:ico,png,jpg|max:1024',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'contact_email' => 'nullable|email|max:255',
            'contact_whatsapp' => 'nullable|string|max:50',
            'contact_location' => 'nullable|string',
            'social_facebook' => 'nullable|string|max:255',
            'social_twitter' => 'nullable|string|max:255',
            'social_instagram' => 'nullable|string|max:255',
            'social_tiktok' => 'nullable|string|max:255',
            'fee_qris_percent' => 'nullable|numeric|min:0',
            'fee_bank_fixed' => 'nullable|numeric|min:0',
            'handling_fee' => 'nullable|numeric|min:0',
        ]);

        foreach ($data as $key => $value) {
            if ($request->hasFile($key)) {
                $path = $request->file($key)->store('settings', 'public');
                \App\Models\Setting::setValue($key, $path);
            } else {
                \App\Models\Setting::setValue($key, $value);
            }
        }

        return back()->with('success', 'Settings updated successfully.');
    }
}
