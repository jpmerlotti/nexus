<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Quest extends Model
{
    protected $fillable = [
        'key',
        'name',
        'description',
        'reward_nex',
        'is_repeatable',
    ];

    protected function casts(): array
    {
        return [
            'reward_nex' => 'integer',
            'is_repeatable' => 'boolean',
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_quest')
            ->withPivot('earned_nex', 'completed_at')
            ->withTimestamps();
    }
}
