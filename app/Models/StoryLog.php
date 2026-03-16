<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoryLog extends Model
{
    /** @use HasFactory<\Database\Factories\StoryLogFactory> */
    use HasFactory;

    const CHANNEL_IC = 'ic';

    const CHANNEL_META = 'meta';

    protected $fillable = [
        'campaign_id',
        'character_id',
        'role',
        'channel',
        'content',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function character()
    {
        return $this->belongsTo(Character::class);
    }
}
