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
        'provider_id',
        'category_id',
        'batch_id',
        'brand',
        'model',
        'serial_number',
        'hilton_name',
        'mac_address',
        'ip_address',
        'status',
        'price',
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

    //Relaciones con otros modelos

    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }

    public function batch()
    {
        return $this->belongsTo(AssetBatch::class, 'batch_id');
    }

    public function accessories()
    {
        return $this->hasMany(AssetAccessory::class); 
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function member()
    {
        //Dueño del activo
        return $this->belongsTo(Member::class);
    }

    public function provider()
    {
        //Proveedor asociado al activo
        return $this->belongsTo(Provider::class);
    }

    public function maintenanceLogs()
    {
        //Historial de mantenimiento del activo
        return $this->hasMany(MaintenanceLog::class);
    }
}
