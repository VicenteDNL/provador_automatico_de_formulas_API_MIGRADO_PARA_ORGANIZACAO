<?php

namespace App\Http\Controllers\Api\admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Arvore\Gerador;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Base;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Construcao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Formula\Argumento;
use Illuminate\Http\Request;

class ArvoreRefutacaoController extends Controller
{

    function __construct() {
        $this->arg = new Argumento;
        $this->gerador = new Gerador;
        $this->constr = new Construcao;


  }

    public function criarArvoreOtimizada(Request $request){

        try{
            $xml = simplexml_load_string($request->xml);
        }
        catch(\Exception $e){
            return response()->json(['success' => false, 'msg'=>'XML INVALIDO!', 'data'=>''],500);
        }


        #Cria a arvore passando o XML
        $listaArgumentos = $this->arg->CriaListaArgumentos($xml);
        $arvore = $this->gerador->inicializarDerivacao($listaArgumentos['premissas'],$listaArgumentos['conclusao']);
        $arv =  $this->gerador->arvoreOtimizada($arvore);
        #--------

        #Gera lista das possicoes de cada no da tabela
        $impresaoAvr = $this->constr->geraListaArvore($arv,$xml,0,true,true);


         #Gera uma string da Formula XML
         $formulaGerada = $this->arg->stringFormula($xml);
         #--------

        return response()->json(['success' => true, 'msg'=>'', 'data'=>['impresao'=>$impresaoAvr,'str'=>$formulaGerada]]);

    }

    public function criarPiorArvore (Request $request){
        try{
            $xml = simplexml_load_string($request->xml);
        }
        catch(\Exception $e){
            return response()->json(['success' => false, 'msg'=>'XML INVALIDO!', 'data'=>''],500);
        }

        #Cria a arvore passando o XML
        $listaArgumentos = $this->arg->CriaListaArgumentos($xml);
        $arvore = $this->gerador->inicializarDerivacao($listaArgumentos['premissas'],$listaArgumentos['conclusao']);
        $arv =  $this->gerador->piorArvore($arvore);

        #Gera lista das possicoes de cada no da tabela
        $impresaoAvr = $this->constr->geraListaArvore($arv,$xml,0,true,true);


        #Gera uma string da Formula XML
        $formulaGerada = $this->arg->stringFormula($xml);
        #--------

        return response()->json(['success' => true, 'msg'=>'', 'data'=>['impresao'=>$impresaoAvr,'str'=>$formulaGerada]]);


    }


    public function premissasConclusao(Request $request){

        $arvore = new Base($request->xml);
        $arvore->setListaPassos( []);
        $arvore->setListaTicagem([]);
        $arvore->setListaFechamento([]);
        $arvore->derivacao->setListaDerivacoes([]);
        $arvore->fecharAutomatido(false);
        $arvore->ticarAutomatico(false);

        if(!$arvore->montarArvore()){
            return  response()->json(['success' => false, 'msg'=>'Error ar criar arvore', 'data'=>''],500);
        }

        return  response()->json([
                'success' => true,
                'msg'=>'',
                'data'=>$arvore->retorno(null,$request->usu_hash, $request->exe_hash, true)
            ]);

    }


    public function adicionaNoIncializacao(Request $request){
        // try{

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
                'data'=>$arvore->retorno(null,$request->usu_hash, $request->exe_hash,true)
                ]);

        // }catch(\Exception $e){
        //     return response()->json(['success' => false, 'msg'=>'erro interno', 'data'=>''],500);
        // }

    }
    public function derivar(Request $request){


        $arvore = new Base($request->xml);
        $arvore->setListaPassos($request->inicio['lista']);
        $arvore->setListaTicagem($request->ticar['lista']);
        $arvore->setListaFechamento($request->fechar['lista']);
        $arvore->derivacao->setListaDerivacoes($request->derivacao['lista']);
        $arvore->fecharAutomatido(false);
        $arvore->ticarAutomatico(false);
        $arvore->inicializacao->setFinalizado(true);

        if(!$arvore->derivar($request->derivacao['no']['idNo'],$request->derivacao['folhas'],$request->derivacao['regra'])){
            return  response()->json([
                'success' => false,
                'msg'=>$arvore->getError(),
                ]);
        }

        return  response()->json([
            'success' => true,
            'msg'=>'',
             'data'=>$arvore->retorno(null,$request->usu_hash, $request->exe_hash,true)
            ]);

    }

    public function ticarNo(Request $request){


        $arvore = new Base($request->xml);
        $arvore->setListaPassos($request->inicio['lista']);
        $arvore->setListaTicagem($request->ticar['lista']);
        $arvore->setListaFechamento($request->fechar['lista']);
        $arvore->derivacao->setListaDerivacoes($request->derivacao['lista']);
        $arvore->fecharAutomatido(false);
        $arvore->ticarAutomatico(false);
        $arvore->inicializacao->setFinalizado(true);

        if(!$arvore->montarArvore()){
            return  response()->json(['success' => false, 'msg'=>$arvore->getError()]);
        }

        if(!$arvore->ticarNo($request->ticar['no'])){
            return  response()->json([
                'success' => false,
                'msg'=>$arvore->getError()
                ]);
        }

        return  response()->json([
            'success' => true,
            'msg'=>'',
            'data'=>$arvore->retorno(null,$request->usu_hash, $request->exe_hash,true)
            ]);
    }


    public function fecharNo(Request $request){

        $arvore = new Base($request->xml);
        $arvore->setListaPassos($request->inicio['lista']);
        $arvore->setListaTicagem($request->ticar['lista']);
        $arvore->setListaFechamento($request->fechar['lista']);
        $arvore->derivacao->setListaDerivacoes($request->derivacao['lista']);
        $arvore->fecharAutomatido(false);
        $arvore->ticarAutomatico(false);
        $arvore->inicializacao->setFinalizado(true);

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
            'data'=>$arvore->retorno(null,$request->usu_hash, $request->exe_hash,true)
            ]);


    }

}
