<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Member extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    const STATUS_PENDING_IT = 'PENDING_IT'; // Creado por RH, falta IT
    const STATUS_ACTIVE     = 'ACTIVO';     // Todo OK
    const STATUS_OFFBOARDING = 'BAJA';      // Falta cerrar algo
    const STATUS_TERMINATED = 'TERMINADO';  // Ciclo cerrado

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
        'hire_end_date', //new column
        'admission_date', //new column
        'termination_date',
        'details',
    ];

    protected $casts = [
        'details'          => 'array',
        'hire_date'        => 'date', // Asignar => 'date'
        'hire_end_date'    => 'date',
        'admission_date'   => 'date',
        'termination_date' => 'date',
    ];

    // State machine
     protected static function booted()
    {
        static::saving(function ($member) {
            $member->status = $member->calculateState();
        });
    }

    /**
     * Lógica de Negocio de Estados basada en Fechas
     */
    public function calculateState(): string
    {
        $hasRhStart = !is_null($this->hire_date);
        $hasRhEnd   = !is_null($this->hire_end_date);
        $hasItStart = !is_null($this->admission_date);
        $hasItEnd   = !is_null($this->termination_date);

        if ($hasRhStart && $hasRhEnd && $hasItStart && $hasItEnd) {
            return 'TERMINADO';
        }

        if ($hasRhEnd || $hasItEnd) {
            return 'BAJA';
        }

        if ($hasItStart && !$hasItEnd && !$hasRhEnd) {
            return 'ACTIVO';
        }

        if ($hasRhStart && !$hasItStart) {
            return 'PENDIENTE_IT';
        }

        // Fallback para registros incompletos
        return 'INCOMPLETO';
    }


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

    public function platformPermissions()
    {
        return $this->belongsToMany(PlatformPermission::class, 'member_platform_permission')
                    ->withPivot(['is_override', 'granted_by'])
                    ->withTimestamps();
    }
}