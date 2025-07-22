<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriversResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $latestLocation = $this->driverLocations()->latest()->first();
        return [
                'id' => $this->id,
                'attributes' =>[
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                'location' => optional($latestLocation)->location,
                'latitude' => optional($latestLocation)->latitude,
                'longitude' => optional($latestLocation)->longitude,
                ],
            
                
        ];
    }
}
