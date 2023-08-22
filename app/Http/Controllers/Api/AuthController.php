<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\AuthRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Throwable;

class AuthController extends Controller
{
    /**
     * @param  AuthRequest  $request
     * @return JsonResponse
     */
    public function login(AuthRequest $request)
    {
        try {
            $params = $request->only('email', 'password');

            if (!Auth::attempt([...$params])) {
                return  ResponseController::json(Type::notAuthentication, Action::login);
            }

            if (!$request->user()->ativo) {
                return  ResponseController::json(Type::notAuthentication, Action::login);
            }

            $token_name = 'authentication_token';
            $token = $request->user()->createToken($token_name);
            $data = [
                'accessToken'  => $token->plainTextToken,
                'tokenType'    => 'bearer',
                'email'        => $request->user()->email,
            ];
            return  ResponseController::json(Type::success, Action::login, $data);
        } catch(Throwable $e) {
            return  ResponseController::json(Type::error, Action::login);
        }
    }

    /**
     * @param  Request      $request
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        try {
            if ($request->user()->currentAccessToken()->delete()) {
                return ResponseController::json(Type::success, Action::logout);
            }
            return ResponseController::json(Type::error, Action::logout);
        } catch(Throwable $e) {
            return ResponseController::json(Type::error, Action::logout);
        }
    }

    /**
     * @param  Request      $request
     * @return JsonResponse
     */
    public function me(Request $request)
    {
        try {
            return ResponseController::json(Type::success, Action::login);
        } catch(Throwable $e) {
            return ResponseController::json(Type::error, Action::login);
        }
    }
}
