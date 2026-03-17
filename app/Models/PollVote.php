<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PollVote extends Model
{
    protected $fillable = [
        'poll_id',
        'option_index',
        'count',
    ];

    public $incrementing = false;
    protected $primaryKey = ['poll_id', 'option_index'];
    protected $keyType = 'string';
}
