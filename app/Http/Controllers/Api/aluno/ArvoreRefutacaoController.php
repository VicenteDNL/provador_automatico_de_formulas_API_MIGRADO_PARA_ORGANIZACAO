<?php

namespace App\Http\Controllers\Api\aluno;

use App\ExercicioMVFLP;
use App\Formula;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Arvore\Gerador;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Base;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Construcao;
use App\Jogador;
use App\Resposta;
use Illuminate\Http\Request;

class ArvoreRefutacaoController extends Controller
{

    function __construct() {
        $this->gerador = new Gerador;
        $this->constr = new Construcao;
    }


    public function adicionaNo(Request $request){
        try{
     
            $exercicio = ExercicioMVFLP::findOrFail($request->exercicio);
            $formula =  Formula::findOrFail($exercicio->id_formula);
            $res_jog= $this->buscaRespostaJogador($request->usu_hash,$exercicio);

            if(!$res_jog['success']){
                return  response()->json(['success' => false, 'msg'=>$res_jog['msg']],403); 
            }
            
            $arvore = new Base($formula->xml);
            $arvore->setListaPassos($request->inicio['lista']);
            
            if(!$arvore->montarArvore($request->inicio['no']['id'],$request->inicio['negacao'])){
                $tentativa_restante = $this->validaResposta($res_jog['resposta'],$exercicio);
                return  response()->json(['success' => false, 'msg'=>$arvore->getError(), 'data'=>['tentativa_restante'=>$tentativa_restante]]); 
            } 
    
            return  response()->json([
                'success' => true, 
                'msg'=>'', 
                'data'=>$arvore->retorno($exercicio->id,$request->usu_hash, $request->exe_hash)
                ]);
        }catch(\Exception $e){
            return response()->json(['success' => false, 'msg'=>'erro interno', 'data'=>''],500);
        }
    }

    public function derivar(Request $request){

        $exercicio = ExercicioMVFLP::findOrFail($request->exercicio);
        $formula =  Formula::findOrFail($exercicio->id_formula);
        $res_jog= $this->buscaRespostaJogador($request->usu_hash,$exercicio);
        
        if(!$res_jog['success']){
            return  response()->json(['success' => false, 'msg'=>$res_jog['msg']],403); 
        }

        $arvore = new Base($formula->xml);
        // Seta todas as configuracoes da arvore
        $arvore->setAll($request->all(),$formula->fechar_automaticamente,$formula->ticar_automaticamente);
        
        if(!$arvore->derivar($request->derivacao['no']['idNo'],$request->derivacao['folhas'],$request->derivacao['regra'])){
           
            $tentativa_restante = $this->validaResposta($res_jog['resposta'],$exercicio);
            return  response()->json(['success' => false, 'msg'=>$arvore->getError(), 'data'=>['tentativa_restante'=>$tentativa_restante]]); 
        }
 
        return  response()->json([
            'success' => true, 
            'msg'=>'',
             'data'=>$arvore->retorno($exercicio->id,$request->usu_hash, $request->exe_hash)
            ]);
    }

    public function ticarNo(Request $request){
        $exercicio = ExercicioMVFLP::findOrFail($request->idExercicio);
        $formula =  Formula::findOrFail($exercicio->id_formula);
        $res_jog= $this->buscaRespostaJogador($request->usu_hash,$exercicio);

        $arvore = new Base($formula->xml);
        // Seta todas ar configuracoes da arvore
        $arvore->setListaPassos($request->listaInicial );
        $arvore->setListaTicagem($request->listaTicagem);
        $arvore->setListaFechamento($request->listaFechamento);
        $arvore->fecharAutomatido($formula->fechar_automaticamente);
        $arvore->ticarAutomatico($formula->ticar_automaticamente);
        $arvore->derivacao->setListaDerivacoes($request->listaDerivacoes);
        $arvore->inicializacao->setFinalizado(true);

        if(!$arvore->montarArvore()){
            return  response()->json(['success' => false, 'msg'=>$arvore->getError()]); 
        }

        if(!$arvore->ticarNo($request->no)){
            $tentativa_restante = $this->validaResposta($res_jog['resposta'],$exercicio);
            return  response()->json(['success' => false, 'msg'=>$arvore->getError(), 'data'=>['tentativa_restante'=>$tentativa_restante]]); 
        }
        
        return  response()->json([
            'success' => true, 
            'msg'=>'', 
            'data'=>[
                'impresao'=>[
                    'nos'=>$arvore->getListaNo(),
                    'arestas'=>$arvore->getListaAresta()
                ],
                'noticado'=>$request->no]
            ]);
    }

    public function fecharNo(Request $request){
   
        $exercicio = ExercicioMVFLP::findOrFail($request->idExercicio);
        $formula =  Formula::findOrFail($exercicio->id_formula);
        $res_jog= $this->buscaRespostaJogador($request->usu_hash,$exercicio);
        


        $arvore = new Base($formula->xml);
        // Seta todas ar configuracoes da arvore
        $arvore->setListaPassos($request->listaInicial );
        $arvore->setListaTicagem($request->listaTicagem);
        $arvore->setListaFechamento($request->listaFechamento);
        $arvore->fecharAutomatido($formula->fechar_automaticamente);
        $arvore->ticarAutomatico($formula->ticar_automaticamente);
        $arvore->derivacao->setListaDerivacoes($request->listaDerivacoes);
        $arvore->inicializacao->setFinalizado(true);

        if(!$arvore->montarArvore()){
            return  response()->json(['success' => false, 'msg'=>$arvore->getError()]); 
        }

        if(!$arvore->fecharNo($request->noFolha, $request->noContradicao)){
            $tentativa_restante = $this->validaResposta($res_jog['resposta'],$exercicio);
            return  response()->json(['success' => false, 'msg'=>$arvore->getError(), 'data'=>['tentativa_restante'=>$tentativa_restante]]);  
        }

        return  response()->json([
            'success' => true, 
            'msg'=>'', 
            'data'=>[
                'impresao'=>[
                    'nos'=>$arvore->getListaNo(),
                    'arestas'=>$arvore->getListaAresta()
                ],
                'nofechado'=>$request->noFolha, 
                'noContradicao'=>$request->noContradicao]]);
            
    }


    private function validaResposta(Resposta $resposta, ExercicioMVFLP $exercicio){
        if($exercicio->qndt_erros==null){
            return null;
        }
        $resposta->tentativas_invalidas = $resposta->tentativas_invalidas + 1;
        $restantes = ($exercicio->qndt_erros) - ($resposta->tentativas_invalidas) ;
        if($restantes<0){
            return 0;   
        }
        $resposta->save();
        return $restantes;


        
        
    }



    // private function validaResposta(Resposta $resposta, ExercicioMVFLP $exercicio){
    //     $saida=['tempo'=>null, 'erros'=>null];
    //     if($exercicio->qndt_erros==null && $exercicio->tempo==null){
    //         return $saida;
    //     }

    //     if($exercicio->qndt_erros!=null){
            
    //         // verifica a quantidade de tentativas invalidas
    //         $resposta->tentativas_invalidas = $resposta->tentativas_invalidas + 1;
    //         $restantes = ($exercicio->qndt_erros) - ($resposta->tentativas_invalidas) ;
    //         if($restantes<0){
    //             $saida['tempo']=0;
    //         }
    //         $resposta->save();
    //         $saida['tempo']=$restantes;
    //     }

    //     if($exercicio->tempo!=null){
    //         $tempo= $exercicio->tempo *60;
    //         $inicio= strtotime($resposta->created_at)+$tempo;
    //         $atual = strtotime(date("Y-m-d H:i:s"));

    //         if($inicio>$atual){
    //             // calcular tempo restante
    //         }
    //         elseif($inicio<$atual){


    //         }

    //     }



        
        
    // }








    private function buscaRespostaJogador($usu_hash,$exercicio){

        $jogador = Jogador::where('token',$usu_hash)->get();
        if(count($jogador)==0){
            return  ['success' => false, 'msg'=>'Hash jogador Invalido']; 
        }
        $resposta = Resposta::where('id_jogador', '=',$jogador[0]->id)->where('id_exercicio','=',$exercicio->id)->first();
        $restantes = ($exercicio->qndt_erros) - ($resposta->tentativas_invalidas) ;
        return ['success' => true ,'jogador'=>$jogador, 'resposta'=>$resposta, 'restante'=>$restantes];

    }
}
