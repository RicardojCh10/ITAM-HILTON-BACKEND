<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    //Campos que se pueden asignar masivamente
    protected $fillable = [
        'property_id',
        'tm_id',
        'name',
        'email',
        'position',
        'department',
        'onq:id',
        'status', 
        'details', // Campo JSON para detalles adicionales
    ];

    //Definición de casts para atributos específicos
    protected $casts = [
        'details' => 'array',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
