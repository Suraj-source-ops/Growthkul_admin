<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'title',
        'description',
        'image',
        'project_url',
        'status',
        'created_at',
        'updated_at',
    ];

    public function projectImage()
    {
        return $this->hasOne(Files::class, 'id', 'image');
    }
}
