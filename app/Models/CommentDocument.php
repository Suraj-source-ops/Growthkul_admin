<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommentDocument extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'comment_id',
        'file_name',
        'mime_type',
        'doc_type',
        'file_path',
        'is_uploaded',
        'is_deleted',
        'deleted_by'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function comment()
    {
        return $this->belongsTo(Product::class, 'comment_id');
    }
}
