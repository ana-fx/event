<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with([
            'tickets' => function ($q) {
                $q->where('is_active', true);
            }
        ])
            ->whereDate('start_date', '>=', now());

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('location', 'like', '%' . $request->search . '%')
                    ->orWhere('city', 'like', '%' . $request->search . '%');
            });
        }

        // Category Filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // City Filter
        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        $events = $query->latest('start_date')->paginate(12)->withQueryString();

        // Get filter options
        $categories = Event::distinct()->whereNotNull('category')->pluck('category');
        $cities = Event::distinct()->whereNotNull('city')->pluck('city');

        return view('events.index', compact('events', 'categories', 'cities'));
    }
}
