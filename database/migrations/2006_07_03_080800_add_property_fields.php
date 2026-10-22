<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table): void {
            $table->string('phone', 20)->nullable();
            $table->text('description')->nullable();
            $table->string('address', 255)->nullable(false)->change();
            $table->enum('status', ['active', 'inactive'])->default('active')->change();
        });
    }

    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table): void {
            $table->dropColumn(['phone', 'description']);
            $table->string('address', 255)->nullable()->change();
            $table->string('status', 20)->default('active')->change();
        });
    }
};
