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
        Schema::table('assets', function (Blueprint $table) {
            // 1. Eliminar columna vieja
            $table->dropColumn(['category']);

            // 2. Agregar nuevas relaciones
            $table->foreignId('category_id')->after('id')->constrained('asset_categories');
            $table->foreignId('batch_id')->nullable()->after('category_id')->constrained('asset_batches')->nullOnDelete();

            // 3. Nuevos campos
            $table->decimal('price', 12, 2)->nullable()->after('status');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
