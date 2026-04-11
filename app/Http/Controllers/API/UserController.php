<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\API\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:200',
            'email' => 'required|unique:users,email|email',
            'password' => 'required|string|min:4|max:20|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => Str::lower($request->email),
            'password' => Hash::make($request->password),
        ]);


        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'data' => new UserResource($user)
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:4|max:20',
        ]);

        $credentials = $request->only('email', 'password');

        if (!$token = auth()->attempt($credentials)) {
            return response()->json([
                'error' => 'Unauthorized'
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'data' => [
                'user' => new UserResource(auth()->user()),
                'token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth()->factory()->getTTL() * 60
            ]
        ], 200);
    }

    public function me()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'error' => 'Unauthorized'
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'User profile retrieved successfully',
            'data' => new UserResource($user)
        ], 200);
    }

    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()->json([
                'status' => 'success',
                'message' => 'User logged out successfully'
            ]);
        } catch (JWTException $e) {

            return response()->json([
                'status' => 'error',
                'message' => 'Token invalid or expired'
            ], 401);
        }
    }
}
