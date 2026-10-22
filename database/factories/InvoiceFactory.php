<?php

namespace Database\Factories;

use App\Models\Contract;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'contract_id' => Contract::factory(),
            'rent_amount' => '0.00',
            'month' => now()->format('Y-m'),
            'electricity' => '0.00',
            'water' => '0.00',
            'service_fee' => '0.00',
            'other_fee' => '0.00',
            'total' => '0.00',
            'status' => 'Chưa thanh toán',
        ];
    }
}
