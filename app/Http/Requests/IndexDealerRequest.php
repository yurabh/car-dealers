<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexDealerRequest extends FormRequest
{
    public const SORTABLE = ['id', 'name', 'city', 'country', 'cars_amount', 'employees_count', 'created_at'];

    public function rules(): array
    {
        return [
            'manufacturer_id' => ['sometimes', 'array'],
            'manufacturer_id.*' => ['integer'],
            'country' => ['sometimes', 'array'],
            'country.*' => ['string'],
            'city' => ['sometimes', 'array'],
            'city.*' => ['string'],
            'cars_amount_min' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'cars_amount_max' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'employees_min' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'employees_max' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'search' => ['sometimes', 'nullable', 'string', 'max:255'],
            'sort' => ['sometimes', Rule::in(self::SORTABLE)],
            'direction' => ['sometimes', Rule::in(['asc', 'desc'])],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }
}
