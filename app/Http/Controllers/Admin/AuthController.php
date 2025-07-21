<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\Register;
use App\Http\Requests\Admin\Login;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Register $request)
    {
        $validatedData = $request->validated();
        $validatedData['password'] = Hash::make($validatedData['password']);
        $admin = Admin::create($validatedData);

        return response()->json([
            'message' => trans('messages.admin_registered'),
            'admin' => $admin,
        ], 201);
    }

    public function login(Login $request)
    {
        $credentials = $request->validated();
        $admin = Admin::where('email', $credentials['email'])->first();

        if (! $admin || ! Hash::check($credentials['password'], $admin->password)) {
            return response()->json([
                'message' => trans('messages.invalid_credentials'),
            ], 401);
        }

        $token = $admin->createToken('admin-token')->plainTextToken;

        return response()->json([
            'message' => trans('messages.login_success'),
            'admin' => $admin,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        $admin = Auth::guard('admin')->user();

        if ($admin) {
            $admin->tokens()->delete();
            return response()->json(['message' => trans('messages.logout_success')]);
        }

        return response()->json(['message' => trans('messages.no_admin_logged_in')], 401);
    }
}
