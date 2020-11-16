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
        $this->resposta = new RespostaController;
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
            $arvore->ticarAutomatico($formula->ticar_automaticamente);
            $arvore->fecharAutomatido($formula->fechar_automaticamente);
            
            if(!$arvore->montarArvore($request->inicio['no']['id'],$request->inicio['negacao'])){
                return  response()->json([
                    'success' => false, 
                    'msg'=>$arvore->getError(), 
                    'data'=>$this->resposta->validaResposta($res_jog['resposta'],$exercicio,'adicionar')
                    ]); 
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
            return  response()->json([
                'success' => false, 
                'msg'=>$arvore->getError(), 
                'data'=>$this->resposta->validaResposta($res_jog['resposta'],$exercicio,'derivar')]); 
        }
 
        return  response()->json([
            'success' => true, 
            'msg'=>'',
             'data'=>$arvore->retorno($exercicio->id,$request->usu_hash, $request->exe_hash)
            ]);
    }

    public function ticarNo(Request $request){
        $exercicio = ExercicioMVFLP::findOrFail($request->exercicio);
        $formula =  Formula::findOrFail($exercicio->id_formula);
        $res_jog= $this->buscaRespostaJogador($request->usu_hash,$exercicio);

        $arvore = new Base($formula->xml);
        // Seta todas ar configuracoes da arvore
        $arvore->setAll($request->all(),$formula->fechar_automaticamente,$formula->ticar_automaticamente);
        if(!$arvore->montarArvore()){
            return  response()->json(['success' => false, 'msg'=>$arvore->getError()]); 
        }
        
        if(!$arvore->ticarNo($request->ticar['no'])){
            return  response()->json([
                'success' => false, 
                'msg'=>$arvore->getError(), 
                'data'=>$this->resposta->validaResposta($res_jog['resposta'],$exercicio),'ticar']); 
        }
        
        return  response()->json([
            'success' => true, 
            'msg'=>'', 
            'data'=>$arvore->retorno($exercicio->id,$request->usu_hash, $request->exe_hash)
            ]);
    }

    public function fecharNo(Request $request){
   
        $exercicio = ExercicioMVFLP::findOrFail($request->exercicio);
        $formula =  Formula::findOrFail($exercicio->id_formula);
        $res_jog= $this->buscaRespostaJogador($request->usu_hash,$exercicio);
        


        $arvore = new Base($formula->xml);
        // Seta todas ar configuracoes da arvore
        $arvore->setAll($request->all(),$formula->fechar_automaticamente,$formula->ticar_automaticamente);

        if(!$arvore->montarArvore()){
            return  response()->json(['success' => false, 'msg'=>$arvore->getError()]); 
        }

        if(!$arvore->fecharNo($request->fechar['folha'], $request->fechar['no'])){
            return  response()->json([
                'success' => false, 
                'msg'=>$arvore->getError(), 
                'data'=>$this->resposta->validaResposta($res_jog['resposta'],$exercicio),'fechar']);  
        }

        return  response()->json([
            'success' => true, 
            'msg'=>'', 
            'data'=>$arvore->retorno($exercicio->id,$request->usu_hash, $request->exe_hash)
            ]);
            
    }



    private function buscaRespostaJogador($usu_hash,$exercicio){

        $jogador = Jogador::where('token',$usu_hash)->get();
        if(count($jogador)==0){
            return  ['success' => false, 'msg'=>'Hash jogador Invalido']; 
        }
        $resposta = Resposta::where('id_jogador', '=',$jogador[0]->id)->where('id_exercicio','=',$exercicio->id)->first();

        return ['success' => true ,'jogador'=>$jogador, 'resposta'=>$resposta];

    }
}
