<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModelNameResource extends JsonResource
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

            ],
            'relationship' => [
                'Types' => [
                    'type_id' => (string)$this->type->id,
                    'type_name' => $this->type->name,
                ],
                'Brand' => [
                    'brand_id' => $this->type->brand->id,

                    'brand_name' => $this->type->brand->name,
                ],

            ]

        ];
    
    }
}
