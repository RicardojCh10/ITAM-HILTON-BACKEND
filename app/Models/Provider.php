<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Provider extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'legal_name',
        'tax_id', 
        'address', 
        'phone', 
        'email', 
        'website',
        'contact_name', 
        'contact_position', 
        'contact_phone', 
        'contact_email'
    ];

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
