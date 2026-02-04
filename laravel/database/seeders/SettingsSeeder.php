<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Copy branding assets from public/ to storage/app/public/ if they exist
        $brandingFiles = ['logo.png', 'logo-white.png', 'icon.png'];
        foreach ($brandingFiles as $file) {
            $source = public_path($file);
            $destination = storage_path('app/public/' . $file);
            if (file_exists($source) && !file_exists($destination)) {
                copy($source, $destination);
            }
        }

        $settings = [
            // Site Identity
            ['key' => 'site_name', 'value' => 'ANTIX'],
            ['key' => 'site_logo', 'value' => 'logo.png'],
            ['key' => 'site_logo_white', 'value' => 'logo-white.png'],
            ['key' => 'site_icon', 'value' => 'icon.png'],

            // SEO
            ['key' => 'seo_title', 'value' => 'ANTIX'],
            ['key' => 'seo_description', 'value' => 'Secure your spot at the best events with ANTIX. Fast, easy, and reliable ticketing platform.'],

            // Social Media
            ['key' => 'social_facebook', 'value' => '#'],
            ['key' => 'social_twitter', 'value' => '#'],
            ['key' => 'social_instagram', 'value' => '#'],
            ['key' => 'social_tiktok', 'value' => '#'],

            // Contact Info
            ['key' => 'contact_email', 'value' => 'hallo@anntix.com'],
            ['key' => 'contact_whatsapp', 'value' => '+62 856-0045-7192'],
            ['key' => 'contact_location', 'value' => 'Tegal, Jawa Tengah'],

            // Payment Configuration
            ['key' => 'fee_qris_percent', 'value' => '0.7'],
            ['key' => 'fee_bank_fixed', 'value' => '4440'],
            ['key' => 'handling_fee', 'value' => '5000'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                ['value' => $setting['value']]
            );
        }
    }
}
