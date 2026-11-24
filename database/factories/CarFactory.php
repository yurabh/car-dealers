<?php

namespace Database\Factories;

use App\Enums\CarColor;
use App\Enums\CarFuel;
use App\Enums\CarSize;
use App\Models\Car;
use App\Models\Dealer;
use App\Models\Manufacturer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Car>
 */
class CarFactory extends Factory
{
    protected $model = Car::class;

    public function definition(): array
    {
        return [
            'manufacturer_id' => Manufacturer::factory(),
            'dealer_id' => Dealer::factory(),
            'model' => ucfirst(fake()->word()).' '.fake()->bothify('##?'),
            'size' => fake()->randomElement(CarSize::cases()),
            'color' => fake()->randomElement(CarColor::cases()),
            'fuel' => fake()->randomElement(CarFuel::cases()),
        ];
    }
}
