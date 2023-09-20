<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Api\Action;
use App\Http\Controllers\Api\ResponseController;
use App\Http\Controllers\Api\Type;
use App\Models\Jogador;
use Closure;
use Illuminate\Http\Request;

class JogadorHash
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response
     */
    public function handle(Request $request, Closure $next)
    {
        $header = $request->header();

        if (isset($header['jogadorhash'])) {
            $hash = $header['jogadorhash'][0];
            $jogador = Jogador::where(['token' =>   $hash])->first();
            $newRequest = $request->merge(['jogador' => $jogador ]);
            return $next($newRequest);
        }
        return ResponseController::json(Type::notAuthentication, Action::login, null, 'hash do jogador não informado');
    }
}
