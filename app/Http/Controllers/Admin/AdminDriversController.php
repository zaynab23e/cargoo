<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\DriversResource;
use App\Models\Booking;
use App\Models\Driver;
use Illuminate\Http\Request;

class AdminDriversController extends Controller
{
    //______________________________________________________________________________________________________
    public function assignDriver(Request $request, $id)
    {
        $booking = Booking::with(['user','location','carmodel.modelName.type.brand','car','driver'])->find($id);

        if (!$booking) {
        return response()->json(['message' => __('messages.booking_not_found')], 404);
        }
         // if ($booking->status !== 'car_assigned') {
        //     return response()->json(['message' => __('messages.booking_status_not_confirmed')], 400);
        // }
        if (!$booking->car) {
            return response()->json(['message' => __('messages.car_not_assigned')], 400);
        }
        
        $request->validate([
            'driver_id' => 'required|exists:drivers,id',
        ]);
        
        $booking->driver_id = $request->driver_id;
        $booking->status = 'driver_assigned';
        $booking->save();
        
        return response()->json(['message' => __('messages.driver_assigned'), 'data' => $booking], 200);
    }
    public function getDrivers()
    {
        $drivers = Driver::all();
        return DriversResource::collection($drivers);
    }
}
