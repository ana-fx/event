<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EventController;
use App\Http\Controllers\PageController; // Added this line


// Error Page Previews
Route::get('/errors/403', function () {
    abort(403);
});
Route::get('/errors/404', function () {
    abort(404);
});
Route::get('/errors/419', function () {
    abort(419);
});
Route::get('/errors/500', function () {
    abort(500);
});
Route::get('/errors/429', function () {
    abort(429);
});
Route::get('/errors/503', function () {
    abort(503);
});

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{event}', [HomeController::class, 'show'])->name('events.show');
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

// Language Switcher
Route::get('/language/{locale}', [\App\Http\Controllers\LanguageController::class, 'switch'])->name('language.switch');

// Static Pages
Route::get('/terms', [PageController::class, 'terms'])->name('pages.terms');
Route::get('/privacy', [PageController::class, 'privacy'])->name('pages.privacy');
Route::get('/cookie-policy', [PageController::class, 'cookie'])->name('pages.cookie');
Route::get('/services', [PageController::class, 'services'])->name('pages.services');
Route::get('/about-us', [PageController::class, 'about'])->name('pages.about');


use App\Http\Controllers\ContactController;
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

use App\Http\Controllers\CheckoutController;
Route::get('/checkout/{event}', [CheckoutController::class, 'create'])->name('checkout.create');
Route::post('/checkout/{event}', [CheckoutController::class, 'store'])->name('checkout.store');

use App\Http\Controllers\PaymentController;
Route::get('/payment/{transaction}', [PaymentController::class, 'show'])->name('payment.show');
Route::post('/payment/{transaction}/token', [PaymentController::class, 'generateToken'])->name('payment.token');
Route::post('/payment/{transaction}/complete', [PaymentController::class, 'updateStatus'])->name('payment.update');
Route::post('/payment/{transaction}/reseller-complete', [PaymentController::class, 'resellerComplete'])->name('payment.reseller.complete');
Route::get('/payment/success/{transaction}', [PaymentController::class, 'success'])->name('payment.success');
Route::post('/midtrans/notification', [PaymentController::class, 'notification'])->name('midtrans.notification');

use App\Http\Controllers\Admin\DashboardController;

Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'admin'])
    ->name('dashboard');

use App\Http\Controllers\Auth\UserLoginController;

Route::middleware('guest')->group(function () {
    Route::get('login', [UserLoginController::class, 'create'])->name('login');
    Route::post('login', [UserLoginController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [UserLoginController::class, 'destroy'])->name('logout');

    // Scanner Routes
    Route::prefix('scanner')->name('scanner.')->group(function () {
        Route::get('/', [\App\Http\Controllers\Scanner\ScanController::class, 'index'])->name('index');
        Route::post('/verify', [\App\Http\Controllers\Scanner\ScanController::class, 'verify'])->name('verify');
        Route::post('/redeem', [\App\Http\Controllers\Scanner\ScanController::class, 'redeem'])->name('redeem');
    });
});

Route::get('/test/email', function () {
    try {
        \Illuminate\Support\Facades\Mail::to('aanjr38@gmail.com')->send(new App\Mail\TestMail());
        return 'Email sent successfully to aanjr38@gmail.com';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});
