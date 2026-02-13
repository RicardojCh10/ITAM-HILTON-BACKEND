<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

     const UPDATED_AT = null; // Deshabilitar la gestión automática de updated_at

    protected $fillable = [
        'name',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function positions()
    {
        return $this->hasMany(Position::class);
    }

    public function members()
    {
        return $this->hasManyThrough(Member::class, Position::class);
    }

}
