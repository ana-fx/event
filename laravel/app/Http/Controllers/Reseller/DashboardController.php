<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Data for reseller dashboard
        $paidTransactions = $user->resellerTransactions()->where('status', 'paid')->with('ticket')->get();
        $totalSales = $paidTransactions->sum('total_price');
        $totalCommission = $user->deposits()->sum('amount');

        $ticketsByName = $paidTransactions->groupBy(fn($t) => $t->ticket->name ?? 'Unknown')
            ->map(fn($group) => $group->sum('quantity'))
            ->sortDesc();

        $stats = [
            'total_sales' => $totalSales,
            'total_commission' => $totalCommission,
            'must_deposited' => $totalSales - $totalCommission,
            'current_balance' => $user->balance,
            'tickets_sold' => $paidTransactions->sum('quantity'),
            'tickets_by_name' => $ticketsByName,
        ];

        $events = $user->resellerEvents()
            ->where('status', 'active')
            ->where('end_date', '>=', now())
            ->get();

        return view('reseller.dashboard', compact('stats', 'events'));
    }
}
