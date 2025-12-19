<?php

namespace App\Services;

use App\Models\Asset;
use Illuminate\Support\Facades\DB;

class AssetService
{
    // Lista EXACTA de columnas que SÍ existen en tu tabla 'assets'
    // Todo lo que NO esté aquí, se guardará en el JSON 'specs'
    protected $standardColumns = [
        'property_id',
        'member_id',
        'category',
        'brand',
        'model',
        'serial_number',
        'hilton_name',    // Agregado
        'mac_address',    // Agregado
        'ip_address',     // Agregado
        'status',
        'purchase_date',
        'warranty_expiry'
    ];

    /**
     * Crea un activo separando datos técnicos (JSON) de datos estándar.
     */
    public function createAsset(array $data)
    {
        return DB::transaction(function () use ($data) {
            $insertData = [];
            $specs = [];

            foreach ($data as $key => $value) {
                // Si el campo es una columna real, lo preparamos para insertar
                if (in_array($key, $this->standardColumns)) {
                    $insertData[$key] = $value;
                } else {
                    // Si no es columna real (ej: 'imei', 'ram', 'toner'), va al JSON
                    $specs[$key] = $value;
                }
            }

            // Asignamos el array de especificaciones al campo 'specs'
            $insertData['specs'] = $specs;

            return Asset::create($insertData);
        });
    }

    /**
     * Obtiene activos filtrados por propiedad y carga la relación del miembro
     */
    public function getAssetsByProperty($propertyId)
    {
        return Asset::with('member') // Traemos datos del dueño
                    ->where('property_id', $propertyId)
                    ->orderBy('created_at', 'desc')
                    ->get();
    }
}