<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Property;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear una Propiedad (Hotel)
        $property = Property::firstOrCreate([
            'code' => 'CUNQR'
        ], [
            'name' => 'Hilton Cancun All Inclusive',
            'code' => 'CUNQR'
        ]);

        // 2. Crear el Usuario Admin
        User::firstOrCreate([
            'email' => 'admin@hilton.com' 
        ], [
            'name' => 'ITAM Admin',
            'email' => 'admin@hilton.com',
            'password' => Hash::make('superuser-itam'), 
            'role' => 'admin',
            'property_id' => $property->id
        ]);
        
        // Mensaje en consola al terminar
        $this->command->info('Usuario Admin creado: admin@hilton.com / superuser-itam');
    }
}