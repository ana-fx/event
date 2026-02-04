<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SitemapController extends Controller
{
    public function index()
    {
        $events = Event::where('status', 'active')->latest()->get();

        return response()->view('sitemap.index', [
            'events' => $events,
        ])->header('Content-Type', 'text/xml');
    }
}
