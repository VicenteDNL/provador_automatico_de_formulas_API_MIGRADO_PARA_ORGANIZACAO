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
        if($exercicio->hash!=$request->exe_hash && !isset($request->usu_hash)){
            return response()->json(['success' => false, 'msg'=>'hash exercicio não informado ou invalido!', 'data'=>''],500);
        }
        
        $resposta = $this->resposta->criarResposta($jogador_cadastrado,$exercicio);
        if(!$resposta['success']){

            return response()->json(['success' => false, 'msg'=>'error ao criar resposta exercicio!', 'data'=>''],500);
        }

        if(!$resposta['novo']){
            $tentativa_restante = $this->resposta->buscaResposta($resposta['data'],$exercicio);
        }
        else{
            $tentativa_restante = $exercicio->qndt_erros;
        }

        $formula =  Formula::findOrFail($exercicio->id_formula);

        $arvore = new Base($formula->xml);

        if($formula->iniciar_zerada==true && $formula->inicio_personalizado==false){
            return  response()->json([
                'success' => true, 
                'msg'=>'', 
                'data'=>[
                    'jogador'=>$jogador_cadastrado, 
                    'impresao'=>[
                        'nos'=>$arvore->getListaNo(),
                        'arestas'=>$arvore->getListaAresta()
                    ],
                    'formula'=>$formula, 
                    'exercicio'=>$exercicio, 
                    'listapcoes'=>$arvore->inicializacao->getListaOpcoes(),
                    'strformula'=>$arvore->getStrFormula(),
                    'tentativa_restante'=>$tentativa_restante
                    ]
                ]);
        }else{
            $arvore->setListaPassos($formula->lista_passos = json_decode ($formula->lista_passos,true));
            $arvore->setListaTicagem($formula->lista_ticagem = json_decode ($formula->lista_ticagem,true));
            $arvore->setListaFechamento($formula->lista_fechamento = json_decode ($formula->lista_fechamento,true));
            $arvore->derivacao->setListaDerivacoes($formula->lista_derivacoes = json_decode ($formula->lista_derivacoes,true));
            if(!$arvore->montarArvore()){
                return  response()->json(['success' => false, 'msg'=>'Error ar criar arvore', 'data'=>''],500);
            }
            return  response()->json([
                'success' => true, 
                'msg'=>'', 
                'data'=>[
                    'impresao'=>[
                        'nos'=>$arvore->getListaNo(),
                        'arestas'=>$arvore->getListaAresta()
                    ],
                    'formula'=>$formula, 
                    'exercicio'=>$exercicio]]);
        }
        

    }

    public function deletarResposta($id,Request $request){

        if(!isset($request->usu_hash)){
            return response()->json(['success' => false, 'msg'=>'hash jogador não informado!', 'data'=>''],500);
        }

        $criadoLogicLive = $this->logicLive_jogador->getJogador($request->usu_hash);
        $jogador_cadastrado = Jogador::where('id_logic_live',$criadoLogicLive['data']['jog_codigo'])->get();
        $exercicio = ExercicioMVFLP::findOrFail($id); 

        $deletar_tentativas = $this->resposta->deletarResposta($jogador_cadastrado[0],$exercicio);

        if(!$deletar_tentativas['success']){
            return response()->json(['success' => false, 'msg'=>'error ao reiniciar exercicio', 'data'=>''],500);
        }
        $tentativa_restante = $this->resposta->buscaResposta($deletar_tentativas['data'],$exercicio);
        return response()->json(['success' => true, 'msg'=>'Exercicio reiniciado resposta', 'data'=>['tentativa_restante'=>$tentativa_restante]]);
    }





}
