<?php

namespace App\Http\Controllers\Api\aluno;

use App\ExercicioMVFLP;
use App\Formula;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Arvore\Gerador;
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

        return  response()->json(['success' => true, 'msg'=>'', 'data'=>['listapcoes'=>$listaStr,'strformula'=>$formulaGerada]]);
       
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


        #ler a string xml, e a transforma em objeto
        try{$xml = simplexml_load_string($formula->xml);}
        catch(\Exception $e){return response()->json(['success' => false, 'msg'=>'XML INVALIDO!', 'data'=>''],500);}
        #-----

        #Inializa a arvore
        $listaArgumentos = $this->arg->CriaListaArgumentos($xml);
        $resposta=$this->gerador->inicializarPassoPasso($listaArgumentos,null,$request->listaInicial, null);
        #-----
        
        if($resposta['sucesso']==true){

            #Reconstroi a arvore
            $listaDerivacoes =$request->listaDerivacoes;
            $arvorePasso = $this->gerador->gerarArvorePassoPasso($resposta['arv'],$listaDerivacoes);
            #-----
 
            #Deriva a tentativa atual, caso erro retorna a mensagem
            $arvoreFinal =$this->gerador->derivar($arvorePasso,$request->derivacao,$request->insercao,$request->regra);
            if($arvoreFinal['sucesso']==true){
                #Adiciona a nova dericação a lista
                array_push( $listaDerivacoes, ['insercao'=>$request->insercao,'derivacao'=>$request->derivacao,'regra'=>$request->regra]);
                #-----


                #tica os nos já derivados
                $arvoreTicada = $this->gerador->ticarTodosNos($arvoreFinal['arv'], $request->listaTicagem);

                if($arvoreTicada['sucesso']==true){

                     #fechar todos os ramos
                    $arvoreFechada = $this->gerador->fecharTodosNos($arvorePasso, $request->listaFechamento);
                    if($arvoreFechada['sucesso']==true){

                        #Gera lista das possicoes de cada no da arvore
                        $impresaoAvr = $this->constr->geraListaArvore($arvoreTicada['arv'],700,350,0,$formula->ticar_automaticamente,$formula->fechar_automaticamente);
                    #-----

                    return  response()->json(['success' => true, 'msg'=>'', 'data'=>['impresao'=>$impresaoAvr,'lista'=>$resposta['lista'],'listaDerivacoes'=>$listaDerivacoes]]);

                    }
                    else{
                        return  response()->json(['success' => false, 'msg'=>$arvoreFechada['messagem'], 'data'=>'']);
                    }
                    #-----
                }
                else{
                    return  response()->json(['success' => false, 'msg'=>$arvoreTicada['messagem'], 'data'=>'']);
                }
                #-----


            }
            else{
                return  response()->json(['success' => false, 'msg'=>$arvoreFinal['messagem'], 'data'=>'']);
            }
            #-----
        
        } else{
            return  response()->json(['success' => false, 'msg'=>$resposta['messagem'], 'data'=>'']);
        }

    }

    public function ticarNo(Request $request){


        $exercicio = ExercicioMVFLP::findOrFail($request->idExercicio);
        $formula =  Formula::findOrFail($exercicio->id_formula);

        try{
            $xml = simplexml_load_string($formula->xml);
        }
        catch(\Exception $e){
            return response()->json(['success' => false, 'msg'=>'XML INVALIDO!', 'data'=>''],500);
        }



        $listaArgumentos = $this->arg->CriaListaArgumentos($xml);
        $resposta=$this->gerador->inicializarPassoPasso($listaArgumentos,null,$request->listaInicial, null);


        if($resposta['sucesso']==true){
            #Reconstroi a arvore
            $arvorePasso = $this->gerador->gerarArvorePassoPasso($resposta['arv'],$request->listaDerivacoes);
            #-----

            #tica os nos já derivados
            $arvoreTicada = $this->gerador->ticarTodosNos($arvorePasso, $request->listaTicagem);
            if($arvoreTicada['sucesso']==true){

                #fechar todos os ramos
                $arvoreFechada = $this->gerador->fecharTodosNos($arvorePasso, $request->listaFechamento);
                if($arvoreFechada['sucesso']==true){

                    $arvorefinal = $this->gerador->ticarNo($arvoreTicada['arv'], $request->no);
                    if( $arvorefinal['sucesso']==true){
                        #Gera lista das possicoes de cada no da arvore

                        $impresaoAvr = $this->constr->geraListaArvore($arvorefinal['arv'],700,350,0,$formula->ticar_automaticamente,$formula->fechar_automaticamente);
                        #-----
                        return  response()->json(['success' => true, 'msg'=>'', 'data'=>['impresao'=>$impresaoAvr,'noticado'=>$request->no]]);
                    }
                    else{
                        return  response()->json(['success' => false, 'msg'=>$arvorefinal['messagem']]);
                    }

                }
                else{
                    return  response()->json(['success' => false, 'msg'=>$arvoreFechada['messagem']]);
                }
                 #-----

            }
            else{
                return  response()->json(['success' => false, 'msg'=>$arvoreTicada['messagem'], 'data'=>'']);
            }
            #-----
        }else{
            return  response()->json(['success' => false, 'msg'=>$resposta['messagem'], 'data'=>'']);
        }
    }


    public function fecharNo(Request $request){


        
        $exercicio = ExercicioMVFLP::findOrFail($request->idExercicio);
        $formula =  Formula::findOrFail($exercicio->id_formula);

        try{
            $xml = simplexml_load_string($formula->xml);
        }
        catch(\Exception $e){
            return response()->json(['success' => false, 'msg'=>'XML INVALIDO!', 'data'=>''],500);
        }

        $listaArgumentos = $this->arg->CriaListaArgumentos($xml);
        $resposta=$this->gerador->inicializarPassoPasso($listaArgumentos,null,$request->listaInicial, null);

        if($resposta['sucesso']==true){
            #Reconstroi a arvore
            $arvorePasso = $this->gerador->gerarArvorePassoPasso($resposta['arv'],$request->listaDerivacoes);
            #-----

            #tica os nos já derivados
            $arvoreTicada = $this->gerador->ticarTodosNos($arvorePasso, $request->listaTicagem);

            if($arvoreTicada['sucesso']==true){


                #fechar todos os ramos
                $arvoreFechada = $this->gerador->fecharTodosNos($arvorePasso, $request->listaFechamento);
                if($arvoreFechada['sucesso']==true){

                    #valida a tentativa do usuario de fechamento do nó
                    $fechada = $this->gerador->fecharNo($arvoreFechada['arv'], $request->noFolha, $request->noContradicao);

                    if($fechada['sucesso']==true){
                        #Gera lista das possicoes de cada no da arvore
                        $impresaoAvr = $this->constr->geraListaArvore($fechada['arv'],700,350,0,$formula->ticar_automaticamente,$formula->fechar_automaticamente);
                        #-----
                        return  response()->json(['success' => true, 'msg'=>'', 'data'=>['impresao'=>$impresaoAvr,'nofechado'=>$request->noFolha, 'noContradicao'=>$request->noContradicao]]);
                    }
                    else{
                        return  response()->json(['success' => false, 'msg'=>$fechada['messagem'], 'data'=>'']);
                    }
                }
                else{
                    return  response()->json(['success' => false, 'msg'=>$arvoreFechada['messagem'], 'data'=>'']);    
                }
                #-----

               
            }

        }


    }
}
