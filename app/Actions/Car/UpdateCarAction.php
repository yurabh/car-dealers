<?php

namespace App\Actions\Car;

use App\Models\Car;

class UpdateCarAction
{
    public function handle(Car $car, array $data): Car
    {
        $car->update($data);

        return $car->load(['manufacturer', 'dealer']);
    }
}
