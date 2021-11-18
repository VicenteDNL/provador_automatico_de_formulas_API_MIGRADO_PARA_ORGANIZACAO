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
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['success' => true, 'msg'=>'Saiu com sucesso', 'data'=>'']);
    }

        /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        //Verificar sê o uso do ME ainda é necessário
        return response()->json(['success' => true, 'msg'=>'', 'data'=>'']);
    }

}
