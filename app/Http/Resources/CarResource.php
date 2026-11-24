<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'manufacturer_id' => $this->manufacturer_id,
            'dealer_id' => $this->dealer_id,
            'model' => $this->model,
            'size' => $this->size,
            'color' => $this->color,
            'fuel' => $this->fuel,
            'manufacturer' => new ManufacturerResource($this->whenLoaded('manufacturer')),
            'dealer' => new DealerResource($this->whenLoaded('dealer')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
