<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    public function getUserLocation()
    {
        $user = Auth::guard('user')->user();
        if (!$user) {
            return response()->json(['message' => __('messages.unauthorized_user')], 403);
        }

        $locations = $user->userLocations()->get();
        if (!$locations) {
             return response()->json(['message' => __('messages.no_user_locations')], 404);
        }

        return response()->json(['message' =>  __('messages.location_added'), 'data' => $locations], 200);
       
    }
    public function getUserActiveLocation()
    {
        $user = Auth::guard('user')->user();
        if (!$user) {
            return response()->json(['message' => __('messages.unauthorized_user')], 403);
        }

        $activeLocation = $user->userLocations()->where('is_active', true)->first();
        if (!$activeLocation) {
             return response()->json(['message' => __('messages.no_user_locations')], 404);
        }

        return response()->json(['message' => __('messages.location_retrieved'), 'data' => $activeLocation], 200);
    }


//___________________________________________________________________________
public function setUserLocation(Request $request)
{
    $validated = $request->validate([
        'location' => 'required|string|max:255',
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
        'is_active' => 'required|boolean',
    ]);

    $user = Auth::guard('user')->user();

    if (!$user) {
        return response()->json(['message' => __('messages.unauthorized_user')], 403);
    }

    $locationsCount = $user->userLocations()->count();

    if ($locationsCount === 0) {
        $validated['is_active'] = true;
    } else {
        if ($validated['is_active'] == true) {
            $user->userLocations()->where('is_active', true)->update(['is_active' => false]);
        } else {
            $validated['is_active'] = false;
        }
    }

    $user->userLocations()->create([
        'location' => $validated['location'],
        'latitude' => $validated['latitude'],
        'longitude' => $validated['longitude'],
        'is_active' => $validated['is_active'],
    ]);

   return response()->json(['message' => __('messages.location_added')], 200);

}

//___________________________________________________________________________
    public function updateUserLocation(Request $request, $id)
    {
        $validated = $request->validate([
            'location' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'is_active' => 'required|boolean',
        ]);

        $user = Auth::guard('user')->user();
        if (!$user) {
           return response()->json(['message' => __('messages.unauthorized_user')], 403);
        }
        
        
        $location = $user->userLocations()->find($id);
    
        if (!$location) {
   return response()->json(['message' => __('messages.no_user_locations')], 404);
        }


        if ($validated['is_active'] == true) {
            $user->userLocations()->where('is_active', true)->update(['is_active' => false]);
        }

        $location->update($validated);

        return response()->json(['message' => __('messages.location_added'), 'data' => $location], 200);
    

    }
}
