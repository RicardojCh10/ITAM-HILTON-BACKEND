<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    //Campos que se pueden asignar masivamente
    protected $fillable = [        
        'property_id',
        'member_id',
        'category',
        'brand',
        'model',
        'serial_number',
        'hilton_name',
        'mac_address',
        'ip_address',
        'status',
        'purchase_date',
        'warranty_expiry',
        'specs',        // JSONB archivo para especificaciones adicionales
    ];

    //Definición de casts para atributos específicos
    protected $casts = [
        'specs' => 'array',
        'purchase_date' => 'date',
        'warranty_expiry' => 'date',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function member()
    {
        //Dueño del activo
        return $this->belongsTo(Member::class);
    }

    public function maintenanceLogs()
    {
        //Historial de mantenimiento del activo
        return $this->hasMany(MaintenanceLog::class);
    }
}
