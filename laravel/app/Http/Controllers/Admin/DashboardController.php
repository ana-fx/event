<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Stats Cards
        $totalRevenue = Transaction::where('status', 'paid')->sum('total_price');
        $ticketsSold = Transaction::where('status', 'paid')->sum('quantity');
        $activeEvents = Event::where('start_date', '>=', now())
                            ->orWhere('end_date', '>=', now())
                            ->count();
        $totalResellers = User::where('role', 'reseller')->count();

        // 2. Chart Data: Daily Revenue (Last 30 Days)
        $dailyRevenue = Transaction::where('status', 'paid')
            ->where('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_price) as revenue'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $chartLabels = $dailyRevenue->pluck('date')->map(fn($date) => Carbon::parse($date)->format('d M'));
        $chartData = $dailyRevenue->pluck('revenue');

        // 3. Recent Transactions
        $recentTransactions = Transaction::with(['event', 'reseller'])
            ->where('status', 'paid')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalRevenue',
            'ticketsSold',
            'activeEvents',
            'totalResellers',
            'chartLabels',
            'chartData',
            'recentTransactions'
        ));
    }
}
