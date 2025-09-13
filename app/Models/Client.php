<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
   protected $fillable = [
        'clientid',
        'name',
        'email',
        'phone',
        'address',
        'created_at',
        'updated_at',
    ];
    public function products()
    {
        return $this->hasMany(Product::class, 'client_id');
    }
}
