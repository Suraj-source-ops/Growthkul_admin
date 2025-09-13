<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Files extends Model
{
    protected $fillable = [
        'user_id',
        'file_name',
        'mime_type',
        'doc_type',
        'file_path',
        'is_uploaded',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function getLocalFileUrlAttribute()
    {
        return  $this->file_path ? asset($this->file_path) : null;
    }
}
