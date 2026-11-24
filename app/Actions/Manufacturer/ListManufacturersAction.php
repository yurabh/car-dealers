<?php

namespace App\Actions\Manufacturer;

use App\Models\Manufacturer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

class ListManufacturersAction
{
    public function handle(array $filters): LengthAwarePaginator
    {
        return Manufacturer::query()
            ->withCount(['dealers', 'cars'])
            ->when(
                !empty($filters['search']),
                fn(Builder $q) => $q->where('name', 'like', '%' . $filters['search'] . '%')
            )
            ->orderBy('name')
            ->paginate($filters['per_page'] ?? 15)
            ->withQueryString();
    }
}
