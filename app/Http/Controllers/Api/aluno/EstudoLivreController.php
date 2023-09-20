<?php

namespace App\Http\Controllers\Api\Aluno;

use App\Core\Base;
use App\Core\Common\Models\Steps\PassoFinalizacao;
use App\Http\Controllers\Api\Action;
use App\Http\Controllers\Api\ResponseController;
use App\Http\Controllers\Api\Type;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Common\ArvoreRefutacao\ArvoreRefutacaoFinalizaRequest;
use App\LogicLive\Common\Models\RespostaModel;
use App\LogicLive\Resources\RespostaResource;
use Throwable;

class EstudoLivreController extends Controller
{
    public function concluir(ArvoreRefutacaoFinalizaRequest $request)
    {
        try {
            $exercicio = $request->exercicio;
            $jogador = $request->jogador;

            $arvore = new Base($request->arvore['formula']['xml']);
            $arvore->carregarCamposEssenciais($request->all());
            $passo = new PassoFinalizacao($request->passo);

            if (!$arvore->tentativaFinalizacao($passo)) {
                return ResponseController::json(Type::error, Action::store, $arvore->getErro());
            }

            $respostaResource = new RespostaResource($jogador->token);
            $resultado = $respostaResource->create(
                new RespostaModel([
                    'exe_hash'        => $exercicio->hash,
                    'usx_completado'  => true,
                    'uer_log'         => 'exercicio estudo livre (árvore) completado',
                    'tempo_exercicio' => null,
                ])
            );

            if (is_null($resultado)) {
                return ResponseController::json(Type::error, Action::update, null, 'erro ao enviar resposta');
            }
            return ResponseController::json(Type::success, Action::update, null, 'concluido');
        } catch (Throwable $e) {
            return ResponseController::json(Type::error, Action::update);
        }
    }
}
