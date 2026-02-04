<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ContactController extends Controller
{
    public function index()
    {
        return view('contact');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'cf-turnstile-response' => [
                'required',
                function ($attribute, $value, $fail) {
                    /** @var \Illuminate\Http\Client\Response $response */
                    $response = Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                        'secret' => config('services.turnstile.secret'),
                        'response' => $value,
                        'remoteip' => request()->ip(),
                    ]);

                    if (!$response->successful() || !$response->json('success')) {
                        $fail('The turnstile verification failed. Please try again.');
                    }
                }
            ],
        ]);

        unset($validated['cf-turnstile-response']);

        Contact::create($validated);

        return back()->with('success', 'Thank you for contacting us! We will get back to you soon.');
    }
}
