<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
    {
        // 1. Tabla Departamentos
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        // 2. Tabla Puestos
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained('departments')->onDelete('cascade');
            $table->string('name');
            $table->unique(['department_id', 'name']); 
            $table->timestamps();
        });

        // 3. Modificar Tabla Members
        Schema::table('members', function (Blueprint $table) {
            $table->date('admission_date')->nullable()->after('status'); // Fecha Ingreso
            $table->date('hire_end_date')->nullable()->after('admission_date'); // Fin contrato
            
            // La nueva relación
            $table->foreignId('position_id')
                  ->nullable() 
                  ->after('id')
                  ->constrained('positions')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['position_id']);
            $table->dropColumn(['position_id', 'admission_date', 'hire_end_date']);
        });
        Schema::dropIfExists('positions');
        Schema::dropIfExists('departments');
    }
};
