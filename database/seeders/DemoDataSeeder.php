<?php

namespace Database\Seeders;

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Property;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('Demo data seeding is only allowed in local or testing environments.');
        }

        $password = Hash::make((string) env('DEMO_ACCOUNT_PASSWORD', 'ChangeMe123!'));

        $property = Property::query()->firstOrCreate(
            ['name' => 'Khu trọ Demo An Bình'],
            [
                'address' => '123 Đường Demo, Quận 1, TP. Hồ Chí Minh',
                'phone' => '0900000000',
                'description' => 'Dữ liệu mẫu để trình diễn các chức năng chính.',
                'status' => 'active',
            ],
        );

        User::query()->firstOrCreate(
            ['username' => 'demo_admin'],
            [
                'name' => 'Quản trị viên Demo',
                'email' => 'demo-admin@example.test',
                'password' => $password,
                'role' => 'admin',
                'property_id' => null,
            ],
        );

        User::query()->firstOrCreate(
            ['username' => 'demo_manager'],
            [
                'name' => 'Quản lý Demo',
                'email' => 'demo-manager@example.test',
                'password' => $password,
                'role' => 'manager',
                'property_id' => $property->id,
            ],
        );

        $room = Room::query()->firstOrCreate(
            ['property_id' => $property->id, 'room_number' => 'A-101'],
            ['price' => '2500000.00', 'area' => '24.00', 'status' => 'Đang thuê'],
        );

        $tenant = Tenant::query()->firstOrCreate(
            ['property_id' => $property->id, 'cccd' => '000000000001'],
            [
                'full_name' => 'Người thuê mẫu',
                'phone' => '0900000001',
                'address' => 'Địa chỉ mẫu',
            ],
        );

        $contract = Contract::query()->firstOrCreate(
            ['room_id' => $room->id, 'tenant_id' => $tenant->id],
            [
                'property_id' => $property->id,
                'start_date' => '2026-01-01',
                'end_date' => null,
                'monthly_rent' => '2500000.00',
                'deposit' => '2500000.00',
                'status' => 'Đang hiệu lực',
                'notes' => 'Hợp đồng mẫu; không chứa dữ liệu cá nhân thật.',
            ],
        );

        Invoice::query()->firstOrCreate(
            ['contract_id' => $contract->id, 'month' => '2026-01'],
            [
                'rent_amount' => '2500000.00',
                'electricity' => '125000.00',
                'water' => '75000.00',
                'service_fee' => '50000.00',
                'other_fee' => '0.00',
                'total' => '2750000.00',
                'status' => 'Chưa thanh toán',
                'due_date' => '2026-01-10',
                'notes' => 'Hóa đơn mẫu.',
            ],
        );
    }
}
