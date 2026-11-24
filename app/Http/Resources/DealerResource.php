<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DealerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'manufacturer_id' => $this->manufacturer_id,
            'name' => $this->name,
            'city' => $this->city,
            'country' => $this->country,
            'cars_amount' => $this->cars_amount,
            'employees_count' => $this->employees_count,
            'manufacturer' => new ManufacturerResource($this->whenLoaded('manufacturer')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
