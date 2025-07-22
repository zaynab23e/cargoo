<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DriverController extends Controller
{
    /**
     * Display the driver's profile.
     */
    public function show($id)
    {
        $driver = Driver::with(['driverLocations', 'bookings'])->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => [
                'id'        => $driver->id,
                'name'      => $driver->name,
                'email'     => $driver->email,
                'phone'     => $driver->phone,
                // 'location'  => $driver->location,
                // 'latitude'  => $driver->latitude,
                // 'longitude' => $driver->longitude,
                'locations' => $driver->driverLocations->map(function ($location) {
                    return [
                        'id'         => $location->id,
                        'location'   => $location->location,
                        'latitude'   => $location->latitude,
                        'longitude'  => $location->longitude,
                        'created_at' => $location->created_at->toIso8601String(),
                    ];
                }),
                'bookings' => $driver->bookings->map(function ($booking) {
                    return [
                        'id'         => $booking->id,
                        // يمكنك إضافة مزيد من الحقول حسب الحاجة
                        'created_at' => $booking->created_at->toIso8601String(),
                    ];
                }),
            ],
        ], 200);
    }

    /**
     * Update the driver's profile.
     */
    public function update(Request $request, $id)
    {
        $driver = Driver::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
        ], [
            'name.required' => __('validation.required', ['attribute' => 'الاسم']),
            'name.string'   => __('validation.string', ['attribute' => 'الاسم']),
            'name.max'      => __('validation.max.string', ['attribute' => 'الاسم', 'max' => 255]),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $driver->name = $request->name;
        $driver->save();

        return response()->json([
            'status'  => 'success',
            'message' => __('messages.updated_successfully'),
            'data'    => [
                'id'        => $driver->id,
                'name'      => $driver->name,
                'email'     => $driver->email,
                'phone'     => $driver->phone,
                'location'  => $driver->location,
                'latitude'  => $driver->latitude,
                'longitude' => $driver->longitude,
            ],
        ], 200);
    }
}
