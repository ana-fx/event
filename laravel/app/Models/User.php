<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use App\Models\Event;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string $role
 * @property bool $is_active
 * @property string|null $profile_photo_path
 * @property string|null $phone
 * @property string|null $address
 * @property string|null $bio
 * @property int $balance
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Event[] $scannedEvents
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Event[] $resellerEvents
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ResellerDeposit[] $deposits
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Transaction[] $resellerTransactions
 */
class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
        'profile_photo_path',
        'phone',
        'address',
        'bio',
        'balance',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    public function scannedEvents()
    {
        return $this->belongsToMany(Event::class, 'event_scanner', 'user_id', 'event_id')->withTimestamps();
    }

    public function resellerEvents()
    {
        return $this->belongsToMany(Event::class, 'event_reseller', 'user_id', 'event_id')->withTimestamps();
    }

    /**
     * Check if user can scan tickets for a given event.
     * Resellers can scan tickets for events they are assigned to.
     */
    public function canScan(Event $event): bool
    {
        if ($this->role === 'admin') {
            return true;
        }

        if ($this->role === 'scanner') {
            return $this->scannedEvents->contains($event);
        }

        if ($this->role === 'reseller') {
            return $this->resellerEvents->contains($event);
        }

        return false;
    }

    public function deposits()
    {
        return $this->hasMany(ResellerDeposit::class);
    }

    public function deposit(float $amount, ?string $note = null, ?int $adminId = null)
    {
        DB::transaction(function () use ($amount, $note, $adminId) {
            $this->increment('balance', $amount);
            $this->deposits()->create([
                'amount' => $amount,
                'note' => $note,
                'created_by' => $adminId
            ]);
        });
    }

    public function resellerTransactions()
    {
        return $this->hasMany(Transaction::class, 'reseller_id');
    }
}
