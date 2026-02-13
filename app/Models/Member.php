<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Member extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'property_id',
        'position_id', //new foreign key
        'tm_id',     
        'hilton_id',  
        'name',       
        'last_name',  
        'email',
        // 'position',
        // 'department',
        'onq_id',     
        'status', 
        'hire_date',  
        'termination_date',
        'admission_date', //new column
        'hire_end_date', //new column
        'details',
    ];

    protected $casts = [
        'details' => 'array',
        'hire_date' => 'date', 
        'termination_date' => 'date',
        'admission_date' => 'date', //new column
        'hire_end_date' => 'date', //new column
    ];

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function getDepartmentNameAttribute()
    {
        return $this->position?->department?->name ?? 'Sin Departamento';
    }

    public function getPositionNameAttribute()
    {
        return $this->position?->name ?? 'Sin Puesto';
    }

    public function getFullNameAttribute()
    {
        return trim("{$this->name} {$this->last_name}");
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}