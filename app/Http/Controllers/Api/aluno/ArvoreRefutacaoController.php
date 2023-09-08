<?php

namespace App\Http\Controllers\Api\Aluno;

use App\Core\Arvore\Gerador;
use App\Core\Base;
use App\Core\Common\Exceptions\ArvoreDeRefutacaoExecption;
use App\Core\Common\Models\Steps\PassoDerivacao;
use App\Core\Common\Models\Steps\PassoFechamento;
use App\Core\Common\Models\Steps\PassoInicializacao;
use App\Core\Common\Models\Steps\PassoTicagem;
use App\Core\Construcao;
use App\Http\Controllers\Api\Action;
use App\Http\Controllers\Api\ResponseController;
use App\Http\Controllers\Api\Type;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\Common\ArvoreRefutacao\ArvoreRefutacaoAdicionaRequest;
use App\Http\Requests\API\Common\ArvoreRefutacao\ArvoreRefutacaoDerivaRequest;
use App\Http\Requests\API\Common\ArvoreRefutacao\ArvoreRefutacaoFechaRequest;
use App\Http\Requests\API\Common\ArvoreRefutacao\ArvoreRefutacaoIniciaRequest;
use App\Http\Requests\API\Common\ArvoreRefutacao\ArvoreRefutacaoOtimizadaRequest;
use App\Http\Requests\API\Common\ArvoreRefutacao\ArvoreRefutacaoRecriarRequest;
use App\Http\Requests\API\Common\ArvoreRefutacao\ArvoreRefutacaoTicaRequest;
use App\LogicLive\Modulos\Resposta as ModulosResposta;
use App\Models\ExercicioMVFLP;
use App\Models\Formula;
use App\Models\Jogador;
use App\Models\Resposta;
use Exception;
use Illuminate\Http\Request;
use Throwable;

class ArvoreRefutacaoController extends Controller
{
    // private $gerador;
    // private $constr;
    // private $resposta;
    // private $logiclive_resposta;
    // private $config;

    // public function __construct()
    // {
    //     $this->gerador = new Gerador();
    //     $this->constr = new Construcao();
    //     $this->resposta = new RespostaController();
    //     $this->logiclive_resposta = new ModulosResposta();
    //     $this->config = new Configuracao();
    // }

    public function arvore(ArvoreRefutacaoOtimizadaRequest $request)
    {
        try {
            $arvore = new Base($request->xml);

            if (!$arvore->arvoreOtimizada()) {
                return ResponseController::json(Type::error, Action::store, null, $arvore->getErro());
            }

            return ResponseController::json(Type::success, Action::store, $arvore->imprimirArvore($request['exibirLinhas'] ?? true));
        } catch(Throwable $e) {
            return ResponseController::json(Type::exception, Action::store);
        }
    }

    public function recria(ArvoreRefutacaoRecriarRequest $request)
    {
        try {
            $arvore = new Base($request->arvore['formula']['xml']);
            $arvore->carregarCamposEssenciais($request->all());

            if (!$arvore->reconstruirPassos()) {
                return ResponseController::json(Type::error, Action::store, null, $arvore->getErro());
            }
            return ResponseController::json(Type::success, Action::store, $arvore->imprimir());
        } catch(Throwable $e) {
            return ResponseController::json(Type::exception, Action::store);
        }
    }

    public function inicia(ArvoreRefutacaoIniciaRequest $request)
    {
        try {
            $arvore = new Base($request->xml);
            return ResponseController::json(Type::success, Action::index, $arvore->imprimir());
        } catch(ArvoreDeRefutacaoExecption $e) {
            return ResponseController::json(Type::error, Action::index, null, $e->getMessage());
        } catch(Throwable $e) {
            return ResponseController::json(Type::exception, Action::index);
        }
    }

    public function adiciona(ArvoreRefutacaoAdicionaRequest $request)
    {
        try {
            $arvore = new Base($request->arvore['formula']['xml']);
            $arvore->carregarCamposEssenciais($request->all());
            $passo = new PassoInicializacao($request->passo);

            if (!$arvore->tentativaInicializacao($passo)) {
                return ResponseController::json(Type::error, Action::store, null, $arvore->getErro());
            }
            return ResponseController::json(Type::success, Action::store, $arvore->imprimir());
        } catch(Throwable $e) {
            return ResponseController::json(Type::exception, Action::store);
        }
    }

    public function deriva(ArvoreRefutacaoDerivaRequest $request)
    {
        try {
            $arvore = new Base($request->arvore['formula']['xml']);
            $arvore->carregarCamposEssenciais($request->all());
            $passo = new PassoDerivacao($request->passo);

            if (!$arvore->tentativaDerivacao($passo)) {
                return ResponseController::json(Type::error, Action::store, null, $arvore->getErro());
            }
            return ResponseController::json(Type::success, Action::store, $arvore->imprimir());
        } catch(Throwable $e) {
            return ResponseController::json(Type::exception, Action::store);
        }
    }

    public function tica(ArvoreRefutacaoTicaRequest $request)
    {
        try {
            $arvore = new Base($request->arvore['formula']['xml']);
            $arvore->carregarCamposEssenciais($request->all());

            $passo = new PassoTicagem($request->passo);

            if (!$arvore->tentativaTicagem($passo)) {
                return ResponseController::json(Type::error, Action::update, null, $arvore->getErro());
            }
            return ResponseController::json(Type::success, Action::update, $arvore->imprimir());
        } catch(Throwable $e) {
            return ResponseController::json(Type::exception, Action::update);
        }
    }

    public function fecha(ArvoreRefutacaoFechaRequest $request)
    {
        try {
            $arvore = new Base($request->arvore['formula']['xml']);
            $arvore->carregarCamposEssenciais($request->all());
            $passo = new PassoFechamento($request->passo);

            if (!$arvore->tentativaFechamento($passo)) {
                return ResponseController::json(Type::error, Action::update, null, $arvore->getErro());
            }
            return ResponseController::json(Type::success, Action::update, $arvore->imprimir());
        } catch(Throwable $e) {
            return ResponseController::json(Type::exception, Action::update);
        }
    }

    // public function validar(Request $request)
    // {
    //     $exercicio = ExercicioMVFLP::findOrFail($request->exercicio);
    //     $formula = Formula::findOrFail($exercicio->formula_id);
    //     $res_jog = $this->buscaRespostaJogador($request->usu_hash, $exercicio);

    //     if (!$res_jog['success']) {
    //         return  response()->json(['success' => false, 'msg' => $res_jog['msg']], 403);
    //     }

    //     $arvore = new Base($formula->xml);
    //     $arvore->setAll($request->all(), $formula->fechar_automaticamente, $formula->ticar_automaticamente);

    //     if (!$arvore->montarArvore()) {
    //         return  response()->json(['success' => false, 'msg' => $arvore->getError()]);
    //     }

    //     $resposta = $arvore->retorno($exercicio->id, $request->usu_hash, $request->exe_hash);

    //     if (!$resposta['finalizada']) {
    //         return  response()->json(['success' => false, 'msg' => 'exercicio nao finalizado'], 500);
    //     }

    //     if (!$arvore->validar()) {
    //         return  response()->json(['success' => false, 'msg' => $arvore->getError()]);
    //     }

    //     if ($arvore->getResposta() == $request->resposta) {
    //         $dados = [
    //             'exe_hash'        => $exercicio->hash,
    //             'usx_completado'  => true,
    //             'uer_log'         => 'exercicio completado',
    //             'tempo_exercicio' => $this->resposta->tempoParaResposta($res_jog['resposta'], $exercicio),
    //             'rec_pontuacao'   => $res_jog['resposta']->pontuacao,
    //         ];
    //         $this->logiclive_resposta->enviarResposta($dados, $request->usu_hash);
    //         $res_jog['resposta']->concluida = false;
    //         $res_jog['resposta']->save();
    //         return  response()->json([
    //             'success' => true,
    //             'msg'     => $arvore->getError(),
    //             'data'    => null]);
    //     } else {
    //         $resposta = $this->resposta->validaResposta($res_jog['resposta'], $exercicio, 'responder');
    //         $dados = [
    //             'exe_hash'        => $exercicio->hash,
    //             'usx_completado'  => false,
    //             'uer_log'         => 'exercicio completado',
    //             'tempo_exercicio' => $this->resposta->tempoParaResposta($res_jog['resposta'], $exercicio),
    //             'rec_pontuacao'   => $resposta['pontuacao']['ponto'],
    //         ];
    //         $this->logiclive_resposta->enviarResposta($dados, $request->usu_hash);
    //         return  response()->json([
    //             'success' => false,
    //             'msg'     => $arvore->getError(),
    //             'data'    => $resposta]);
    //     }
    // }

    // public function adicionaNo(Request $request)
    // {
    //     try {
    //         $exercicio = ExercicioMVFLP::findOrFail($request->exercicio_id);
    //         $formula = Formula::findOrFail($exercicio->formula_id);

    //         $res_jog = $this->buscaRespostaJogador($request->usu_hash, $exercicio);

    //         if (!$res_jog['success']) {
    //             return   ResponseController::json(Type::error, Action::index, null, $res_jog['msg']);
    //         }

    //         $arvore = new Base($formula->xml);
    //         $arvore->setListaPassos($request->inicio['lista']);
    //         $arvore->ticarAutomatico($formula->ticar_automaticamente);
    //         $arvore->fecharAutomatido($formula->fechar_automaticamente);

    //         if (!$arvore->montarArvore($request->inicio['no']['id'], $request->inicio['negacao'])) {
    //             return  response()->json([
    //                 'success' => false,
    //                 'msg'     => $arvore->getError(),
    //                 'data'    => $this->resposta->validaResposta($res_jog['resposta'], $exercicio, 'adicionar'),
    //             ]);
    //         }

    //         return  response()->json([
    //             'success' => true,
    //             'msg'     => '',
    //             'data'    => $arvore->retorno($exercicio->id, $request->usu_hash, $request->exe_hash),
    //         ]);
    //     } catch(Exception $e) {
    //         return response()->json(['success' => false, 'msg' => 'erro interno', 'data' => ''], 500);
    //     }
    // }

    // public function derivar(Request $request)
    // {
    //     $exercicio = ExercicioMVFLP::findOrFail($request->exercicio_id);
    //     $formula = Formula::findOrFail($exercicio->formula_id);

    //     $res_jog = $this->buscaRespostaJogador($request->usu_hash, $exercicio);

    //     if (!$res_jog['success']) {
    //         return  response()->json(['success' => false, 'msg' => $res_jog['msg']], 403);
    //     }

    //     $arvore = new Base($formula->xml);
    //     // Seta todas as configuracoes da arvore
    //     $arvore->setAll($request->all(), $formula->fechar_automaticamente, $formula->ticar_automaticamente);

    //     if (!$arvore->derivar($request->derivacao['no']['idNo'], $request->derivacao['folhas'], $request->derivacao['regra'])) {
    //         return  response()->json([
    //             'success' => false,
    //             'msg'     => $arvore->getError(),
    //             'data'    => $this->resposta->validaResposta($res_jog['resposta'], $exercicio, 'derivar')]);
    //     }

    //     return  response()->json([
    //         'success' => true,
    //         'msg'     => '',
    //         'data'    => $arvore->retorno($exercicio->id, $request->usu_hash, $request->exe_hash),
    //     ]);
    // }

    // public function ticarNo(Request $request)
    // {
    //     $exercicio = ExercicioMVFLP::findOrFail($request->exercicio_id);
    //     $formula = Formula::findOrFail($exercicio->formula_id);
    //     $res_jog = $this->buscaRespostaJogador($request->usu_hash, $exercicio);

    //     $arvore = new Base($formula->xml);
    //     // Seta todas ar configuracoes da arvore
    //     $arvore->setAll($request->all(), $formula->fechar_automaticamente, $formula->ticar_automaticamente);

    //     if (!$arvore->montarArvore()) {
    //         return  response()->json(['success' => false, 'msg' => $arvore->getError()]);
    //     }

    //     if (!$arvore->ticarNo($request->ticar['no'])) {
    //         return  response()->json([
    //             'success' => false,
    //             'msg'     => $arvore->getError(),
    //             'data'    => $this->resposta->validaResposta($res_jog['resposta'], $exercicio), 'ticar']);
    //     }

    //     return  response()->json([
    //         'success' => true,
    //         'msg'     => '',
    //         'data'    => $arvore->retorno($exercicio->id, $request->usu_hash, $request->exe_hash),
    //     ]);
    // }

    // public function fecharNo(Request $request)
    // {
    //     $exercicio = ExercicioMVFLP::findOrFail($request->exercicio_id);
    //     $formula = Formula::findOrFail($exercicio->formula_id);
    //     $res_jog = $this->buscaRespostaJogador($request->usu_hash, $exercicio);

    //     $arvore = new Base($formula->xml);
    //     // Seta todas ar configuracoes da arvore
    //     $arvore->setAll($request->all(), $formula->fechar_automaticamente, $formula->ticar_automaticamente);

    //     if (!$arvore->montarArvore()) {
    //         return  response()->json(['success' => false, 'msg' => $arvore->getError()]);
    //     }

    //     if (!$arvore->fecharNo($request->fechar['folha'], $request->fechar['no'])) {
    //         return  response()->json([
    //             'success' => false,
    //             'msg'     => $arvore->getError(),
    //             'data'    => $this->resposta->validaResposta($res_jog['resposta'], $exercicio), 'fechar']);
    //     }

    //     return  response()->json([
    //         'success' => true,
    //         'msg'     => '',
    //         'data'    => $arvore->retorno($exercicio->id, $request->usu_hash, $request->exe_hash),
    //     ]);
    // }

    // private function buscaRespostaJogador($usu_hash, $exercicio)
    // {
    //     $jogador = Jogador::where('token', $usu_hash)->get();

    //     if (count($jogador) == 0) {
    //         return  ['success' => false, 'msg' => 'Hash jogador Invalido'];
    //     }
    //     $resposta = Resposta::where('jogador_id', '=', $jogador[0]->id)->where('exercicio_id', '=', $exercicio->id)->first();

    //     return ['success' => true, 'jogador' => $jogador, 'resposta' => $resposta];
    // }
}
