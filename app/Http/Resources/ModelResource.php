<?php
namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $isShowDetailsRoute = $request->routeIs('show-details');
        $isShowRoute = $request->routeIs('show-model');

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
                'image' => $this->image ? asset($this->image) : null,
            ],
            'relationship' => array_filter([
                'cars' => $this->whenLoaded('cars', function () {
                    return $this->cars->map(fn($car) => [
                        'id' => $car->id,
                        'capacity' => $car->capacity,
                        'description' => $car->description,
                        'color' => $car->color,
                        'plate_number' => $car->plate_number,
                        'status' => $car->status,
                        'image' => $car->image ? asset($car->image) : null,
                    ]);
                }),
                'model_names' => $this->modelName ? [
                    'model_name_id' => (string) $this->modelName->id,
                    'model_name' => $this->modelName->name,
                ] : null,
                'images' => ($isShowDetailsRoute || $isShowRoute) && $this->relationLoaded('images')
                    ? $this->images->filter(fn($image) => $image->image)
                                   ->map(fn($image) => asset($image->image))
                                   ->values()
                    : null,
                'types' => $this->modelName && $this->modelName->type ? [
                    'type_id' => (string) $this->modelName->type->id,
                    'type_name' => $this->modelName->type->name,
                ] : null,
                'brand' => $this->modelName && $this->modelName->type && $this->modelName->type->brand ? [
                    'brand_id' => $this->modelName->type->brand->id,
                    'brand_name' => $this->modelName->type->brand->name,
                ] : null,
                'ratings' => array_filter([
                    'average_rating' => $this->avgRating() ? number_format($this->avgRating(), 1) : null,
                    'ratings_count' => $this->whenLoaded('ratings', fn() => $this->ratings->count()),
                    'reviews' => $isShowDetailsRoute && $this->relationLoaded('ratings')
                        ? $this->ratings->map(fn($rating) => [
                            'user_id' => $rating->user->id,
                            'user_name' => $rating->user->name,
                            'last_name' => $rating->user->last_name,
                            'email' => $rating->user->email,
                            'rating' => (int) $rating->rating,
                            'review' => $rating->review,
                        ])
                        : null,
                ], fn($value) => !is_null($value)),
            ], fn($value) => !is_null($value)),
        ];
    }
}
