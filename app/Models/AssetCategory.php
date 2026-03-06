<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetCategory extends Model
{

    protected $fillable = [
        'name',
        'slug',
        'prefix',
        'icon',
        'is_serialized',
        'has_network_fields'
    ];

    protected $casts = [
        'is_serialized' => 'boolean',
        'has_network_fields' => 'boolean',
    ];

    public function assets()
    {
        return $this->hasMany(Asset::class, 'category_id');
    }
}
