<?php

namespace App\Imports;

use App\Models\Asset;
use App\Models\Member;
use App\Models\Property;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class AssetsImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function model(array $row)
    {
        if (!isset($row['property_code']) || !isset($row['category']) || !isset($row['status'])) {
            return null;
        }

        $inputProperty = $row['property_code'];
        $property = Property::where('code', $inputProperty)->first();

        if (!$property && is_numeric($inputProperty)) {
            $property = Property::find(intval($inputProperty));
        }

        if (!$property) return null; // O throw exception

        $memberId = null;
        if (!empty($row['tm_id'])) {
            $member = Member::where('tm_id', trim($row['tm_id']))
                            ->first();
            if ($member) {
                $memberId = $member->id;
            }
        }

        $specs = [
            'ram' => $row['ram'] ?? null,
            'storage' => $row['storage'] ?? null,
            'processor' => $row['processor'] ?? null,
            'imei' => $row['imei'] ?? null, // Para celulares
            'phone_number' => $row['phone_number'] ?? null,
            'description' => $row['description'] ?? 'Importado masivamente',
        ];

        $specs = array_filter($specs, fn($value) => !is_null($value) && $value !== '');

        return new Asset([
            'property_id'     => $property->id,
            'member_id'       => $memberId, 
            'category'        => trim($row['category']),
            'brand'           => isset($row['brand']) ? trim($row['brand']) : null,
            'model'           => isset($row['model']) ? trim($row['model']) : null,
            'serial_number'   => isset($row['serial_number']) ? trim($row['serial_number']) : null,
            'hilton_name'     => isset($row['hilton_name']) ? trim($row['hilton_name']) : null,
            'mac_address'     => isset($row['mac_address']) ? trim($row['mac_address']) : null,
            'ip_address'      => isset($row['ip_address']) ? trim($row['ip_address']) : null,
            
            'status' => $this->mapStatus($row['status']),
            
            'purchase_date'   => $this->transformDate($row['purchase_date'] ?? null),
            'warranty_expiry' => $this->transformDate($row['warranty_expiry'] ?? null),
            
            'specs'           => $specs,
        ]);
    }

    private function mapStatus($value)
    {
        if (empty($value)) return 'active'; // Default

        $status = strtoupper(trim($value));

        $map = [
            'ACTIVO'      => 'active',
            'REPARACION'  => 'repair',
            'REPARACIÓN'  => 'repair',
            'PERDIDO'     => 'lost',
            'BAJA'        => 'retired',
            'RETIRADO'    => 'retired',
            'ALMACEN'     => 'stored',
            'ALMACÉN'     => 'stored',
            'STOCK'       => 'stored',
        ];

        // Retorna el código traducido o 'active' si no encuentra coincidencia
        return $map[$status] ?? 'active';
    }

    private function transformDate($value)
    {
        if (empty($value)) return null;
        try {
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
            }
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'property_code' => 'required',
            'category'      => 'required',
            'status'        => 'required',
            'serial_number' => 'nullable|unique:assets,serial_number',
        ];
    }
}