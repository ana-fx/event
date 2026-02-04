<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Force HTTPS for URL generation when behind proxy (like ngrok)
        if (request()->header('X-Forwarded-Proto') === 'https' || request()->isSecure()) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $settings = \App\Models\Setting::all()->pluck('value', 'key')->toArray();
                \Illuminate\Support\Facades\View::share('global_settings', $settings);
            }
        } catch (\Exception $e) {
            // Table might not exist during migration
        }
    }
}
