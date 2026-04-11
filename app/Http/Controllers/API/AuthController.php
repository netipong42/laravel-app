<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function checkAuthJwt(Request $request)
    {
        try {
            $user = Auth::guard('api')->user();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid token'
                ], 401);
            }

            return response()->json([
                'status' => 'success',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid token'
            ], 401);
        }
    }
}
