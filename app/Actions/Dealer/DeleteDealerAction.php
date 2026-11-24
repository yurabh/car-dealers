<?php

namespace App\Actions\Dealer;

use App\Models\Dealer;

class DeleteDealerAction
{
    public function handle(Dealer $dealer): void
    {
        $dealer->delete();
    }
}
