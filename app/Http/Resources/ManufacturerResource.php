<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ManufacturerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'dealers_count' => $this->whenCounted('dealers'),
            'cars_count' => $this->whenCounted('cars'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
