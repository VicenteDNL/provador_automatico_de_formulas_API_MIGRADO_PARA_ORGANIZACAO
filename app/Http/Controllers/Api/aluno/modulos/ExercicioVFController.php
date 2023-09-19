<?php

namespace App\Http\Controllers\Api\Aluno;

use App\Core\Arvore\Gerador;
use App\Core\Base;
use App\Core\Construcao;
use App\Http\Controllers\Api\Action;
use App\Http\Controllers\Api\ResponseController;
use App\Http\Controllers\Api\Type;
use App\Http\Controllers\Controller;
use App\LogicLive\Modulos\Jogador as ModulosJogador;
use App\Models\ExercicioMVFLP;
use App\Models\Formula;
use App\Models\Jogador;
use Illuminate\Http\Request;

class ExercicioVFController extends Controller
{
    private $exercicio;
    private $gerador;
    private $constr;
    private $config;
    private $logicLive_jogador;
    private $resposta;

    public function __construct(ExercicioMVFLP $exercicio)
    {
        $this->exercicio = $exercicio;
        $this->gerador = new Gerador();
        $this->constr = new Construcao();
        $this->config = new Configuracao();
        $this->logicLive_jogador = new ModulosJogador();
        $this->resposta = new RespostaController();
    }

    public function buscarExercicio(Request $request, $id, Jogador $jogador)
    {
        $exercicio = ExercicioMVFLP::findOrFail($id);

        if ($this->config->ativo()) {
            if ($exercicio->hash != $request->exe_hash || !isset($request->exe_hash)) {
                return ResponseController::json(Type::error, Action::index, null, 'hash exercicio não informado ou invalido');
            }

            if (!isset($request->usu_hash)) {
                return ResponseController::json(Type::error, Action::index, null, 'hash jogador não informado');
            }

            $criadoLogicLive = $this->logicLive_jogador->getJogador($request->usu_hash);

            if ($criadoLogicLive['success'] == false) {
                return ResponseController::json(Type::error, Action::index, null, $criadoLogicLive['msg']);
            }

            $jogador_cadastrado = Jogador::where('logic_live_id', $criadoLogicLive['data']['jog_codigo'])->get();

            if (count($jogador_cadastrado) == 0) {
                $jogador_cadastrado = new Jogador();
                $jogador_cadastrado->nome = $criadoLogicLive['data']['jog_nome'];
                $jogador_cadastrado->usunome = $criadoLogicLive['data']['jog_nome'];
                $jogador_cadastrado->email = $criadoLogicLive['data']['jog_email'];
                $jogador_cadastrado->avatar = $criadoLogicLive['data']['jog_avatar'];
                $jogador_cadastrado->token = $request->usu_hash;
                $jogador_cadastrado->ativo = $criadoLogicLive['data']['jog_ativo'];
                $jogador_cadastrado->provedor = $criadoLogicLive['data']['jog_provedor'];
                $jogador_cadastrado->logic_live_id = $criadoLogicLive['data']['jog_codigo'];
                $jogador_cadastrado->save();
            } else {
                $jogador_cadastrado = $jogador_cadastrado[0];
                $jogador_cadastrado->nome = $criadoLogicLive['data']['jog_nome'];
                $jogador_cadastrado->usunome = $criadoLogicLive['data']['jog_nome'];
                $jogador_cadastrado->email = $criadoLogicLive['data']['jog_email'];
                $jogador_cadastrado->avatar = $criadoLogicLive['data']['jog_avatar'];
                $jogador_cadastrado->token = $request->usu_hash;
                $jogador_cadastrado->ativo = $criadoLogicLive['data']['jog_ativo'];
                $jogador_cadastrado->provedor = $criadoLogicLive['data']['jog_provedor'];
                $jogador_cadastrado->logic_live_id = $criadoLogicLive['data']['jog_codigo'];
                $jogador_cadastrado->save();
            }
        } else {
            $str = rand();
            $mdr = md5($str);
            $jogador_cadastrado = $jogador;
            $jogador_cadastrado->nome = $mdr;
            $jogador_cadastrado->usunome = $mdr;
            $jogador_cadastrado->email = $mdr . '@moduloarvorerefutacao.com';
            $jogador_cadastrado->avatar = '';
            $jogador_cadastrado->token = $mdr;
            $jogador_cadastrado->ativo = true;
            $jogador_cadastrado->provedor = '';
            $jogador_cadastrado->logic_live_id = null;
            $jogador_cadastrado->save();
        }

        $resposta = $this->resposta->criarResposta($jogador_cadastrado, $exercicio);

        if (!$resposta['success']) {
            return ResponseController::json(Type::error, Action::index, null, 'error ao criar resposta exercicio!');
        }

        $validacoes = $this->resposta->validaResposta($resposta['data'], $exercicio, 'buscar', true);

        $formula = Formula::findOrFail($exercicio->formula_id);

        $arvore = new Base($formula->xml);
        $arvore->setListaPassos($formula->lista_passos == [] ? [] : json_decode($formula->lista_passos, true));
        $arvore->setListaTicagem($formula->lista_ticagem == [] ? [] : json_decode($formula->lista_ticagem, true));
        $arvore->setListaFechamento($formula->lista_fechamento == [] ? [] : json_decode($formula->lista_fechamento, true));
        $arvore->derivacao->setListaDerivacoes($formula->lista_derivacoes == [] ? [] : json_decode($formula->lista_derivacoes, true));
        $arvore->fecharAutomatido($formula->fechar_automaticamente);
        $arvore->ticarAutomatico($formula->ticar_automaticamente);
        $arvore->inicializacao->setFinalizado($formula->inicializacao_completa);

        if (!$arvore->montarArvore()) {
            return  ResponseController::json(Type::error, Action::index, null, 'error ao criar arvore');
        }

        return  ResponseController::json(
            Type::success,
            Action::index,
            [
                'exercicio'  => $exercicio,
                'tentativas' => $validacoes,
                'arvore'     => $arvore->retorno($exercicio->id, $jogador_cadastrado->token, $exercicio->hash),
            ]
        );
    }
}
