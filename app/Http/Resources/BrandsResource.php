<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string)$this->id,
            'attributes' =>[
                'name' =>$this->name,
                'logo' => $this->logo  ? asset($this->logo) : null
            ],
            'relationship' => [
                'Types' => TypesResource::collection($this->types),
            ]
        ];
    }
}
