<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    public function terms()
    {
        return view('pages.terms');
    }

    public function privacy()
    {
        return view('pages.privacy');
    }

    public function services()
    {
        return view('pages.services');
    }

    public function cookie()
    {
        return view('pages.cookie');
    }

    public function about()
    {
        return view('pages.about');
    }
}
