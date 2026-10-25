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
     * Populate a repeatable, varied dataset for local walkthroughs.
     */
    public function run(): void
    {
        if (! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('Demo data seeding is only allowed in local or testing environments.');
        }

        $password = Hash::make('12345678');

        User::query()->updateOrCreate(
            ['username' => 'demo_admin'],
            [
                'name' => 'Quản trị viên Demo',
                'email' => 'demo-admin@example.test',
                'password' => $password,
                'role' => 'admin',
                'property_id' => null,
            ],
        );

        $propertyData = [
            [
                'name' => 'Khu trọ An Bình',
                'address' => '123 Đường An Bình, Quận 1, TP. Hồ Chí Minh',
                'phone' => '0901000001',
                'description' => 'Khu trọ gần trung tâm, có nhiều loại phòng và tiện ích cơ bản.',
                'manager' => ['username' => 'demo_manager', 'name' => 'Nguyễn Minh Anh', 'email' => 'manager-anbinh@example.test'],
                'rooms' => [
                    ['A-101', 2500000, 24, 'Đang thuê'],
                    ['A-102', 3200000, 30, 'Đang thuê'],
                    ['A-103', 2100000, 20, 'Trống'],
                    ['A-201', 2800000, 26, 'Đang sửa'],
                    ['A-202', 3500000, 32, 'Trống'],
                ],
                'tenants' => [
                    ['Nguyễn Hoàng Nam', '0901000101', '001100000101'],
                    ['Trần Thảo Vy', '0901000102', '001100000102'],
                    ['Lê Quốc Bảo', '0901000103', '001100000103'],
                    ['Phạm Ngọc Hà', '0901000104', '001100000104'],
                ],
                'contracts' => [
                    ['room' => 0, 'tenant' => 0, 'start' => '2026-01-01', 'end' => null, 'status' => 'Đang hiệu lực', 'rent' => 2500000, 'deposit' => 2500000],
                    ['room' => 1, 'tenant' => 1, 'start' => '2026-04-15', 'end' => null, 'status' => 'Đang hiệu lực', 'rent' => 3200000, 'deposit' => 3200000],
                    ['room' => 2, 'tenant' => 2, 'start' => '2025-01-01', 'end' => '2025-12-31', 'status' => 'Đã kết thúc', 'rent' => 2100000, 'deposit' => 2100000],
                    ['room' => 4, 'tenant' => 3, 'start' => '2026-10-01', 'end' => null, 'status' => 'Nháp', 'rent' => 3500000, 'deposit' => 3500000],
                ],
            ],
            [
                'name' => 'Nhà trọ Hòa Bình',
                'address' => '45 Đường Hòa Bình, Quận Tân Phú, TP. Hồ Chí Minh',
                'phone' => '0902000001',
                'description' => 'Khu trọ yên tĩnh, phù hợp sinh viên và người đi làm.',
                'manager' => ['username' => 'demo_manager_tanphu', 'name' => 'Võ Thanh Tùng', 'email' => 'manager-tanphu@example.test'],
                'rooms' => [
                    ['B-01', 1800000, 18, 'Đang thuê'],
                    ['B-02', 2200000, 22, 'Đang thuê'],
                    ['B-03', 2400000, 24, 'Đang thuê'],
                    ['B-04', 2000000, 20, 'Trống'],
                    ['B-05', 3000000, 28, 'Trống'],
                ],
                'tenants' => [
                    ['Đặng Gia Huy', '0902000101', '002200000101'],
                    ['Bùi Khánh Linh', '0902000102', '002200000102'],
                    ['Phan Đức Long', '0902000103', '002200000103'],
                    ['Đỗ Mai Chi', '0902000104', '002200000104'],
                ],
                'contracts' => [
                    ['room' => 0, 'tenant' => 0, 'start' => '2026-02-01', 'end' => null, 'status' => 'Đang hiệu lực', 'rent' => 1800000, 'deposit' => 1800000],
                    ['room' => 1, 'tenant' => 1, 'start' => '2026-03-01', 'end' => null, 'status' => 'Đang hiệu lực', 'rent' => 2200000, 'deposit' => 2200000],
                    ['room' => 2, 'tenant' => 2, 'start' => '2026-05-01', 'end' => null, 'status' => 'Đang hiệu lực', 'rent' => 2400000, 'deposit' => 2400000],
                ],
            ],
            [
                'name' => 'Căn hộ mini Mộc Lan',
                'address' => '78 Đường Mộc Lan, TP. Thủ Đức, TP. Hồ Chí Minh',
                'phone' => '0903000001',
                'description' => 'Căn hộ mini có phòng diện tích rộng, một số phòng đang bảo trì.',
                'manager' => ['username' => 'demo_manager_thuduc', 'name' => 'Phạm Thu Trang', 'email' => 'manager-thuduc@example.test'],
                'rooms' => [
                    ['C-301', 4200000, 38, 'Đang thuê'],
                    ['C-302', 4500000, 42, 'Trống'],
                    ['C-303', 3900000, 35, 'Đang sửa'],
                    ['C-401', 4800000, 45, 'Trống'],
                    ['C-402', 3600000, 33, 'Trống'],
                ],
                'tenants' => [
                    ['Hoàng Tuấn Kiệt', '0903000101', '003300000101'],
                    ['Nguyễn Diệu My', '0903000102', '003300000102'],
                    ['Trương Anh Khoa', '0903000103', '003300000103'],
                    ['Vũ Bảo Ngọc', '0903000104', '003300000104'],
                ],
                'contracts' => [
                    ['room' => 0, 'tenant' => 0, 'start' => '2026-01-10', 'end' => null, 'status' => 'Đang hiệu lực', 'rent' => 4200000, 'deposit' => 4200000],
                    ['room' => 3, 'tenant' => 2, 'start' => '2025-08-01', 'end' => '2026-07-31', 'status' => 'Đã kết thúc', 'rent' => 4800000, 'deposit' => 4800000],
                    ['room' => 4, 'tenant' => 3, 'start' => '2026-10-20', 'end' => null, 'status' => 'Nháp', 'rent' => 3600000, 'deposit' => 3600000],
                ],
            ],
        ];

        foreach ($propertyData as $data) {
            $property = Property::query()->firstOrCreate(
                ['name' => $data['name']],
                [
                    'address' => $data['address'],
                    'phone' => $data['phone'],
                    'description' => $data['description'],
                    'status' => 'active',
                ],
            );

            User::query()->updateOrCreate(
                ['username' => $data['manager']['username']],
                [
                    'name' => $data['manager']['name'],
                    'email' => $data['manager']['email'],
                    'password' => $password,
                    'role' => 'manager',
                    'property_id' => $property->id,
                ],
            );

            $rooms = [];
            foreach ($data['rooms'] as [$roomNumber, $price, $area, $status]) {
                $rooms[] = Room::query()->updateOrCreate(
                    ['property_id' => $property->id, 'room_number' => $roomNumber],
                    ['price' => $price, 'area' => $area, 'status' => $status],
                );
            }

            $tenants = [];
            foreach ($data['tenants'] as [$name, $phone, $cccd]) {
                $tenants[] = Tenant::query()->firstOrCreate(
                    ['property_id' => $property->id, 'cccd' => $cccd],
                    [
                        'full_name' => $name,
                        'phone' => $phone,
                        'address' => $data['address'],
                    ],
                );
            }

            foreach ($data['contracts'] as $index => $contractData) {
                $room = $rooms[$contractData['room']];
                $tenant = $tenants[$contractData['tenant']];
                $contract = Contract::query()->firstOrCreate(
                    ['room_id' => $room->id, 'tenant_id' => $tenant->id],
                    [
                        'property_id' => $property->id,
                        'start_date' => $contractData['start'],
                        'end_date' => $contractData['end'],
                        'monthly_rent' => $contractData['rent'],
                        'deposit' => $contractData['deposit'],
                        'status' => $contractData['status'],
                        'notes' => 'Hợp đồng dữ liệu mẫu để trình diễn; không chứa thông tin cá nhân thật.',
                    ],
                );

                if ($contractData['status'] !== 'Đang hiệu lực') {
                    continue;
                }

                foreach ([
                    ['month' => '2026-08', 'electricity' => 180000, 'water' => 60000, 'paid' => true],
                    ['month' => '2026-09', 'electricity' => 210000, 'water' => 75000, 'paid' => $index % 2 === 0],
                    ['month' => '2026-10', 'electricity' => 195000, 'water' => 68000, 'paid' => false],
                ] as $bill) {
                    $serviceFee = 50000;
                    $otherFee = $index % 2 === 0 ? 0 : 25000;
                    Invoice::query()->firstOrCreate(
                        ['contract_id' => $contract->id, 'month' => $bill['month']],
                        [
                            'rent_amount' => $contractData['rent'],
                            'electricity' => $bill['electricity'],
                            'water' => $bill['water'],
                            'service_fee' => $serviceFee,
                            'other_fee' => $otherFee,
                            'total' => $contractData['rent'] + $bill['electricity'] + $bill['water'] + $serviceFee + $otherFee,
                            'status' => $bill['paid'] ? 'Đã thanh toán' : 'Chưa thanh toán',
                            'due_date' => $bill['month'].'-10',
                            'paid_at' => $bill['paid'] ? $bill['month'].'-08 12:00:00' : null,
                            'notes' => 'Hóa đơn dữ liệu mẫu.',
                        ],
                    );
                }
            }
        }
    }
}
