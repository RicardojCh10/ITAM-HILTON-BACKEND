<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'property_id',
        'tm_id',     
        'hilton_id',  
        'name',       
        'last_name',  
        'email',
        'position',
        'department',
        'onq_id',     
        'status', 
        'hire_date',  
        'termination_date',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
        'hire_date' => 'date', 
        'termination_date' => 'date',
    ];

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