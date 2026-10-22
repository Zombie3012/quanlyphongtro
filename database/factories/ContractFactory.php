<?php

namespace Database\Factories;

use App\Models\Contract;
use App\Models\Property;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contract>
 */
class ContractFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'property_id' => Property::factory(),
            'room_id' => Room::factory(),
            'tenant_id' => Tenant::factory(),
            'start_date' => now()->toDateString(),
            'end_date' => null,
            'monthly_rent' => '0.00',
            'deposit' => '0.00',
            'status' => 'Đang hiệu lực',
            'notes' => null,
        ];
    }
}
