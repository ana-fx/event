<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Reseller\DashboardController;

Route::middleware(['auth', 'reseller'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::get('create/{event:slug}', [App\Http\Controllers\Reseller\TransactionController::class, 'create'])->name('create');
        Route::post('store/{event:slug}', [App\Http\Controllers\Reseller\TransactionController::class, 'store'])->name('store');
    });

    // Reports
    Route::get('reports', [App\Http\Controllers\Reseller\ReportController::class, 'index'])->name('reports.index');

    // Deposits
    Route::get('deposits', [App\Http\Controllers\Reseller\DepositController::class, 'index'])->name('deposits.index');
});
