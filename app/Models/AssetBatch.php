<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetBatch extends Model
{
    protected $fillable = ['category_id', 'property_id', 'created_by', 'quantity', 'unit_price', 'po_number'];

    public function assets()
    {
        return $this->hasMany(Asset::class, 'batch_id');
    }

    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }
}