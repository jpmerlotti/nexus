<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentVote extends Model
{
    protected $fillable = [
        'comment_id',
        'ip_hash',
        'vote_type',
    ];

    // Using composite keys is not natively supported by Eloquent for standard operations
    // without packages, but we'll disable incrementing and use a custom query for saves if needed.
    public $incrementing = false;
    protected $primaryKey = ['comment_id', 'ip_hash'];

    public function comment()
    {
        return $this->belongsTo(Comment::class);
    }
}
