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
        Schema::create('asset_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('asset_categories');
            $table->foreignId('property_id')->constrained('properties');
            $table->foreignId('created_by')->constrained('users');
            $table->integer('quantity');
            $table->decimal('unit_price', 12, 2)->nullable();
            $table->string('po_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_batches');
    }
};
