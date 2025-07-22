<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\login;
use App\Http\Requests\User\store;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class DriverAuthController extends Controller
{
    public function register(store $request)
    {
        $validatedData = $request->validated();

        $driver = Driver::create($validatedData);
        $token = $driver->createToken('api-token')->plainTextToken;

        return response()->json([
            'message' => __('messages.register_success'),
            'driver' => $driver,
            'token' => $token,
        ]);
    }

    public function login(login $request)
    {
        $validatedData = $request->validated();

        $driver = Driver::where('email', $validatedData['email'])->first();

        if (!$driver || !Hash::check($validatedData['password'], $driver->password)) {
            return response()->json(['message' => __('messages.invalid_credentials')], 401);
        }

        $token = $driver->createToken('api-token')->plainTextToken;

        return response()->json([
            'driver' => $driver,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $driver = Auth::guard('driver')->user(); // Use the user guard
        if ($driver) {
            $driver->tokens()->delete();
            return response()->json(['message' => __('messages.logout_success')]);
        }

        return response()->json(['message' => __('messages.not_logged_in')], 401);
    }
}
