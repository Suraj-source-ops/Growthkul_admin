<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    // use SoftDeletes;
    protected $table = 'products';

    protected $fillable = [
        'client_id',
        'product_type',
        'due_date',
        'product_code',
        'slug',
        'product_description',
        'graphic_type',
        'assigned_team',
        'assigned_member',
        'product_status',
        'status_changed_by',
        'updated_by',
        'deleted_at',
        'deleted_by '
    ];

    public function setGraphicTypeAttribute($value)
    {
        $this->attributes['graphic_type'] = is_array($value) ? implode(',', $value) : $value;
    }

    public function getGraphicTypeAttribute($value)
    {
        return explode(',', $value);
    }

    public function documents()
    {
        return $this->hasMany(ProductDocuments::class, 'product_id', 'id');
    }

    public function stages()
    {
        return $this->hasMany(ProductTracking::class, 'product_id', 'id');
    }
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }
    public function team()
    {
        return $this->belongsTo(Team::class, 'assigned_team', 'id');
    }
    public function member()
    {
        return $this->belongsTo(User::class, 'assigned_member', 'id');
    }
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function masterStages()
    {
        return $this->hasOneThrough(
            MasterProductStages::class,
            ProductTracking::class,
            'product_id',
            'id',
            'id',
            'stage_id'
        );
    }


    public function assignedMembers()
    {
        return $this->belongsToMany(User::class, 'product_users', 'product_id', 'user_id')
            ->withPivot('assigned_by')
            ->withTimestamps();
    }
}
