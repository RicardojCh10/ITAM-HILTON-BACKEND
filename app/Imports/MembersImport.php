<?php

namespace App\Imports;

use App\Models\Member;
use App\Models\Property;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class MembersImport implements ToModel, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    public function model(array $row)
    {

        if (!isset($row['tm_id']) || !isset($row['property_code'])) {
            return null;
        }

        $inputProperty = trim($row['property_code']);

        $property = Property::where('code', $inputProperty)->first();

        if (!$property && is_numeric($inputProperty)) {
            $property = Property::find($inputProperty);
        }

        if (!$property) {
            return null; 
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
            'status'      => isset($row['status']) ? strtoupper(trim($row['status'])) : 'ACTIVO',
            
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