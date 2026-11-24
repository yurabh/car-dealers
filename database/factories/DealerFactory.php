<?php

namespace Database\Factories;

use App\Models\Dealer;
use App\Models\Manufacturer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Dealer>
 */
class DealerFactory extends Factory
{
    protected $model = Dealer::class;

    public function definition(): array
    {
        return [
            'manufacturer_id' => Manufacturer::factory(),
            'name' => fake()->company().' Motors',
            'city' => fake()->city(),
            'country' => fake()->country(),
            'employees_count' => fake()->numberBetween(3, 120),
        ];
    }
}
