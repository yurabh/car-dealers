<?php

namespace App\Actions\Dealer;

use App\Models\Dealer;

class CreateDealerAction
{
    public function handle(array $data): Dealer
    {
        return Dealer::create($data)->refresh()->load('manufacturer');
    }
}
