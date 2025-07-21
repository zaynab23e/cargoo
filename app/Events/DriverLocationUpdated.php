<?php
namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DriverLocationUpdated implements ShouldBroadcast
{
    use SerializesModels;

    public $user_id, $driverId,$location, $latitude, $longitude;

    public function __construct($user_id, $driverId,$location, $latitude, $longitude)
    {
        $this->user_id = $user_id;
        $this->driverId = $driverId;
        $this->location = $location;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function broadcastOn()
    {
        return new Channel('user.' . $this->user_id);
    }

    public function broadcastAs()
    {
        return 'driver.location.updated';
    }

    public function broadcastWith()
    {
        return [
            'driver_id' => $this->driverId,
            'user_id' => $this->user_id,
            'location' => $this->location,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }
}