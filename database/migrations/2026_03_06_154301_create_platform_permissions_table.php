<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('platform_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('platform_id')->constrained('platforms')->cascadeOnDelete();
            $table->string('name', 100); // Ej. 'POS Card', 'Manager'
            $table->string('description')->nullable();
            $table->timestamps();

            // REGLA DE NEGOCIO: No pueden existir dos permisos con el mismo nombre en la misma plataforma
            $table->unique(['platform_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('platform_permissions');
    }
};
