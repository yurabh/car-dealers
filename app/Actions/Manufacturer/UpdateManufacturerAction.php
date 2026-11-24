<?php

namespace App\Actions\Manufacturer;

use App\Models\Manufacturer;

class UpdateManufacturerAction
{
    public function handle(Manufacturer $manufacturer, array $data): Manufacturer
    {
        $manufacturer->update($data);

        return $manufacturer->loadCount(['dealers', 'cars']);
    }
}
