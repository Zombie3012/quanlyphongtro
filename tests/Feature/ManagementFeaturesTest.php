<?php

namespace Tests\Feature;

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Property;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagementFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_uses_username_and_logout_clears_the_session(): void
    {
        $user = User::factory()->admin()->create();

        $this->post(route('login.submit'), [
            'username' => $user->username,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_admin_area_and_property_management_are_admin_only(): void
    {
        $property = Property::factory()->create();
        $admin = User::factory()->admin()->create();
        $manager = User::factory()->managerFor($property->id)->create();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Khu vực quản trị');

        $this->actingAs($admin)
            ->get(route('properties.index'))
            ->assertOk();

        $this->actingAs($manager)
            ->get(route('admin.dashboard'))
            ->assertForbidden();

        $this->actingAs($manager)
            ->get(route('properties.index'))
            ->assertForbidden();
    }

    public function test_property_admin_resource_is_under_admin_prefix(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('properties.index'))
            ->assertOk()
            ->assertHeader('Content-Type', 'text/html; charset=UTF-8');

        $this->assertStringContainsString('/admin/properties', route('properties.index', absolute: false));
    }

    public function test_admin_property_search_paginates_and_preserves_search_term(): void
    {
        $admin = User::factory()->admin()->create();

        for ($index = 1; $index <= 11; $index++) {
            Property::factory()->create(['name' => sprintf('Needle Lodge %02d', $index)]);
        }

        $response = $this->actingAs($admin)
            ->get(route('properties.index', ['keyword' => 'Needle']));

        $response->assertOk()
            ->assertSee('Needle Lodge 11')
            ->assertDontSee('Needle Lodge 01')
            ->assertSee('keyword=Needle&amp;page=2', false);
    }

    public function test_room_search_paginates_and_preserves_search_term(): void
    {
        $property = Property::factory()->create();
        $manager = User::factory()->managerFor($property->id)->create();

        for ($index = 1; $index <= 11; $index++) {
            Room::factory()->for($property)->create([
                'room_number' => sprintf('Needle-%02d', $index),
            ]);
        }

        $response = $this->actingAs($manager)
            ->get(route('rooms.index', ['property' => $property, 'search' => 'Needle']));

        $response->assertOk()
            ->assertSee('Needle-01')
            ->assertDontSee('Needle-11')
            ->assertSee('search=Needle&amp;page=2', false);
    }

    public function test_tenant_contract_and_invoice_lists_paginate_and_keep_search(): void
    {
        $property = Property::factory()->create();
        $manager = User::factory()->managerFor($property->id)->create();

        for ($index = 1; $index <= 11; $index++) {
            $room = Room::factory()->for($property)->create([
                'room_number' => sprintf('SearchRoom-%02d', $index),
            ]);
            $tenant = Tenant::factory()->for($property)->create([
                'full_name' => sprintf('Search Tenant %02d', $index),
            ]);
            $contract = Contract::factory()
                ->for($property)
                ->for($room, 'room')
                ->for($tenant, 'tenant')
                ->create([
                    'monthly_rent' => '1000000.00',
                    'start_date' => '2026-01-01',
                ]);
            Invoice::factory()->for($contract)->create([
                'month' => sprintf('2026-%02d', $index),
            ]);
        }

        $this->actingAs($manager)
            ->get(route('tenants.index', ['property' => $property, 'search' => 'Search Tenant']))
            ->assertOk()
            ->assertSee('Search Tenant 01')
            ->assertDontSee('Search Tenant 11')
            ->assertSee('search=Search%20Tenant&amp;page=2', false);

        $this->actingAs($manager)
            ->get(route('contracts.index', ['property' => $property, 'search' => 'SearchRoom']))
            ->assertOk()
            ->assertSee('SearchRoom-11')
            ->assertDontSee('SearchRoom-01')
            ->assertSee('search=SearchRoom&amp;page=2', false);

        $this->actingAs($manager)
            ->get(route('invoices.index', ['property' => $property, 'search' => 'SearchRoom']))
            ->assertOk()
            ->assertSee('SearchRoom-11')
            ->assertDontSee('SearchRoom-01')
            ->assertSee('search=SearchRoom&amp;page=2', false);
    }

    public function test_user_update_accepts_unchanged_username(): void
    {
        [$admin, $property, $manager] = $this->userUpdateContext();

        $this->actingAs($admin)
            ->put(route('users.update', $manager), $this->userUpdateInput($manager, $property))
            ->assertRedirect(route('users.index'))
            ->assertSessionHas('success', 'Cập nhật tài khoản thành công!');

        $this->assertDatabaseHas('users', ['id' => $manager->id, 'username' => $manager->username]);
    }

    public function test_user_update_accepts_a_new_unique_username(): void
    {
        [$admin, $property, $manager] = $this->userUpdateContext();

        $this->actingAs($admin)
            ->put(route('users.update', $manager), $this->userUpdateInput($manager, $property, [
                'username' => 'manager-renamed',
            ]))
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', ['id' => $manager->id, 'username' => 'manager-renamed']);
    }

    public function test_user_update_rejects_a_username_already_owned_by_another_account(): void
    {
        [$admin, $property, $manager] = $this->userUpdateContext();
        $otherManager = User::factory()->managerFor($property->id)->create();

        $this->actingAs($admin)
            ->put(route('users.update', $manager), $this->userUpdateInput($manager, $property, [
                'username' => $otherManager->username,
            ]))
            ->assertSessionHasErrors('username');

        $this->assertDatabaseHas('users', ['id' => $manager->id, 'username' => $manager->username]);
    }

    public function test_admin_account_can_be_updated_without_a_property_assignment(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->put(route('users.update', $admin), [
                'username' => $admin->username,
                'name' => 'Admin Updated',
                'email' => $admin->email,
            ])
            ->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'id' => $admin->id,
            'name' => 'Admin Updated',
            'property_id' => null,
        ]);
    }

    public function test_room_validation_errors_and_success_flash_are_visible(): void
    {
        $property = Property::factory()->create();
        $manager = User::factory()->managerFor($property->id)->create(['username' => 'manager_test']);

        $this->actingAs($manager)
            ->post(route('rooms.store', $property), [
                'room_number' => 'A-102',
                'price' => '-1',
                'area' => '20',
                'status' => 'Trống',
            ])
            ->assertSessionHasErrors('price');

        $this->assertDatabaseMissing('rooms', ['property_id' => $property->id, 'room_number' => 'A-102']);

        $this->actingAs($manager)
            ->post(route('rooms.store', $property), [
                'room_number' => 'A-102',
                'price' => '2500000',
                'area' => '20',
                'status' => 'Trống',
            ])
            ->assertRedirect(route('rooms.index', $property))
            ->assertSessionHas('success', 'Thêm phòng thành công!');

        $this->assertDatabaseHas('rooms', ['property_id' => $property->id, 'room_number' => 'A-102']);
    }

    public function test_property_validation_flash_and_manager_assignment_work(): void
    {
        $admin = User::factory()->admin()->create();
        $manager = User::factory()->create(['username' => 'available_manager']);

        $this->actingAs($admin)
            ->post(route('properties.store'), [
                'name' => 'Validated Property',
                'phone' => '0900000000',
                'status' => 'active',
            ])
            ->assertSessionHasErrors('address');

        $this->assertDatabaseMissing('properties', ['name' => 'Validated Property']);

        $this->actingAs($admin)
            ->post(route('properties.store'), [
                'name' => 'Validated Property',
                'address' => '1 Test Street',
                'phone' => '0900000000',
                'status' => 'active',
                'manager_id' => $manager->id,
            ])
            ->assertRedirect(route('properties.index'))
            ->assertSessionHas('success', 'Thêm khu trọ thành công!');

        $this->assertDatabaseHas('properties', ['name' => 'Validated Property']);
        $this->assertDatabaseHas('users', ['id' => $manager->id, 'property_id' => Property::where('name', 'Validated Property')->value('id')]);
    }

    public function test_tenant_validation_and_flash_work(): void
    {
        $property = Property::factory()->create();
        $manager = User::factory()->managerFor($property->id)->create();

        $this->actingAs($manager)
            ->post(route('tenants.store', $property), [
                'full_name' => '',
                'phone' => '0900000000',
                'cccd' => '123456789012',
                'address' => 'Test address',
            ])
            ->assertSessionHasErrors('full_name');

        $this->actingAs($manager)
            ->post(route('tenants.store', $property), [
                'full_name' => 'Tenant Test',
                'phone' => '0900000000',
                'cccd' => '123456789012',
                'address' => 'Test address',
            ])
            ->assertRedirect(route('tenants.index', $property))
            ->assertSessionHas('success', 'Thêm người thuê thành công!');

        $this->assertDatabaseHas('tenants', [
            'property_id' => $property->id,
            'full_name' => 'Tenant Test',
            'cccd' => '123456789012',
        ]);
    }

    public function test_contract_validation_and_flash_work_and_room_status_updates(): void
    {
        $property = Property::factory()->create();
        $manager = User::factory()->managerFor($property->id)->create();
        $room = Room::factory()->for($property)->create(['status' => 'Trống']);
        $tenant = Tenant::factory()->for($property)->create();

        $input = [
            'room_id' => $room->id,
            'tenant_id' => $tenant->id,
            'start_date' => '2026-10-01',
            'monthly_rent' => '1250000.00',
            'deposit' => '1250000.00',
            'status' => 'Đang hiệu lực',
        ];

        $this->actingAs($manager)
            ->post(route('contracts.store', $property), array_merge($input, ['monthly_rent' => '-1']))
            ->assertSessionHasErrors('monthly_rent');

        $this->assertDatabaseMissing('contracts', ['room_id' => $room->id]);

        $this->actingAs($manager)
            ->post(route('contracts.store', $property), $input)
            ->assertRedirect(route('contracts.index', $property))
            ->assertSessionHas('success', 'Tạo hợp đồng thành công!');

        $this->assertDatabaseHas('contracts', [
            'property_id' => $property->id,
            'room_id' => $room->id,
            'tenant_id' => $tenant->id,
            'monthly_rent' => '1250000.00',
        ]);
        $this->assertDatabaseHas('rooms', ['id' => $room->id, 'status' => 'Đang thuê']);
    }

    public function test_demo_seeder_creates_related_sample_data_and_is_repeatable(): void
    {
        $this->seed(DemoDataSeeder::class);
        $this->seed(DemoDataSeeder::class);

        $this->assertDatabaseCount('properties', 1);
        $this->assertDatabaseCount('rooms', 1);
        $this->assertDatabaseCount('tenants', 1);
        $this->assertDatabaseCount('contracts', 1);
        $this->assertDatabaseCount('invoices', 1);
        $this->assertDatabaseHas('users', ['username' => 'demo_admin', 'role' => 'admin']);
        $this->assertDatabaseHas('users', ['username' => 'demo_manager', 'role' => 'manager']);
    }

    public function test_manager_dashboard_and_tables_have_mobile_friendly_layout_markers(): void
    {
        $property = Property::factory()->create();
        $manager = User::factory()->managerFor($property->id)->create();

        $this->actingAs($manager)
            ->get(route('rooms.index', $property))
            ->assertOk()
            ->assertSee('name="viewport"', false)
            ->assertSee('table-responsive', false)
            ->assertSee('aria-label="Mở hoặc đóng menu điều hướng"', false);
    }

    /** @return array{User, Property, User} */
    private function userUpdateContext(): array
    {
        $property = Property::factory()->create();
        $admin = User::factory()->admin()->create();
        $manager = User::factory()->managerFor($property->id)->create(['username' => 'manager_test']);

        return [$admin, $property, $manager];
    }

    /** @return array<string, mixed> */
    private function userUpdateInput(User $user, Property $property, array $overrides = []): array
    {
        return array_merge([
            'username' => $user->username,
            'name' => $user->name,
            'email' => $user->email,
            'property_id' => $property->id,
            'password' => '',
            'password_confirmation' => '',
        ], $overrides);
    }
}
