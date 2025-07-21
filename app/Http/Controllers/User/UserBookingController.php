<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\bokingStore;
use App\Models\Booking;
use App\Models\CarModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserBookingController extends Controller
{
    public function carBooking(string $id, bokingStore $request)
    {
        $model = CarModel::with('modelName.type.brand')->find($id);
        if (!$model) {
            return response()->json(['message' => __('messages.model_not_found')], 404);
        }

        $price = $model->price;
        $days = ((strtotime($request->end_date) - strtotime($request->start_date)) / (60 * 60 * 24)) + 1;
        $finalPrice = $price * $days;

        $user = Auth::guard('user')->user();
        if (!$user) {
            return response()->json(['message' => __('messages.unauthorized_user')], 403);
        }

        $validated = $request->validated();

        if ($request->additional_driver) {
            $request->validate([
                'location_id' => 'required|exists:user_locations,id',
            ]);

            $location = $user->userLocations()->find($request->location_id);
            if (!$location) {
                return response()->json(['message' => __('messages.location_not_found')], 404);
            }
        }

        $booking = Booking::create([
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'final_price' => $finalPrice,
            'status' => 'initiated',
            'user_id' => $user->id,
            'carmodel_id' => $model->id,
            'additional_driver' => $request->additional_driver,
        ]);

        if (isset($location)) {
            $booking->location_id = $location->id;
            $booking->save();
        }

        $booking->load(['user', 'location', 'carmodel.modelName.type.brand', 'driver']);

        return response()->json([
            'message' => __('messages.booking_created'),
            'data' => ['booking' => $booking],
        ], 201);
    }

    public function setPaymentMethod(string $modelId, string $id, Request $request)
    {
        $booking = Booking::find($id);
        if (!$booking) {
            return response()->json(['message' => __('messages.booking_not_found')], 404);
        }

        if ($booking->status !== 'initiated') {
            return response()->json(['message' => __('messages.payment_method_update_denied')], 400);
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:cash,visa',
        ]);

        $booking->status = $validated['payment_method'] === 'cash'
            ? 'confirmed'
            : 'awaiting_payment';

        $booking->payment_method = $validated['payment_method'];
        $booking->save();

        return response()->json([
            'message' => __('messages.payment_method_updated'),
            'data' => $booking,
        ], 200);
    }

    public function setPaymobInfo(string $modelId, string $id, Request $request)
    {
        $booking = Booking::find($id);
        if (!$booking) {
            return response()->json(['message' => __('messages.booking_not_found')], 404);
        }

        if ($booking->status !== 'awaiting_payment') {
            return response()->json(['message' => __('messages.payment_info_update_denied')], 400);
        }

        $validated = $request->validate([
            'payment_status' => 'required|in:Successful,Pending,Declined',
            'transaction_id' => 'required|string|max:255',
        ]);

        $statusMap = [
            'Successful' => 'confirmed',
            'Pending' => 'payment_pending',
            'Declined' => 'canceled',
        ];

        $booking->status = $statusMap[$validated['payment_status']] ?? 'awaiting_payment';
        $booking->payment_status = $validated['payment_status'];
        $booking->transaction_id = $validated['transaction_id'];
        $booking->save();

        return response()->json([
            'message' => __('messages.payment_info_updated'),
            'data' => $booking,
        ], 200);
    }
}
