<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'refresh']]);
    }

    public function refresh()
    {
        try {
            $token = JWTAuth::refresh();
            return response()->json([
                'token' => $token
            ]);
        } catch (TokenExpiredException $e) {
            return response()->json([
                'error' => 'Token has expired and cannot be refreshed'
            ], 401);
        } catch (JWTException $e) {
            return response()->json([
                'error' => 'Could not refresh token'
            ], 401);
        }
    }
}
