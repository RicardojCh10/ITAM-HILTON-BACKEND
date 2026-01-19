<?php

namespace App\Imports;

use App\Models\Member;
use App\Models\Property;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MembersImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $inputProperty = trim($row['property_code']);

        // 1. Buscamos por código
        $property = Property::where('code', $inputProperty)->first();

        // 2. Si no hay código y es numérico, buscamos por ID
        if (!$property && is_numeric($inputProperty)) {
            $property = Property::find($inputProperty);
        }

        // 3. Si sigue sin existir, lanzamos error
        if (!$property) {
            throw new \Exception("Error en la fila: No se encontró ninguna propiedad con código o ID: '{$inputProperty}'");
        }

        return new Member([
            'property_id' => $property->id,
            'tm_id'       => $row['tm_id'],
            'hilton_id'   => $row['hilton_id'],
            'name'        => $row['name'],     
            'last_name'   => $row['last_name'], 
            'email'       => $row['email'],
            'position'    => $row['position'],
            'department'  => $row['department'],
            'onq_id'      => $row['onq_id'],
            'status'      => 'Active', // Cambiado a 'Active' para consistencia con tus otros datos
            
            'hire_date'   => $this->transformDate($row['hire_date']),
            
            'details'     => ['notes' => 'Importado masivamente'],
        ]);
    }

    // Transforma fechas de Excel o cadenas a Carbon
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
            'email' => 'unique:members,email',
            'tm_id' => 'required',
            'property_code' => 'required',
        ];
    }
}