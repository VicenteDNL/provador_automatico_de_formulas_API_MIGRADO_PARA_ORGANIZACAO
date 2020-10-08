<?php

namespace App\Http\Controllers\Api\aluno;

use App\Http\Controllers\Controller;
use App\ExercicioMVFLP;
use App\Formula;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Arvore\Gerador;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Construcao;
use App\Http\Controllers\ModuloArvoreDeRefutacao\Formula\Argumento;
use Illuminate\Http\Request;

class ExercicioMvflpController extends Controller
{


    public function __construct(ExercicioMVFLP $exercicio )
    {
        $this->exercicio = $exercicio; 
        $this->arg = new Argumento;
        $this->gerador = new Gerador;
        $this->constr = new Construcao;
    }


    public function buscarExercicio($id){

        $exercicio = ExercicioMVFLP::findOrFail($id);
        $formula =  Formula::findOrFail($exercicio->id_formula);
        $formula->lista_passos =json_decode ($formula->lista_passos,true);
        $formula->lista_derivacoes =json_decode ($formula->lista_derivacoes,true);
        $formula->lista_ticagem =json_decode ($formula->lista_ticagem,true);
        $formula->lista_fechamento =json_decode ($formula->lista_fechamento,true);

        #ler a string xml, e a transforma em objeto
        try{$xml = simplexml_load_string($formula->xml);}
        catch(\Exception $e){return response()->json(['success' => false, 'msg'=>'XML INVALIDO!', 'data'=>''],500);}
        #-----

        if($formula->iniciar_zerada==true && $formula->inicio_personalizado==false){

            $listaArgumentos = $this->arg->CriaListaArgumentos($xml);
            $listaStr = $this->constr->geraListaPremissasConclsao($listaArgumentos,[]);
            $formulaGerada = $this->arg->stringFormula($xml);

            return  response()->json(['success' => true, 'msg'=>'', 'data'=>['impresao'=>['nos'=>[],'arestas'=>[]],'formula'=>$formula, 'exercicio'=>$exercicio, 'listapcoes'=>$listaStr,'strformula'=>$formulaGerada]]);
        }

        else{
            #Cria a arvore inicial do exercicio
                       

            #ler a string xml, e a transforma em objeto
            try{$xml = simplexml_load_string($formula->xml);}
            catch(\Exception $e){return response()->json(['success' => false, 'msg'=>'XML INVALIDO!', 'data'=>''],500);}
            #-----

            #Inializa a arvore
            $listaArgumentos = $this->arg->CriaListaArgumentos($xml);
            $resposta=$this->gerador->inicializarPassoPasso($listaArgumentos,null,$formula->lista_passos, null);
            #-----
        
            if($resposta['sucesso']==true){
                #Reconstroi a arvore
                $listaDerivacoes =$formula->lista_derivacoes;
              
                $arvorePasso = $this->gerador->gerarArvorePassoPasso($resposta['arv'],$listaDerivacoes);
                #-----

                #tica os nos já derivados
                $arvoreTicada = $this->gerador->ticarTodosNos($arvorePasso, $formula->lista_ticagem);


                if($arvoreTicada['sucesso']==true){

                    #fechar informados os ramos
                    $arvoreFechada = $this->gerador->fecharTodosNos($arvorePasso, $formula->lista_fechamento);

                    if($arvoreFechada['sucesso']==true){
                        #Gera lista das possicoes de cada no da arvore
                        $impresaoAvr = $this->constr->geraListaArvore($arvoreTicada['arv'],700,350,0);
                        #-----
                        return  response()->json(['success' => true, 'msg'=>'', 'data'=>['impresao'=>$impresaoAvr,'formula'=>$formula, 'exercicio'=>$exercicio]]);
                    }
                    else{
                        return  response()->json(['success' => false, 'msg'=>$arvoreFechada['messagem'], 'data'=>'']);
                    }
                    #-----
                }
                else{
                    return  response()->json(['success' => false, 'msg'=>$arvoreTicada['messagem'], 'data'=>'']); 
                }
            }
            else{
                return  response()->json(['success' => false, 'msg'=>$resposta['messagem'], 'data'=>'']); 
            }

        }
        

    }

    public function criarArvoreExercicio(Request $request){

      
    }
}
