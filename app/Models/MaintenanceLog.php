<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceLog extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'reported_by', //ID del miembro que reportó el mantenimiento
        'event_type', //Tipo de evento: 'repair', 'inspection', 'upgrade', etc.
        'title',
        'description',
        'cost',
        'event_date',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'resolved_date' => 'datetime',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }
}
