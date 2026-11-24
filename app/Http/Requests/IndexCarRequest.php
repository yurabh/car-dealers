<?php

namespace App\Http\Requests;

use App\Enums\CarColor;
use App\Enums\CarFuel;
use App\Enums\CarSize;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexCarRequest extends FormRequest
{
    public const SORTABLE = ['id', 'model', 'size', 'color', 'fuel', 'created_at'];

    public function rules(): array
    {
        return [
            'manufacturer_id' => ['sometimes', 'array'],
            'manufacturer_id.*' => ['integer'],
            'dealer_id' => ['sometimes', 'array'],
            'dealer_id.*' => ['integer'],
            'size' => ['sometimes', 'array'],
            'size.*' => [Rule::enum(CarSize::class)],
            'color' => ['sometimes', 'array'],
            'color.*' => [Rule::enum(CarColor::class)],
            'fuel' => ['sometimes', 'array'],
            'fuel.*' => [Rule::enum(CarFuel::class)],
            'search' => ['sometimes', 'nullable', 'string', 'max:255'],
            'sort' => ['sometimes', Rule::in(self::SORTABLE)],
            'direction' => ['sometimes', Rule::in(['asc', 'desc'])],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }
}
