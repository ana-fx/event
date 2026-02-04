<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Banner;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banners = Banner::latest()->get();
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        $events = \App\Models\Event::where('status', 'active')->latest()->get();
        return view('admin.banners.create', compact('events'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([

            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'link_url' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('banners', 'public');
            $validated['image_path'] = $path;
        }

        $linkUrl = $validated['link_url'];
        if ($request->input('link_type') === 'event' && $request->filled('event_id')) {
            $linkUrl = $request->input('event_id'); // Value is the route URL
        }

        Banner::create([
            'slug' => \Illuminate\Support\Str::slug('banner-' . \Illuminate\Support\Str::random(8)),
            'image_path' => $validated['image_path'],
            'link_url' => $linkUrl,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner created successfully.');
    }

    public function edit(Banner $banner)
    {
        $events = \App\Models\Event::where('status', 'active')->latest()->get();
        return view('admin.banners.edit', compact('banner', 'events'));
    }

    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'link_url' => 'nullable|url',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('banners', 'public');
            $validated['image_path'] = $path;
        }

        $linkUrl = $validated['link_url'];
        if ($request->input('link_type') === 'event' && $request->filled('event_id')) {
            $linkUrl = $request->input('event_id');
        }

        $banner->update([
            'image_path' => $validated['image_path'] ?? $banner->image_path,
            'link_url' => $linkUrl,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully.');
    }

    public function destroy(Banner $banner)
    {
        $banner->delete();
        return redirect()->route('admin.banners.index')->with('success', 'Banner deleted successfully.');
    }
}
