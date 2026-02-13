<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Position extends Model
{
    use HasFactory;

     const UPDATED_AT = null; // Deshabilitar la gestión automática de updated_at

    protected $fillable = [
        'department_id',
        'name',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function members()
    {
        return $this->hasMany(Member::class);
    }
}
