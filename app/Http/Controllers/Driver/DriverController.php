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

public function update(Request $request, $id)
{
    $driver = Driver::findOrFail($id);

    $validated = $request->validate([
        'name'      => 'required|string|max:255',
        'email'     => 'required|email|max:255',
        'phone'     => 'required|string|max:20',
        'location'  => 'nullable|string|max:255',
        'latitude'  => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
    ], [
        'name.required'     => __('validation.required', ['attribute' => __('attributes.name')]),
        'name.string'       => __('validation.string', ['attribute' => __('attributes.name')]),
        'name.max'          => __('validation.max.string', ['attribute' => __('attributes.name'), 'max' => 255]),

        'email.required'    => __('validation.required', ['attribute' => __('attributes.email')]),
        'email.email'       => __('validation.email', ['attribute' => __('attributes.email')]),
        'email.max'         => __('validation.max.string', ['attribute' => __('attributes.email'), 'max' => 255]),

        'phone.required'    => __('validation.required', ['attribute' => __('attributes.phone')]),
        'phone.string'      => __('validation.string', ['attribute' => __('attributes.phone')]),
        'phone.max'         => __('validation.max.string', ['attribute' => __('attributes.phone'), 'max' => 20]),

        'location.string'   => __('validation.string', ['attribute' => __('attributes.location')]),
        'location.max'      => __('validation.max.string', ['attribute' => __('attributes.location'), 'max' => 255]),

        'latitude.numeric'  => __('validation.numeric', ['attribute' => __('attributes.latitude')]),
        'longitude.numeric' => __('validation.numeric', ['attribute' => __('attributes.longitude')]),
    ]);

    // تحديث البيانات
    $driver->update($validated);

    return response()->json([
        'status'  => 'success',
        'message' => __('messages.updated_successfully'),
        'data'    => $driver,
    ], 200);
}

}
