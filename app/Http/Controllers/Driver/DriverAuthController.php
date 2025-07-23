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
    public function register(request $request)
    {
        $validatedData = $request->validat([
        'name' => 'required|string|max:255',
        'last_name' => 'nullable|string|max:255',
        'email' => 'required|email|unique:drivers,email',
        'phone' => 'required|string|max:15|unique:drivers,phone',
        'password' => 'required|string|confirmed|min:8',
        // 'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $driver = Driver::create($validatedData);
        
        if (Driver::where('email', $request->email)->exists()) {
            return response()->json(['message' => 'البريد الإلكتروني موجود بالفعل'], 409);
        }
        $token = $driver->createToken('api-token')->plainTextToken;
        return response()->json([
            'message' => __('messages.register_success'),
            'driver' => $driver,
            'token' => $token,
        ]);
    }



    public function login(login $request)
    {
        $validatedData = $request->validat();

        $driver = Driver::where('email', $validatedData['email'])->first();

        if (!$driver || !Hash::check($validatedData['password'], $driver->password)) {
            return response()->json(['message' => __('messages.invalid_credentials')], 401);
        }

        $token = $driver->createToken('api-token')->plainTextToken;

        return response()->json([
    'driver' => [
        'id'    => $driver->id,
        'name'  => $driver->name,
        'email' => $driver->email,
        'phone' => $driver->phone,
    ],
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
