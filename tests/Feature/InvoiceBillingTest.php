<?php

namespace Tests\Feature;

use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Property;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class InvoiceBillingTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_uses_contract_rent_and_all_fee_components_instead_of_client_total(): void
    {
        $property = Property::factory()->create();
        $contract = $this->makeContract($property, '1250.00');

        $response = $this->actingAs(User::factory()->admin()->create())
            ->post(route('invoices.store', $property), $this->invoiceInput($contract, [
                'electricity' => '11.25',
                'water' => '22.50',
                'service_fee' => '30.00',
                'other_fee' => '6.25',
                'total' => '1.00',
            ]));

        $response->assertRedirect(route('invoices.index', $property));
        $response->assertSessionHas('success', 'Tạo hóa đơn thành công!');
        $this->assertDatabaseHas('invoices', [
            'contract_id' => $contract->id,
            'rent_amount' => '1250.00',
            'electricity' => '11.25',
            'water' => '22.50',
            'service_fee' => '30.00',
            'other_fee' => '6.25',
            'total' => '1320.00',
        ]);
    }

    public function test_zero_components_produce_zero_total(): void
    {
        $property = Property::factory()->create();
        $contract = $this->makeContract($property, '0.00');

        $this->actingAs(User::factory()->admin()->create())
            ->post(route('invoices.store', $property), $this->invoiceInput($contract))
            ->assertRedirect(route('invoices.index', $property));

        $this->assertDatabaseHas('invoices', ['contract_id' => $contract->id, 'total' => '0.00']);
    }

    public function test_rent_without_consumption_fees_is_included_in_total(): void
    {
        $property = Property::factory()->create();
        $contract = $this->makeContract($property, '975.40');

        $this->actingAs(User::factory()->admin()->create())
            ->post(route('invoices.store', $property), $this->invoiceInput($contract))
            ->assertRedirect(route('invoices.index', $property));

        $this->assertDatabaseHas('invoices', ['contract_id' => $contract->id, 'rent_amount' => '975.40', 'total' => '975.40']);
    }

    #[DataProvider('invalidMoneyValues')]
    public function test_store_rejects_invalid_money_values(string $field, ?string $value): void
    {
        $property = Property::factory()->create();
        $contract = $this->makeContract($property, '100.00');
        $input = $this->invoiceInput($contract);

        if ($value === null) {
            unset($input[$field]);
        } else {
            $input[$field] = $value;
        }

        $this->actingAs(User::factory()->admin()->create())
            ->from(route('invoices.create', $property))
            ->post(route('invoices.store', $property), $input)
            ->assertRedirect(route('invoices.create', $property))
            ->assertSessionHasErrors($field);

        $this->assertDatabaseCount('invoices', 0);
    }

    public static function invalidMoneyValues(): array
    {
        return [
            'negative amount' => ['electricity', '-0.01'],
            'nonnumeric amount' => ['water', 'abc'],
            'too many decimals' => ['service_fee', '1.234'],
            'missing other fee' => ['other_fee', null],
        ];
    }

    public function test_store_rejects_contract_from_another_property(): void
    {
        $property = Property::factory()->create();
        $otherProperty = Property::factory()->create();
        $foreignContract = $this->makeContract($otherProperty, '400.00');

        $this->actingAs(User::factory()->admin()->create())
            ->post(route('invoices.store', $property), $this->invoiceInput($foreignContract))
            ->assertNotFound();

        $this->assertDatabaseCount('invoices', 0);
    }

    public function test_store_rejects_duplicate_contract_month(): void
    {
        $property = Property::factory()->create();
        $contract = $this->makeContract($property, '400.00');
        $input = $this->invoiceInput($contract);
        $user = User::factory()->admin()->create();

        $this->actingAs($user)->post(route('invoices.store', $property), $input)->assertRedirect();
        $this->from(route('invoices.create', $property))
            ->post(route('invoices.store', $property), $input)
            ->assertRedirect(route('invoices.create', $property))
            ->assertSessionHasErrors('month');

        $this->assertDatabaseCount('invoices', 1);
    }

    public function test_update_recalculates_from_snapshot_and_keeps_contract_and_month_immutable(): void
    {
        $property = Property::factory()->create();
        $contract = $this->makeContract($property, '500.00');
        $invoice = $contract->invoices()->create([
            'rent_amount' => '500.00',
            'month' => '2026-01',
            'electricity' => '0.00',
            'water' => '0.00',
            'service_fee' => '0.00',
            'other_fee' => '0.00',
            'total' => '500.00',
            'status' => 'Chưa thanh toán',
        ]);
        $contract->update(['monthly_rent' => '900.00']);
        $foreignContract = $this->makeContract(Property::factory()->create(), '800.00');

        $response = $this->actingAs(User::factory()->admin()->create())
            ->put(route('invoices.update', [$property, $invoice]), [
                'electricity' => '1.25',
                'water' => '2.50',
                'service_fee' => '3.75',
                'other_fee' => '4.50',
                'total' => '0.01',
                'rent_amount' => '999.99',
                'contract_id' => $foreignContract->id,
                'month' => '2025-01',
                'status' => 'Chưa thanh toán',
            ]);

        $response->assertRedirect(route('invoices.index', $property));
        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'contract_id' => $contract->id,
            'month' => '2026-01',
            'rent_amount' => '500.00',
            'total' => '512.00',
        ]);
    }

    public function test_manager_cannot_create_invoice_for_another_property(): void
    {
        $property = Property::factory()->create();
        $otherProperty = Property::factory()->create();
        $contract = $this->makeContract($otherProperty, '400.00');
        $manager = User::factory()->managerFor($property->id)->create();

        $this->actingAs($manager)
            ->post(route('invoices.store', $otherProperty), $this->invoiceInput($contract))
            ->assertForbidden();

        $this->assertDatabaseCount('invoices', 0);
    }

    public function test_manager_can_create_invoice_in_assigned_property(): void
    {
        $property = Property::factory()->create();
        $contract = $this->makeContract($property, '600.00');
        $manager = User::factory()->managerFor($property->id)->create();

        $this->actingAs($manager)
            ->post(route('invoices.store', $property), $this->invoiceInput($contract, [
                'electricity' => '10.00',
                'other_fee' => '5.00',
            ]))
            ->assertRedirect(route('invoices.index', $property));

        $this->assertDatabaseHas('invoices', [
            'contract_id' => $contract->id,
            'rent_amount' => '600.00',
            'total' => '615.00',
        ]);
    }

    public function test_paid_invoice_cannot_be_updated_or_deleted(): void
    {
        $property = Property::factory()->create();
        $contract = $this->makeContract($property, '500.00');
        $invoice = $contract->invoices()->create([
            'rent_amount' => '500.00',
            'month' => '2026-01',
            'electricity' => '0.00',
            'water' => '0.00',
            'service_fee' => '0.00',
            'other_fee' => '0.00',
            'total' => '500.00',
            'status' => 'Đã thanh toán',
        ]);
        $user = User::factory()->admin()->create();

        $this->actingAs($user)
            ->put(route('invoices.update', [$property, $invoice]), $this->updateInput())
            ->assertSessionHasErrors('invoice');
        $this->delete(route('invoices.destroy', [$property, $invoice]))
            ->assertSessionHasErrors('invoice');

        $this->assertDatabaseHas('invoices', ['id' => $invoice->id, 'total' => '500.00', 'status' => 'Đã thanh toán']);
    }

    public function test_legacy_invoice_without_snapshot_is_not_recalculated(): void
    {
        $property = Property::factory()->create();
        $contract = $this->makeContract($property, '900.00');
        $invoice = $contract->invoices()->create([
            'rent_amount' => null,
            'month' => '2026-01',
            'electricity' => '10.00',
            'water' => '0.00',
            'service_fee' => '0.00',
            'other_fee' => '0.00',
            'total' => '10.00',
            'status' => 'Chưa thanh toán',
        ]);

        $this->actingAs(User::factory()->admin()->create())
            ->put(route('invoices.update', [$property, $invoice]), $this->updateInput())
            ->assertSessionHasErrors('invoice');

        $this->delete(route('invoices.destroy', [$property, $invoice]))
            ->assertSessionHasErrors('invoice');

        $this->assertDatabaseHas('invoices', ['id' => $invoice->id, 'rent_amount' => null, 'total' => '10.00']);
    }

    public function test_list_and_detail_render_snapshot_and_every_total_component(): void
    {
        $property = Property::factory()->create();
        $contract = $this->makeContract($property, '500.00');
        $invoice = $contract->invoices()->create([
            'rent_amount' => '500.00',
            'month' => '2026-01',
            'electricity' => '10.00',
            'water' => '20.00',
            'service_fee' => '30.00',
            'other_fee' => '40.00',
            'total' => '600.00',
            'status' => 'Chưa thanh toán',
        ]);
        $user = User::factory()->admin()->create();

        $this->actingAs($user)
            ->get(route('invoices.index', $property))
            ->assertOk()
            ->assertSee('500,00 VNĐ')
            ->assertSee('40,00 VNĐ');
        $this->get(route('invoices.show', [$property, $invoice]))
            ->assertOk()
            ->assertSee('Tiền thuê snapshot')
            ->assertSee('600,00 VNĐ');
    }

    public function test_create_and_edit_forms_render_the_correct_money_fields(): void
    {
        $property = Property::factory()->create();
        $contract = $this->makeContract($property, '700.00');
        $invoice = $contract->invoices()->create([
            'rent_amount' => '700.00',
            'month' => '2026-01',
            'electricity' => '1.00',
            'water' => '2.00',
            'service_fee' => '3.00',
            'other_fee' => '4.00',
            'total' => '710.00',
            'status' => 'Chưa thanh toán',
        ]);
        $user = User::factory()->admin()->create();

        $this->actingAs($user)
            ->get(route('invoices.create', $property))
            ->assertOk()
            ->assertSee('name="other_fee"', false)
            ->assertDontSee('name="total"', false)
            ->assertSee('rent-preview', false);

        $this->get(route('invoices.edit', [$property, $invoice]))
            ->assertOk()
            ->assertSee('name="other_fee"', false)
            ->assertDontSee('name="total"', false)
            ->assertSee('700,00 VNĐ');
    }

    public function test_admin_can_log_in_create_update_and_view_invoice_over_http(): void
    {
        $property = Property::factory()->create();
        $contract = $this->makeContract($property, '350.00');
        $admin = User::factory()->admin()->create();

        $this->post(route('login.submit'), [
            'username' => $admin->username,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($admin);

        $this->post(route('invoices.store', $property), $this->invoiceInput($contract, [
            'electricity' => '1.10',
            'water' => '2.20',
            'service_fee' => '3.30',
            'other_fee' => '4.40',
        ]))->assertRedirect(route('invoices.index', $property))
            ->assertSessionHas('success');

        $invoice = Invoice::query()->where('contract_id', $contract->id)->firstOrFail();
        $this->get(route('invoices.show', [$property, $invoice]))
            ->assertOk()
            ->assertSee('361,00 VNĐ');

        $this->put(route('invoices.update', [$property, $invoice]), $this->updateInput([
            'electricity' => '10.00',
            'water' => '20.00',
            'service_fee' => '30.00',
            'other_fee' => '40.00',
        ]))->assertRedirect(route('invoices.index', $property))
            ->assertSessionHas('success');

        $this->get(route('invoices.show', [$property, $invoice]))
            ->assertOk()
            ->assertSee('450,00 VNĐ');
    }

    private function makeContract(Property $property, string $monthlyRent): Contract
    {
        $room = Room::factory()->for($property)->create();
        $tenant = Tenant::factory()->for($property)->create();

        return Contract::factory()
            ->for($property)
            ->for($room)
            ->for($tenant)
            ->create(['monthly_rent' => $monthlyRent]);
    }

    /** @return array<string, mixed> */
    private function invoiceInput(Contract $contract, array $overrides = []): array
    {
        return array_merge([
            'contract_id' => $contract->id,
            'month' => '2026-01',
            'electricity' => '0.00',
            'water' => '0.00',
            'service_fee' => '0.00',
            'other_fee' => '0.00',
            'total' => '0.00',
            'status' => 'Chưa thanh toán',
        ], $overrides);
    }

    /** @return array<string, mixed> */
    private function updateInput(array $overrides = []): array
    {
        return array_merge([
            'electricity' => '1.00',
            'water' => '2.00',
            'service_fee' => '3.00',
            'other_fee' => '4.00',
            'total' => '0.01',
            'status' => 'Chưa thanh toán',
        ], $overrides);
    }
}
