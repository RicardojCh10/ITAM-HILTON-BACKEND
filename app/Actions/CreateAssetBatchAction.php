<?php

namespace App\Actions;

use App\Models\Asset;
use App\Models\AssetBatch;
use App\Models\AssetCategory;
use App\Models\Property;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CreateAssetBatchAction
{
    public function execute(array $data): AssetBatch
    {
        return DB::transaction(function () use ($data) {
            
            // 1. Obtener la Categoría y la Propiedad para extraer prefijos
            $category = AssetCategory::findOrFail($data['category_id']);
            $property = Property::findOrFail($data['property_id']);
            
            $quantity = (int) $data['quantity'];
            $propertyPrefix = $property->code ?? 'CUNQR'; // Ej: H-CUN o CUNQR

            // 2. Crear el Lote (Batch)
            $batch = AssetBatch::create([
                'category_id' => $category->id,
                'property_id' => $property->id,
                'created_by'  => Auth::id() ?? 1, 
                'quantity'    => $quantity,
                'unit_price'  => $data['price'] ?? null,
                'po_number'   => $data['po_number'] ?? null,
            ]);

            // 3. Determinar el último número consecutivo para esta Propiedad y Categoría
            $lastAsset = Asset::where('category_id', $category->id)
                              ->where('property_id', $property->id)
                              ->orderBy('id', 'desc')
                              ->first();
            
            $lastNumber = 0;
            // Extraemos el número del último hilton_name (Ej: de "CUNQR-LT-0045" extraemos "45")
            if ($lastAsset && preg_match('/-(\d+)$/', $lastAsset->hilton_name, $matches)) {
                $lastNumber = (int) $matches[1];
            }

            // 4. Bucle para generar los N Activos
            for ($i = 0; $i < $quantity; $i++) {
                $lastNumber++; // Incrementamos el consecutivo
                
                // Generar Hilton Name (Ej: CUNQR-LT-0046)
                // %s = String, %04d = Número con 4 ceros a la izquierda (0001, 0002, etc)
                $hiltonName = sprintf("%s-%s-%04d", $propertyPrefix, $category->prefix, $lastNumber);

                // Determinar el Serial Number
                // Si la categoría exige serial y el usuario mandó el array, lo tomamos. Si no, 'N/A'
                $serialNumber = 'N/A';
                if ($category->is_serialized && isset($data['serials']) && is_array($data['serials'])) {
                    $serialNumber = $data['serials'][$i] ?? 'N/A';
                }

                // Crear el activo individual en la BD
                $asset = Asset::create([
                    'category_id'     => $category->id,
                    'batch_id'        => $batch->id,
                    'property_id'     => $property->id,
                    'provider_id'     => $data['provider_id'] ?? null,
                    'brand'           => $data['brand'] ?? null,
                    'model'           => $data['model'] ?? null,
                    'serial_number'   => $serialNumber,
                    'hilton_name'     => $hiltonName,
                    'price'           => $data['price'] ?? null,
                    'status'          => $data['status'] ?? 'active',
                    'purchase_date'   => $data['purchase_date'] ?? now(),
                    'warranty_expiry' => $data['warranty_expiry'] ?? null,
                    'specs'           => $data['specs'] ?? null, // Guardamos RAM, procesador, etc.
                ]);

                // 5. Insertar Accesorios Físicos (Si el usuario los definió en accessories_base)
                if (!empty($data['accessories_base'])) {
                    $accessoriesData = [];
                    
                    foreach ($data['accessories_base'] as $acc) {
                        $accessoriesData[] = [
                            'type'          => $acc['type'],
                            'brand'         => $acc['brand'] ?? null,
                            'serial_number' => null, // En carga masiva, los accesorios menores (como un mouse) rara vez se serializan al inicio
                        ];
                    }

                    // Guardamos todos los accesorios vinculados a ESTE activo recién creado
                    if (count($accessoriesData) > 0) {
                        $asset->accessories()->createMany($accessoriesData);
                    }
                }
            }

            // Retornamos el Lote completo (El controlador lo usará para enviar la respuesta)
            return $batch;
        });
    }
}