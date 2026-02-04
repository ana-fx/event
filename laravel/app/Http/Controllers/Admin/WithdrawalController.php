<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Withdrawal;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function create(Event $event)
    {
        $availableSaldo = $event->available_saldo;
        $withdrawals = $event->withdrawals()->latest()->get();

        return view('admin.withdrawals.create', compact('event', 'availableSaldo', 'withdrawals'));
    }

    public function store(Request $request, Event $event)
    {
        $availableSaldo = $event->available_saldo;

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $availableSaldo,
            'note' => 'nullable|string|max:500',
        ]);

        $event->withdrawals()->create([
            'amount' => $validated['amount'],
            'reference' => $event->name,
            'note' => $validated['note'],
        ]);

        return redirect()->route('admin.events.withdrawals.create', $event)->with('success', 'Withdrawal recorded successfully.');
    }

    public function edit(Event $event, Withdrawal $withdrawal)
    {
        $availableSaldo = $event->available_saldo + $withdrawal->amount;
        return view('admin.withdrawals.edit', compact('event', 'withdrawal', 'availableSaldo'));
    }

    public function update(Request $request, Event $event, Withdrawal $withdrawal)
    {
        $availableSaldo = $event->available_saldo + $withdrawal->amount;

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1|max:' . $availableSaldo,
            'note' => 'nullable|string|max:500',
        ]);

        $withdrawal->update([
            'amount' => $validated['amount'],
            'note' => $validated['note'],
        ]);

        return redirect()->route('admin.events.withdrawals.create', $event)->with('success', 'Withdrawal updated successfully.');
    }

    public function destroy(Event $event, Withdrawal $withdrawal)
    {
        $withdrawal->delete();
        $event->refresh();
        return redirect()->route('admin.events.withdrawals.create', $event)->with('success', 'Withdrawal deleted successfully.');
    }
}
