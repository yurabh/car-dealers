<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\Dealer;
use App\Models\Manufacturer;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $manufacturers = collect(['Toyota', 'BMW', 'Volkswagen', 'Ford', 'Tesla', 'Hyundai'])
            ->map(fn(string $name) => Manufacturer::factory()->create(['name' => $name]));

        $dealers = $manufacturers->flatMap(
            fn(Manufacturer $manufacturer) => Dealer::factory()
                ->count(3)
                ->for($manufacturer)
                ->create()
        );

        $dealers->each(function (Dealer $dealer) use ($manufacturers) {
            foreach (range(1, fake()->numberBetween(4, 10)) as $i) {
                Car::factory()->create([
                    'dealer_id' => $dealer->id,
                    'manufacturer_id' => $manufacturers->random()->id,
                ]);
            }
        });
    }
}
