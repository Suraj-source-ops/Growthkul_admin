<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'product_id',
        'product_type',
        'comment',
        'comment_by',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'comment_by');
    }
    public function documents()
    {
        return $this->hasMany(CommentDocument::class, 'comment_id');
    }
}
