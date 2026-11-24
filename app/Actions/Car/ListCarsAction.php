<?php

namespace App\Actions\Car;

use App\Models\Car;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListCarsAction
{
    public function handle(array $filters): LengthAwarePaginator
    {
        return Car::query()
            ->with(['manufacturer', 'dealer'])
            ->filter($filters)
            ->orderBy($filters['sort'] ?? 'id', $filters['direction'] ?? 'desc')
            ->orderBy('id')
            ->paginate($filters['per_page'] ?? 15)
            ->withQueryString();
    }
}
