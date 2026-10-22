<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table): void {
            $table->unsignedBigInteger('property_id')->nullable(false)->change();
            $table->unique(['id', 'property_id'], 'uq_rooms_id_property');
            $table->unique(['property_id', 'room_number'], 'uq_rooms_property_number');
        });

        Schema::table('tenants', function (Blueprint $table): void {
            $table->unsignedBigInteger('property_id')->nullable(false)->change();
            $table->unique(['id', 'property_id'], 'uq_tenants_id_property');
            $table->index('full_name', 'idx_tenants_name');
            $table->index('phone', 'idx_tenants_phone');
            $table->index('cccd', 'idx_tenants_cccd');
        });

        Schema::table('contracts', function (Blueprint $table): void {
            $table->unsignedBigInteger('property_id')->nullable(false)->change();
            $table->foreign(['room_id', 'property_id'], 'fk_contracts_room_property')
                ->references(['id', 'property_id'])->on('rooms')->restrictOnDelete();
            $table->foreign(['tenant_id', 'property_id'], 'fk_contracts_tenant_property')
                ->references(['id', 'property_id'])->on('tenants')->restrictOnDelete();
            $table->index('property_id', 'idx_contracts_property');
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table): void {
            $table->dropForeign('fk_contracts_room_property');
            $table->dropForeign('fk_contracts_tenant_property');
            $table->dropIndex('idx_contracts_property');
            $table->unsignedBigInteger('property_id')->nullable()->change();
        });

        Schema::table('tenants', function (Blueprint $table): void {
            $table->dropUnique('uq_tenants_id_property');
            $table->dropIndex('idx_tenants_name');
            $table->dropIndex('idx_tenants_phone');
            $table->dropIndex('idx_tenants_cccd');
            $table->unsignedBigInteger('property_id')->nullable()->change();
        });

        Schema::table('rooms', function (Blueprint $table): void {
            $table->dropUnique('uq_rooms_id_property');
            $table->dropUnique('uq_rooms_property_number');
            $table->unsignedBigInteger('property_id')->nullable()->change();
        });
    }
};
