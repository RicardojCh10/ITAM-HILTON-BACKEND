<?php

namespace App\Services;

use App\Models\Department;
use Illuminate\Pagination\LengthAwarePaginator;

class DepartmentService
{
    public function getAllDepartments($perPage = 15, $search = null): LengthAwarePaginator
    {
        $query = Department::orderBy('name', 'asc');

        if ($search) {
            $query->where('name', 'LIKE', "%{$search}%");
        }

        return $query->paginate($perPage);
    }

    public function createDepartment(array $data)
    {
        return Department::create($data);
    }

    public function getDepartmentById(int $id)
    {
        return Department::findOrFail($id);
    }

    public function updateDepartment($id, array $data)
    {
        $department = Department::findOrFail($id);
        $department->update($data);
        return $department;
    }

    public function deleteDepartment($id)
    {
        $department = Department::findOrFail($id);
        $department->delete();
    }
}