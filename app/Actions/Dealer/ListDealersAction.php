<?php

namespace App\Actions\Dealer;

use App\Models\Dealer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListDealersAction
{
    public function handle(array $filters): LengthAwarePaginator
    {
        return Dealer::query()
            ->with('manufacturer')
            ->filter($filters)
            ->orderBy($filters['sort'] ?? 'name', $filters['direction'] ?? 'asc')
            ->orderBy('id')
            ->paginate($filters['per_page'] ?? 15)
            ->withQueryString();
    }
}
