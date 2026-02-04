<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function create(Event $event)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->resellerEvents->contains($event)) {
            abort(403, 'Unauthorized access to this event.');
        }

        $event->load([
            'tickets' => function ($query) {
                $query->where('start_date', '<=', now())
                    ->where('end_date', '>=', now())
                    ->where('is_active', true)
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
            return redirect()->route('reseller.dashboard')
                ->with('error', 'No tickets are currently available for this event.');
        }

        return view('reseller.transactions.create', compact('event'));
    }

    public function store(Request $request, Event $event)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->resellerEvents->contains($event)) {
            abort(403, 'Unauthorized access to this event.');
        }

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
                'quantity' => 'Not enough available seats. There are currently ' . $availableQuota . ' tickets remaining, as others are held in pending checkout sessions.'
            ])->withInput();
        }

        // Calculate Reseller Fee (Commission charged to buyer per ticket)
        $resellerFee = 0;

        // Ensure we handle both fixed and percent types
        if ($event->reseller_fee_type === 'percent') {
            $resellerFee = $ticket->price * ($event->reseller_fee_value / 100);
        } else {
            // Default to fixed
            $resellerFee = $event->reseller_fee_value;
        }

        $totalPrice = ($ticket->price + $resellerFee) * $validated['quantity'];

        // Create transaction linked to reseller
        $transaction = Transaction::create([
            'code' => 'RES-ANNTIX-' . strtoupper(Str::random(10)),
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
            'reseller_id' => Auth::id(), // Track reseller
        ]);

        return redirect()->route('payment.show', $transaction->code);
    }
}
