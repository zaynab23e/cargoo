<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\ModelResource;
use Illuminate\Support\Facades\Auth;
use App\Models\Favorite;
use App\Models\CarModel;

class FavoriteController extends Controller
{
    public function toggleFavorite(CarModel $carModel)
    {
        $user = Auth::guard('user')->user();
        if (!$user) {
            return response()->json(['message' => __('messages.unauthorized')], 401);
        }

        $favorite = Favorite::where('user_id', $user->id)
            ->where('car_model_id', $carModel->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['message' => __('messages.favorite_removed')]);
        }

        Favorite::create([
            'user_id' => $user->id,
            'car_model_id' => $carModel->id,
        ]);

        return response()->json(['message' => __('messages.favorite_added')]);
    }

    public function getFavorites()
    {
        $user = Auth::guard('user')->user();
        if (!$user) {
            return response()->json(['message' => __('messages.unauthorized')], 401);
        }

        $favorites = $user->favorites()->with('carModel.modelName.type.brand')->get(); 
        $formatted = $favorites->map(function ($favorite) {
            return [
                'fav_id' => $favorite->id,
                'user_id' => $favorite->user_id,
                'car_model' => new ModelResource($favorite->carModel),
            ];
        });

    return response()->json($formatted);        
    }
}
