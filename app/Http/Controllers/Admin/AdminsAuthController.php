<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginAdminRequest;
use App\Http\Requests\Admin\StoreAdminRequest;
use App\Models\Admin;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminsAuthController extends Controller
{
    use HttpResponses;

    // Admin login
    public function login(LoginAdminRequest $request)
    {
        $admin = Admin::where('email', $request->email)->first();

        // Check if admin exists and verify password manually
        if (!$admin || !Hash::check($request->password, $admin->password)) {
            return $this->error('', trans('messages.invalid_credentials'), 401);
        }

        return $this->success([
            'admin' => $admin,
            'token' => $admin->createToken('Access Token for ' . $admin->name)->plainTextToken,
        ]);
    }

    // Register new admin (only if no admin exists)
    public function register(StoreAdminRequest $request)
    {
        if (Admin::count() > 0) {
            return response()->json(['message' => trans('messages.admin_already_registered')], 403);
        }

        $admin = Admin::create($request->validated());

        return $this->success([
            'admin' => $admin,
            'token' => $admin->createToken('admin-token')->plainTextToken,
        ], trans('messages.admin_registered_successfully'), 201);
    }

    // Admin logout
    public function logout(Request $request)
    {
        $admin = Auth::guard('admin')->user(); // Use the admin guard

        if (!$admin) {
            return response()->json([
                'status' => trans('messages.error_occurred'),
                'message' => trans('messages.unauthorized_admin'),
                'data' => ''
            ], 403);
        }

        // Revoke current access token (using admin guard)
        $admin->currentAccessToken()?->delete(); // Safe way to delete token

        return response()->json([
            'status' => trans('messages.success'),
            'message' => trans('messages.logout_successfully'),
            'data' => ''
        ]);
    }
}
