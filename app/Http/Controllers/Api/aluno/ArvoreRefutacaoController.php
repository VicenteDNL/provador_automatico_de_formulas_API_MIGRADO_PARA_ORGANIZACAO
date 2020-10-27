<?php

namespace App\Http\Controllers\Api\aluno;

use App\ExercicioMVFLP;
use App\Formula;
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

    public function criarArvore(Request $request){

        $arvore =  new Base($request->xml) ;

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
        $impresaoAvr = $this->constr->geraListaArvore($arv,$request->width,($request->width/2),0,true,true);


         #Gera uma string da Formula XML
         $formulaGerada = $this->arg->stringFormula($xml);
         #--------

        return response()->json(['success' => true, 'msg'=>'', 'data'=>['impresao'=>$impresaoAvr,'str'=>$formulaGerada]]);

    }


    public function premissasConclusao(Request $request){
        try{
            $xml = simplexml_load_string($request->xml);
        }
        catch(\Exception $e){
            return response()->json(['success' => false, 'msg'=>'XML INVALIDO!', 'data'=>''],500);
        }

        $listaArgumentos = $this->arg->CriaListaArgumentos($xml);
        $listaStr = $this->constr->geraListaPremissasConclsao($listaArgumentos,[]);
        $formulaGerada = $this->arg->stringFormula($xml);

        return  response()->json([
            'success' => true, 
            'msg'=>'', 
            'data'=>['listapcoes'=>$listaStr,'strformula'=>$formulaGerada]
            ]);
       
    }


    public function adicionaNoIncializacao(Request $request){

        
        $exercicio = ExercicioMVFLP::findOrFail($request->idExercicio);
        $formula =  Formula::findOrFail($exercicio->id_formula);


        try{
            $xml = simplexml_load_string($formula->xml);
        }
        catch(\Exception $e){
            return response()->json(['success' => false, 'msg'=>'XML INVALIDO!', 'data'=>''],500);
        }

        
        $listaArgumentos = $this->arg->CriaListaArgumentos($xml);
        $resposta=$this->gerador->inicializarPassoPasso($listaArgumentos,$request->idNo,$request->lista, $request->negacao);
        
        
        if($resposta['sucesso']==true){
            $impresaoAvr = $this->constr->geraListaArvore($resposta['arv'],700,350,0);
            $listaStr = $this->constr->geraListaPremissasConclsao($listaArgumentos,$resposta['lista']);
            $finalizado= count($listaStr)==0? true:false;
            return  response()->json(['success' => true, 'msg'=>'', 'data'=>['impresao'=>$impresaoAvr,'lista'=>$resposta['lista'],'listasopcoes'=>$listaStr, 'finalizado'=>$finalizado]]);
        }
        else{
            return  response()->json(['success' => false, 'msg'=>$resposta['messagem'], 'data'=>'']);
        }

    }
    public function derivar(Request $request){

        $exercicio = ExercicioMVFLP::findOrFail($request->idExercicio);
        $formula =  Formula::findOrFail($exercicio->id_formula);

        $arvore = new Base($formula->xml);
        $arvore->setListaPassos($request->listaInicial );
        $arvore->setListaTicagem($request->listaTicagem);
        $arvore->setListaFechamento($request->listaFechamento);
        $arvore->derivacao->setListaDerivacoes($request->listaDerivacoes);


        if(!$arvore->derivar($request->derivacao,$request->insercao,$request->regra)){
            return  response()->json(['success' => false, 'msg'=>'error ao construir arvore'],500); 
        }
 

        #Gera lista das possicoes de cada no da arvore
        $impresaoAvr = $this->constr->geraListaArvore($arvore->getArvore(),700,350,0,$formula->ticar_automaticamente,$formula->fechar_automaticamente);
        #-----

        return  response()->json([
            'success' => true, 
            'msg'=>'', 'data'=>[
                'impresao'=>$impresaoAvr,
                'lista'=>$arvore->getListaPassos(),
                'listaDerivacoes'=> $arvore->derivacao->getListaDerivacoes()
                ]
            ]);



    }

    public function ticarNo(Request $request){
        $exercicio = ExercicioMVFLP::findOrFail($request->idExercicio);
        $formula =  Formula::findOrFail($exercicio->id_formula);

        $arvore = new Base($formula->xml);
        $arvore->setListaPassos($request->listaInicial );
        $arvore->setListaTicagem($request->listaTicagem);
        $arvore->setListaFechamento($request->listaFechamento);
        $arvore->derivacao->setListaDerivacoes($request->listaDerivacoes);


        if(!$arvore->montarArvore()){
            return  response()->json(['success' => false, 'msg'=>'error ao construir arvore'],500); 
        }

        $arvorefinal = $this->gerador->ticarNo($arvore->getArvore(), $request->no);
        if(!$arvorefinal['sucesso']){
            return  response()->json(['success' => false, 'msg'=>$arvorefinal['messagem']]);
        }
        
        #Gera lista das possicoes de cada no da arvore
        $impresaoAvr = $this->constr->geraListaArvore($arvorefinal['arv'],700,350,0,$formula->ticar_automaticamente,$formula->fechar_automaticamente);
        return  response()->json(['success' => true, 'msg'=>'', 'data'=>['impresao'=>$impresaoAvr,'noticado'=>$request->no]]);
    }


    public function fecharNo(Request $request){
   
        $exercicio = ExercicioMVFLP::findOrFail($request->idExercicio);
        $formula =  Formula::findOrFail($exercicio->id_formula);

        $arvore = new Base($formula->xml);
        $arvore->setListaPassos($request->listaInicial );
        $arvore->setListaTicagem($request->listaTicagem);
        $arvore->setListaFechamento($request->listaFechamento);
        $arvore->derivacao->setListaDerivacoes($request->listaDerivacoes);

        if(!$arvore->montarArvore()){
            return  response()->json(['success' => false, 'msg'=>'error ao construir arvore'],500); 
        }

        // var_dump($arvore->getArvore());
        #valida a tentativa do usuario de fechamento do nó
        $fechada = $this->gerador->fecharNo($arvore->getArvore(), $request->noFolha, $request->noContradicao);

        if(!$fechada['sucesso']){
            return  response()->json(['success' => false, 'msg'=>$fechada['messagem'], 'data'=>'']);   
        }

        #Gera lista das possicoes de cada no da arvore
        $impresaoAvr = $this->constr->geraListaArvore($fechada['arv'],700,350,0,$formula->ticar_automaticamente,$formula->fechar_automaticamente);
        #-----
        return  response()->json(['success' => true, 'msg'=>'', 'data'=>['impresao'=>$impresaoAvr,'nofechado'=>$request->noFolha, 'noContradicao'=>$request->noContradicao]]);
            

   
    }
}
