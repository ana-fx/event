<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::where('reseller_id', Auth::id())
            ->where('status', 'paid')
            ->with(['event', 'ticket'])
            ->latest();

        // Optional filtering
        if ($request->has('event_id') && $request->event_id) {
            $query->where('event_id', $request->event_id);
        }

        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $transactions = $query->paginate(20);

        // Stats for cards
        $totalSales = $query->sum('total_price');
        $totalCommission = $query->get()->sum('commission');
        $totalNet = $totalSales - $totalCommission;
        $totalTickets = $query->sum('quantity');

        // Events list for filter
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $totalDeposit = $user->deposits()->sum('amount');
        $events = $user->resellerEvents;

        return view('reseller.reports.index', compact('transactions', 'totalSales', 'totalCommission', 'totalNet', 'totalTickets', 'totalDeposit', 'events'));
    }
}
