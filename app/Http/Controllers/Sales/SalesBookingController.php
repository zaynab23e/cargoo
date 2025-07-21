<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Car;
use Illuminate\Http\Request; 

class SalesBookingController extends Controller
{
    public function ConfirmedBooking()
    {

        $bookings = Booking::with([
            'carModel.modelName.type.brand','user','location'
        ])
        ->where('status', 'confirmed')
        ->orderBy('created_at', 'desc')
        ->get();

        if ($bookings->isEmpty()) {
            return response()->json([
                'message' => 'لا توجد حجوزات ',
                'data' => []
            ], 404);
        }

        // اختيار الحقول المطلوبة فقط
        $data = $bookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'start_date' => $booking->start_date,
                'end_date'   => $booking->end_date,
                'status'   => $booking->status,
                'payment_method'   => $booking->payment_method,
                'final_price'   => $booking->final_price,
                'car_model_id' => optional($booking->carModel)->id,
                'car_model_year' => optional($booking->carModel)->year,
                'car_model_image' => asset(optional($booking->carModel)->image),
                'model_name'     => optional(optional($booking->carModel)->modelName)->name,
                'brand_name'     => optional(optional(optional($booking->carModel)->modelName)->type->brand)->name,
                'user_name' => optional($booking->user)->name,
                'user_email' => optional($booking->user)->email,
                'location' => optional($booking->location)->name,
            ];
        });

        return response()->json([
            'message' =>__('messages.confirmed_bookings_retrieved'),
            'data' => $data
        ]);
    }
    public function CompletedBooking()
    {

        $bookings = Booking::with([
            'carModel.modelName.type.brand','user','location'
        ])
        ->where('status', 'completed')
        ->orderBy('created_at', 'desc')
        ->get();

        if ($bookings->isEmpty()) {
            return response()->json([
                'message' => __('messages.no_bookings'),
                'data' => []
            ], 404);
        }

        // اختيار الحقول المطلوبة فقط
        $data = $bookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'start_date' => $booking->start_date,
                'end_date'   => $booking->end_date,
                'status'   => $booking->status,
                'payment_method'   => $booking->payment_method,
                'final_price'   => $booking->final_price,
                'car_model_id' => optional($booking->carModel)->id,
                'car_model_year' => optional($booking->carModel)->year,
                'car_model_image' => asset(optional($booking->carModel)->image),
                'model_name'     => optional(optional($booking->carModel)->modelName)->name,
                'brand_name'     => optional(optional(optional($booking->carModel)->modelName)->type->brand)->name,
                'user_name' => optional($booking->user)->name,
                'user_email' => optional($booking->user)->email,
                'location' => optional($booking->location)->name,
            ];
        });

        return response()->json([
            'message' => __('messages.completed_bookings_retrieved'),
            'data' => $data
        ]);
    }
    public function AssignedBooking()
    {

        $bookings = Booking::with([
            'carModel.modelName.type.brand','user','location'
        ])
        ->where('status', 'assigned')
        ->orderBy('created_at', 'desc')
        ->get();

        if ($bookings->isEmpty()) {
            return response()->json([
                'message' => __('messages.no_bookings'),
                'data' => []
            ], 404);
        }

        // اختيار الحقول المطلوبة فقط
        $data = $bookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'start_date' => $booking->start_date,
                'end_date'   => $booking->end_date,
                'status'   => $booking->status,
                'payment_method'   => $booking->payment_method,
                'final_price'   => $booking->final_price,
                'car_model_id' => optional($booking->carModel)->id,
                'car_model_year' => optional($booking->carModel)->year,
                'car_model_image' => asset(optional($booking->carModel)->image),
                'model_name'     => optional(optional($booking->carModel)->modelName)->name,
                'brand_name'     => optional(optional(optional($booking->carModel)->modelName)->type->brand)->name,
                'user_name' => optional($booking->user)->name,
                'user_email' => optional($booking->user)->email,
                'location' => optional($booking->location)->name,
            ];
        });

        return response()->json([
            'message' => __('messages.assigned_bookings_retrieved'),
            'data' => $data
        ]);
    }
    public function CanceledBooking()
    {

        $bookings = Booking::with([
            'carModel.modelName.type.brand','user','location'
        ])
        ->where('status', 'canceled')
        ->orderBy('created_at', 'desc')
        ->get();

        if ($bookings->isEmpty()) {
            return response()->json([
                'message' => __('messages.no_bookings'),
                'data' => []
            ], 404);
        }

        // اختيار الحقول المطلوبة فقط
        $data = $bookings->map(function ($booking) {
            return [
                'id' => $booking->id,
                'start_date' => $booking->start_date,
                'end_date'   => $booking->end_date,
                'status'   => $booking->status,
                'payment_method'   => $booking->payment_method,
                'final_price'   => $booking->final_price,
                'car_model_id' => optional($booking->carModel)->id,
                'car_model_year' => optional($booking->carModel)->year,
                'car_model_image' => asset(optional($booking->carModel)->image),
                'model_name'     => optional(optional($booking->carModel)->modelName)->name,
                'brand_name'     => optional(optional(optional($booking->carModel)->modelName)->type->brand)->name,
                'user_name' => optional($booking->user)->name,
                'user_email' => optional($booking->user)->email,
                'location' => optional($booking->location)->name,
            ];
        });

        return response()->json([
            'message' => __('messages.assigned_bookings_retrieved'),
            'data' => $data
        ]);
    }
    public function getCars(string $bookingId)
    {
        $booking = Booking::with('carModel.cars')->where('status', 'confirmed')->where('id', $bookingId)->first();

        if (!$booking) {
            return response()->json([
                'message' => 'حجز غير موجود',
                'data' => []
            ], 404);
        }

        $cars = $booking->carModel->cars;
        if ($cars->isEmpty()) {
            return response()->json([
                'message' => __('messages.no_cars'),
                'data' => []
            ], 404);
        }
        $cars->load('carModel.modelName');
        $cars = $cars->map(function ($car) {
            return [
                'id' => $car->id,
                'plate_number' => $car->plate_number,
                'status' => $car->status,
                'color' => $car->color,
                'car_model' => $car->carModel ? [
                    'id' => $car->carModel->id,
                    'year' => $car->carModel->year,
                    'name' => $car->carModel->modelName->name,
                    'brand' =>[
                        'id' => $car->carModel->modelName->type->brand->id,
                        'name' => $car->carModel->modelName->type->brand->name,
                    ]
                ] : null,
            ];
        });


        return response()->json([
            'message' => __('messages.cars_retrieved_successfully'),
            'data' => $cars
        ]);
    }
    public function assignCar(string $bookingId, string $carId)
    {
        $booking = Booking::find($bookingId);
        if (!$booking) {
            return response()->json(['message' => __('messages.booking_not_found')], 404);
        }
        $car = Car::find($carId);
        if (!$car || $car->status !== 'available') {
            return response()->json(['message' => __('messages.car_not_available')], 400);
        }
        if (!$booking) {
            return response()->json(['message' => __('messages.booking_not_found')], 404);
        }
        if (!$car) {
            return response()->json(['message' => __('messages.car_not_found')], 404);
        }
        if ($booking->status !== 'confirmed') {
            return response()->json(['message' => __('messages.booking_status_not_confirmed')], 400);
        }
        
        $booking->car_id = $car->id;
        $car->status = 'rented';
        $booking->save();
        $car->save();

        return response()->json(['message' => __('messages.car_assigned_successfully'), 'data' => $booking], 200);
    }
}
