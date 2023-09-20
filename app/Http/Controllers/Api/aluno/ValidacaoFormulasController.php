<?php

namespace App\Http\Controllers\Api\Aluno;

use App\Core\Base;
use App\Core\Common\Models\Steps\PassoDerivacao;
use App\Core\Common\Models\Steps\PassoFechamento;
use App\Core\Common\Models\Steps\PassoFinalizacao;
use App\Core\Common\Models\Steps\PassoInicializacao;
use App\Core\Common\Models\Steps\PassoTicagem;
use App\Http\Controllers\Api\Action;
use App\Http\Controllers\Api\ResponseController;
use App\Http\Controllers\Api\Type;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Common\ArvoreRefutacao\ArvoreRefutacaoAdicionaRequest;
use App\Http\Requests\API\Common\ArvoreRefutacao\ArvoreRefutacaoDerivaRequest;
use App\Http\Requests\API\Common\ArvoreRefutacao\ArvoreRefutacaoFechaRequest;
use App\Http\Requests\API\Common\ArvoreRefutacao\ArvoreRefutacaoFinalizaRequest;
use App\Http\Requests\API\Common\ArvoreRefutacao\ArvoreRefutacaoTicaRequest;
use App\LogicLive\Common\Models\RespostaModel;
use App\LogicLive\Resources\RespostaResource;
use App\Models\Formula;
use App\Models\Recompensa;
use App\Models\Resposta;
use DateTime;
use Illuminate\Http\Request;
use Throwable;

class ValidacaoFormulasController extends Controller
{
    private RespostaController $resposta;

    public function __construct()
    {
        $this->resposta = new RespostaController();
    }

    /**
     * @param  Request  $request
     * @return Response
     */
    public function inicia(Request $request)
    {
        try {
            $exercicio = $request->exercicio;
            $jogador = $request->jogador;
            $formula = Formula::findOrFail($exercicio->formula_id);
            $arvore = new Base($formula->xml);
            $arvore->carregarCamposEssenciais([
                'arvore' => [
                    'iniciar' => [
                        'passosExecutados' => $formula->lista_passos == [] ? [] : json_decode($formula->lista_passos, true),
                    ],
                    'derivar' => [
                        'passosExecutados' => $formula->lista_derivacoes == [] ? [] : json_decode($formula->lista_derivacoes, true),
                    ],
                    'ticar'   => [
                        'passosExecutados' => $formula->lista_ticagem == [] ? [] : json_decode($formula->lista_ticagem, true),
                        'isAutomatico'     => $formula->ticar_automaticamente,
                    ],
                    'fechar'  => [
                        'passosExecutados' => $formula->lista_fechamento == [] ? [] : json_decode($formula->lista_fechamento, true),
                        'isAutomatico'     => $formula->fechar_automaticamente,
                    ],
                ],
            ]);

            if (!$arvore->reconstruirPassos()) {
                return ResponseController::json(Type::error, Action::show, null, 'erro ao reconstruir árvore');
            }

            $isNovaResposta = false;
            $resposta = Resposta::where(['jogador_id' => $jogador->id, 'exercicio_id' => $exercicio->id])->first();

            if (is_null($resposta)) {
                $recompensa = Recompensa::where(['id' => $exercicio->recompensa_id])->first();

                $isNovaResposta = true;
                $resposta = new Resposta();
                $resposta->jogador_id = $jogador->id;
                $resposta->exercicio_id = $exercicio->id;
                $resposta->ativa = true;
                $resposta->tentativas_invalidas = 0;
                $resposta->tempo = 0;
                $resposta->ultima_interacao = date('Y-m-d H:i:s');
                $resposta->concluida = false;
                $resposta->pontuacao = $recompensa->pontuacao;
                $resposta->repeticao = 0;
                $resposta->save();
            }

            return  ResponseController::json(
                Type::success,
                Action::show,
                [
                    'exercicio'  => $exercicio,
                    'saude'      => $this->resposta->saudeResposta($jogador, $exercicio, $isNovaResposta),
                    'arvore'     => $arvore->imprimir(),
                ]
            );
        } catch(Throwable $e) {
            return ResponseController::json(Type::error, Action::show);
        }
    }

    public function adiciona(ArvoreRefutacaoAdicionaRequest $request)
    {
        try {
            $exercicio = $request->exercicio;
            $jogador = $request->jogador;

            $arvore = new Base($request->arvore['formula']['xml']);
            $arvore->carregarCamposEssenciais($request->all());
            $passo = new PassoInicializacao($request->passo);

            if (!$arvore->tentativaInicializacao($passo)) {
                $this->resposta->aplicarPenalidade($jogador, $exercicio);
                return ResponseController::json(
                    Type::error,
                    Action::store,
                    ['saude' => $this->resposta->saudeResposta($jogador, $exercicio)],
                    $arvore->getErro()
                );
            }
            return ResponseController::json(
                Type::success,
                Action::store,
                [
                    'exercicio'  => $exercicio,
                    'saude'      => $this->resposta->saudeResposta($jogador, $exercicio),
                    'arvore'     => $arvore->imprimir(),
                ]
            );
        } catch(Throwable $e) {
            return ResponseController::json(Type::exception, Action::store);
        }
    }

    public function deriva(ArvoreRefutacaoDerivaRequest $request)
    {
        try {
            $exercicio = $request->exercicio;
            $jogador = $request->jogador;

            $arvore = new Base($request->arvore['formula']['xml']);
            $arvore->carregarCamposEssenciais($request->all());
            $passo = new PassoDerivacao($request->passo);

            if (!$arvore->tentativaDerivacao($passo)) {
                $this->resposta->aplicarPenalidade($jogador, $exercicio);
                return ResponseController::json(
                    Type::error,
                    Action::store,
                    ['saude' => $this->resposta->saudeResposta($jogador, $exercicio)],
                    $arvore->getErro()
                );
            }
            return ResponseController::json(
                Type::success,
                Action::store,
                [
                    'exercicio'  => $exercicio,
                    'saude'      => $this->resposta->saudeResposta($jogador, $exercicio),
                    'arvore'     => $arvore->imprimir(),
                ]
            );
        } catch(Throwable $e) {
            return ResponseController::json(Type::exception, Action::store);
        }
    }

    public function tica(ArvoreRefutacaoTicaRequest $request)
    {
        try {
            $exercicio = $request->exercicio;
            $jogador = $request->jogador;

            $arvore = new Base($request->arvore['formula']['xml']);
            $arvore->carregarCamposEssenciais($request->all());
            $passo = new PassoTicagem($request->passo);

            if (!$arvore->tentativaTicagem($passo)) {
                $this->resposta->aplicarPenalidade($jogador, $exercicio);
                return ResponseController::json(
                    Type::error,
                    Action::store,
                    ['saude' => $this->resposta->saudeResposta($jogador, $exercicio)],
                    $arvore->getErro()
                );
            }
            return ResponseController::json(
                Type::success,
                Action::store,
                [
                    'exercicio'  => $exercicio,
                    'saude'      => $this->resposta->saudeResposta($jogador, $exercicio),
                    'arvore'     => $arvore->imprimir(),
                ]
            );
        } catch(Throwable $e) {
            return ResponseController::json(Type::exception, Action::store);
        }
    }

    public function fecha(ArvoreRefutacaoFechaRequest $request)
    {
        try {
            $exercicio = $request->exercicio;
            $jogador = $request->jogador;

            $arvore = new Base($request->arvore['formula']['xml']);
            $arvore->carregarCamposEssenciais($request->all());
            $passo = new PassoFechamento($request->passo);

            if (!$arvore->tentativaFechamento($passo)) {
                $this->resposta->aplicarPenalidade($jogador, $exercicio);
                return ResponseController::json(
                    Type::error,
                    Action::store,
                    ['saude' => $this->resposta->saudeResposta($jogador, $exercicio)],
                    $arvore->getErro()
                );
            }
            return ResponseController::json(
                Type::success,
                Action::store,
                [
                    'exercicio'  => $exercicio,
                    'saude'      => $this->resposta->saudeResposta($jogador, $exercicio),
                    'arvore'     => $arvore->imprimir(),
                ]
            );
        } catch(Throwable $e) {
            return ResponseController::json(Type::exception, Action::update);
        }
    }

    public function reiniciar(Request $request)
    {
        try {
            $exercicio = $request->exercicio;
            $jogador = $request->jogador;
            $formula = Formula::findOrFail($exercicio->formula_id);

            $arvore = new Base($formula->xml);
            $arvore->carregarCamposEssenciais([
                'arvore' => [
                    'iniciar' => [
                        'passosExecutados' => $formula->lista_passos == [] ? [] : json_decode($formula->lista_passos, true),
                    ],
                    'derivar' => [
                        'passosExecutados' => $formula->lista_derivacoes == [] ? [] : json_decode($formula->lista_derivacoes, true),
                    ],
                    'ticar'   => [
                        'passosExecutados' => $formula->lista_ticagem == [] ? [] : json_decode($formula->lista_ticagem, true),
                        'isAutomatico'     => $formula->ticar_automaticamente,
                    ],
                    'fechar'  => [
                        'passosExecutados' => $formula->lista_fechamento == [] ? [] : json_decode($formula->lista_fechamento, true),
                        'isAutomatico'     => $formula->fechar_automaticamente,
                    ],
                ],
            ]);

            if (!$arvore->reconstruirPassos()) {
                return ResponseController::json(Type::error, Action::show, null, 'erro ao reconstruir árvore');
            }

            $this->resposta->aplicarNovaTentativa($jogador, $exercicio);

            return  ResponseController::json(
                Type::success,
                Action::show,
                [
                    'exercicio'  => $exercicio,
                    'saude'      => $this->resposta->saudeResposta($jogador, $exercicio, true),
                    'arvore'     => $arvore->imprimir(),
                ]
            );
        } catch(Throwable $e) {
            return ResponseController::json(Type::exception, Action::update);
        }
    }

    public function concluir(ArvoreRefutacaoFinalizaRequest $request)
    {
        try {
            $dateTime = new DateTime(date('Y-m-d H:i:s'));
            $exercicio = $request->exercicio;
            $jogador = $request->jogador;

            $resposta = Resposta::where(['jogador_id' => $jogador->id, 'exercicio_id' => $exercicio->id])->firstOrFail();

            if ($resposta->concluida) {
                return ResponseController::json(Type::exception, Action::store, null, 'este exercício já foi concluído');
            }

            $arvore = new Base($request->arvore['formula']['xml']);
            $arvore->carregarCamposEssenciais($request->all());
            $passo = new PassoFinalizacao($request->passo);

            if (!$arvore->tentativaFinalizacao($passo)) {
                $this->resposta->aplicarPenalidade($jogador, $exercicio);
                return ResponseController::json(
                    Type::error,
                    Action::store,
                    ['saude'     => $this->resposta->saudeResposta($jogador, $exercicio)],
                    $arvore->getErro()
                );
            }

            $respostaResource = new RespostaResource($jogador->token);
            $resultado = $respostaResource->create(
                new RespostaModel([
                    'exe_hash'        => $exercicio->hash,
                    'usx_completado'  => true,
                    'uer_log'         => 'exercicio validacao fórmula completado',
                    'tempo_exercicio' => null,
                ])
            );

            if (is_null($resultado)) {
                return ResponseController::json(Type::error, Action::update, null, 'erro ao enviar resposta');
            }
            $this->resposta->finalizarExercicio($dateTime, $jogador, $exercicio);

            return ResponseController::json(Type::success, Action::store);
        } catch(Throwable $e) {
            return ResponseController::json(Type::exception, Action::store);
        }
    }
}
