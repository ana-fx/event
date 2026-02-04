<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    /**
     * Switch application language
     */
    public function switch(Request $request, $locale)
    {
        // Validate locale
        if (!in_array($locale, ['id', 'en'])) {
            abort(400);
        }

        // Store locale in session
        Session::put('locale', $locale);

        // Redirect back
        return redirect()->back()->with('success', 'Language changed successfully');
    }
}
