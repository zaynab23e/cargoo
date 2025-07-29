<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;

class StatisticsController extends Controller
{
    public function bookingStatusStats()
    {
        $stats = [
            'initiated' => Booking::where('status', 'initiated')->count(),
            'assigned' => Booking::where('status', 'driver_assigned')->count(), 
            'canceled' => Booking::where('status', 'canceled')->count(),
            'completed' => Booking::where('status', 'completed')->count(),
        ];

        return response()->json([
            'data' => $stats
        ]);
    }
}
