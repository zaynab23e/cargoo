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


        public function statisticsHome()
    {
        $bookings = Booking::all();

        $totalRented = $bookings->count();

        $totalRentedDays = $bookings->sum(function ($booking) {
            return now()->parse($booking->start_date)->diffInDays(now()->parse($booking->end_date));
        });

        $totalRevenue = $bookings->sum('final_price');

        return response()->json([
            'total_rented' => $totalRented,
            'total_rented_days' => $totalRentedDays,
            'total_revenue' => $totalRevenue,
        ]);
    }

}
