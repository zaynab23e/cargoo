<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Http\Requests\Admin\bokingStore;
use App\Http\Requests\Admin\bokingupdate;
use App\Http\Resources\BookingResource;
use App\Models\Car;

//________________________________________________________________________________________________________
class BookingController extends Controller
{
    public function ConfirmedBooking()
    {

        $bookings = Booking::with([
            'carModel.modelName.type.brand','user','location', 'carModel.ratings'
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
                'Ratings' => [
                    'average_rating' => $booking->carModel->avgRating() ? number_format($booking->carModel->avgRating(), 1) : null,
                    'ratings_count' => $booking->carModel->ratings->count(),
                ],
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
            'carModel.modelName.type.brand','user','location',''
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
                'model_name'     => optional(optional($booking->carModel)->modelName)->name,
                'car_model_year' => optional($booking->carModel)->year,
                'car_model_image' => asset(optional($booking->carModel)->image),
                'Ratings' => [
                    'average_rating' => $booking->carModel->avgRating() ? number_format($booking->carModel->avgRating(), 1) : null,
                    'ratings_count' => $booking->carModel->ratings->count(),
                ],                
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
    public function DriverAssignedBooking()
    {

        $bookings = Booking::with([
            'carModel.modelName.type.brand','user','location','driver','location'
        ])
        ->where('status', 'driver_assigned')
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
                'model_name'     => optional(optional($booking->carModel)->modelName)->name,
                'car_model_year' => optional($booking->carModel)->year,
                'car_model_image' => asset(optional($booking->carModel)->image),
                'Ratings' => [
                    'average_rating' => $booking->carModel->avgRating() ? number_format($booking->carModel->avgRating(), 1) : null,
                    'ratings_count' => $booking->carModel->ratings->count(),
                ],                
                'brand_name'     => optional(optional(optional($booking->carModel)->modelName)->type->brand)->name,
                'user_name' => optional($booking->user)->name,
                'user_email' => optional($booking->user)->email,
                'user_phone' => optional($booking->user)->phone,
                'driver_name' => optional($booking->driver)->name,
                'driver_email' => optional($booking->driver)->email,
                'driver_phone' => optional($booking->driver)->phone,
                'location' => optional($booking->location)->location,
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
            return
             [
                'id' => $booking->id,
                'start_date' => $booking->start_date,
                'end_date'   => $booking->end_date,
                'status'   => $booking->status,
                'payment_method'   => $booking->payment_method,
                'final_price'   => $booking->final_price,
                'car_model_id' => optional($booking->carModel)->id,
                'model_name'     => optional(optional($booking->carModel)->modelName)->name,
                'car_model_year' => optional($booking->carModel)->year,
                'car_model_image' => asset(optional($booking->carModel)->image),
                'Ratings' => [
                    'average_rating' => $booking->carModel->avgRating() ? number_format($booking->carModel->avgRating(), 1) : null,
                    'ratings_count' => $booking->carModel->ratings->count(),
                ],                
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
    public function bookingDetails($id)
    {
        $booking = Booking::with(['user', 'location', 'carmodel.modelName.type.brand', 'car'])->find($id);
        if (!$booking) {
            return response()->json(['message' => __('messages.booking_not_found')], 404);
        }

        return response()->json([
            'message' => __('messages.booking_details'),
           'data'=> new BookingResource($booking),
    ], 200);
    }    

    public function destroy($id)
    {
        $booking = Booking::find($id);
        
        if (!$booking) {
        return response()->json(['message' => __('messages.booking_not_found')], 404);
        }
        
        $booking->delete();
        
        return response()->json(['message' => __('messages.booking_deleted')], 200);
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
    public function assignCar(Request $request, string $bookingId)
    {
        $request->validate([
            'car_id' => 'required|exists:cars,id',
        ]);

        $booking = Booking::find($bookingId);

        if (!$booking) {
            return response()->json(['message' => __('messages.booking_not_found')], 404);
        }
        if ($booking->status !== 'confirmed') {
            return response()->json(['message' => __('messages.booking_status_not_confirmed')], 400);
        } 
        
        
        $car = Car::find($request->car_id);
        if (!$car) {
            return response()->json(['message' => __('messages.car_not_found')], 404);
        }
        if ($booking->carModel->id !== $car->carModel->id) {
            return response()->json(['message' => __('messages.car_model_mismatch')], 404);
        }
        if (!$car || $car->status !== 'available') {
            return response()->json(['message' => __('messages.car_not_available')], 400);
        }

        
        $booking->car_id = $car->id;
        $booking->status = 'car_assigned';
        $car->status = 'rented';
        $booking->save();
        $car->save();

        return response()->json(['message' => __('messages.car_assigned_successfully'), 'data' => $booking], 200);
    }    
    //________________________________________________________________________________________________________

    public function changeStatus(Request $request, $id)
    {
        $booking = Booking::with(['user','location','carmodel.modelName.type.brand','car','driver'])->find($id);

        if (!$booking) {
        return response()->json(['message' => __('messages.booking_not_found')], 404);
        }
        
        $request->validate([
            'status' => 'required|in:canceled,completed',
        ]);
        $booking->status = $request->status;
        if (isset($booking->car)) {
            $booking->car->status = 'available';
            $booking->car->save();

        }
        $booking->save();
        
        return response()->json(['message' => __('messages.status_updated'), 'data' => $booking], 200);
    }
}
