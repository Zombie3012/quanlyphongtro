<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('username', 50)->nullable()->unique();
            $table->enum('role', ['admin', 'manager'])->default('manager');
            $table->string('name', 100)->change();
            $table->string('email', 150)->nullable()->change();
        });

        DB::table('users')->whereNull('username')->orderBy('id')->each(function (object $user): void {
            DB::table('users')->where('id', $user->id)->update([
                'username' => 'user-'.$user->id,
            ]);
        });

        Schema::table('users', function (Blueprint $table): void {
            $table->string('username', 50)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique(['username']);
            $table->dropColumn(['username', 'role']);
            $table->string('name')->change();
            $table->string('email')->nullable(false)->change();
        });
    }
};
