<?php

namespace App\Models;

use App\Enums\CarColor;
use App\Enums\CarFuel;
use App\Enums\CarSize;
use App\Observers\CarObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy(CarObserver::class)]
class Car extends Model
{
    use HasFactory;

    protected $fillable = [
        'manufacturer_id',
        'dealer_id',
        'model',
        'size',
        'color',
        'fuel',
    ];

    protected $casts = [
        'size' => CarSize::class,
        'color' => CarColor::class,
        'fuel' => CarFuel::class,
    ];

    public function manufacturer(): BelongsTo
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function dealer(): BelongsTo
    {
        return $this->belongsTo(Dealer::class);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        foreach (['manufacturer_id', 'dealer_id', 'size', 'color', 'fuel'] as $field) {
            $query->when(
                !empty($filters[$field]),
                fn(Builder $q) => $q->whereIn($field, (array)$filters[$field])
            );
        }

        return $query->when(
            !empty($filters['search']),
            fn(Builder $q) => $q->where('model', 'like', '%' . $filters['search'] . '%')
        );
    }
}
