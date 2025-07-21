<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    //_____________________________________________________________________________________________________________
    public function markAsAssigned(Request $request, $id)
    {
        $booking = Booking::with(['user', 'location', 'carmodel.modelName.type.brand', 'car', 'driver'])->find($id);
        if (!$booking) {
            return response()->json(['message' => __('messages.booking_not_found')], 404);
        }

        $booking->status = 'assigned';
        $booking->save();

        return response()->json(['message' => __('messages.driver_assigned'), 'data' => $booking], 200);
    }
    //_____________________________________________________________________________________________________________
    public function changeStatus(Request $request, $id)
    {
        $booking = Booking::with(['user', 'location', 'carmodel.modelName.type.brand', 'car', 'driver'])->find($id);
        if (!$booking) {
            return response()->json(['message' => __('messages.booking_not_found')], 404);
        }

        $request->validate([
            'status' => 'required|in:canceled,completed',
        ]);

        $booking->status = $request->status;
        $booking->save();

        return response()->json(['message' => __('messages.status_updated'), 'data' => $booking], 200);
    }
    public function bookingDetails($id)
    {
        $booking = Booking::with(['user', 'location', 'carmodel.modelName.type.brand', 'car', 'driver'])->find($id);
        if (!$booking) {
            return response()->json(['message' => __('messages.booking_not_found')], 404);
        }

        return response()->json(['data' => $booking], 200);
    }

}
