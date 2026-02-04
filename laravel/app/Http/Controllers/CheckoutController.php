<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

use App\Models\Transaction;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function create(Event $event)
    {
        $event->load([
            'tickets' => function ($query) {
                $query->where('is_active', true)
                    ->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->withSum([
                        'transactions as paid_qty' => function ($q) {
                            $q->where('status', 'paid');
                        }
                    ], 'quantity')
                    ->withSum([
                        'transactions as pending_qty' => function ($q) {
                            $q->where('status', 'pending')
                                ->where('created_at', '>=', now()->subDay());
                        }
                    ], 'quantity')
                    ->orderBy('price', 'asc');
            }
        ]);

        if ($event->tickets->isEmpty()) {
            return redirect()->route('events.show', $event)
                ->with('error', 'No tickets are currently available for this event.');
        }

        return view('checkout.create', compact('event'));
    }

    public function store(Request $request, Event $event)
    {
        $validated = $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'quantity' => 'required|integer|min:1',
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|regex:/^[\d\+\-\s]+$/|min:10|max:20',
            'city' => 'required|string|max:255',
            'nik' => 'required|digits_between:12,20',
            'gender' => 'required|in:male,female',
        ]);

        $ticket = $event->tickets()->where('is_active', true)->findOrFail($validated['ticket_id']);

        // Validate Max Purchase
        if ($validated['quantity'] > $ticket->max_purchase_per_user) {
            return back()->withErrors(['quantity' => 'Max purchase for this ticket is ' . $ticket->max_purchase_per_user]);
        }

        // Validate Quota with Soft Lock (1 Day)
        $paidCount = $ticket->transactions()->where('status', 'paid')->sum('quantity');
        $pendingCount = $ticket->transactions()
            ->where('status', 'pending')
            ->where('created_at', '>=', now()->subDay())
            ->sum('quantity');

        $availableQuota = $ticket->quota - ($paidCount + $pendingCount);

        if ($availableQuota < $validated['quantity']) {
            return back()->withErrors([
                'quantity' => 'Not enough available seats. There are currently ' . $availableQuota . ' tickets remaining, as others are held in pending checkout sessions. Please try again soon or reduce your quantity.'
            ])->withInput();
        }

        $handlingFee = (int) \App\Models\Setting::getValue('handling_fee', 0);
        $totalPrice = ($ticket->price + $handlingFee) * $validated['quantity'];

        $transaction = Transaction::create([
            'code' => 'ANNTIX-' . strtoupper(Str::random(10)),
            'event_id' => $event->id,
            'ticket_id' => $ticket->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'city' => $validated['city'],
            'nik' => $validated['nik'],
            'gender' => $validated['gender'],
            'quantity' => $validated['quantity'],
            'total_price' => $totalPrice,
            'status' => 'pending',
        ]);

        return redirect()->route('payment.show', $transaction->code);
    }
}
