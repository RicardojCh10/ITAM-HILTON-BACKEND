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
       Schema::create('position_platform_permission', function (Blueprint $table) {
            $table->foreignId('position_id')->constrained('positions')->cascadeOnDelete();
            $table->foreignId('platform_permission_id')->constrained('platform_permissions')->cascadeOnDelete();
            
            // RENDIMIENTO: Llave primaria compuesta evita duplicados y crea el mejor índice posible
            $table->primary(['position_id', 'platform_permission_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('position_platform_permission');
    }
};
