<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TypesResource extends JsonResource
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
                'description'=>$this->description,
                'brand_id' => (string)$this->brand_id,
            ],
            'relationship' => [
                'Brands' => $this->brand,
                'Model Names' => $this->modelNames,
            ]
        ];
    }
}
