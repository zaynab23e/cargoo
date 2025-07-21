<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ModelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $isShowDetailsRoute = $request->routeIs('show-details'); // <- Adjust this to your actual show route name
        $isShowRoute = $request->routeIs('show-model'); // <- Adjust this to your actual show route name

        return [
            'id' => (string)$this->id,
            'attributes' =>[
                'year' =>$this->year,
                'price' =>$this->price,
                'engine_type' => $this->engine_type,
                'transmission_type' => $this->transmission_type,
                'seat_type' => $this->seat_type,
                'seats_count' => $this->seats_count,
                'acceleration' => $this->acceleration,
                'image' =>$this->image ? asset($this->image) : null

            ],
            'relationship' => array_filter([
                'Model Names' => [
                    'model_name_id' => (string)$this->modelName->id,
                    'model_name' => $this->modelName->name,
                ],
                'Images' =>$isShowDetailsRoute || $isShowRoute ? $this->images->map(function ($image) {

                        return asset($image->image) ? asset($image->image) : null;

                }) : null,
                'Types' => [
                    'type_id' => (string)$this->modelName->type->id,
                    'type_name' => $this->modelName->type->name,
                ],
                'Brand' => [
                    'brand_id' => $this->modelName->type->brand->id,

                    'brand_name' => $this->modelName->type->brand->name,
                ],
                'Ratings' => array_filter([
                    'average_rating' => $this->avgRating() ? number_format($this->avgRating(), 1) : null,
                    'ratings_count' => $this->ratings->count(),
                    // Only include reviews on show route
                    'reviews' => $isShowDetailsRoute ? $this->ratings->map(function ($rating) {
                        return [
                            'user_id' => $rating->user->id,
                            'user_name' => $rating->user->name,
                            'last_name' => $rating->user->last_name,
                            'email' => $rating->user->email,
                            'rating' => (int) $rating->rating,
                            'review' => $rating->review,
                        ];
                    }) : null,
                ]),

            ]),


        ];
    
    }
}
