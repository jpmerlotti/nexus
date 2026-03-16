<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    /** @use HasFactory<\Database\Factories\CampaignFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'narration_detail_level',
        'difficulty',
        'starting_level',
        'play_style',
        'progression_type',
    ];

    /**
     * Get the user that owns the campaign.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the messages for the campaign.
     */
    public function messages()
    {
        return $this->hasMany(CampaignMessage::class);
    }

    /**
     * Get the characters playing in this campaign.
     */
    public function characters()
    {
        return $this->belongsToMany(Character::class);
    }
}
