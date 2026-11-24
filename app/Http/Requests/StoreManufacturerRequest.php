<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreManufacturerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => [
                'required', 'string', 'max:255',
                Rule::unique('manufacturers', 'name')->ignore($this->route('manufacturer')),
            ],
        ];
    }
}
