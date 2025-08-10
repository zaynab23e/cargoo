<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ModelResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => (string) $this->id,
            'attributes' => [
                'year' => (string) $this->year,
                'price' => (string) $this->price,
                'engine_type' => $this->engine_type,
                'transmission_type' => $this->transmission_type,
                'seat_type' => $this->seat_type,
                'seats_count' => $this->seats_count,
                'acceleration' => (string) $this->acceleration,
                'image' => $this->image ? $this->image : null,
            ],
            'relationship' => [
                // cars: اعرض مجموعة السيارات لكن بمفاتيح محددة فقط
                'cars' => $this->whenLoaded('cars', function () {
                    return $this->cars->map(function ($car) {
                        return [
                            'id' => $car->id,
                            'capacity' => $car->capacity,
                            'description' => $car->description,
                            'color' => $car->color,
                            'plate_number' => $car->plate_number,
                            'status' => $car->status,
                            'image' => $car->image,
                        ];
                    });
                }, []),

                // model name / type / brand: اعرضهم فقط إنهم موجودين
                'model_names' => $this->when($this->relationLoaded('modelName') && $this->modelName, function () {
                    return [
                        'model_name_id' => (string) $this->modelName->id,
                        'model_name' => $this->modelName->name,
                    ];
                }),

                'types' => $this->when($this->relationLoaded('modelName') && $this->modelName && $this->modelName->relationLoaded('type') && $this->modelName->type, function () {
                    return [
                        'type_id' => (string) $this->modelName->type->id,
                        'type_name' => $this->modelName->type->name,
                    ];
                }),

                'brand' => $this->when($this->relationLoaded('modelName') && $this->modelName && $this->modelName->relationLoaded('type') && $this->modelName->type && $this->modelName->type->relationLoaded('brand') && $this->modelName->type->brand, function () {
                    return [
                        'brand_id' => (int) $this->modelName->type->brand->id,
                        'brand_name' => $this->modelName->type->brand->name,
                    ];
                }),

                // ratings (مثال بسيط)
                'ratings' => [
                    'ratings_count' => $this->whenLoaded('cars') ? $this->cars->count() : 0,
                ],
            ],
        ];
    }
}
