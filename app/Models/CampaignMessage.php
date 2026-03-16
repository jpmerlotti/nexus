<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignMessage extends Model
{
    protected $fillable = [
        'campaign_id',
        'role',
        'content',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
        ];
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
