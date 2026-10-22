<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->unsignedBigInteger('property_id')->nullable()->after('id');

            $table->foreign('property_id')
                ->references('id')
                ->on('properties')
                ->restrictOnDelete();
        });

        // Gán toàn bộ phòng hiện tại vào nhà trọ cũ.
        DB::table('rooms')->update([
            'property_id' => 1,
        ]);
    }

    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropForeign(['property_id']);
            $table->dropColumn('property_id');
        });
    }
};