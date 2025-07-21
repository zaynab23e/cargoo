<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'final_price' => $this->final_price,
            'status' => $this->status,
            'additional_driver' => $this->additional_driver,
            'payment_method' => $this->payment_method,
            'payment_status' => $this->payment_status,
            'transaction_id' => $this->transaction_id,
            'car_model' => new ModelResource($this->carModel),
            'location' => $this->location,
            'user' => $this->user,
            'driver' => $this->driver,
            'car' => $this->car ?? null,
        ];
    }
}
