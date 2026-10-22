<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rooms', function (Blueprint $table): void {
            $table->id();
            $table->string('room_number', 20);
            $table->decimal('price', 12, 2);
            $table->decimal('area', 8, 2)->nullable();
            $table->enum('status', ['Trống', 'Đang thuê', 'Đang sửa'])->default('Trống');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('tenants', function (Blueprint $table): void {
            $table->id();
            $table->string('full_name', 100);
            $table->string('phone', 20)->nullable();
            $table->string('cccd', 20)->nullable();
            $table->string('address', 255)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['Nam', 'Nữ', 'Khác'])->nullable();
            $table->string('email', 150)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('contracts', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('room_id');
            $table->unsignedBigInteger('tenant_id');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('monthly_rent', 12, 2);
            $table->decimal('deposit', 12, 2)->default(0);
            $table->enum('status', ['Nháp', 'Đang hiệu lực', 'Đã kết thúc', 'Đã hủy'])->default('Đang hiệu lực');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['start_date', 'end_date'], 'idx_contracts_dates');
            $table->index('room_id', 'idx_contracts_room');
            $table->index('tenant_id', 'idx_contracts_tenant');
            $table->index('status', 'idx_contracts_status');
        });

        Schema::create('invoices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('contract_id')->constrained('contracts')->restrictOnDelete();
            $table->string('month', 7);
            $table->decimal('electricity', 12, 2)->default(0);
            $table->decimal('water', 12, 2)->default(0);
            $table->decimal('service_fee', 12, 2)->default(0);
            $table->decimal('other_fee', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->enum('status', ['Chưa thanh toán', 'Đã thanh toán'])->default('Chưa thanh toán');
            $table->date('due_date')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['contract_id', 'month'], 'uq_invoices_contract_month');
            $table->index('status', 'idx_invoices_status');
            $table->index('due_date', 'idx_invoices_due_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('contracts');
        Schema::dropIfExists('tenants');
        Schema::dropIfExists('rooms');
    }
};
