<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTracking extends Model
{
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function masterStage()
    {
        return $this->belongsTo(MasterProductStages::class, 'stage_id');
    }
}
