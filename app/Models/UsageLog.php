<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsageLog extends Model
{
    protected $fillable = [
        'user_id',
        'campaign_id',
        'action_type',
        'tokens_input',
        'tokens_output',
        'nex_spent',
        'driver_used',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'nex_spent' => 'decimal:4',
            'tokens_input' => 'integer',
            'tokens_output' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
