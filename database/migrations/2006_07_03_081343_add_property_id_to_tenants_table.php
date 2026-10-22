<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->unsignedBigInteger('property_id')
                ->nullable()
                ->after('id');

            $table->foreign('property_id')
                ->references('id')
                ->on('properties')
                ->restrictOnDelete();
        });

        // Toàn bộ người thuê hiện tại thuộc nhà trọ cũ.
        DB::table('tenants')->update([
            'property_id' => 1,
        ]);
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['property_id']);
            $table->dropColumn('property_id');
        });
    }
};