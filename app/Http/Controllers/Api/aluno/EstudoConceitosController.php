<?php

namespace App\Http\Controllers\Api\Aluno;

use App\Http\Controllers\Api\Action;
use App\Http\Controllers\Api\ResponseController;
use App\Http\Controllers\Api\Type;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Aluno\EstudoConceitos\EstudoConceitosConcluirRequest;
use App\LogicLive\Common\Models\RespostaModel;
use App\LogicLive\Resources\RespostaResource;
use Throwable;

class EstudoConceitosController extends Controller
{
    public function __construct()
    {
    }

    public function concluir(EstudoConceitosConcluirRequest $request)
    {
        try {
            $exercicio = $request->exercicio;
            $jogador = $request->jogador;

            $respostaResource = new RespostaResource($jogador->token);
            $resultado = $respostaResource->create(
                new RespostaModel([
                    'exe_hash'        => $exercicio->hash,
                    'usx_completado'  => true,
                    'uer_log'         => 'exercicio estudo conceitos (árvore) completado',
                    'tempo_exercicio' => null,
                ])
            );

            if (is_null($resultado)) {
                return ResponseController::json(Type::error, Action::update, null, 'erro ao enviar resposta');
            }
            return ResponseController::json(Type::success, Action::update, null, 'concluido');
        } catch(Throwable $e) {
            return ResponseController::json(Type::error, Action::update);
        }
    }
}
