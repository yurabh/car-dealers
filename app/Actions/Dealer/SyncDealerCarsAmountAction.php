<?php

namespace App\Actions\Dealer;

use App\Models\Car;
use App\Models\Dealer;

class SyncDealerCarsAmountAction
{
    public function handle(?int $dealerId): void
    {
        if ($dealerId === null) {
            return;
        }

        Dealer::whereKey($dealerId)->update([
            'cars_amount' => Car::where('dealer_id', $dealerId)->count(),
        ]);
    }
}
