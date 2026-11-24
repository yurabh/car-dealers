<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDealerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'manufacturer_id' => ['required', 'integer', 'exists:manufacturers,id'],
            'name' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'employees_count' => ['required', 'integer', 'min:0'],
        ];
    }
}
