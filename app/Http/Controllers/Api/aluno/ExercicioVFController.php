<?php

namespace App\Http\Controllers\Api\aluno;

use App\Http\Controllers\Controller;
use App\ExercicioMVFLP;
use App\Formula;
use App\Http\Controllers\LogicLive\config\Configuracao;
use App\Http\Controllers\LogicLive\modulos\Jogador as ModulosJogador;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Arvore\Gerador;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Base;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Construcao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Formula\Argumento;
use App\Jogador;
use Illuminate\Http\Request;

class ExercicioVFController extends Controller
{


    public function __construct(ExercicioMVFLP $exercicio)
    {
        $this->exercicio = $exercicio; 
        $this->arg = new Argumento;
        $this->gerador = new Gerador;
        $this->constr = new Construcao;
        $this->config = new Configuracao;
        $this->logicLive_jogador =  new ModulosJogador;
        $this->resposta = new RespostaController;
 
    }


    public function buscarExercicio(Request $request,$id,  Jogador $jogador){

        if(!isset($request->usu_hash)){
            return response()->json(['success' => false, 'msg'=>'hash jogador não informado!', 'data'=>''],500);
        }

        if($this->config->ativo()){
            $criadoLogicLive = $this->logicLive_jogador->getJogador($request->usu_hash);
            if($criadoLogicLive['success']=false){
                return response()->json(['success' => false, 'msg'=>$criadoLogicLive['msg'], 'data'=>''],500);
            }

            $jogador_cadastrado = Jogador::where('id_logic_live',$criadoLogicLive['data']['jog_codigo'])->get(); 
            if(count($jogador_cadastrado)==0){
                $jogador_cadastrado = $jogador;
                $jogador_cadastrado->nome=$criadoLogicLive['data']['jog_nome'];
                $jogador_cadastrado->usunome=$criadoLogicLive['data']['jog_nome'];
                $jogador_cadastrado->email=$criadoLogicLive['data']['jog_email'];
                $jogador_cadastrado->avatar=$criadoLogicLive['data']['jog_avatar'];
                $jogador_cadastrado->token=$request->usu_hash;
                $jogador_cadastrado->ativo=$criadoLogicLive['data']['jog_ativo'];
                $jogador_cadastrado->provedor=$criadoLogicLive['data']['jog_provedor'];
                $jogador_cadastrado->id_logic_live=$criadoLogicLive['data']['jog_codigo'];
                $jogador_cadastrado->save();
            }
            else{
                $jogador_cadastrado= $jogador_cadastrado[0];
                $jogador_cadastrado->nome=$criadoLogicLive['data']['jog_nome'];
                $jogador_cadastrado->usunome=$criadoLogicLive['data']['jog_nome'];
                $jogador_cadastrado->email=$criadoLogicLive['data']['jog_email'];
                $jogador_cadastrado->avatar=$criadoLogicLive['data']['jog_avatar'];
                $jogador_cadastrado->token=$request->usu_hash;
                $jogador_cadastrado->ativo=$criadoLogicLive['data']['jog_ativo'];
                $jogador_cadastrado->provedor=$criadoLogicLive['data']['jog_provedor'];
                $jogador_cadastrado->id_logic_live=$criadoLogicLive['data']['jog_codigo'];
                $jogador_cadastrado->save();
            }

        }

        $exercicio = ExercicioMVFLP::findOrFail($id);
        if($exercicio->hash!=$request->exe_hash || !isset($request->exe_hash)){
            return response()->json(['success' => false, 'msg'=>'hash exercicio não informado ou invalido!', 'data'=>''],500);
        }
        
        $resposta = $this->resposta->criarResposta($jogador_cadastrado,$exercicio);
        if(!$resposta['success']){
            return response()->json(['success' => false, 'msg'=>'error ao criar resposta exercicio!', 'data'=>''],500);
        }

        $validacoes = $this->resposta->validaResposta($resposta['data'],$exercicio,'buscar',true);

        $formula =  Formula::findOrFail($exercicio->id_formula);

        $arvore = new Base($formula->xml);
        // $arvore->setListaPassos( json_decode ($formula->lista_passos,true));
        $arvore->setListaPassos( $formula->lista_passos==[] ? [] :json_decode ($formula->lista_passos,true));
        $arvore->setListaTicagem($formula->lista_ticagem==[] ? [] : json_decode ($formula->lista_ticagem,true));
        $arvore->setListaFechamento( $formula->lista_fechamento==[]?[] : json_decode ($formula->lista_fechamento,true));
        $arvore->derivacao->setListaDerivacoes($formula->lista_derivacoes==[] ? [] : json_decode ($formula->lista_derivacoes,true));
        $arvore->fecharAutomatido($formula->fechar_automaticamente);
        $arvore->ticarAutomatico($formula->ticar_automaticamente);
        $arvore->inicializacao->setFinalizado($formula->inicializacao_completa);
        if(!$arvore->montarArvore()){
            return  response()->json(['success' => false, 'msg'=>'Error ar criar arvore', 'data'=>''],500);
        }

        return  response()->json([
            'success' => true, 
            'msg'=>'', 
            'data'=>[
                'exercicio'=>$exercicio, 
                'tentativas'=>$validacoes,
                'arvore'=>$arvore->retorno($exercicio->id,$request->usu_hash, $request->exe_hash)
                ]
            ]);

    }







}
