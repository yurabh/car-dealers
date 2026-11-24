<?php

namespace App\Actions\FilterOptions;

use App\Enums\CarColor;
use App\Enums\CarFuel;
use App\Enums\CarSize;
use App\Models\Dealer;
use App\Models\Manufacturer;

class GetCarFilterOptionsAction
{
    public function handle(): array
    {
        return [
            'manufacturers' => Manufacturer::orderBy('name')->get(['id', 'name']),
            'dealers' => Dealer::orderBy('name')->get(['id', 'name']),
            'sizes' => array_column(CarSize::cases(), 'value'),
            'colors' => array_column(CarColor::cases(), 'value'),
            'fuels' => array_column(CarFuel::cases(), 'value'),
        ];
    }
}
