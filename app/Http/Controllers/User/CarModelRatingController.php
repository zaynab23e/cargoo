<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CarModelRating;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class CarModelRatingController extends Controller
{
    public function setRate(Request $request, string $modelId)
    {
        $user = Auth::guard('user')->user();

        if (!$user) {
            return response()->json(['message' => __('messages.unauthorized_user')], 401);
        }
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);
        $rating = CarModelRating::updateOrCreate(
            ['user_id' => $user->id, 'car_model_id' => $modelId],
            ['rating' => $request->input('rating'), 'review' => $request->input('review')]
        );
        if (!$rating) {
            return response()->json(['message' => __('messages.setRateFailed')], 500);
        }
        
        return response()->json(['message' => __('messages.setRateSuccess')], 201);
    }
    public function resetRate(string $modelId)
    {
        $user = Auth::guard('user')->user();

        if (!$user) {
            return response()->json(['message' => __('messages.unauthorized_user')], 401);
        }
        $rating = CarModelRating::where('user_id', $user->id)
            ->where('car_model_id', $modelId)
            ->first();
        if (!$rating) {
            return response()->json(['message' => __('messages.ratingNotFound')], 404);

        }

        $rating->delete();

        return response()->json([
            'message' => __('messages.deleteRateSuccess'),
        ]);
    }
}
