<?php

namespace App\Http\Controllers\Reseller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function index()
    {
        $deposits = auth()->user()->deposits()
            ->latest()
            ->paginate(10);

        return view('reseller.deposits.index', compact('deposits'));
    }
}
