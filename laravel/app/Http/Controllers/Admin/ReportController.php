<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Basic Stats within Date Range
        $totalRevenue = Transaction::where('status', 'paid')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('total_price');

        $ticketsSold = Transaction::where('status', 'paid')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('quantity');

        $totalTransactions = Transaction::where('status', 'paid')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->count();

        // Revenue by Event
        $revenueByEvent = Transaction::where('transactions.status', 'paid')
            ->whereBetween('transactions.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->join('events', 'transactions.event_id', '=', 'events.id')
            ->select('events.name', DB::raw('SUM(transactions.total_price) as total_revenue'), DB::raw('SUM(transactions.quantity) as total_tickets'))
            ->groupBy('events.name')
            ->orderByDesc('total_revenue')
            ->limit(10)
            ->get();

        // Daily Revenue Chart Data
        $dailyRevenue = Transaction::where('status', 'paid')
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_price) as revenue'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Prepare chart data format
        $chartDates = $dailyRevenue->pluck('date');
        $chartRevenue = $dailyRevenue->pluck('revenue');

        // Scanned Stats within Date Range
        $ticketsRedeemed = Transaction::whereNotNull('redeemed_at')
            ->whereBetween('redeemed_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('quantity');

        // Recent Activity (Mixed) -> optional, but let's just keep the summary focus


        return view('admin.reports.index', compact(
            'totalRevenue',
            'ticketsSold',
            'ticketsRedeemed',
            'totalTransactions',
            'revenueByEvent',
            'startDate',
            'endDate',
            'chartDates',
            'chartRevenue',
            'dailyRevenue'
        ));
    }
    public function transactions(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');

        $query = Transaction::with(['event:id,name', 'ticket:id,name,price'])
            ->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($request->has('reseller_id')) {
            $query->where('reseller_id', $request->reseller_id);
        }

        if ($request->has('source')) {
            if ($request->source === 'reseller') {
                $query->whereNotNull('reseller_id');
            } elseif ($request->source === 'online') {
                $query->whereNull('reseller_id');
            }
        }

        $transactions = $query->paginate(15)->withQueryString();
        $handlingFeeValue = (int) \App\Models\Setting::getValue('handling_fee', 0);

        return view('admin.reports.transactions', compact('transactions', 'handlingFeeValue'));
    }

    public function showTransaction(Transaction $transaction)
    {
        $transaction->load(['event', 'ticket']);
        $handlingFeeValue = (int) \App\Models\Setting::getValue('handling_fee', 0);

        $ticketSales = $transaction->quantity * ($transaction->ticket->price ?? 0);
        $handlingTotal = $transaction->quantity * $handlingFeeValue;
        $serviceFee = (float) $transaction->total_price - ($ticketSales + $handlingTotal);
        if ($serviceFee < 0)
            $serviceFee = 0;

        return view('admin.reports.transaction-show', compact('transaction', 'serviceFee', 'handlingTotal'));
    }
    public function scanner(Request $request)
    {
        $search = $request->input('search');

        $query = Transaction::whereNotNull('redeemed_at')
            ->with(['event:id,name', 'ticket:id,name', 'scanner:id,name'])
            ->latest('redeemed_at');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('event', function ($e) use ($search) {
                        $e->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $transactions = $query->paginate(20)->withQueryString();

        return view('admin.reports.scanner', compact('transactions'));
    }
    public function resendEmail(Transaction $transaction)
    {
        // Only allow resending email for paid transactions
        if ($transaction->status !== 'paid') {
            return back()->with('error', 'Cannot resend email. Only paid transactions can receive success emails.');
        }

        try {
            $transaction->load('event'); // Ensure event relationship is loaded
            \Illuminate\Support\Facades\Mail::to($transaction->email)->send(new \App\Mail\PaymentSuccess($transaction));
            return back()->with('success', 'Payment success email has been resent to ' . $transaction->email);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to resend email: ' . $e->getMessage());
        }
    }
}
