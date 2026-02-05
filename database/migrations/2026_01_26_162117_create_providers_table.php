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
        Schema::create('providers', function (Blueprint $table) {
            $table->id();

            // 1. Datos de la Empresa
            $table->string('name'); // Nombre comercial
            $table->string('legal_name')->nullable(); // Razon social
            $table->string('tax_id', 20)->nullable()->index(); //RFC
            $table->text('address')->nullable();
            
            // 2. Contacto General
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();

            // 3. Datos del Representante (Account Manager)
            $table->string('contact_name')->nullable();
            $table->string('contact_position')->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->string('contact_email')->nullable();

            $table->timestamps();
            $table->softDeletes(); // Importante para no perder historial
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('providers');
    }
};
