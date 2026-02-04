<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ResellerManagementController extends Controller
{
    public function index()
    {
        $resellers = \App\Models\User::where('role', 'reseller')
            ->withSum(['resellerTransactions as total_sales_sum' => function ($query) {
                $query->where('status', 'paid');
            }], 'total_price')
            ->withSum(['deposits as total_deposit_sum'], 'amount')
            ->latest()
            ->paginate(10);

        return view('admin.reseller-management.index', compact('resellers'));
    }
}
