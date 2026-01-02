<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    /**
     * Obtener usuarios paginados y opcionalmente filtrados por propiedad.
     */
    public function getAllUsers($perPage = 15, $propertyId = null): LengthAwarePaginator
    {
        $query = User::with('property')->orderBy('id', 'desc');

        // Si nos envían un ID de propiedad, filtramos
        if ($propertyId) {
            $query->where('property_id', $propertyId);
        }

        // Ejecutamos la paginación
        return $query->paginate($perPage);
    }

    public function createUser(array $data): User
    {
        // Laravel encripta la contraseña automáticamente gracias al cast 'hashed' en el Modelo
        return User::create($data);
    }

    public function getUserById($id): User
    {
        return User::findOrFail($id);
    }

    public function updateUser($id, array $data): User
    {
        $user = User::findOrFail($id);
        
        // Lógica de seguridad: Si la contraseña viene vacía o nula, 
        // la quitamos del array para NO sobrescribir la actual con un vacío.
        if (empty($data['password'])) {
            unset($data['password']);
        }
        
        $user->update($data);
        return $user;
    }

    public function deleteUser($id): void
    {
        $user = User::findOrFail($id);
        $user->delete();
    }
}