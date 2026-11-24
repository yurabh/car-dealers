<?php

namespace App\Http\Controllers\Api;

use App\Actions\Car\CreateCarAction;
use App\Actions\Car\DeleteCarAction;
use App\Actions\Car\ListCarsAction;
use App\Actions\Car\UpdateCarAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\IndexCarRequest;
use App\Http\Requests\StoreCarRequest;
use App\Http\Requests\UpdateCarRequest;
use App\Http\Resources\CarResource;
use App\Models\Car;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CarController extends Controller
{
    public function index(IndexCarRequest $request, ListCarsAction $action): AnonymousResourceCollection
    {
        return CarResource::collection($action->handle($request->validated()));
    }

    public function store(StoreCarRequest $request, CreateCarAction $action): CarResource
    {
        return new CarResource($action->handle($request->validated()));
    }

    public function show(Car $car): CarResource
    {
        return new CarResource($car->load(['manufacturer', 'dealer']));
    }

    public function update(UpdateCarRequest $request, Car $car, UpdateCarAction $action): CarResource
    {
        return new CarResource($action->handle($car, $request->validated()));
    }

    public function destroy(Car $car, DeleteCarAction $action): Response
    {
        $action->handle($car);

        return response()->noContent();
    }
}
