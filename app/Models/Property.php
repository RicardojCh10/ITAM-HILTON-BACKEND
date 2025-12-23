<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    //
    use HasFactory;

    const UPDATED_AT = null; // Deshabilitar la gestión automática de updated_at
    //Campos que se pueden asignar 
    protected $fillable = [
        'name',
        'code',
    ];

    public function members()
    {
        return $this->hasMany(Member::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
