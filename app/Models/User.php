<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
// use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public $timestamps = false;
   
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'property_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];
    
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role,
            'property_id' => $this->property_id,
        ];
    }
}
