<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{




    public function index(Event $event)
    {
        $handlingFeeValue = (int) \App\Models\Setting::getValue('handling_fee', 0);

        $tickets = $event->tickets()
            ->withSum([
                'transactions as online_qty_paid' => function ($query) {
                    $query->where('status', 'paid')->whereNull('reseller_id');
                }
            ], 'quantity')
            ->withSum([
                'transactions as reseller_qty_paid' => function ($query) {
                    $query->where('status', 'paid')->whereNotNull('reseller_id');
                }
            ], 'quantity')
            ->withSum([
                'transactions as online_total_paid' => function ($query) {
                    $query->where('status', 'paid')->whereNull('reseller_id');
                }
            ], 'total_price')
            ->withSum([
                'transactions as reseller_total_paid' => function ($query) {
                    $query->where('status', 'paid')->whereNotNull('reseller_id');
                }
            ], 'total_price')
            ->withSum([
                'transactions as transactions_sum_quantity_paid' => function ($query) {
                    $query->where('status', 'paid');
                }
            ], 'quantity')
            ->withSum([
                'transactions as transactions_sum_quantity_pending' => function ($query) {
                    $query->where('status', 'pending')
                        ->where('created_at', '>=', now()->subDay());
                }
            ], 'quantity')
            ->latest()
            ->paginate(10);

        $recentSales = $event->transactions()
            ->where('status', 'paid')
            ->with('ticket')
            ->latest()
            ->take(10)
            ->get();

        // Calculate Global Totals for this Event
        $totalTicketRevenue = 0;
        foreach ($event->tickets()->withTrashed()->get() as $t) {
            $qPaid = $t->transactions()->where('status', 'paid')->sum('quantity');
            $totalTicketRevenue += ($qPaid * $t->price);
        }

        $totalSaldo = $event->calculateSaldo();
        $totalWithdrawn = $event->total_withdrawn;
        $availableSaldo = $event->available_saldo;

        // Calculate Platform Revenue (Organizer Fees + Handling Fees)
        $totalOrgTax = 0;
        $totalHandling = 0;
        foreach ($event->tickets()->withTrashed()->get() as $t) {
            $onlineQty = $t->transactions()->where('status', 'paid')->whereNull('reseller_id')->sum('quantity');
            $resellerQty = $t->transactions()->where('status', 'paid')->whereNotNull('reseller_id')->sum('quantity');

            $onlinePlatformFee = $event->organizer_fee_online_type === 'percent'
                ? $t->price * ($event->organizer_fee_online / 100)
                : $event->organizer_fee_online;

            $resellerPlatformFee = $event->organizer_fee_reseller_type === 'percent'
                ? $t->price * ($event->organizer_fee_reseller / 100)
                : $event->organizer_fee_reseller;

            $totalOrgTax += ($onlineQty * $onlinePlatformFee) + ($resellerQty * $resellerPlatformFee);
            $totalHandling += ($onlineQty * $handlingFeeValue);
        }
        $totalPlatformRevenue = $totalOrgTax + $totalHandling;

        return view('admin.tickets.index', compact(
            'event',
            'tickets',
            'recentSales',
            'handlingFeeValue',
            'totalTicketRevenue',
            'totalOrgTax',
            'totalSaldo',
            'totalWithdrawn',
            'availableSaldo',
            'totalPlatformRevenue'
        ));
    }

    public function create(Event $event)
    {
        return view('admin.tickets.create', compact('event'));
    }

    public function store(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quota' => 'required|integer|min:1',
            'max_purchase_per_user' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'required|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $event->tickets()->create($validated);

        // Redirect back to event index or ticket index?
        // "after create event can you update to create ticket" -> user flow continues.
        // Maybe redirect to the event index with success?
        // Or redirect to the ticket list for this event.
        // Let's redirect to the event list for now or keep them on the ticket page.
        // Usually, after adding a ticket, you might want to add another.
        // Let's redirect to event index saying ticket created.

        return redirect()->route('admin.events.index')->with('success', 'Ticket created successfully.');
    }

    public function edit(Ticket $ticket)
    {
        $event = $ticket->event;

        return view('admin.tickets.edit', compact('ticket', 'event'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quota' => 'required|integer|min:1',
            'max_purchase_per_user' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'description' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $ticket->update($validated);

        return back()->with('success', 'Ticket updated successfully.');
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();

        return back()->with('success', 'Ticket deleted successfully.');
    }

    public function toggleActive(Ticket $ticket)
    {
        $ticket->update(['is_active' => !$ticket->is_active]);

        return back()->with('success', 'Ticket status updated successfully.');
    }
}
