<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ==========================================
        // 1. ACTUALIZACIÓN DE USERS (Solo Nombre y Apellido)
        // ==========================================
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'last_name')) {
                $table->string('last_name', 100)->nullable()->after('name');
            }
        });

        // Migrar datos de Users (Separar nombres viejos)
        $users = DB::table('users')->select('id', 'name')->get();
        foreach ($users as $user) {
            $parts = explode(' ', trim($user->name), 2);
            DB::table('users')->where('id', $user->id)->update([
                'name' => $parts[0],
                'last_name' => $parts[1] ?? ''
            ]);
        }

        // ==========================================
        // 2. ACTUALIZACIÓN DE MEMBERS (Hilton ID + Apellido + Fecha)
        // ==========================================
        Schema::table('members', function (Blueprint $table) {
            // Apellido
            if (!Schema::hasColumn('members', 'last_name')) {
                $table->string('last_name', 150)->nullable()->after('name');
            }
            
            // AQUI AGREGAMOS EL HILTON ID (HR)
            if (!Schema::hasColumn('members', 'hilton_id')) {
                $table->string('hilton_id', 50)->nullable()->after('tm_id');
            }

            // Fecha de contratación
            if (!Schema::hasColumn('members', 'hire_date')) {
                $table->date('hire_date')->nullable()->after('status');
            }
        });

        // Migrar datos de Members (Separar nombres viejos)
        $members = DB::table('members')->select('id', 'name')->get();
        foreach ($members as $member) {
            $parts = explode(' ', trim($member->name), 2);
            DB::table('members')->where('id', $member->id)->update([
                'name' => $parts[0],
                'last_name' => $parts[1] ?? ''
            ]);
        }
    }

    public function down(): void
    {        
        // 1. Regresar Users
        $users = DB::table('users')->select('id', 'name', 'last_name')->get();
        foreach ($users as $user) {
            DB::table('users')->where('id', $user->id)->update([
                'name' => trim($user->name . ' ' . $user->last_name)
            ]);
        }
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('last_name');
        });

        // 2. Regresar Members
        $members = DB::table('members')->select('id', 'name', 'last_name')->get();
        foreach ($members as $member) {
            DB::table('members')->where('id', $member->id)->update([
                'name' => trim($member->name . ' ' . $member->last_name)
            ]);
        }
        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['last_name', 'hilton_id', 'hire_date']);
        });
    }
};