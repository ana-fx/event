<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category');
            $table->string('status')->default('draft');

            // Media
            $table->string('banner_path')->nullable();
            $table->string('thumbnail_path')->nullable();

            // Dates
            $table->dateTime('start_date');
            $table->dateTime('end_date');

            // Details
            $table->longText('description');
            $table->longText('terms')->nullable();

            // Location
            $table->string('location')->nullable(); // Venue Name
            $table->string('province')->nullable();
            $table->string('city')->nullable();
            $table->string('zip')->nullable();
            $table->text('google_map_embed')->nullable();

            // SEO
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();

            // Organizer
            $table->string('organizer_name')->nullable();
            $table->string('organizer_logo_path')->nullable();

            // Fees & Commissions
            $table->enum('reseller_fee_type', ['fixed', 'percent'])->default('fixed');
            $table->decimal('reseller_fee_value', 12, 2)->default(0);

            $table->enum('organizer_fee_online_type', ['fixed', 'percent'])->default('fixed');
            $table->decimal('organizer_fee_online', 12, 2)->default(0);

            $table->enum('organizer_fee_reseller_type', ['fixed', 'percent'])->default('fixed');
            $table->decimal('organizer_fee_reseller', 12, 2)->default(0);

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
