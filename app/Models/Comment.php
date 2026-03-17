<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'post_id',
        'author',
        'content',
        'score',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    public function votes()
    {
        return $this->hasMany(CommentVote::class);
    }
}
