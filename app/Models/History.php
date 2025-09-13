<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $fillable = ['product_id', 'user_id', 'action', 'changes', 'note', 'status', 'assign_to'];
    protected $casts = ['changes' => 'array'];


    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
