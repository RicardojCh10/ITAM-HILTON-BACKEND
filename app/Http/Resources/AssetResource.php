<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AssetResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // 1. Obtenemos el array base del modelo
        $baseData = parent::toArray($request);

        // 2. Extraemos el nombre del dueño (si existe) para mostrarlo fácil en la tabla
        $ownerName = $this->member ? $this->member->name : 'Sin Asignar (Bodega)';

        // 3. Mezclamos todo: Datos base + Nombre Dueño + Contenido del JSON specs
        // De esta forma, en Vue podrás acceder a 'item.ram' directamente
        return array_merge($baseData, [
            'owner_name' => $ownerName,
            // Al hacer esto, los campos dentro de specs (ej: imei) quedan al mismo nivel
            // Si prefieres mantenerlos separados, usa: 'details' => $this->specs
            'details' => $this->specs, 
        ]);
    }
}