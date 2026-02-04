<?php

namespace App\Http\Controllers;

use App\Models\Event;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $events = Event::with('tickets')
            ->where('status', 'active')
            ->latest()
            ->take(8)
            ->get();

        $banners = \App\Models\Banner::where('is_active', true)->latest()->get();

        // SEO data for homepage
        $seoData = [
            'title' => 'Beli Tiket Event, Konser & Festival Online Terpercaya',
            'description' => 'Platform pembelian tiket event terpercaya di Indonesia. Temukan dan beli tiket konser, festival musik, seminar, workshop dengan mudah dan aman. Proses cepat, harga terjangkau!',
            'keywords' => 'tiket event, beli tiket online, konser indonesia, festival musik, event organizer, tiket konser murah, seminar online, workshop jakarta'
        ];

        return view('home', compact('events', 'banners', 'seoData'));
    }

    public function show(Event $event)
    {
        if (auth()->user()?->role === 'reseller') {
            return redirect()->route('reseller.transactions.create', $event);
        }
        $event->load([
            'tickets' => function ($query) {
                $query->where('is_active', true);
            }
        ]);

        $relatedEvents = Event::where('status', 'active')
            ->where('id', '!=', $event->id)
            ->with([
                'tickets' => function ($query) {
                    $query->where('is_active', true)
                        ->where('start_date', '<=', now())
                        ->where('end_date', '>=', now())
                        ->orderBy('price', 'asc');
                }
            ])
            ->inRandomOrder()
            ->take(3)
            ->get();

        return view('events.show', compact('event', 'relatedEvents'));
    }
}
