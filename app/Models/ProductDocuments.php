<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductDocuments extends Model
{
    protected $table = 'product_documents';

    protected $fillable = [
        'user_id',
        'product_id',
        'stage_id',
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
}
