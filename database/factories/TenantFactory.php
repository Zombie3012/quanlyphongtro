<?php

namespace Database\Factories;

use App\Models\Property;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Tenant>
 */
class TenantFactory extends Factory
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
            'full_name' => fake()->name(),
            'phone' => fake()->numerify('09########'),
            'cccd' => fake()->unique()->numerify('############'),
            'address' => fake()->address(),
        ];
    }
}
