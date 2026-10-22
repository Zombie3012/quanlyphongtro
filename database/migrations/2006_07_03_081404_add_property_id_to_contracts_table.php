<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->unsignedBigInteger('property_id')
                ->nullable()
                ->after('id');

            $table->foreign('property_id')
                ->references('id')
                ->on('properties')
                ->restrictOnDelete();
        });

        /*
         * Hợp đồng cũ lấy khu từ phòng mà hợp đồng đang thuê.
         */
        DB::table('contracts')
            ->whereNull('property_id')
            ->whereExists(function ($query): void {
                $query->selectRaw('1')
                    ->from('rooms')
                    ->whereColumn('rooms.id', 'contracts.room_id')
                    ->whereNotNull('rooms.property_id');
            })
            ->update([
                'property_id' => DB::raw(
                    '(SELECT rooms.property_id FROM rooms WHERE rooms.id = contracts.room_id)'
                ),
            ]);
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropForeign(['property_id']);
            $table->dropColumn('property_id');
        });
    }
};
