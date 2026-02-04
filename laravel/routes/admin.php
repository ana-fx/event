<?php

use App\Http\Controllers\Auth\UserLoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ContactController;

Route::middleware(['auth', 'admin'])->group(function () {
    Route::post('logout', [UserLoginController::class, 'destroy'])->name('logout');
    Route::get('dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::get('profile', [\App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile');
    Route::put('profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');

    // Contact Messages
    Route::resource('contacts', ContactController::class)->only(['index', 'show', 'destroy']);

    Route::resource('events', \App\Http\Controllers\Admin\EventController::class);
    Route::get('events/{event}/scanners', [\App\Http\Controllers\Admin\EventController::class, 'scanners'])->name('events.scanners.index');
    Route::post('events/{event}/assign-scanner', [\App\Http\Controllers\Admin\EventController::class, 'assignScanner'])->name('events.assign-scanner');
    Route::delete('events/{event}/unassign-scanner/{scanner}', [\App\Http\Controllers\Admin\EventController::class, 'unassignScanner'])->name('events.unassign-scanner');
    Route::get('events/{event}/resellers', [\App\Http\Controllers\Admin\EventController::class, 'resellers'])->name('events.resellers.index');
    Route::post('events/{event}/assign-reseller', [\App\Http\Controllers\Admin\EventController::class, 'assignReseller'])->name('events.assign-reseller');
    Route::delete('events/{event}/unassign-reseller/{reseller}', [\App\Http\Controllers\Admin\EventController::class, 'unassignReseller'])->name('events.unassign-reseller');
    Route::resource('events.tickets-report', \App\Http\Controllers\Admin\TicketController::class)
        ->shallow()
        ->parameters(['tickets-report' => 'ticket']);
    Route::resource('banners', \App\Http\Controllers\Admin\BannerController::class);
    Route::get('events/{event}/withdrawals/create', [\App\Http\Controllers\Admin\WithdrawalController::class, 'create'])->name('events.withdrawals.create');
    Route::post('events/{event}/withdrawals', [\App\Http\Controllers\Admin\WithdrawalController::class, 'store'])->name('events.withdrawals.store');
    Route::get('events/{event}/withdrawals/{withdrawal}/edit', [\App\Http\Controllers\Admin\WithdrawalController::class, 'edit'])->name('events.withdrawals.edit');
    Route::put('events/{event}/withdrawals/{withdrawal}', [\App\Http\Controllers\Admin\WithdrawalController::class, 'update'])->name('events.withdrawals.update');
    Route::delete('events/{event}/withdrawals/{withdrawal}', [\App\Http\Controllers\Admin\WithdrawalController::class, 'destroy'])->name('events.withdrawals.destroy');

    // Report
    Route::get('reports', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/transactions', [\App\Http\Controllers\Admin\ReportController::class, 'transactions'])->name('reports.transactions');
    Route::get('reports/scanner', [\App\Http\Controllers\Admin\ReportController::class, 'scanner'])->name('reports.scanner');
    Route::get('reports/transactions/{transaction:id}', [\App\Http\Controllers\Admin\ReportController::class, 'showTransaction'])->name('reports.transactions.show');
    Route::post('reports/transactions/{transaction}/resend-email', [\App\Http\Controllers\Admin\ReportController::class, 'resendEmail'])->name('reports.resend-email');

    // Scanners
    // Scanners
    Route::resource('scanners', \App\Http\Controllers\Admin\ScannerController::class)->except(['show']);
    Route::post('scanners/{scanner}/toggle-active', [\App\Http\Controllers\Admin\ScannerController::class, 'toggleActive'])->name('scanners.toggle-active');

    // Admins
    Route::resource('admins', \App\Http\Controllers\Admin\AdminUserController::class)->except(['show']);

    // Resellers
    Route::resource('resellers', \App\Http\Controllers\Admin\ResellerController::class)->except(['show']);
    Route::post('resellers/{reseller}/toggle-active', [\App\Http\Controllers\Admin\ResellerController::class, 'toggleActive'])->name('resellers.toggle-active');
    Route::get('resellers-management/{reseller}/deposits', [\App\Http\Controllers\Admin\ResellerController::class, 'deposits'])->name('resellers.deposits');
    Route::post('resellers-management/{reseller}/deposits', [\App\Http\Controllers\Admin\ResellerController::class, 'storeDeposit'])->name('resellers.deposits.store');
    Route::get('resellers-management/{reseller}/deposits/{deposit}/edit', [\App\Http\Controllers\Admin\ResellerController::class, 'editDeposit'])->name('resellers.deposits.edit');
    Route::put('resellers-management/{reseller}/deposits/{deposit}', [\App\Http\Controllers\Admin\ResellerController::class, 'updateDeposit'])->name('resellers.deposits.update');
    Route::delete('resellers-management/{reseller}/deposits/{deposit}', [\App\Http\Controllers\Admin\ResellerController::class, 'destroyDeposit'])->name('resellers.deposits.destroy');

    // Reseller Management (Financial/Performance)
    Route::get('reseller-management', [\App\Http\Controllers\Admin\ResellerManagementController::class, 'index'])->name('reseller-management.index');

    // Settings
    Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])->name('settings.update');
});
