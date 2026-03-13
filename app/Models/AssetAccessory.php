<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetAccessory extends Model
{
    protected $fillable = ['asset_id', 'type', 'brand', 'serial_number'];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
