<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $fillable = [
        'type',
        'title',
        'slug',
        'content',
        'author',
        'image',
        'meta_title',
        'meta_description',
        'social_media_type',
        'social_link',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'author', 'id');
    }

    public function blogImage()
    {
        return $this->hasOne(Files::class, 'id', 'image');
    }
}
