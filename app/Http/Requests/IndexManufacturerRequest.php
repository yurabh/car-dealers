<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IndexManufacturerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'search' => ['sometimes', 'nullable', 'string', 'max:255'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ];
    }
}
