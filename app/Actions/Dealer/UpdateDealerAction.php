<?php

namespace App\Actions\Dealer;

use App\Models\Dealer;

class UpdateDealerAction
{
    public function handle(Dealer $dealer, array $data): Dealer
    {
        $dealer->update($data);

        return $dealer->load('manufacturer');
    }
}
