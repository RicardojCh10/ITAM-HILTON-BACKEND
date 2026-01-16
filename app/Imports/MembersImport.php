<?php

namespace App\Imports;

use App\Models\Member;
use App\Models\Property;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MembersImport implements ToModel, WithHeadingRow, WithValidation
{
   
    public function model(array $row)
    {
        // Lógica para encontrar la propiedad por nombre o código si viene en el Excel
        // Asumimos que en el Excel viene una columna 'property_code' (ej: CUNQR)
        $property = Property::where('code', $row['property_code'])->first();
        
        if (!$property) return null; // O lanzar error

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
            'status'      => 'ACTIVO', // Default al importar
            'hire_date'   => \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['hire_date']),
            'details'     => ['notes' => 'Importado masivamente'],
        ]);
    }

    // Reglas de validación para cada fila
    public function rules(): array
    {
        return [
            'email' => 'unique:members,email', // Evitar duplicados
            'tm_id' => 'required',
            'property_code' => 'required',
        ];
    }
}