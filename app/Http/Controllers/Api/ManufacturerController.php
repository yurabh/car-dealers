<?php

namespace App\Http\Controllers\Api;

use App\Actions\Manufacturer\CreateManufacturerAction;
use App\Actions\Manufacturer\DeleteManufacturerAction;
use App\Actions\Manufacturer\ListManufacturersAction;
use App\Actions\Manufacturer\UpdateManufacturerAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\IndexManufacturerRequest;
use App\Http\Requests\StoreManufacturerRequest;
use App\Http\Requests\UpdateManufacturerRequest;
use App\Http\Resources\ManufacturerResource;
use App\Models\Manufacturer;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ManufacturerController extends Controller
{
    public function index(IndexManufacturerRequest $request, ListManufacturersAction $action): AnonymousResourceCollection
    {
        return ManufacturerResource::collection($action->handle($request->validated()));
    }

    public function store(StoreManufacturerRequest $request, CreateManufacturerAction $action): ManufacturerResource
    {
        return new ManufacturerResource($action->handle($request->validated()));
    }

    public function show(Manufacturer $manufacturer): ManufacturerResource
    {
        return new ManufacturerResource($manufacturer->loadCount(['dealers', 'cars']));
    }

    public function update(UpdateManufacturerRequest $request, Manufacturer $manufacturer,
                           UpdateManufacturerAction  $action
    ): ManufacturerResource
    {
        return new ManufacturerResource($action->handle($manufacturer, $request->validated()));
    }

    public function destroy(Manufacturer $manufacturer, DeleteManufacturerAction $action): Response
    {
        $action->handle($manufacturer);

        return response()->noContent();
    }
}
