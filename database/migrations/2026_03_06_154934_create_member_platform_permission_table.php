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
        Schema::create('member_platform_permission', function (Blueprint $table) {
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->foreignId('platform_permission_id')->constrained('platform_permissions')->cascadeOnDelete();
            
            // CAMPOS DE AUDITORÍA Y LÓGICA
            $table->boolean('is_override')->default(false);
            $table->foreignId('granted_by')->nullable()->constrained('users')->nullOnDelete(); // El usuario IT que autorizó

            $table->timestamps(); // Vital para auditorías (saber CUÁNDO se le dio el acceso)

            $table->primary(['member_id', 'platform_permission_id'], 'member_permission_primary');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_platform_permission');
    }
};
