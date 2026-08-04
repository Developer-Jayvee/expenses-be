<?php

namespace Database\Factories;

use App\Enums\BillStatusEnum;
use App\Models\BillsModel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BillsModel>
 */
class BillsModelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(), 
            'amount' => fake()->randomFloat(2,10,10000),
            'billing_date' => fake()->date(),
            'end_date' => fake()->date(),
            'status' => BillStatusEnum::ACTIVE
        ];
    }
}
