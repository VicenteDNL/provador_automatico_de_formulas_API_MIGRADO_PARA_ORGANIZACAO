<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $token_name = 'authentication_token';
        $token = $request->user()->createToken($token_name);
        return [
            'success' => true,
            'access_token' => $token->plainTextToken,
            'token_type' => 'bearer',
            'email' => $request->user()->email];
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth('api')->logout();
        return response()->json(['success' => true, 'msg'=>'Successfully logged out', 'data'=>'']);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json(
            [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
                'email' => auth('api')->user()->email
            ]
        );
    }


        /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
//        if(auth('api')->user()==null){
//            return response()->json(['success' => false,'msg' => 'Unauthorized', 'data'=>'']);
//        }
        return response()->json(['success' => true, 'msg'=>'', 'data'=>'']);
    }

}
