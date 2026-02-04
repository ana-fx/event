<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::withCount([
            'tickets as total_tickets' => function ($query) {
                $query->select(\Illuminate\Support\Facades\DB::raw('SUM(quota)'));
            },
            'scanners'
        ])->latest()->paginate(10);

        // Calculate sold tickets count (mock logic for now or real if 'tickets' relation exists on transactions)
        // For accurate 'sold' vs 'total', we'd need to sum qty from transactions.
        // Let's just carry total capacity for now as per view usage: tickets_count usually implies available or sold.
        // Assuming current view logic: tickets_count = currently sold? Or created?
        // Let's map it: total_tickets = sum of ticket types quantity. tickets_count = sold.
        // Actually, let's keep it simple:
        $events = Event::with('tickets')->withCount(['tickets', 'scanners', 'resellers'])->latest()->paginate(10);

        // Improve: calculating real metrics
        $events->getCollection()->transform(function ($event) {
            $event->total_tickets = $event->tickets->sum('quota');
            $event->tickets_count = $event->transactions()->where('status', 'paid')->count(); // Example: sold tickets
            return $event;
        });

        return view('admin.events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.events.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'status' => 'required|string|in:draft,active,ended',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'required|string',
            'terms' => 'nullable|string',
            'location' => 'required|string',
            'province' => 'required|string',
            'city' => 'required|string',
            'zip' => 'required|string',
            'google_map_embed' => 'nullable|string',
            'seo_title' => 'nullable|string',
            'seo_description' => 'nullable|string',
            'organizer_name' => 'nullable|string',
            'banner' => 'nullable|image|max:2048',
            'thumbnail' => 'nullable|image|max:2048',
            'organizer_logo' => 'nullable|image|max:1024',
            'reseller_fee_type' => 'nullable|in:fixed,percent',
            'reseller_fee_value' => 'nullable|numeric|min:0',
            'organizer_fee_online_type' => 'nullable|in:fixed,percent',
            'organizer_fee_online' => 'nullable|numeric|min:0',
            'organizer_fee_reseller_type' => 'nullable|in:fixed,percent',
            'organizer_fee_reseller' => 'nullable|numeric|min:0',
        ]);

        $slug = Str::slug($validated['name']);
        // Ensure unique slug
        $count = Event::where('slug', 'LIKE', "{$slug}%")->count();
        if ($count > 0) {
            $slug .= '-' . ($count + 1);
        }
        $validated['slug'] = $slug;

        // Handle File Uploads
        if ($request->hasFile('banner')) {
            $validated['banner_path'] = $request->file('banner')->store('events/banners', 'public');
        }
        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail_path'] = $request->file('thumbnail')->store('events/thumbnails', 'public');
        }
        if ($request->hasFile('organizer_logo')) {
            $validated['organizer_logo_path'] = $request->file('organizer_logo')->store('events/organizers', 'public');
        }

        // Set defaults for fee fields if they are null (because DB columns are not nullable)
        $validated['reseller_fee_type'] = $validated['reseller_fee_type'] ?? 'fixed';
        $validated['reseller_fee_value'] = $validated['reseller_fee_value'] ?? 0;
        $validated['organizer_fee_online_type'] = $validated['organizer_fee_online_type'] ?? 'fixed';
        $validated['organizer_fee_online'] = $validated['organizer_fee_online'] ?? 0;
        $validated['organizer_fee_reseller_type'] = $validated['organizer_fee_reseller_type'] ?? 'fixed';
        $validated['organizer_fee_reseller'] = $validated['organizer_fee_reseller'] ?? 0;

        $event = Event::create($validated);

        return redirect()->route('admin.events.tickets-report.create', $event)->with('success', 'Event created successfully. Now please add tickets.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        $event->load(['tickets', 'scanners', 'resellers']);

        $scanners = \App\Models\User::where('role', 'scanner')
            ->whereDoesntHave('scannedEvents', function ($query) use ($event) {
                $query->where('event_id', $event->id);
            })->get();

        $resellers = \App\Models\User::where('role', 'reseller')
            ->whereDoesntHave('resellerEvents', function ($query) use ($event) {
                $query->where('event_id', $event->id);
            })->get();

        return view('admin.events.show', compact('event', 'scanners', 'resellers'));
    }

    public function scanners(Event $event)
    {
        $event->load('scanners');
        // Fetch all available scanners to add
        $availableScanners = \App\Models\User::where('role', 'scanner')
            ->whereDoesntHave('scannedEvents', function ($query) use ($event) {
                $query->where('event_id', $event->id);
            })->get();

        return view('admin.events.scanners.index', compact('event', 'availableScanners'));
    }

    public function assignScanner(Request $request, Event $event)
    {
        $validated = $request->validate([
            'scanner_id' => 'required|exists:users,id',
        ]);

        $event->scanners()->attach($validated['scanner_id']);

        return back()->with('success', 'Scanner assigned successfully.');
    }

    public function unassignScanner(Event $event, \App\Models\User $scanner)
    {
        $event->scanners()->detach($scanner->id);

        return back()->with('success', 'Scanner unassigned successfully.');
    }

    public function resellers(Event $event)
    {
        $event->load('resellers');
        // Fetch all available resellers to add
        $availableResellers = \App\Models\User::where('role', 'reseller')
            ->whereDoesntHave('resellerEvents', function ($query) use ($event) {
                $query->where('event_id', $event->id);
            })->get();

        return view('admin.events.resellers.index', compact('event', 'availableResellers'));
    }

    public function assignReseller(Request $request, Event $event)
    {
        $validated = $request->validate([
            'reseller_id' => 'required|exists:users,id',
        ]);

        $event->resellers()->attach($validated['reseller_id']);

        return back()->with('success', 'Reseller assigned successfully.');
    }

    public function unassignReseller(Event $event, \App\Models\User $reseller)
    {
        $event->resellers()->detach($reseller->id);

        return back()->with('success', 'Reseller unassigned successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'status' => 'required|string|in:draft,active,ended',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'required|string',
            'terms' => 'nullable|string',
            'location' => 'required|string',
            'province' => 'required|string',
            'city' => 'required|string',
            'zip' => 'required|string',
            'google_map_embed' => 'nullable|string',
            'seo_title' => 'nullable|string',
            'seo_description' => 'nullable|string',
            'organizer_name' => 'nullable|string',
            'banner' => 'nullable|image|max:2048',
            'thumbnail' => 'nullable|image|max:2048',
            'organizer_logo' => 'nullable|image|max:1024',
            'reseller_fee_type' => 'nullable|in:fixed,percent',
            'reseller_fee_value' => 'nullable|numeric|min:0',
            'organizer_fee_online_type' => 'nullable|in:fixed,percent',
            'organizer_fee_online' => 'nullable|numeric|min:0',
            'organizer_fee_reseller_type' => 'nullable|in:fixed,percent',
            'organizer_fee_reseller' => 'nullable|numeric|min:0',
        ]);

        // Regenerate slug if name changed
        if ($event->name !== $validated['name']) {
            $slug = Str::slug($validated['name']);
            $count = Event::where('slug', 'LIKE', "{$slug}%")->where('id', '!=', $event->id)->count();
            if ($count > 0) {
                $slug .= '-' . ($count + 1);
            }
            $validated['slug'] = $slug;
        }

        if ($request->hasFile('banner')) {
            if ($event->banner_path) {
                Storage::disk('public')->delete($event->banner_path);
            }
            $validated['banner_path'] = $request->file('banner')->store('events/banners', 'public');
        }
        if ($request->hasFile('thumbnail')) {
            if ($event->thumbnail_path) {
                Storage::disk('public')->delete($event->thumbnail_path);
            }
            $validated['thumbnail_path'] = $request->file('thumbnail')->store('events/thumbnails', 'public');
        }
        if ($request->hasFile('organizer_logo')) {
            if ($event->organizer_logo_path) {
                Storage::disk('public')->delete($event->organizer_logo_path);
            }
            $validated['organizer_logo_path'] = $request->file('organizer_logo')->store('events/organizers', 'public');
        }

        // Set defaults for fee fields if they are null
        $validated['reseller_fee_type'] = $validated['reseller_fee_type'] ?? 'fixed';
        $validated['reseller_fee_value'] = $validated['reseller_fee_value'] ?? 0;
        $validated['organizer_fee_online_type'] = $validated['organizer_fee_online_type'] ?? 'fixed';
        $validated['organizer_fee_online'] = $validated['organizer_fee_online'] ?? 0;
        $validated['organizer_fee_reseller_type'] = $validated['organizer_fee_reseller_type'] ?? 'fixed';
        $validated['organizer_fee_reseller'] = $validated['organizer_fee_reseller'] ?? 0;

        $event->update($validated);

        return redirect()->route('admin.events.index')->with('success', 'Event updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        if ($event->banner_path) {
            Storage::disk('public')->delete($event->banner_path);
        }
        if ($event->thumbnail_path) {
            Storage::disk('public')->delete($event->thumbnail_path);
        }
        if ($event->organizer_logo_path) {
            Storage::disk('public')->delete($event->organizer_logo_path);
        }

        $event->delete();

        return redirect()->route('admin.events.index')->with('success', 'Event deleted successfully.');
    }
}
