<?php

namespace App\Actions\Car;

use App\Models\Car;

class CreateCarAction
{
    public function handle(array $data): Car
    {
        return Car::create($data)->load(['manufacturer', 'dealer']);
    }
}
