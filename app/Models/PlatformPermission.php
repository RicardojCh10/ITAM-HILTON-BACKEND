<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlatformPermission extends Model
{
    use HasFactory;

    protected $fillable = ['platform_id', 'name', 'description'];

    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    public function positions()
    {
        return $this->belongsToMany(Position::class, 'position_platform_permission');
    }

    public function members()
    {
        return $this->belongsToMany(Member::class, 'member_platform_permission')
                    ->withPivot(['is_override', 'granted_by'])
                    ->withTimestamps();
    }
}