<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Car;
use App\Http\Requests\Admin\bokingStore;
use App\Http\Requests\Admin\bokingupdate;
use App\Http\Resources\BookingResource;

class BookingController extends Controller
{
    public function ConfirmedBooking()
    {
        $bookings = Booking::with([
            'carModel.modelName.type.brand', 'carModel.ratings', 'user', 'location'
        ])
        ->where('status', 'confirmed')
        ->orderBy('created_at', 'desc')
        ->get();

        if ($bookings->isEmpty()) {
            return response()->json([
                'message' => __('messages.no_bookings'),
                'data' => []
            ], 404);
        }

        $data = $bookings->map(function ($booking) {
            return $this->formatBookingData($booking);
        });

        return response()->json([
            'message' => __('messages.confirmed_bookings_retrieved'),
            'data' => $data
        ]);
    }
//_______________________________________________________________________________________________
public function CompletedBooking()
{
        $bookings = Booking::with([
            'carModel.modelName.type.brand', 'carModel.ratings', 'user', 'location'
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

        $data = $bookings->map(function ($booking) {
            return $this->formatBookingData($booking);
        });
        
        return response()->json([
            'message' => __('messages.completed_bookings_retrieved'),
            'data' => $data
        ]);
    }
    
    //_______________________________________________________________________________________________
    public function DriverAssignedBooking()
    {
        $bookings = Booking::with([
            'carModel.modelName.type.brand', 'carModel.ratings', 'user', 'location', 'driver'
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
            
            $data = $bookings->map(function ($booking) {
                $data = $this->formatBookingData($booking);
                $data['user_phone'] = optional($booking->user)->phone;
                $data['driver_name'] = optional($booking->driver)->name;
                $data['driver_email'] = optional($booking->driver)->email;
                $data['driver_phone'] = optional($booking->driver)->phone;
                return $data;
            });
            
            return response()->json([
                'message' => __('messages.assigned_bookings_retrieved'),
                'data' => $data
            ]);
        }
        
        //_______________________________________________________________________________________________
        public function CanceledBooking()
        {
            $bookings = Booking::with([
                'carModel.modelName.type.brand', 'carModel.ratings', 'user', 'location'
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
                
                $data = $bookings->map(function ($booking) {
                    return $this->formatBookingData($booking);
                });
                
                return response()->json([
                    'message' => __('messages.canceled_bookings_retrieved'),
                    'data' => $data
                ]);
            }
            
            public function bookingDetails($id)
            {
                $booking = Booking::with([
                    'carModel.modelName.type.brand', 'car', 'user', 'location'
                    ])->find($id);
                    
                    if (!$booking) {
                        return response()->json(['message' => __('messages.booking_not_found')], 404);
                    }
                    
                    return response()->json([
                        'message' => __('messages.booking_details'),
                        'data' => new BookingResource($booking)
                    ], 200);
                }
                
                //_______________________________________________________________________________________________
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
                        return response()->json(['message' => __('messages.booking_not_found'), 'data' => []], 404);
                    }
                    
                    $cars = $booking->carModel->cars;
                    
                    if ($cars->isEmpty()) {
                        return response()->json(['message' => __('messages.no_cars'), 'data' => []], 404);
                    }
                    
                    $cars->load('carModel.modelName');
                    
                    $carsData = $cars->map(function ($car) {
                        return [
                            'id' => $car->id,
                            'plate_number' => $car->plate_number,
                            'status' => $car->status,
                            'color' => $car->color,
                            'car_model' => $car->carModel ? [
                                'id' => $car->carModel->id,
                                'year' => $car->carModel->year,
                                'name' => $car->carModel->modelName->name,
                                'brand' => [
                                    'id' => $car->carModel->modelName->type->brand->id,
                                    'name' => $car->carModel->modelName->type->brand->name,
                                    ]
                                    ] : null,
                                ];
                            });
                            
                            return response()->json([
                                'message' => __('messages.cars_retrieved_successfully'),
                                'data' => $carsData
                            ]);
                        }
                        
                        //_______________________________________________________________________________________________
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
        
        if (optional($booking->carModel)->id !== optional($car->carModel)->id) {
            return response()->json(['message' => __('messages.car_model_mismatch')], 400);
        }
        
        if ($car->status !== 'available') {
            return response()->json(['message' => __('messages.car_not_available')], 400);
        }
        
        $booking->car_id = $car->id;
        $booking->status = 'car_assigned';
        $booking->save();
        
        $car->status = 'rented';
        $car->save();
        
        return response()->json([
            'message' => __('messages.car_assigned_successfully'),
            'data' => $booking
        ], 200);
    }
    
    //_______________________________________________________________________________________________
    public function changeStatus(Request $request, $id)
    {
        $booking = Booking::with([
            'carModel.modelName.type.brand', 'car', 'user', 'location', 'driver'
            ])->find($id);
            
            if (!$booking) {
                return response()->json(['message' => __('messages.booking_not_found')], 404);
            }

        $request->validate([
            'status' => 'required|in:canceled,completed,available',
        ]);
        
        $booking->status = $request->status;
        
        if ($booking->car) {
            $booking->car->status = 'available';
            $booking->car->save();
        }
        
        $booking->save();
        
        return response()->json([
            'message' => __('messages.status_updated'),
            'data' => $booking
        ], 200);
    }
    

    //_______________________________________________________________________________________________
    private function formatBookingData($booking)
    {
        return [
            'id' => $booking->id,
            'start_date' => $booking->start_date,
            'end_date' => $booking->end_date,
            'status' => $booking->status,
            'payment_method' => $booking->payment_method,
            'final_price' => $booking->final_price,
            'car_model_id' => optional($booking->carModel)->id,
            'car_model_year' => optional($booking->carModel)->year,
            'car_model_image' => asset(optional($booking->carModel)->image),
            'model_name' => optional(optional($booking->carModel)->modelName)->name,
            'Ratings' => [
                'average_rating' => optional($booking->carModel)->avgRating()
                ? number_format($booking->carModel->avgRating(), 1)
                : null,
                'ratings_count' => optional($booking->carModel)->ratings->count() ?? 0,
            ],
            'brand_name' => optional(optional(optional($booking->carModel)->modelName)->type->brand)->name,
            'user_name' => optional($booking->user)->name,
            'user_email' => optional($booking->user)->email,
            'location' => optional($booking->location)->name,
        ];
    }
    //_______________________________________________________________________________________________
public function NewBookings()
{
    $bookings = Booking::with([
        'carModel.modelName.type.brand', 'carModel.ratings', 'user', 'location'
    ])
    ->whereIn('status', ['initiated', 'awaiting_payment', 'payment_pending'])
    ->orderBy('created_at', 'desc')
    ->get();

    if ($bookings->isEmpty()) {
        return response()->json([
            'message' => __('messages.no_bookings'),
            'data' => []
        ], 404);
    }

    $data = $bookings->map(function ($booking) {
        return $this->formatBookingData($booking);
    });

    return response()->json([
        'message' => __('messages.new_bookings_retrieved'),
        'data' => $data
    ]);
}
    //_______________________________________________________________________________________________
}
