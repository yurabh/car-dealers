<?php

namespace App\Observers;

use App\Actions\Dealer\SyncDealerCarsAmountAction;
use App\Models\Car;

readonly class CarObserver
{
    public function __construct(
        private SyncDealerCarsAmountAction $syncDealerCarsAmount,
    )
    {
    }

    public function created(Car $car): void
    {
        $this->syncDealerCarsAmount->handle($car->dealer_id);
    }

    public function updated(Car $car): void
    {
        if ($car->wasChanged('dealer_id')) {
            $this->syncDealerCarsAmount->handle($car->getOriginal('dealer_id'));
            $this->syncDealerCarsAmount->handle($car->dealer_id);
        }
    }

    public function deleted(Car $car): void
    {
        $this->syncDealerCarsAmount->handle($car->dealer_id);
    }
}
