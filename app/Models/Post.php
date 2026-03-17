<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'title_en',
        'content_en',
        'theme_preference',
        'claps',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'content_en' => 'array',
        ];
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
