<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. PROPERTIES
        if (!Schema::hasTable('properties')) {
            Schema::create('properties', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('code', 50)->unique();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        // 2. USERS
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('email', 150)->unique();
                $table->string('password', 255);
                $table->string('role', 50)->default('admin');
                $table->foreignId('property_id')->nullable()->constrained('properties')->onDelete('set null');
                $table->timestamp('created_at')->useCurrent();
            });
        }

        // 3. SESSIONS
        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }

        // 4. MEMBERS
        if (!Schema::hasTable('members')) {
            Schema::create('members', function (Blueprint $table) {
                $table->id();
                $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
                $table->string('tm_id', 50)->nullable();
                $table->string('name', 150);
                $table->string('email', 150)->nullable();
                $table->string('position', 100)->nullable();
                $table->string('department', 100)->nullable();
                $table->string('onq_id', 50)->nullable();
                $table->string('status', 20)->default('active');
                $table->jsonb('details')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        // 5. ASSETS
        if (!Schema::hasTable('assets')) {
            Schema::create('assets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('property_id')->constrained('properties')->onDelete('cascade');
                $table->foreignId('member_id')->nullable()->constrained('members')->onDelete('set null');
                $table->string('category', 50);
                $table->string('brand', 50)->nullable();
                $table->string('model', 100)->nullable();
                $table->string('serial_number', 100)->nullable();
                $table->string('hilton_name', 100)->nullable();
                $table->string('mac_address', 50)->nullable();
                $table->string('ip_address', 50)->nullable();
                $table->string('status', 50)->default('active');
                $table->date('purchase_date')->nullable();
                $table->date('warranty_expiry')->nullable();
                $table->jsonb('specs')->default(new \Illuminate\Database\Query\Expression("'{}'::jsonb"));
                $table->timestamps();
            });
        }

        // 6. MAINTENANCE_LOGS
        if (!Schema::hasTable('maintenance_logs')) {
            Schema::create('maintenance_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
                $table->foreignId('reported_by')->nullable()->constrained('users')->onDelete('set null');
                $table->string('event_type', 50);
                $table->string('title', 150)->nullable();
                $table->text('description')->nullable();
                $table->decimal('cost', 10, 2)->default(0);
                $table->timestamp('event_date')->useCurrent();
                $table->timestamp('resolved_date')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        // 7. CACHE
        if (!Schema::hasTable('cache')) {
            Schema::create('cache', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->mediumText('value');
                $table->integer('expiration');
            });
        }
        
        if (!Schema::hasTable('cache_locks')) {
            Schema::create('cache_locks', function (Blueprint $table) {
                $table->string('key')->primary();
                $table->string('owner');
                $table->integer('expiration');
            });
        }
    }

    public function down(): void
    {
        // Dejar vacío o borrar tablas si quieres hacer rollback
    }
};