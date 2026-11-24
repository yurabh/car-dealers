<?php

namespace App\Actions\FilterOptions;

use App\Models\Dealer;
use App\Models\Manufacturer;

class GetDealerFilterOptionsAction
{
    public function handle(): array
    {
        return [
            'manufacturers' => Manufacturer::orderBy('name')->get(['id', 'name']),
            'countries' => Dealer::distinct()->orderBy('country')->pluck('country'),
            'cities' => Dealer::select('city', 'country')->distinct()->orderBy('city')->get(),
            'cars_amount' => [
                'min' => (int)Dealer::min('cars_amount'),
                'max' => (int)Dealer::max('cars_amount'),
            ],
            'employees_count' => [
                'min' => (int)Dealer::min('employees_count'),
                'max' => (int)Dealer::max('employees_count'),
            ],
        ];
    }
}
