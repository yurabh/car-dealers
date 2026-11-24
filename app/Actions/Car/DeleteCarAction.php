<?php

namespace App\Actions\Car;

use App\Models\Car;

class DeleteCarAction
{
    public function handle(Car $car): void
    {
        $car->delete();
    }
}
