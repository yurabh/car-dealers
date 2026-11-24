<?php

namespace App\Actions\Manufacturer;

use App\Actions\Dealer\SyncDealerCarsAmountAction;
use App\Models\Car;
use App\Models\Manufacturer;
use Illuminate\Support\Facades\DB;

readonly class DeleteManufacturerAction
{
    public function __construct(
        private SyncDealerCarsAmountAction $syncDealerCarsAmount,
    )
    {
    }

    public function handle(Manufacturer $manufacturer): void
    {
        DB::transaction(function () use ($manufacturer) {
            $affectedDealerIds = Car::where('manufacturer_id', $manufacturer->id)
                ->distinct()
                ->pluck('dealer_id');

            $manufacturer->delete();

            $affectedDealerIds->each(fn(int $dealerId) => $this->syncDealerCarsAmount->handle($dealerId));
        });
    }
}
