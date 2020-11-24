<?php

namespace App\Http\Controllers\Api\aluno\modulos;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Base;
use Illuminate\Http\Request;

class estudoLivreController extends Controller
{
    
    public function arvore(Request $request){

        $arvore = new Base($request->xml);
        
        if(!$arvore->otimizada()){
            return  response()->json([
                'success' => false, 
                'msg'=>$arvore->getError() 
                ]); 
        }

        return  response()->json([
            'success' => true, 
            'msg'=>'', 
            'data'=>$arvore->retornoOtimizada()
            ]);

    }


    public function iniciar(Request $request){
        $arvore = new Base($request->xml);

        if(!$arvore->montarArvore()){
            return  response()->json(['success' => false, 'msg'=>'Error ar criar arvore', 'data'=>''],500);
        }

        return  response()->json([
            'success' => true, 
            'msg'=>'', 
            'data'=>[
                'arvore'=>$arvore->retorno(null,null, null)
                ]
            ]);

    }


    public function adiciona(Request $request){
        try{
            $arvore = new Base($request->xml);
            $arvore->setListaPassos($request->inicio['lista']);
            if(!$arvore->montarArvore($request->inicio['no']['id'],$request->inicio['negacao'])){
                return  response()->json([
                    'success' => false, 
                    'msg'=>$arvore->getError()
                    ]); 
            } 
    
            return  response()->json([
                'success' => true, 
                'msg'=>'', 
                'data'=>$arvore->retorno(null,null, null)
                ]);
        }catch(\Exception $e){
            return response()->json(['success' => false, 'msg'=>'erro interno', 'data'=>''],500);
        }
    }



    public function deriva(Request $request){

        $arvore = new Base($request->xml);
        // Seta todas as configuracoes da arvore
        $arvore->setAll($request->all(),false,false);
        if(!$arvore->derivar($request->derivacao['no']['idNo'],$request->derivacao['folhas'],$request->derivacao['regra'])){
            return  response()->json([
                'success' => false, 
                'msg'=>$arvore->getError(), 
                ]); 
        }
 
        return  response()->json([
            'success' => true, 
            'msg'=>'',
             'data'=>$arvore->retorno(null,null, null)
            ]);
    }


    public function tica(Request $request){

        $arvore = new Base($request->xml);
        // Seta todas ar configuracoes da arvore
        $arvore->setAll($request->all(),false,false);
        if(!$arvore->montarArvore()){
            return  response()->json(['success' => false, 'msg'=>$arvore->getError()]); 
        }
        
        if(!$arvore->ticarNo($request->ticar['no'])){
            return  response()->json([
                'success' => false, 
                'msg'=>$arvore->getError(), 
               ]); 
        }
        
        return  response()->json([
            'success' => true, 
            'msg'=>'', 
            'data'=>$arvore->retorno(null,null,null)
            ]);
    }


    public function fecha(Request $request){
        $arvore = new Base($request->xml);
        // Seta todas ar configuracoes da arvore
        $arvore->setAll($request->all(),false, false);

        if(!$arvore->montarArvore()){
            return  response()->json(['success' => false, 'msg'=>$arvore->getError()]); 
        }

        if(!$arvore->fecharNo($request->fechar['folha'], $request->fechar['no'])){
            return  response()->json([
                'success' => false, 
                'msg'=>$arvore->getError(), 
                ]);  
        }

        return  response()->json([
            'success' => true, 
            'msg'=>'', 
            'data'=>$arvore->retorno(null,null,null)
            ]);
            
    }
}
