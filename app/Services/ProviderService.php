<?php

namespace App\Services;

use App\Models\Provider;
use Illuminate\Pagination\LengthAwarePaginator;

class ProviderService
{
    public function getAllProviders($perPage = 15, $search = null): LengthAwarePaginator
    {
        $query = Provider::orderBy('name', 'asc');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('legal_name', 'LIKE', "%{$search}%")
                  ->orWhere('tax_id', 'LIKE', "%{$search}%")
                  ->orWhere('contact_name', 'LIKE', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    /**
     * Obtener lista ligera para Dropdowns (como lo haces en selects)
     */
    public function getProvidersDropdown()
    {
        return Provider::orderBy('name')->select('id', 'name')->get();
    }

    public function createProvider(array $data)
    {
        return Provider::create($data);
    }

    public function getProviderById(int $id)
    {
        return Provider::findOrFail($id);
    }

    public function updateProvider($id, array $data)
    {
        $provider = Provider::findOrFail($id);
        $provider->update($data);
        return $provider;
    }

    public function deleteProvider($id)
    {
        $provider = Provider::findOrFail($id);
        $provider->delete();
    }
}