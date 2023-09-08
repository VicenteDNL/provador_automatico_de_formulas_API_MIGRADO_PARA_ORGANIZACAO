<?php

namespace App\Http\Controllers\Api\Aluno;

use App\Core\Base;
use App\Http\Controllers\Api\Action;
use App\Http\Controllers\Api\ResponseController;
use App\Http\Controllers\Api\Type;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Aluno\EstudoLivre\EstudoLivreConcluirRequest;
use App\LogicLive\Common\Enums\Types;
use App\LogicLive\Common\Models\RespostaModel;
use App\LogicLive\Managers\EstudoLivre\EstudoLivreExercicio;
use App\LogicLive\Resources\RespostaResource;
use App\Models\LogicLive;
use Throwable;

class EstudoLivreController extends Controller
{
    public function concluir(EstudoLivreConcluirRequest $request)
    {
        try {
            $exercicio = LogicLive::where([
                'tipo'   => Types::EXERCICIO->descricao(),
                'hash'   => $request->exeHash,
                'modelo' => EstudoLivreExercicio::class,
            ])->first();

            if (is_null($exercicio)) {
                return ResponseController::json(Type::error, Action::update, null, 'exercicio não encontrado');
            }

            $arvore = new Base($request->arvore['formula']['xml']);
            $arvore->carregarCamposEssenciais($request->all());

            if (!$arvore->reconstruirPassos()) {
                return ResponseController::json(Type::error, Action::store, null, $arvore->getErro());
            }

            if (!$arvore->isFinalizada()) {
                return ResponseController::json(Type::error, Action::store, null, 'Derivação não finalizada');
            };

            $respostaResource = new RespostaResource($request->usuHash);
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
        } catch(Throwable $e) {
            return ResponseController::json(Type::error, Action::update);
        }
    }
}
