<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            // Borramos las columnas de texto antiguo
            $table->dropColumn(['position', 'department']);
        });
    }

    public function down(): void
    {
        // En caso de emergencia, las volvemos a crear
        Schema::table('members', function (Blueprint $table) {
            $table->string('position', 100)->nullable();
            $table->string('department', 100)->nullable();
        });
    }
};