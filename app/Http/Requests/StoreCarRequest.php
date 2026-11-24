<?php

namespace App\Http\Requests;

use App\Enums\CarColor;
use App\Enums\CarFuel;
use App\Enums\CarSize;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCarRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'manufacturer_id' => ['required', 'integer', 'exists:manufacturers,id'],
            'dealer_id' => ['required', 'integer', 'exists:dealers,id'],
            'model' => ['required', 'string', 'max:255'],
            'size' => ['required', Rule::enum(CarSize::class)],
            'color' => ['required', Rule::enum(CarColor::class)],
            'fuel' => ['required', Rule::enum(CarFuel::class)],
        ];
    }
}
