<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Dealer extends Model
{
    use HasFactory;

    protected $fillable = [
        'manufacturer_id',
        'name',
        'city',
        'country',
        'employees_count',
    ];

    protected $casts = [
        'cars_amount' => 'integer',
        'employees_count' => 'integer',
    ];

    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        foreach (['manufacturer_id', 'country', 'city'] as $field) {
            $query->when(
                !empty($filters[$field]),
                fn(Builder $q) => $q->whereIn($field, (array)$filters[$field])
            );
        }

        $ranges = [
            'cars_amount' => ['cars_amount_min', 'cars_amount_max'],
            'employees_count' => ['employees_min', 'employees_max'],
        ];

        foreach ($ranges as $column => [$min, $max]) {
            $query
                ->when(isset($filters[$min]), fn(Builder $q) => $q->where($column, '>=', $filters[$min]))
                ->when(isset($filters[$max]), fn(Builder $q) => $q->where($column, '<=', $filters[$max]));
        }

        return $query->when(
            !empty($filters['search']),
            fn(Builder $q) => $q->where('name', 'like', '%' . $filters['search'] . '%')
        );
    }
}
