<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Api\Action;
use App\Http\Controllers\Api\ResponseController;
use App\Http\Controllers\Api\Type;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Admin\Resposta\RespostaIndexRequest;
use App\Models\Resposta;
use Throwable;

class RespostaController extends Controller
{
    private Resposta $resposta;

    public function __construct()
    {
        $this->resposta = new Resposta();
    }

    public function index(RespostaIndexRequest $request)
    {
        try {
            $filters = [];

            if (isset($request->completa)) {
                $filters[] = ['respostas.concluida', '=', $request->completa];
            }

            if (isset($request->jogador_id)) {
                $filters[] = ['respostas.jogador_id', '=', $request->jogador_id];
            }

            if (isset($request->exercicio_id)) {
                $filters[] = ['respostas.exercicio_id', '=', $request->exercicio_id];
            }

            if (isset($request->ativa)) {
                $filters[] = ['respostas.ativa', '=', $request->ativa];
            }
            $resposta = $this->resposta
            ->with('jogador')
            ->with('exercicio')
            ->with('exercicio.recompensa')
            ->where($filters)
            ->paginate(30);
            // $resposta->paginate(30);
            return ResponseController::json(Type::success, Action::index, $resposta);
        } catch(Throwable $e) {
            return ResponseController::json(Type::error, Action::store);
        }
    }
}
