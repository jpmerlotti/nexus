<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable implements MustVerifyEmail
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
        'username',
        'email',
        'phone',
        'password',
        'profile_photo_path',
        'two_factor_type',
        'nex_balance',
        'custom_api_key',
        'ai_driver_preference',
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
            'nex_balance' => 'integer',
            'custom_api_key' => 'encrypted',
        ];
    }

    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo_path
                    ? asset('storage/'.$this->profile_photo_path)
                    : 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=7F9CF5&background=EBF4FF';
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

    /**
     * Get the campaigns owned by the user
     */
    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    /**
     * Get the characters owned by the user
     */
    public function characters()
    {
        return $this->hasMany(Character::class);
    }

    /**
     * Get the usage logs for the user.
     */
    public function usageLogs()
    {
        return $this->hasMany(UsageLog::class);
    }

    /**
     * Get the quests completed by the user.
     */
    public function completedQuests()
    {
        return $this->belongsToMany(Quest::class, 'user_quest')
            ->withPivot('earned_nex', 'completed_at')
            ->withTimestamps();
    }

    /**
     * Check if the user has enough Nex for an action.
     */
    public function hasEnoughNex(int $amount = 1): bool
    {
        if ($this->ai_driver_preference === 'byok' && ! empty($this->custom_api_key)) {
            return true;
        }

        return $this->nex_balance >= $amount;
    }
}
