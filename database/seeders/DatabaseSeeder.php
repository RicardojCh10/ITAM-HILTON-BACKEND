<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Property;
use App\Models\Provider;
use App\Models\Member;
use App\Models\AssetCategory;
use App\Models\AssetBatch;
use App\Models\Asset;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

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

        // 2. Crear el Usuario Admin (Lo guardamos en variable para usar su ID)
        $admin = User::firstOrCreate([
            'email' => 'admin@hilton.com' 
        ], [
            'name' => 'ITAM',
            'last_name' => 'Admin',
            'email' => 'admin@hilton.com',
            'password' => Hash::make('superuser-itam'), 
            'role' => 'admin',
            'property_id' => $property->id
        ]);
        
        // 3. Crear un Proveedor
        $provider = Provider::firstOrCreate([
            'tax_id' => 'AAA010101AAA' // RFC o Tax ID de prueba
        ], [
            'name' => 'Dell Tecnologías México',
            'legal_name' => 'Dell México S.A. de C.V.',
            'email' => 'ventas@dell.com.mx',
            'phone' => '5551234567',
            'contact_name' => 'Carlos Ventas',
            'contact_position' => 'Ejecutivo de Cuentas'
        ]);

        // 4. Crear un Miembro (Empleado)
        $member = Member::firstOrCreate([
            'tm_id' => '123456'
        ], [
            'property_id' => $property->id,
            'name' => 'Juan',
            'last_name' => 'Pérez',
            'email' => 'juan.perez@hilton.com',
            'onq_id' => 'JPEREZ',
            'status' => 'active',
            'hire_date' => Carbon::now()->subMonths(6)->format('Y-m-d')
        ]);

        // 5. Crear una Categoría de Activo (Para la nueva arquitectura)
        $category = AssetCategory::firstOrCreate([
            'slug' => 'laptop'
        ], [
            'name' => 'Laptop',
            'prefix' => 'LT',
            'icon' => 'pi-laptop',
            'is_serialized' => true,
            'has_network_fields' => true
        ]);

        // 6. Crear un Lote (Batch) de Activos
        $batch = AssetBatch::firstOrCreate([
            'po_number' => 'PO-9999' // Orden de compra de prueba
        ], [
            'category_id' => $category->id,
            'property_id' => $property->id,
            'created_by' => $admin->id,
            'quantity' => 1,
            'unit_price' => 25000.00
        ]);

        // 7. Crear el Activo (Asset) uniendo TODAS las tablas anteriores
        Asset::firstOrCreate([
            'serial_number' => 'DELL-TEST-001'
        ], [
            'property_id' => $property->id,
            'category_id' => $category->id,
            'batch_id' => $batch->id,
            'provider_id' => $provider->id,
            'member_id' => $member->id, // ¡Asignado a Juan Pérez!
            
            'brand' => 'Dell',
            'model' => 'Latitude 5530',
            'hilton_name' => 'CUNQR-LT-0001',
            'mac_address' => '00:1B:44:11:3A:B7',
            'ip_address' => '10.10.20.50',
            'status' => 'active',
            'price' => 25000.00,
            
            'purchase_date' => Carbon::now()->subDays(10)->format('Y-m-d'),
            
            'specs' => [
                'ram' => '16GB',
                'storage' => '512GB SSD',
                'processor' => 'Intel Core i7'
            ]
        ]);

        // Mensaje en consola al terminar
        $this->command->info('==========================================');
        $this->command->info('✅ Base de datos reconstruida con éxito.');
        $this->command->info('👤 Admin: admin@hilton.com / superuser-itam');
        $this->command->info('💻 Se generó 1 Laptop asignada a Juan Pérez.');
        $this->command->info('==========================================');
    }
}