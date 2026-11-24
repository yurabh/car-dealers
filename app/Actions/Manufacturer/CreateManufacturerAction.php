<?php

namespace App\Actions\Manufacturer;

use App\Models\Manufacturer;

class CreateManufacturerAction
{
    public function handle(array $data): Manufacturer
    {
        return Manufacturer::create($data)->loadCount(['dealers', 'cars']);
    }
}
