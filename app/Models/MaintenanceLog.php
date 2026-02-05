<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceLog extends Model
{
    //
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'asset_id',
        'reported_by', 
        'event_type', 
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

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by');
    }
}
