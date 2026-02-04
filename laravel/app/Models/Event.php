<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model; // Added this line
use Illuminate\Database\Eloquent\SoftDeletes; // Added this line

class Event extends Model
{
    use HasFactory, SoftDeletes; // Modified this line

    protected $fillable = [
        'name',
        'slug',
        'category',
        'status',
        'banner_path',
        'thumbnail_path',
        'start_date',
        'end_date',
        'description',
        'terms',
        'location',
        'province',
        'city',
        'zip',
        'google_map_embed',
        'seo_title',
        'seo_description',
        'organizer_name',
        'organizer_logo_path',
        'reseller_fee_type',
        'reseller_fee_value',
        'organizer_fee_online_type',
        'organizer_fee_online',
        'organizer_fee_reseller_type',
        'organizer_fee_reseller',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function scanners()
    {
        return $this->belongsToMany(User::class, 'event_scanner', 'event_id', 'user_id')->withTimestamps();
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function resellers()
    {
        return $this->belongsToMany(User::class, 'event_reseller', 'event_id', 'user_id')->withTimestamps();
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function calculateSaldo()
    {
        // Use a fresh query directly on the tickets table to avoid any cached relationship data
        $tickets = \Illuminate\Support\Facades\DB::table('tickets')
            ->where('event_id', $this->id)
            ->get();

        $totalSaldo = 0;

        foreach ($tickets as $t) {
            // Get fresh transaction counts for this specific ticket
            $onlineQty = \Illuminate\Support\Facades\DB::table('transactions')
                ->where('ticket_id', $t->id)
                ->where('status', 'paid')
                ->whereNull('reseller_id')
                ->whereNull('deleted_at')
                ->sum('quantity');

            $resellerQty = \Illuminate\Support\Facades\DB::table('transactions')
                ->where('ticket_id', $t->id)
                ->where('status', 'paid')
                ->whereNotNull('reseller_id')
                ->whereNull('deleted_at')
                ->sum('quantity');

            // 1. Revenue
            $revenue = ($onlineQty + $resellerQty) * $t->price;

            // 2. Platform Fees
            $onlinePlatformFee = $this->organizer_fee_online_type === 'percent'
                ? $t->price * ($this->organizer_fee_online / 100)
                : $this->organizer_fee_online;

            $resellerPlatformFee = $this->organizer_fee_reseller_type === 'percent'
                ? $t->price * ($this->organizer_fee_reseller / 100)
                : $this->organizer_fee_reseller;

            $totalPlatformFee = ($onlineQty * $onlinePlatformFee) + ($resellerQty * $resellerPlatformFee);

            // 3. Final calculation for this ticket
            $totalSaldo += ($revenue - $totalPlatformFee);
        }

        return (float) $totalSaldo;
    }

    public function getTotalWithdrawnAttribute()
    {
        // Use a fresh query directly on the withdrawals table
        return (float) (\Illuminate\Support\Facades\DB::table('withdrawals')
            ->where('event_id', $this->id)
            ->sum('amount') ?: 0);
    }

    public function getAvailableSaldoAttribute()
    {
        // Re-calculate everything fresh
        return (float) ($this->calculateSaldo() - $this->total_withdrawn);
    }
}
