<?php

namespace App\Http\Controllers\Api;

use App\Actions\Dealer\CreateDealerAction;
use App\Actions\Dealer\DeleteDealerAction;
use App\Actions\Dealer\ListDealersAction;
use App\Actions\Dealer\UpdateDealerAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\IndexDealerRequest;
use App\Http\Requests\StoreDealerRequest;
use App\Http\Requests\UpdateDealerRequest;
use App\Http\Resources\DealerResource;
use App\Models\Dealer;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class DealerController extends Controller
{
    public function index(IndexDealerRequest $request, ListDealersAction $action): AnonymousResourceCollection
    {
        return DealerResource::collection($action->handle($request->validated()));
    }

    public function store(StoreDealerRequest $request, CreateDealerAction $action): DealerResource
    {
        return new DealerResource($action->handle($request->validated()));
    }

    public function show(Dealer $dealer): DealerResource
    {
        return new DealerResource($dealer->load('manufacturer'));
    }

    public function update(UpdateDealerRequest $request, Dealer $dealer, UpdateDealerAction $action): DealerResource
    {
        return new DealerResource($action->handle($dealer, $request->validated()));
    }

    public function destroy(Dealer $dealer, DeleteDealerAction $action): Response
    {
        $action->handle($dealer);

        return response()->noContent();
    }
}
