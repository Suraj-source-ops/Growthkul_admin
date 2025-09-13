<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MasterProductStages extends Model
{
    protected $table = 'master_product_stages';

    protected $fillable = [
        'sequence',
        'is_active',
        'name',
        'type',
    ];

    public function trackings()
    {
        return $this->hasMany(ProductTracking::class, 'stage_id');
    }
}
