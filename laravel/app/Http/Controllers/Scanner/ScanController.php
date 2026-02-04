<?php

namespace App\Http\Controllers\Scanner;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScanController extends Controller
{
    public function index()
    {
        // Get events assigned to this scanner
        $events = Auth::user()->scannedEvents()
            ->where('end_date', '>=', now())
            ->get();

        return view('scanner.index', compact('events'));
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'event_id' => 'required|exists:events,id',
        ]);

        $code = $request->code;
        $eventId = $request->event_id;

        // Verify the scanner is assigned to this event
        if (!Auth::user()->scannedEvents()->where('events.id', $eventId)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You are not authorized to scan for this event.'
            ], 403);
        }

        $transaction = Transaction::where('code', $code)->first();

        if (!$transaction) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid ticket code.'
            ], 404);
        }

        // Check if transaction belongs to the event
        if ($transaction->ticket->event_id != $eventId) {
            return response()->json([
                'status' => 'error',
                'message' => 'This ticket does not belong to the selected event.'
            ], 400);
        }

        if ($transaction->status !== 'paid') {
            return response()->json([
                'status' => 'error',
                'message' => 'Ticket is not paid (' . $transaction->status . ').'
            ], 400);
        }

        // CHANGED: Check if already redeemed but RETURN the data (don't block)
        $alreadyRedeemed = $transaction->redeemed_at !== null;

        // Get scanner information if already redeemed
        $scannedBy = null;
        if ($alreadyRedeemed && $transaction->redeemed_by) {
            $scanner = \App\Models\User::find($transaction->redeemed_by);
            $scannedBy = $scanner ? $scanner->name : 'Unknown Scanner';
        }

        return response()->json([
            'status' => $alreadyRedeemed ? 'warning' : 'pending',
            'message' => $alreadyRedeemed
                ? 'Already scanned at ' . $transaction->redeemed_at->format('H:i:s')
                : 'Ready to check in',
            'already_redeemed' => $alreadyRedeemed,
            'data' => [
                'transaction_id' => $transaction->id,
                'code' => $transaction->code,
                'name' => $transaction->name,
                'email' => $transaction->email,
                'phone' => $transaction->phone,
                'city' => $transaction->city,
                'nik' => $transaction->nik,
                'gender' => $transaction->gender,
                'ticket_type' => $transaction->ticket->name,
                'quantity' => $transaction->quantity,
                'redeemed_at' => $transaction->redeemed_at?->format('d M Y, H:i:s'),
                'scanned_by' => $scannedBy,
            ]
        ]);
    }

    public function redeem(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'event_id' => 'required|exists:events,id',
        ]);

        $transaction = Transaction::findOrFail($request->transaction_id);
        $eventId = $request->event_id;

        // Verify scanner authorization
        if (!Auth::user()->scannedEvents()->where('events.id', $eventId)->exists()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized.'
            ], 403);
        }

        // Check if transaction belongs to the event
        if ($transaction->ticket->event_id != $eventId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid event.'
            ], 400);
        }

        // Check if already redeemed
        if ($transaction->redeemed_at) {
            return response()->json([
                'status' => 'error',
                'message' => 'Already redeemed.'
            ], 400);
        }

        // Mark as redeemed
        $transaction->redeemed_at = now();
        $transaction->redeemed_by = Auth::id();
        $transaction->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Check-in successful!',
            'data' => [
                'redeemed_at' => $transaction->redeemed_at->format('H:i:s'),
            ]
        ]);
    }
}
