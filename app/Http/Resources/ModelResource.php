<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'attributes' => [
                'year' => $this->year,
                'price' => $this->price,
                'engine_type' => $this->engine_type,
                'transmission_type' => $this->transmission_type,
                'seat_type' => $this->seat_type,
                'seats_count' => $this->seats_count,
                'acceleration' => $this->acceleration,
                'image' => $this->image ? asset('storage/' . $this->image) : null,
            ],
            'relationship' => [
                'Model' => [
                    'model_id' => $this->modelName?->id,
                    'model_name' => $this->modelName?->name,
                ],
                'Model Names' => [
                    'model_name_id' => $this->modelName?->id,
                    'model_name' => $this->modelName?->name,
                ],
                'Types' => [
                    'type_id' => $this->modelName?->type?->id,
                    'type_name' => $this->modelName?->type?->name,
                ],
                'Brand' => [
                    'brand_id' => $this->modelName?->type?->brand?->id,
                    'brand_name' => $this->modelName?->type?->brand?->name,
                ],
            ],
        ];
    }
}
